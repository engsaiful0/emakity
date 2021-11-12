<?php

namespace App\Http\Controllers;

use App\DataTables\DeliveryTimeDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateDeliveryTimeRequest;
use App\Http\Requests\UpdateDeliveryTimeRequest;
use App\Repositories\DeliveryTimeRepository;
use App\Repositories\CustomFieldRepository;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Repositories\CountryRepository;

class DeliveryTimeController extends Controller
{
    /** @var  DeliveryTimeRepository */
    private $deliveryTimeRepository;

    /**
     * @var CustomFieldRepository
     */
    private $countryRepository;
    

    public function __construct(DeliveryTimeRepository $deliveryTimeRepo,CountryRepository $countryRepository )
    {
        parent::__construct();
        $this->deliveryTimeRepository = $deliveryTimeRepo;
        $this->countryRepository = $countryRepository;
        
    }

    /**
     * Display a listing of the DeliveryTime.
     *
     * @param DeliveryTimeDataTable $deliveryTimeDataTable
     * @return Response
     */
    public function index(DeliveryTimeDataTable $deliveryTimeDataTable)
    {
        return $deliveryTimeDataTable->render('delivery_times.index');
    }

    /**
     * Show the form for creating a new DeliveryTime.
     *
     * @return Response
     */
    public function create()
    {
        
        $countries = $this->countryRepository->all()->pluck('name','id');

        return view('delivery_times.create')->with('countries',$countries);
    }

    /**
     * Store a newly created DeliveryTime in storage.
     *
     * @param CreateDeliveryTimeRequest $request
     *
     * @return Response
     */
    public function store(CreateDeliveryTimeRequest $request)
    {
        $input = $request->all();
       try {
            $deliveryTime = $this->deliveryTimeRepository->create($input);
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.delivery_time')]));

        return redirect(route('deliveryTimes.index'));
    }

    /**
     * Display the specified DeliveryTime.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $deliveryTime = $this->deliveryTimeRepository->findWithoutFail($id);

        if (empty($deliveryTime)) {
            Flash::error('Delivery Time not found');

            return redirect(route('deliveryTimes.index'));
        }

        return view('delivery_times.show')->with('deliveryTime', $deliveryTime);
    }

    /**
     * Show the form for editing the specified DeliveryTime.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $deliveryTime = $this->deliveryTimeRepository->findWithoutFail($id);
        
        

        if (empty($deliveryTime)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.delivery_time')]));

            return redirect(route('deliveryTimes.index'));
        }
        $countries = $this->countryRepository->all()->pluck('name','id');

        return view('delivery_times.edit')->with('deliveryTime', $deliveryTime)->with('countries',$countries);
    }

    /**
     * Update the specified DeliveryTime in storage.
     *
     * @param  int              $id
     * @param UpdateDeliveryTimeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDeliveryTimeRequest $request)
    {
        $deliveryTime = $this->deliveryTimeRepository->findWithoutFail($id);

        if (empty($deliveryTime)) {
            Flash::error('Delivery Time not found');
            return redirect(route('deliveryTimes.index'));
        }
        $input = $request->all();
        try {
            $deliveryTime = $this->deliveryTimeRepository->update($input, $id);
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully',['operator' => __('lang.delivery_time')]));

        return redirect(route('deliveryTimes.index'));
    }

    /**
     * Remove the specified DeliveryTime from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $deliveryTime = $this->deliveryTimeRepository->findWithoutFail($id);

        if (empty($deliveryTime)) {
            Flash::error('Delivery Time not found');

            return redirect(route('deliveryTimes.index'));
        }

        $this->deliveryTimeRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.delivery_time')]));

        return redirect(route('deliveryTimes.index'));
    }

        /**
     * Remove Media of DeliveryTime
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $deliveryTime = $this->deliveryTimeRepository->findWithoutFail($input['id']);
        try {
            if($deliveryTime->hasMedia($input['collection'])){
                $deliveryTime->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
