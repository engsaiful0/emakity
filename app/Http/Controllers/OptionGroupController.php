<?php

namespace App\Http\Controllers;

use App\DataTables\OptionGroupDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateOptionGroupRequest;
use App\Http\Requests\UpdateOptionGroupRequest;
use App\Repositories\OptionGroupRepository;
use App\Repositories\CustomFieldRepository;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Repositories\CountryRepository;

class OptionGroupController extends Controller
{
    /** @var  OptionGroupRepository */
    private $optionGroupRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    private $countryRepository;
    

    public function __construct(OptionGroupRepository $optionGroupRepo, CustomFieldRepository $customFieldRepo ,CountryRepository $countryRepository)
    {
        parent::__construct();
        $this->optionGroupRepository = $optionGroupRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->countryRepository = $countryRepository;
        
    }

    /**
     * Display a listing of the OptionGroup.
     *
     * @param OptionGroupDataTable $optionGroupDataTable
     * @return Response
     */
    public function index(OptionGroupDataTable $optionGroupDataTable)
    {
        return $optionGroupDataTable->render('option_groups.index');
    }

    /**
     * Show the form for creating a new OptionGroup.
     *
     * @return Response
     */
    public function create()
    {
        
        
        $hasCustomField = in_array($this->optionGroupRepository->model(),setting('custom_field_models',[]));
            if($hasCustomField){
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
                $html = generateCustomField($customFields);
            }
        $countries = $this->countryRepository->all()->pluck('name','id');
        return view('option_groups.create')->with("customFields", isset($html) ? $html : false)->with('countries',$countries);
    }

    /**
     * Store a newly created OptionGroup in storage.
     *
     * @param CreateOptionGroupRequest $request
     *
     * @return Response
     */
    public function store(CreateOptionGroupRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
        try {
            $optionGroup = $this->optionGroupRepository->create($input);
            $optionGroup->customFieldsValues()->createMany(getCustomFieldsValues($customFields,$request));
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.option_group')]));

        return redirect(route('optionGroups.index'));
    }

    /**
     * Display the specified OptionGroup.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error('Option Group not found');

            return redirect(route('optionGroups.index'));
        }

        return view('option_groups.show')->with('optionGroup', $optionGroup);
    }

    /**
     * Show the form for editing the specified OptionGroup.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);
        
        

        if (empty($optionGroup)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.option_group')]));

            return redirect(route('optionGroups.index'));
        }
        $customFieldsValues = $optionGroup->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
        $hasCustomField = in_array($this->optionGroupRepository->model(),setting('custom_field_models',[]));
        if($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        $countries = $this->countryRepository->all()->pluck('name','id');
        return view('option_groups.edit')->with('optionGroup', $optionGroup)->with("customFields", isset($html) ? $html : false)->with('countries',$countries);
    }

    /**
     * Update the specified OptionGroup in storage.
     *
     * @param  int              $id
     * @param UpdateOptionGroupRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOptionGroupRequest $request)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error('Option Group not found');
            return redirect(route('optionGroups.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
        try {
            $optionGroup = $this->optionGroupRepository->update($input, $id);
            
            
            foreach (getCustomFieldsValues($customFields, $request) as $value){
                $optionGroup->customFieldsValues()
                    ->updateOrCreate(['custom_field_id'=>$value['custom_field_id']],$value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully',['operator' => __('lang.option_group')]));

        return redirect(route('optionGroups.index'));
    }

    /**
     * Remove the specified OptionGroup from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error('Option Group not found');

            return redirect(route('optionGroups.index'));
        }

        $this->optionGroupRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.option_group')]));

        return redirect(route('optionGroups.index'));
    }

        /**
     * Remove Media of OptionGroup
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $optionGroup = $this->optionGroupRepository->findWithoutFail($input['id']);
        try {
            if($optionGroup->hasMedia($input['collection'])){
                $optionGroup->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
