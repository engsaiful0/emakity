<?php

namespace App\Http\Controllers\API;


use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;

/**
 * Class PaymentController
 * @package App\Http\Controllers\API
 */
class PaymentAPIController extends Controller
{
    /** @var  PaymentRepository */
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the Payment.
     * GET|HEAD /payments
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->paymentRepository->pushCriteria(new RequestCriteria($request));
            $this->paymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $payments = $this->paymentRepository->all();

        return $this->sendResponse($payments->toArray(), 'Payments retrieved successfully');
    }

    /**
     * Display the specified Payment.
     * GET|HEAD /payments/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Payment $payment */
        if (!empty($this->paymentRepository)) {
            $payment = $this->paymentRepository->findWithoutFail($id);
        }

        if (empty($payment)) {
            return $this->sendError('Payment not found');
        }

        return $this->sendResponse($payment->toArray(), 'Payment retrieved successfully');
    }

    public function byMonth()
    {
        $payments = [];
        if (!empty($this->paymentRepository)) {
            if (auth()->user()->hasRole('branch'))
            {
                $payments = $this->paymentRepository->whereHas('user.country', function($q){
                    return $q->where('countries.id',get_role_country_id('branch'));
                })->where('status','Paid')->orderBy("created_at",'asc')->get()->map(function ($row) {
                    $row['month'] = $row['created_at']->format('M');
                    return $row;
                })->groupBy('month')->map(function ($row) {
                    return $row->sum('price');
                });
            }
            else if (auth()->user()->hasRole('manager'))
            {
                $payments = $this->paymentRepository->join("orders", "payments.id", "=", "orders.payment_id")
                ->join("product_orders", "orders.id", "=", "product_orders.order_id")
                ->join("products", "products.id", "=", "product_orders.product_id")
                ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
                ->where('user_markets.user_id', auth()->id())
                ->where('payments.status','Paid')
                ->groupBy('payments.id')
                ->orderBy('payments.created_at', 'asc')
                ->select('payments.*')->get()->map(function ($row) {
                    $row['month'] = $row['created_at']->format('M');
                    return $row;
                })->groupBy('month')->map(function ($row) {
                    return $row->sum('price');
                });
            }
            else
            {
                $payments = $this->paymentRepository->orderBy("created_at",'asc')->all()->map(function ($row) {
                    $row['month'] = $row['created_at']->format('M');
                    return $row;
                })->groupBy('month')->map(function ($row) {
                    return $row->sum('price');
                });

            }
        }
        return $this->sendResponse([array_values($payments->toArray()),array_keys($payments->toArray())], 'Payment retrieved successfully');
    }
}
