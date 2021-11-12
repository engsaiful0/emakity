<?php

namespace App\Http\Controllers;

use App\DataTables\TestimonialDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Repositories\TestimonialRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UploadRepository;
use App\Repositories\MarketRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Repositories\CountryRepository;

class TestimonialController extends Controller
{
    /** @var  TestimonialRepository */
    private $testimonialRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;
    private $countryRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;
    /**
     * @var MarketRepository
     */
    private $marketRepository;

    public function __construct(
        TestimonialRepository $testimonialRepo,
        CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo,
        MarketRepository $marketRepo,
        CountryRepository $countryRepository
    ) {
        parent::__construct();
        $this->testimonialRepository = $testimonialRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->marketRepository = $marketRepo;
        $this->countryRepository = $countryRepository;
    }

    /**
     * Display a listing of the Testimonial.
     *
     * @param TestimonialDataTable $testimonialDataTable
     * @return Response
     */
    public function index(TestimonialDataTable $testimonialDataTable)
    {
        return $testimonialDataTable->render('testimonials.index');
    }

    /**
     * Show the form for creating a new Testimonial.
     *
     * @return Response
     */
    public function create()
    {
        $market = $this->marketRepository->pluck('name', 'id');
        $marketsSelected = [];
        $hasCustomField = in_array($this->testimonialRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->testimonialRepository->model());
            $html = generateCustomField($customFields);
        }
        $countries = $this->countryRepository->all()->pluck('name', 'id');

        return view('testimonials.create')->with("customFields", isset($html) ? $html : false)->with("market", $market)->with("marketsSelected", $marketsSelected)->with('countries', $countries);
    }

    /**
     * Store a newly created Testimonial in storage.
     *
     * @param CreateTestimonialRequest $request
     *
     * @return Response
     */
    public function store(CreateTestimonialRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->testimonialRepository->model());
        try {
            $testimonial = $this->testimonialRepository->create($input);
            //dd($testimonial);
            $testimonial->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                //dd($cacheUpload->getMedia('image')->first());
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($testimonial, 'image');
            }
            if (isset($input['web_image']) && $input['web_image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['web_image']);
                //dd($cacheUpload->getMedia('web_image')->first());
                $mediaItem = $cacheUpload->getMedia('web_image')->first();
                $mediaItem->copy($testimonial, 'web_image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.testimonial')]));

        return redirect(route('testimonials.index'));
    }

    /**
     * Display the specified Testimonial.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $testimonial = $this->testimonialRepository->findWithoutFail($id);

        if (empty($testimonial)) {
            Flash::error('Testimonial not found');

            return redirect(route('testimonials.index'));
        }

        return view('testimonials.show')->with('testimonial', $testimonial);
    }

    /**
     * Show the form for editing the specified Testimonial.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $testimonial = $this->testimonialRepository->findWithoutFail($id);
        if (empty($testimonial)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.testimonial')]));

            return redirect(route('testimonials.index'));
        }
        $customFieldsValues = $testimonial->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->testimonialRepository->model());
        $hasCustomField = in_array($this->testimonialRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        $countries = $this->countryRepository->all()->pluck('name', 'id');

        return view('testimonials.edit')->with('testimonial', $testimonial)->with("customFields", isset($html) ? $html : false)->with('countries', $countries);
    }

    /**
     * Update the specified Testimonial in storage.
     *
     * @param  int              $id
     * @param UpdateTestimonialRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTestimonialRequest $request)
    {
        $testimonial = $this->testimonialRepository->findWithoutFail($id);

        if (empty($testimonial)) {
            Flash::error('Testimonial not found');
            return redirect(route('testimonials.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->testimonialRepository->model());
        try {
            $testimonial = $this->testimonialRepository->update($input, $id);
            $input['markets'] = isset($input['markets']) ? $input['markets'] : [];
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($testimonial, 'image');
            }
            if (isset($input['web_image']) && $input['web_image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['web_image']);
                //dd($cacheUpload->getMedia('web_image')->first());
                $mediaItem = $cacheUpload->getMedia('web_image')->first();
                $mediaItem->copy($testimonial, 'web_image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $testimonial->customFieldsValues()
                    ->updateOrCreate(['custom_testimonial_id' => $value['custom_testimonial_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.testimonial')]));

        return redirect(route('testimonials.index'));
    }

    /**
     * Remove the specified Testimonial from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $testimonial = $this->testimonialRepository->findWithoutFail($id);

        if (empty($testimonial)) {
            Flash::error('Testimonial not found');

            return redirect(route('testimonials.index'));
        }

        $this->testimonialRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.testimonial')]));

        return redirect(route('testimonials.index'));
    }

    /**
     * Remove Media of Testimonial
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $testimonial = $this->testimonialRepository->findWithoutFail($input['id']);
        try {
            if ($testimonial->hasMedia($input['collection'])) {
                $testimonial->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
