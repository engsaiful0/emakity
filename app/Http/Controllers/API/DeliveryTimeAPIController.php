<?php

namespace App\Http\Controllers\API;


use App\Models\DeliveryTime;
use App\Repositories\DeliveryTimeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;

/**
 * Class DeliveryTimeController
 * @package App\Http\Controllers\API
 */

class DeliveryTimeAPIController extends Controller
{
    /** @var  DeliveryTimeRepository */
    private $deliveryTimeRepository;

    public function __construct(DeliveryTimeRepository $deliveryTimeRepo)
    {
        $this->deliveryTimeRepository = $deliveryTimeRepo;
    }

    /**
     * Display a listing of the DeliveryTime.
     * GET|HEAD /deliveryTimes
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->deliveryTimeRepository->pushCriteria(new RequestCriteria($request));
            $this->deliveryTimeRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $deliveryTimes = $this->deliveryTimeRepository->all();

        return $this->sendResponse($deliveryTimes->toArray(), 'Delivery Times retrieved successfully');
    }

    /**
     * Display the specified DeliveryTime.
     * GET|HEAD /deliveryTimes/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var DeliveryTime $deliveryTime */
        if (!empty($this->deliveryTimeRepository)) {
            $deliveryTime = $this->deliveryTimeRepository->findWithoutFail($id);
        }

        if (empty($deliveryTime)) {
            return $this->sendError('Delivery Time not found');
        }

        return $this->sendResponse($deliveryTime->toArray(), 'Delivery Time retrieved successfully');
    }
}
