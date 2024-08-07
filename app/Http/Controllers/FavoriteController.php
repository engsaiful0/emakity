<?php

namespace App\Http\Controllers;

use App\DataTables\FavoriteDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Models\Favorite;
use App\Repositories\FavoriteRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OptionRepository;
use App\Repositories\UserRepository;
use Flash,Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class FavoriteController extends Controller
{
    /** @var  FavoriteRepository */
    private $favoriteRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(FavoriteRepository $favoriteRepo, CustomFieldRepository $customFieldRepo, ProductRepository $productRepo
        , OptionRepository $optionRepo
        , UserRepository $userRepo)
    {
        parent::__construct();
        $this->favoriteRepository = $favoriteRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->productRepository = $productRepo;
        $this->optionRepository = $optionRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the Favorite.
     *
     * @param FavoriteDataTable $favoriteDataTable
     * @return Response
     */
    public function index(FavoriteDataTable $favoriteDataTable)
    {
        return $favoriteDataTable->render('favorites.index');
    }

    /**
     * Show the form for creating a new Favorite.
     *
     * @return Response
     */
    public function create()
    {
        $product = $this->productRepository->where('in_stock','>',0)->pluck('name', 'id');
        $option = $this->optionRepository->pluck('name', 'id');
        $user = $this->userRepository->pluck('name', 'id');
        $optionsSelected = [];
        $hasCustomField = in_array($this->favoriteRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->favoriteRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('favorites.create')->with("customFields", isset($html) ? $html : false)->with("product", $product)->with("option", $option)->with("optionsSelected", $optionsSelected)->with("user", $user);
    }

    /**
     * Store a newly created Favorite in storage.
     *
     * @param CreateFavoriteRequest $request
     *
     * @return Response
     */
    public function store(CreateFavoriteRequest $request)
    {
        $input = $request->all();


        $old_fav = Favorite::with('product')->where('user_id',Auth::user()->id);
        if (!empty($old_fav)) {

            $old_product=$old_fav->where('product_id',$input['product_id'])->first();
            if($old_product)
            {
                if($request->ajax()){
                    $data = view('frontend.favorites.single',['product'=>$old_product])->render();
                    return $this->sendResponse($data, __('lang.saved_successfully',['operator' => __('lang.cart')]));                   
                }
                Flash::success(__('lang.updated_successfully', ['operator' => __('lang.cart')]));
                return redirect()->back();  
            }
        }


        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->favoriteRepository->model());
        try {
            $favorite = $this->favoriteRepository->create($input);
            $favorite->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        if($request->ajax()){
            $data = view('frontend.favorites.single',['product'=>$favorite])->render();
            return $this->sendResponse($data, __('lang.saved_successfully',['operator' => __('lang.cart')]));
            
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.favorite')]));

        return redirect()->back();
    }

    /**
     * Display the specified Favorite.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $favorite = $this->favoriteRepository->findWithoutFail($id);

        if (empty($favorite)) {
            Flash::error('Favorite not found');

            return redirect(route('favorites.index'));
        }

        return view('favorites.show')->with('favorite', $favorite);
    }

    /**
     * Show the form for editing the specified Favorite.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $favorite = $this->favoriteRepository->findWithoutFail($id);
        $product = $this->productRepository->where('in_stock','>',0)->pluck('name', 'id');
        $option = $this->optionRepository->pluck('name', 'id');
        $user = $this->userRepository->pluck('name', 'id');
        $optionsSelected = $favorite->options()->pluck('options.id')->toArray();

        if (empty($favorite)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.favorite')]));

            return redirect(route('favorites.index'));
        }
        $customFieldsValues = $favorite->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->favoriteRepository->model());
        $hasCustomField = in_array($this->favoriteRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('favorites.edit')->with('favorite', $favorite)->with("customFields", isset($html) ? $html : false)->with("product", $product)->with("option", $option)->with("optionsSelected", $optionsSelected)->with("user", $user);
    }

    /**
     * Update the specified Favorite in storage.
     *
     * @param int $id
     * @param UpdateFavoriteRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFavoriteRequest $request)
    {
        $favorite = $this->favoriteRepository->findWithoutFail($id);

        if (empty($favorite)) {
            Flash::error('Favorite not found');
            return redirect(route('favorites.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->favoriteRepository->model());
        try {
            $favorite = $this->favoriteRepository->update($input, $id);
            $input['options'] = isset($input['options']) ? $input['options'] : [];

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $favorite->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.favorite')]));

        return redirect(route('favorites.index'));
    }

    /**
     * Remove the specified Favorite from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $favorite = $this->favoriteRepository->findWithoutFail($id);

        if (empty($favorite)) {
            Flash::error('Favorite not found');

            return redirect(route('favorites.index'));
        }

        $this->favoriteRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.favorite')]));

        return redirect(route('favorites.index'));
    }

    /**
     * Remove Media of Favorite
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $favorite = $this->favoriteRepository->findWithoutFail($input['id']);
        try {
            if ($favorite->hasMedia($input['collection'])) {
                $favorite->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
