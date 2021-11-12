<?php

namespace App\Http\Controllers\API;

use App\Criteria\Earnings\EarningOfUserCriteria;
use App\Criteria\Markets\MarketsOfManagerCriteria;
use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Criteria\Products\ProductsOfUserCriteria;
use App\Repositories\EarningRepository;
use App\Repositories\MarketRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class DashboardAPIController extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;

    /** @var  MarketRepository */
    private $marketRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var EarningRepository
     */
    private $earningRepository;
    private $paymentRepository;


    public function __construct(OrderRepository $orderRepo, EarningRepository $earningRepository, MarketRepository $marketRepo, PaymentRepository $paymentRepo,ProductRepository $productRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepo;
        $this->marketRepository = $marketRepo;
        $this->productRepository = $productRepository;
        $this->earningRepository = $earningRepository;
        $this->paymentRepository = $paymentRepo;

    }

    /**
     * Display a listing of the Faq.
     * GET|HEAD /faqs
     * @param  int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manager($id, Request $request)
    {
        $statistics = [];
        try{

            //$this->earningRepository->pushCriteria(new EarningOfUserCriteria(auth()->id()));
            $earning['description'] = 'total_earning';
            $earning['value'] = $this->paymentRepository->join("orders", "payments.id", "=", "orders.payment_id")
            ->join("product_orders", "orders.id", "=", "product_orders.order_id")
            ->join("products", "products.id", "=", "product_orders.product_id")
            ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
            ->where('user_markets.user_id', auth()->id())
            ->where('payments.status','Paid')
            ->groupBy('payments.id')
            ->orderBy('payments.id', 'desc')
            ->select('payments.*')->get()->sum('price');
            $statistics[] = $earning;

            //$this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
            $ordersCount['description'] = "total_orders";
            $ordersCount['value'] = $this->orderRepository->join("product_orders", "orders.id", "=", "product_orders.order_id")
            ->join("products", "products.id", "=", "product_orders.product_id")
            ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
            ->where('user_markets.user_id', auth()->id())
            ->groupBy('orders.id')
            ->select('orders.*')->get()->count();
            $statistics[] = $ordersCount;

            //$this->marketRepository->pushCriteria(new MarketsOfManagerCriteria(auth()->id()));
            $marketsCount['description'] = "total_markets";
            $marketsCount['value'] = $this->marketRepository->join("user_markets", "market_id", "=", "markets.id")
            ->where('user_markets.user_id', auth()->id())
            ->groupBy("markets.id")
            ->select("markets.*")->get()->count();
            $statistics[] = $marketsCount;

            //$this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
            $productsCount['description'] = "total_products";
            $productsCount['value'] = $this->productRepository->with("market.country")->with("category")
            ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
            ->where('user_markets.user_id', auth()->id())
            ->groupBy('products.id')
            ->select('products.*')->orderBy('products.updated_at', 'desc')->get()->count();
            $statistics[] = $productsCount;


        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($statistics, 'Statistics retrieved successfully');
    }
}
