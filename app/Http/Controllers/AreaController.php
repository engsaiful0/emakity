<?php

namespace App\Http\Controllers;

use App\DataTables\AreaDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Repositories\CountryRepository;
use App\Repositories\StateRepository;
use App\Repositories\AreaRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class AreaController extends Controller
{


    /**
  * @var AreaCategoryRepository
  */
    private $areaRepository;
    private $countryRepository;
    private $stateRepository;

    public function __construct(AreaRepository $areaRepo,CountryRepository $countryRepo,StateRepository $stateRepo)
    {
        parent::__construct();
        $this->areaRepository = $areaRepo;
        $this->countryRepository = $countryRepo;
        $this->stateRepository = $stateRepo;

    }

    /**
     * Display a listing of the Area.
     *
     * @param AreaDataTable $areaDataTable
     * @return Response
     */
    public function index(AreaDataTable $areaDataTable)
    {
        return $areaDataTable->render('settings.areas.index');
    }

    /**
     * Show the form for creating a new Area.
     *
     * @return Response
     */
    public function create()
    {
        $area = $this->areaRepository->pluck('name','id');
        
        $countries = $this->countryRepository->all()->pluck('name','id');
        $states = $this->stateRepository->all()->pluck('name','id');

        return view('settings.areas.create')->with("area",$area)->with('countries',$countries)->with('states',$states);
    }

    /**
     * Store a newly created Area in storage.
     *
     * @param CreateAreaRequest $request
     *
     * @return Response
     */
    public function store(CreateAreaRequest $request)
    {
        $input = $request->all();
        try {
            $area = $this->areaRepository->create($input);
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.area')]));

        return redirect(route('areas.index'));
    }

    /**
     * Display the specified Area.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $area = $this->areaRepository->findWithoutFail($id);

        if (empty($area)) {
            Flash::error('Area not found');

            return redirect(route('areas.index'));
        }

        return view('settings.areas.show')->with('area', $area);
    }

    /**
     * Show the form for editing the specified Area.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $area = $this->areaRepository->findWithoutFail($id);
        

        if (empty($area)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.area')]));

            return redirect(route('areas.index'));
        }
        $countries = $this->countryRepository->all()->pluck('name','id');
        $states = $this->stateRepository->all()->pluck('name','id');

        return view('settings.areas.edit')->with("area",$area)->with("countries",$countries)->with("states",$states);
    }

    /**
     * Update the specified Area in storage.
     *
     * @param  int              $id
     * @param UpdateAreaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAreaRequest $request)
    {
        $area = $this->areaRepository->findWithoutFail($id);

        if (empty($area)) {
            Flash::error('Area not found');
            return redirect(route('areas.index'));
        }
        $input = $request->all();
        try {
            $area = $this->areaRepository->update($input, $id);
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully',['operator' => __('lang.area')]));

        return redirect(route('areas.index'));
    }

    /**
     * Remove the specified Area from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $area = $this->areaRepository->findWithoutFail($id);

        if (empty($area)) {
            Flash::error('Area not found');

            return redirect(route('areas.index'));
        }

        $this->areaRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.area')]));

        return redirect(route('areas.index'));
    }

        /**
     * Remove Media of Area
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $area = $this->areaRepository->findWithoutFail($input['id']);
        try {
            if($area->hasMedia($input['collection'])){
                $area->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
