<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\MarketRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Repositories\CountryRepository;

class DashboardController extends Controller
{

    /** @var  OrderRepository */
    private $orderRepository;


    /**
     * @var UserRepository
     */
    private $userRepository;

    /** @var  MarketRepository */
    private $marketRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;
    private $countryRepository;

    public function __construct(OrderRepository $orderRepo, UserRepository $userRepo, PaymentRepository $paymentRepo, MarketRepository $marketRepo,CountryRepository $countryRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepo;
        $this->userRepository = $userRepo;
        $this->marketRepository = $marketRepo;
        $this->paymentRepository = $paymentRepo;
        $this->countryRepository = $countryRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $country_id=1;
        $countries = $this->countryRepository->all()->pluck('name','id');

        if($request->input('country_id'))
        {
            $country_id=$request->input('country_id');
        }
        if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('branch'))
        {
            $country_id=auth()->user()->country_id;
        }
        $country = $this->countryRepository->find($country_id);
        //dd($country);
        if (auth()->user()->hasRole('branch'))
        {
            $ordersCount = $this->orderRepository->whereHas('user.country', function($q){
                return $q->where('countries.id',get_role_country_id('branch'));
            })->count();
            $membersCount = $this->userRepository->where('country_id',get_role_country_id('branch'))->count();
            $marketsCount = $this->marketRepository->where('country_id',get_role_country_id('branch'))->count();
            $markets = $this->marketRepository->where('country_id',get_role_country_id('branch'))->limit(4)->get();
            $earning = $this->paymentRepository->whereHas('user.country', function($q){
                return $q->where('countries.id',get_role_country_id('branch'));
            })->where('status','Paid')->sum('price');
            $ajaxEarningUrl = route('payments.byMonth',['api_token'=>auth()->user()->api_token]);
        }
        else if(auth()->user()->hasRole('manager'))
        {
            $ordersCount = $this->orderRepository
                ->join("product_orders", "orders.id", "=", "product_orders.order_id")
                ->join("products", "products.id", "=", "product_orders.product_id")
                ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
                ->where('user_markets.user_id', auth()->id())
                ->groupBy('orders.id')
                ->select('orders.*')->get()->count();
            $membersCount = $this->userRepository->where('id',auth()->id())->count();
            $marketsCount = $this->marketRepository->join("user_markets", "market_id", "=", "markets.id")
                ->where('user_markets.user_id', auth()->id())
                ->groupBy("markets.id")
                ->select("markets.*")->get()->count();
            $markets = $this->marketRepository->join("user_markets", "market_id", "=", "markets.id")
                ->where('user_markets.user_id', auth()->id())
                ->groupBy("markets.id")
                ->select("markets.*")->limit(4)->get();
            $earning = $this->paymentRepository->join("orders", "payments.id", "=", "orders.payment_id")
            ->join("product_orders", "orders.id", "=", "product_orders.order_id")
            ->join("products", "products.id", "=", "product_orders.product_id")
            ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
            ->where('user_markets.user_id', auth()->id())
            ->where('payments.status','Paid')
            ->groupBy('payments.id')
            ->orderBy('payments.id', 'desc')
            ->select('payments.*')->get()->sum('price');
            $ajaxEarningUrl = route('payments.byMonth',['api_token'=>auth()->user()->api_token]);
            //dd($ordersCount);
        }
        else
        {
            $ordersCount = $this->orderRepository->count();
            $membersCount = $this->userRepository->count();
            $marketsCount = $this->marketRepository->count();
            $markets = $this->marketRepository->limit(4)->get();
            $earning = $this->paymentRepository->where('status','Paid')->sum('price');
            $ajaxEarningUrl = route('payments.byMonth',['api_token'=>auth()->user()->api_token]);

        }

//        dd($ajaxEarningUrl);
        return view('dashboard.index')
            ->with("ajaxEarningUrl", $ajaxEarningUrl)
            ->with("ordersCount", $ordersCount)
            ->with("marketsCount", $marketsCount)
            ->with("markets", $markets)
            ->with("membersCount", $membersCount)
            ->with("earning", $earning)
            ->with("countries", $countries)
            ->with("country", $country);

    }
}
