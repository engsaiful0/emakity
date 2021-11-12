<?php namespace App\Http\Middleware;

use App\Repositories\UploadRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CartRepository;
use App\Repositories\FavoriteRepository;
use App\Repositories\FieldRepository;
use App\Repositories\SlideRepository;
use App\Models\Country;
use App\Repositories\CurrencyRepository;
use Carbon\Carbon;
use Closure;

class App
{

    /**
     * The availables languages.
     *
     * @array $languages
     */
    protected $languages = ['en', 'fr']; // en, fr

    protected $uploadRepository;
    protected $categoryRepository;
    protected $cartRepository;
    protected $favoriteRepository;
    protected $fieldRepository;
    protected $slideRepository;
    protected $currencyRepository;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            app()->setLocale(setting('language', app()->getLocale()));
        } else {
            app()->setLocale($request->getPreferredLanguage($this->languages));
        }
        try {
            Carbon::setLocale(app()->getLocale());
            // config(['app.timezone' => setting('timezone')]);

            $this->uploadRepository = new UploadRepository(app());
            $this->categoryRepository = new CategoryRepository(app());
            $this->cartRepository = new CartRepository(app());
            $this->favoriteRepository = new FavoriteRepository(app());
            $this->fieldRepository = new FieldRepository(app());
            $this->currencyRepository = new CurrencyRepository(app());
            $this->slideRepository = new SlideRepository(app());


            $upload = $this->uploadRepository->findByField('uuid', setting('app_logo', ''))->first();
            $banner_upload1 = $this->uploadRepository->findByField('uuid', setting('app_banner1', ''))->first();
            $banner_upload2 = $this->uploadRepository->findByField('uuid', setting('app_banner2', ''))->first();
            $appLogo = asset('images/logo_default.png');
            $banner1 = asset('frontend/assets/img/testimonial/testimonials-bg.jpg');
            $banner2 = asset('frontend/assets/img/newsletter/newsletter-bg.jpg');
            
            if ($upload && $upload->hasMedia('app_logo')) {
                $appLogo = $upload->getFirstMediaUrl('app_logo');
            }
            if ($banner_upload1 && $banner_upload1->hasMedia('app_banner1')) {
                $banner1 = $banner_upload1->getFirstMediaUrl('app_banner1');
            }

            if ($banner_upload2 && $banner_upload2->hasMedia('app_banner2')) {
                $banner2 = $banner_upload2->getFirstMediaUrl('app_banner2');
            }

            
            
            $slides = $this->slideRepository;
            $fields = $this->fieldRepository->get();
            $currencies = $this->currencyRepository->all()->pluck('name_symbol', 'id');
            $countries = Country::where('active',1)->get();
            
            view()->share('app_logo', $appLogo);
            view()->share('app_banner1', $banner1);
            view()->share('app_banner2', $banner2);
            view()->share('app_slides', $slides);
            view()->share('app_currencies', $currencies);
            view()->share('app_countries', $countries);
            view()->share('app_fields', $fields);           
            if(auth()->user())
            {
                view()->share('app_carts',$this->cartRepository->where('user_id',auth()->id())->get()->count());
                view()->share('app_favorites',$this->favoriteRepository->where('user_id',auth()->id())->get()->count());
            }
        } catch (\Exception $exception) { 
            //dd($exception->getMessage());
        }

        return $next($request);
    }

}