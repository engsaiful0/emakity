<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use WebToPay;
use WebToPayException;

use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\MarketRepository;
use App\Repositories\UserRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\CartRepository;
use App\Repositories\TestimonialRepository;
use Flash, Auth;


use App\Criteria\Products\NearCriteria;
use App\Criteria\Products\ProductsOfCategoriesCriteria;
use App\Criteria\Products\ProductsOfFieldsCriteria;
use App\Criteria\Products\ProductCurrencyCriteria;
use App\Criteria\Products\ProductCountryCriteria;
use App\Criteria\Products\TrendingWeekCriteria;
use App\Criteria\Markets\MarketCurrencyCriteria;
use App\Criteria\Markets\MarketCountryCriteria;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\State;
use App\Models\Area;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Market;
use Session;

class HomeController extends Controller
{
    private $orderRepository;
    private $userRepository;
    private $marketRepository;
    private $paymentRepository;
    private $categoryRepository;
    private $productRepository;
    private $cartRepository;

    public function __construct(OrderRepository $orderRepo, UserRepository $userRepo, PaymentRepository $paymentRepo, MarketRepository $marketRepo, CategoryRepository $categoryRepo, ProductRepository $productRepo, CartRepository $cartRepo, TestimonialRepository $testimonialRepo)
    {
        parent::__construct();
        $this->orderRepository = $orderRepo;
        $this->userRepository = $userRepo;
        $this->marketRepository = $marketRepo;
        $this->categoryRepository = $categoryRepo;
        $this->productRepository = $productRepo;
        $this->paymentRepository = $paymentRepo;
        $this->cartRepository = $cartRepo;
        $this->testimonialRepository = $testimonialRepo;
    }

    public function index(Request $request)
    {
        //return redirect('login'); 

        $products=new Product();
        $markets=new Market();
        if ($request->session()->has('country')) {
            
            $country = $request->session()->get('country');
            $country = Country::where('code', $country)->get()->first();
            $country_id = $country->id;
            
            $products=$products->whereHas('market',function($query) use ($country_id){
                $query->where('markets.country_id',$country_id);
            });

            $markets=$markets->where('country_id',$country_id);
        }

        if ($request->has('fields')) 
        {
            $fields = $request->get('fields');
            if (in_array('0', $fields)) 
            { 
                $products=$products;
            }
            else
            {
                $products =  $products->join('market_fields', 'market_fields.market_id', '=', 'products.market_id')
                ->whereIn('market_fields.field_id', $request->get('fields', []))->groupBy('products.id');
            }

        }
        $products=$products->get();

        if ($request->input('first_query') == 'delivery') {
            if ($request->input('second_query') == 'open') 
            {
                $markets=$markets->where('available_for_delivery',1)->where('closed',0);
            } 
            else 
            {
                $markets=$markets->where('available_for_delivery',1);
            }
        } 
        else if($request->input('second_query') == 'open') 
        {
            $markets=$markets->where('closed',0);
        }
        
        $markets=$markets->get();


        $testimonials = $this->testimonialRepository->all();



        return view('frontend.home', compact('products', 'markets', 'testimonials'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $request['search'] = 'name:' . $query . ';description;' . $query;
        $request['searchFields'] = 'name:like;description:like';

        //dd($request->all());


        $this->productRepository->pushCriteria(new RequestCriteria($request));
        $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->productRepository->pushCriteria(new ProductsOfFieldsCriteria($request));
        $this->productRepository->pushCriteria(new ProductCurrencyCriteria($request));
        $this->productRepository->pushCriteria(new ProductCountryCriteria($request));
        if ($request->get('trending', null) == 'week') {
            $this->productRepository->pushCriteria(new TrendingWeekCriteria($request));
        } else {
            $this->productRepository->pushCriteria(new NearCriteria($request));
        }

        $this->marketRepository->pushCriteria(new RequestCriteria($request));
        $this->marketRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->marketRepository->pushCriteria(new MarketCurrencyCriteria($request));
        $this->marketRepository->pushCriteria(new MarketCountryCriteria($request));
        $markets = $this->marketRepository->all();

        $products = $this->productRepository->all();

        return view('frontend.search', compact('products', 'markets'));
    }

    public function product($id)
    {
        $products = $this->productRepository->all();
        $product = $this->productRepository->find($id);
        $user = $this->userRepository->pluck('name', 'id');
        $customFields = false;
        return view('frontend.product', compact('user', 'product', 'products', 'customFields'));
    }

    public function market($id)
    {
        $market = $this->marketRepository->find($id);
        $user = $this->userRepository->pluck('name', 'id');
        $customFields = false;
        return view('frontend.market', compact('user', 'market', 'customFields'));
    }
    public function cart()
    {
        $products = $this->cartRepository->with('product', 'options')->where('user_id', auth()->id())->get();
        $product = $this->cartRepository->with('product', 'product.market', 'product.market.currency')->where('user_id', Auth::user()->id)->first();
        if (empty($product)) {
            Flash::error('Please Select Products in Cart First');
            return redirect(route('home'));
        }
        $deliveryAddress = getDeliveryAddressID();
        return view('frontend.carts.index')->with("customFields", isset($html) ? $html : false)->with("deliveryAddress", $deliveryAddress)->with("product", $product)->with("products", $products);
    }
    public function cart_ajax()
    {
        $products = $this->cartRepository->with('product', 'options')->where('user_id', auth()->id())->get();
        $data = view('frontend.carts.all', ['products' => $products])->render();
        return $this->sendResponse($data, __('lang.saved_successfully', ['operator' => __('lang.cart')]));
    }
    public function cart_ajax_update(Request $request)
    {
        $cart = $this->cartRepository->find($request->input('cart_id'));
        
        if ($cart) {
            if ($request->type == 'update_cart') {
                if($cart->product->in_stock < ($cart->quantity+1)){
                    return $this->sendError('Maximum product quantity should be less then or Equal to '.$cart->product->in_stock,500);
                }
                $cart->quantity = $cart->quantity + 1;
                $cart->update();
            } else {
                $cart->quantity = $cart->quantity - 1;
                $cart->update();
            }
        }
        $products = $this->cartRepository->with('product', 'options')->where('user_id', auth()->id())->get();
        $data = view('frontend.carts.all', ['products' => $products])->render();
        return $this->sendResponse($data, __('lang.saved_successfully', ['operator' => __('lang.cart')]));
    }

    public function cart_ajax_delete(Request $request)
    {
        $cart = $this->cartRepository->find($request->input('cart_id'));
        $cart->delete();
        $products = $this->cartRepository->with('product', 'options')->where('user_id', auth()->id())->get();
        $data = view('frontend.carts.all', ['products' => $products])->render();
        return $this->sendResponse($data, __('lang.deleted_successfully', ['operator' => __('lang.cart')]));
    }
    public function ajaxGetStates()
    {
        $country_id = $_GET['country_id'];
        $states = State::where('country_id', $country_id)->get();
        return response()->json($states);
    }
    public function ajaxGetAreas()
    {
        $state_id = $_GET['state_id'];
        $areas = Area::where('state_id', $state_id)->get();
        return response()->json($areas);
    }
    public function changeCountry(Request $request)
    {

        //dd($request->all());
        $request->session()->put('country', $request->country);

        $country = Country::where('code', $request->country)->get()->first();
        //dd($request->country);

        $currency = Currency::where('code', $country->currency->code)->first();
        if (!$currency) {
            $currency = Currency::get()->first();
            $request->session()->put('currency_code', $currency->code);
            flash('Currency changed to' . $currency->name)->success();
            return $this->sendResponse($currency, __('lang.saved_successfully', ['operator' => __('lang.country')]));
        } else {
            $request->session()->put('currency_code', $currency->code);
            return $this->sendResponse($currency, __('lang.saved_successfully', ['operator' => __('lang.country')]));
        }
    }
}
