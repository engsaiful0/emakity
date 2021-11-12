<?php

namespace App\Http\Controllers;

use App\DataTables\StateDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateStateRequest;
use App\Http\Requests\UpdateStateRequest;
use App\Models\Country;
use App\Repositories\CountryRepository;
use App\Repositories\StateRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts;
use Prettus\Validator\Exceptions\ValidatorException;

class StateController extends Controller
{


    /**
  * @var StateCategoryRepository
  */
    private $stateRepository;
    private $countryRepository;

    public function __construct(StateRepository $stateRepo,CountryRepository $countryRepo)
    {
        parent::__construct();
        $this->stateRepository = $stateRepo;
        $this->countryRepository = $countryRepo;

    }

    /**
     * Display a listing of the State.
     *
     * @param StateDataTable $stateDataTable
     * @return Response
     */
    public function index(StateDataTable $stateDataTable)
    {
        return $stateDataTable->render('settings.states.index');
    }

    /**
     * Show the form for creating a new State.
     *
     * @return Response
     */
    public function create()
    {
        $state = $this->stateRepository->pluck('name','id');
        
        $countries = $this->countryRepository->all()->pluck('name','id');

        return view('settings.states.create')->with("state",$state)->with('countries',$countries);
    }

    /**
     * Store a newly created State in storage.
     *
     * @param CreateStateRequest $request
     *
     * @return Response
     */
    public function store(CreateStateRequest $request)
    {
        $country=Country::find($request->input('country_id'));
        $input = $request->all();
        $input['country_code']=$country->code;
        $input['iso2']=$country->iso3;
        $input['covered']=1;
        try {
            $state = $this->stateRepository->create($input);
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.state')]));

        return redirect(route('states.index'));
    }

    /**
     * Display the specified State.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $state = $this->stateRepository->findWithoutFail($id);

        if (empty($state)) {
            Flash::error('State not found');

            return redirect(route('states.index'));
        }

        return view('settings.states.show')->with('state', $state);
    }

    /**
     * Show the form for editing the specified State.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $state = $this->stateRepository->findWithoutFail($id);
        

        if (empty($state)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.state')]));

            return redirect(route('states.index'));
        }
        $countries = $this->countryRepository->all()->pluck('name','id');


        return view('settings.states.edit')->with("state",$state)->with("countries",$countries);
    }

    /**
     * Update the specified State in storage.
     *
     * @param  int              $id
     * @param UpdateStateRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStateRequest $request)
    {
        $state = $this->stateRepository->findWithoutFail($id);

        if (empty($state)) {
            Flash::error('State not found');
            return redirect(route('states.index'));
        }
        $country=Country::find($request->input('country_id'));
        $input = $request->all();
        $input['country_code']=$country->code;
        $input['iso2']=$country->iso3;
        $input['covered']=1;
        //dd($input);
        try {
            $state = $this->stateRepository->update($input, $id);
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully',['operator' => __('lang.state')]));

        return redirect(route('states.index'));
    }

    /**
     * Remove the specified State from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $state = $this->stateRepository->findWithoutFail($id);

        if (empty($state)) {
            Flash::error('State not found');

            return redirect(route('states.index'));
        }

        $this->stateRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.state')]));

        return redirect(route('states.index'));
    }

        /**
     * Remove Media of State
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $state = $this->stateRepository->findWithoutFail($input['id']);
        try {
            if($state->hasMedia($input['collection'])){
                $state->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
