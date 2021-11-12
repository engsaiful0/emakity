<?php
/**
 * File name: OrderController.php
 * Last modified: 2020.05.05 at 16:55:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;

use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Criteria\Users\ClientsCriteria;
use App\Criteria\Users\DriversCriteria;
use App\Criteria\Users\DriversOfMarketCriteria;
use App\DataTables\OrderDataTable;
use App\DataTables\ProductOrderDataTable;
use App\Events\OrderChangedEvent;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Product;
use App\Notifications\AssignedOrder;
use App\Notifications\StatusChangedOrder;
use App\Repositories\CustomFieldRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderStatusRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use App\Repositories\CartRepository;
use App\Repositories\ProductOrderRepository;
use Flash,Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Notifications\NewOrder;
class CheckoutController extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;
    private $cartRepository;
    private $productOrderRepository;
    

    public function __construct(OrderRepository $orderRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo
        , OrderStatusRepository $orderStatusRepo, NotificationRepository $notificationRepo, PaymentRepository $paymentRepo
        , CartRepository $cartRepo,ProductOrderRepository $productOrderRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->orderStatusRepository = $orderStatusRepo;
        $this->notificationRepository = $notificationRepo;
        $this->paymentRepository = $paymentRepo;
        $this->cartRepository = $cartRepo;
        $this->productOrderRepository = $productOrderRepository;
    }

    /**
     * Display a listing of the Order.
     *
     * @param OrderDataTable $orderDataTable
     * @return Response
     */
    public function index()
    {
        $products=$this->cartRepository->with('product','options')->where('user_id',auth()->id())->get();
        $product = $this->cartRepository->with('product','product.market','product.market.currency')->where('user_id',Auth::user()->id)->first();
        if(empty($product))
        {
            Flash::error('Please Select Products in Cart First');
            return redirect(route('home'));
        }
        $deliveryAddress=getDeliveryAddressID();
        //dd($product);
        return view('frontend.checkout')->with("customFields", isset($html) ? $html : false)->with("deliveryAddress", $deliveryAddress)->with("product", $product)->with("products", $products);
    }

    /**
     * Show the form for creating a new Order.
     *
     * @return Response
     */
    public function create()
    {
        //dd(auth()->user()->getRoleNames()->toArray());
        $user = $this->userRepository->getByCriteria(new ClientsCriteria())->pluck('name', 'id');
        $driver = $this->userRepository->getByCriteria(new DriversCriteria())->pluck('name', 'id');

        $orderStatus = $this->orderStatusRepository->pluck('status', 'id');

        $hasCustomField = in_array($this->orderRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
            $html = generateCustomField($customFields);
        }
        $product = $this->cartRepository->with('product','product.market')->where('user_id',Auth::user()->id)->first();
        if(empty($product))
        {
            Flash::error('Please Select Cart First');
            return redirect(route('carts.create'));
        }
        if(count(Auth::user()->deliveryAddress()->get())==0)
        {
            Flash::error('Please Add Delivery Address First');
            return redirect(route('deliveryAddresses.create'));
        }
        //dd(count(Auth::user()->deliveryAddress()->get()));
        return view('orders.create')->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("driver", $driver)->with("orderStatus", $orderStatus)->with("product", $product);
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param CreateOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderRequest $request)
    {
        //dd('asas');
        $input = $request->all();
        updateDeliveryAddress($input['delivery_address_id'],$input);
        try {
            $payment = $request->only('payment');
            //dd($request->all());
            if (isset($payment) && $payment) {
                if ($payment == "Credit Card (Stripe Gateway)") {
                    return $this->stripPayment($request);
                } else {
                    return $this->cashPayment($request);
    
                }
            }

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    private function stripPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {
            $user = $this->userRepository->findWithoutFail($input['user_id']);
            if (empty($user)) {
                return $this->sendError('User not found');
            }
            $stripeToken = Token::create(array(
                "card" => array(
                    "number" => $input['stripe_number'],
                    "exp_month" => $input['stripe_exp_month'],
                    "exp_year" => $input['stripe_exp_year'],
                    "cvc" => $input['stripe_cvc'],
                    "name" => $user->name,
                )
            ));
            if ($stripeToken->created > 0) {
                if (empty($input['delivery_address_id'])) {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'hint')
                    );
                } else {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
                    );
                }

                $products=$this->cartRepository->with('product','options')->where('user_id',$input['user_id'])->get();
                // dd($products);
                foreach ($products as $productOrder) {
                    $amount += getPriceValue($productOrder['product'],'discount_price') * $productOrder['quantity'];
                    $productOrder['order_id'] = $order->id;
                    $productOrder['price'] = getPriceValue($productOrder['product'],'discount_price') * $productOrder['quantity'];           
                    //dd($productOrder);
                    $this->productOrderRepository->create($productOrder->only('price','quantity','product_id','order_id'));
                    $update_product=Product::find($productOrder['product_id']);
                    $update_product->in_stock=$update_product->in_stock-$productOrder['quantity'];
                    $update_product->save();
                }
                $amount += $order->delivery_fee;
                $amountWithTax = $amount + ($amount * $order->tax / 100);
                $charge = $user->charge((int)($amountWithTax * 100), ['source' => $stripeToken]);
                $payment = $this->paymentRepository->create([
                    "user_id" => $input['user_id'],
                    "description" => trans("lang.payment_order_done"),
                    "price" => $amountWithTax,
                    "status" => $charge->status, // $charge->status
                    "method" => $input['payment'],
                ]);
                $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

                $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

                Notification::send($order->productOrders[0]->product->market->users, new NewOrder($order));
            }
        } catch (ValidatorException $e) {
            Log::error($e->getMessage());
        }
        Flash::success(__('lang.created_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function cashPayment(Request $request)
    {
        
        $input = $request->all();
        $amount = 0;
        try {
            $order = $this->orderRepository->create(
                $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
            );
            $order->delivery_address_id=getDeliveryAddressID()->id;
            $order->save();
            //Log::info($input['products']);
            $products=$this->cartRepository->with('product','options')->where('user_id',$input['user_id'])->get();
           // dd($products);
            foreach ($products as $productOrder) {
                $amount += getPriceValue($productOrder['product'],'discount_price') * $productOrder['quantity'];
                $productOrder['order_id'] = $order->id;
                $productOrder['price'] = getPriceValue($productOrder['product'],'discount_price') * $productOrder['quantity'];           
                //dd($productOrder);
                $this->productOrderRepository->create($productOrder->only('price','quantity','product_id','order_id'));
                $update_product=Product::find($productOrder['product_id']);
                $update_product->in_stock=$update_product->in_stock-$productOrder['quantity'];
                $update_product->save();
            }
            $amount += $order->delivery_fee;
            $amountWithTax = $amount + ($amount * $order->tax / 100);
            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_order_waiting"),
                "price" => $amountWithTax,
                "status" => 'Waiting for Client',
                "method" => $input['payment'],
            ]);

            $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

            $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

            Notification::send($order->productOrders[0]->product->market->users, new NewOrder($order));

        } catch (ValidatorException $e) {
            Log::error($e->getMessage());
        }

        Flash::success(__('lang.created_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    /**
     * Display the specified Order.
     *
     * @param int $id
     * @param ProductOrderDataTable $productOrderDataTable
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */

    public function show(ProductOrderDataTable $productOrderDataTable, $id)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $order = $this->orderRepository->findWithoutFail($id);
        if (empty($order)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

            return redirect(route('orders.index'));
        }
        $subtotal = 0;

        foreach ($order->productOrders as $productOrder) {
            foreach ($productOrder->options as $option) {
                $productOrder->price += $option->price;
            }
            $subtotal += $productOrder->price * $productOrder->quantity;
        }

        $total = $subtotal + $order['delivery_fee'];
        $taxAmount = $total * $order['tax'] / 100;
        $total += $taxAmount;
        $productOrderDataTable->id = $id;

        return $productOrderDataTable->render('orders.show', ["order" => $order, "total" => $total, "subtotal" => $subtotal,"taxAmount" => $taxAmount]);
    }

    /**
     * Show the form for editing the specified Order.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit($id)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $order = $this->orderRepository->findWithoutFail($id);
        if (empty($order)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

            return redirect(route('orders.index'));
        }

        $market = $order->productOrders()->first();
        $market = isset($market) ? $market->product['market_id'] : 0;

        $user = $this->userRepository->getByCriteria(new ClientsCriteria())->pluck('name', 'id');
        $driver = $this->userRepository->getByCriteria(new DriversOfMarketCriteria($market))->pluck('name', 'id');
        $orderStatus = $this->orderStatusRepository->pluck('status', 'id');


        $customFieldsValues = $order->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
        $hasCustomField = in_array($this->orderRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        $product = $this->cartRepository->with('product','product.market')->where('user_id',Auth::user()->id)->first();

        return view('orders.edit')->with('order', $order)->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("driver", $driver)->with("orderStatus", $orderStatus)->with("product", $product);
    }

    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param UpdateOrderRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateOrderRequest $request)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $oldOrder = $this->orderRepository->findWithoutFail($id);
        if (empty($oldOrder)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));
            return redirect(route('orders.index'));
        }
        $oldStatus = $oldOrder->payment->status;
        $input = $request->except('payment');
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
        try {

            $order = $this->orderRepository->update($input, $id);

            if (setting('enable_notifications', false)) {
                if (isset($input['order_status_id']) && $input['order_status_id'] != $oldOrder->order_status_id) {
                    Notification::send([$order->user], new StatusChangedOrder($order));
                }

                if (isset($input['driver_id']) && ($input['driver_id'] != $oldOrder['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedOrder($order));
                    }
                }
            }

            $this->paymentRepository->update([
                "status" => $input['status'],
            ], $order['payment_id']);
            //dd($input['status']);

            event(new OrderChangedEvent($oldStatus, $order));

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $order->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    /**
     * Remove the specified Order from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function destroy($id)
    {
        if (!env('APP_DEMO', false)) {
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
            $order = $this->orderRepository->findWithoutFail($id);

            if (empty($order)) {
                Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

                return redirect(route('orders.index'));
            }

            $this->orderRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.order')]));


        } else {
            Flash::warning('This is only demo app you can\'t change this section ');
        }
        return redirect(route('orders.index'));
    }

    /**
     * Remove Media of Order
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $order = $this->orderRepository->findWithoutFail($input['id']);
        try {
            if ($order->hasMedia($input['collection'])) {
                $order->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
