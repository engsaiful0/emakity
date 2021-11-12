@extends('frontend.layouts.app')
@push('css_lib')

@endpush
@section('content')
    <div class="container-fluid">
        @include('flash::message')
    </div>
    @php
        $categories = \App\Models\Category::with(['markets','markets.products']);
           
        if (request()->session()->has('country')) {
            
            $country = request()->session()->get('country');
            $country = \App\Models\Country::where('code', $country)->get()->first();
            $country_id = $country->id;
            
            $categories=$categories->whereHas('markets',function($query) use ($country_id){
                $query->where('markets.country_id',$country_id);
            });
        }
    @endphp


    <!-- :::::: Start Main Container Wrapper :::::: -->
    <main id="main-container" class="main-container">
        <!-- ::::::  Start Hero Section  ::::::  -->
        <div
            class="hero slider-dot-fix slider-dot slider-dot-fix slider-dot--center slider-dot-size--medium slider-dot-circle  slider-dot-style--fill slider-dot-style--fill-white-active-green dot-gap__X--10">
            <!-- Start Single Hero Slide -->
            @foreach ($app_slides->limit(3)->get() as $slide)
                <div class="hero-slider">
                    {!! getMediaurl_frontend($slide, 'web_image', '', asset('frontend/assets/img/hero/hero-home-1-img-1.jpg'),1920,845) !!}
                    <div class="hero__content">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-lg-8">
                                    <div class="hero__content--inner">
                                        <h1 class="title__hero title__hero--xlarge font--regular text-uppercase">
                                            {{ $slide->text }}</h1>
                                            @php
                                            $url='javascript:void(0)';
                                        if($slide->product_id!=null)
                                            $url=route('product',$slide->product_id);                                    
                                        elseif($slide->market_id!=null)
                                            $url=route('market',$slide->market_id);
                                        @endphp
                                        <a href="{{$url}}"
                                            class="btn btn--large btn--radius btn--black btn--black-hover-green font--bold text-uppercase">{{ $slide->button }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach


        </div>

        <!-- ::::::  Start banner Section  ::::::  -->
        <div class="banner m-t-30">
            <div class="container-fluid">
                <div class="row">
                    @foreach ($app_slides->offset(3)->limit(2)->get()
        as $slide)
                        @php
                            $url = 'javascript:void(0)';
                            if ($slide->product_id != null) {
                                $url = route('product', $slide->product_id);
                            } elseif ($slide->market_id != null) {
                                $url = route('market', $slide->market_id);
                            }
                        @endphp
                        <div class="col-md-6 col-12">
                            <div class="banner__box">
                                <!-- Start Single Banner Item -->
                                <div class="banner__box--single banner__box--single-text-style-1 pos-relative">
                                    <a href="{{ $url }}" class="banner__link">
                                        {!! getMediaurl_frontend($slide, 'web_image', '', asset('frontend/assets/img/banner/size-wide/banner-home-1-img-1-wide.jpg'),925,400) !!}

                                    </a>
                                    <div class="banner__content banner__content--center pos-absolute">
                                        <h2
                                            class="banner__title banner__title--large font--medium letter-spacing--4  text-uppercase">
                                            {{ $slide->text }}</h2>


                                        <div class="text-center">
                                            <a href="{{ $url }}"
                                                class="btn btn--medium btn--radius btn--transparent btn--border-black btn--border-black-hover-green font--light text-uppercase">{{ $slide->button }}</a>
                                        </div>
                                    </div>
                                </div> <!-- End Single Banner Item -->
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div> <!-- ::::::  End banner Section  ::::::  -->

        <!-- ::::::  Start  Product Style - Catagory Section  ::::::  -->
        <div class="product m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Start Section Title -->
                        <div class="section-content section-content--border m-b-35">
                            <h5 class="section-content__title">Top categories</h5>
                            <a href="{{ asset('/') }}"
                                class="btn btn--icon-left btn--small btn--radius btn--transparent btn--border-green btn--border-green-hover-green font--regular text-capitalize">More
                                categories <i class="fal fa-angle-right"></i></a>
                        </div> <!-- End Section Title -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="product__catagory">

                            @foreach ($categories->limit(8)->get() as $category)

                                <div class="product__catagory--single">
                                    <!-- Start Product Content -->
                                    <div class="product__content product__content--catagory">
                                        <a href="javascript:void(0)" class="product__link">{{ $category->name }} </a>
                                        <span class="product__items--text">{{ $category->markets->count() }}
                                            Markets</span>
                                    </div> <!-- End Product Content -->
                                    <!-- Start Product Image -->
                                    <div class="product__img-box product__img-box--catagory">
                                        <a href="{{ route('market', $category->id) }}" class="product__img--link">
                                            {!! getMediaurl_frontend($category, 'web_image', 'product__img img-fluid',asset('frontend/assets/img/product/category/category-home-1-img-1.jpg'),140,120) !!}
                                        </a>
                                    </div> <!-- End Product Image -->
                                </div> <!-- End Single Default Product -->
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End  Product Style - Catagory Section  ::::::  -->




        <!-- ::::::  Start  Product Style - Default Section  ::::::  -->
        <div class="product m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Start Section Title -->
                        <div class="section-content section-content--border m-b-35">
                            <h5 class="section-content__title">Markets</h5>
                            <a href="{{ route('home') }}"
                                class="btn btn--icon-left btn--small btn--radius btn--transparent btn--border-green btn--border-green-hover-green font--regular text-capitalize">More
                                Markets<i class="fal fa-angle-right"></i></a>
                        </div> <!-- End Section Title -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="default-slider default-slider--hover-bg-red product-default-slider">
                            <div class="product-default-slider-4grid-1rows gap__col--30 gap__row--40">




                                @foreach ($markets as $market)
                                    <div class="product__box product__default--single text-center">
                                        <!-- Start Product Image -->
                                        <div class="product__img-box  pos-relative">
                                            <a href="{{ route('market', $market->id) }}" class="product__img--link">
                                                {!! getMediaurl_frontend($market, 'web_image', 'product__img img-fluid',asset('frontend/assets/img/product/size-normal/product-home-1-img-1.jpg'),480,268) !!}
                                            </a>
                                        </div> <!-- End Product Image -->
                                        <!-- Start Product Content -->
                                        <div class="product__content m-t-20">
                                            @include('frontend.layouts.components.product_review',['rate'=>($market->rate!=null)?((int)$market->rate):0])
                                            <a href="{{ route('market', $market->id) }}"
                                                class="product__link">{{ $market->name }}</a>
                                            <p class="blog-feed__excerpt">{!! \Illuminate\Support\Str::limit($market->description, 50, ' (...)') !!}</p>
                                        </div> <!-- End Product Content -->
                                    </div>


                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End  Product Style - Default Section  ::::::  -->



        <!-- ::::::  Start  Product Style - Default Section  ::::::  -->
        <div class="product m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Start Section Title -->
                        <div class="section-content section-content--border m-b-35">
                            <h5 class="section-content__title">Products</h5>
                            <a href="{{ route('home') }}"
                                class="btn btn--icon-left btn--small btn--radius btn--transparent btn--border-green btn--border-green-hover-green font--regular text-capitalize">More
                                Products<i class="fal fa-angle-right"></i></a>
                        </div> <!-- End Section Title -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="default-slider default-slider--hover-bg-red product-default-slider">
                            <div class="product-default-slider-4grid-1rows gap__col--30 gap__row--40">




                                @foreach ($products as $product)

                                @include('frontend.product-structure',['product'=>$product])


                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End  Product Style - Default Section  ::::::  -->


        <!-- ::::::  Start  Product Style - Default Section  ::::::  -->
        <div class="product m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Start Section Title -->
                        <div class="section-content section-content--border m-b-35">
                            <h5 class="section-content__title">Top {{ __('lang.market_plural') }}</h5>
                            <ul class="tablist tablist--style-blue tablist--style-gap-20 nav">
                                @foreach ($categories->limit(4)->get() as $category)
                                    <li><a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab"
                                            href="#{{ $category->name }}">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </div> <!-- End Section Title -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="tab-content tab-animate-zoom">
                            <!-- Start Single Tab Item -->

                            @foreach ($categories->limit(4)->get() as $category)
                                <div class="tab-pane {{ $loop->first ? 'show active' : '' }}"
                                    id="{{ $category->name }}">
                                    <div class="default-slider default-slider--hover-bg-red product-default-slider">
                                        <div class="product-default-slider-4grid-2rows gap__col--30 gap__row--40">
                                            @foreach ($category->markets as $market)
                                                <!-- Start Single Default Product -->
                                                <div class="product__box product__default--single text-center">
                                                    <!-- Start Product Image -->
                                                    <div class="product__img-box  pos-relative">
                                                        <a href="{{ route('market', $market->id) }}"
                                                            class="product__img--link">
                                                            {!! getMediaurl_frontend($market, 'web_image', 'product__img img-fluid',asset('frontend/assets/img/product/size-normal/product-home-1-img-1.jpg'),480,268) !!}
                                                        </a>
                                                    </div> <!-- End Product Image -->
                                                    <!-- Start Product Content -->
                                                    <div class="product__content m-t-20">
                                                        @include('frontend.layouts.components.product_review',['rate'=>($market->rate!=null)?((int)$market->rate):0])
                                                        <a href="{{ route('market', $market->id) }}"
                                                            class="product__link">{{ $market->name }}</a>
                                                        <p class="blog-feed__excerpt">{!! \Illuminate\Support\Str::limit($market->description, 50, ' (...)') !!}</p>
                                                    </div> <!-- End Product Content -->
                                                </div> <!-- End Single Default Product -->
                                            @endforeach
                                        </div>
                                    </div>
                                </div> <!-- End Single Tab Item -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End  Product Style - Default Section  ::::::  -->
        <!-- ::::::  Start banner Section  ::::::  -->

        @foreach ($app_slides->offset(5)->limit(1)->get()
        as $slide)
            <div class="banner m-t-100 pos-relative">
                <div class="banner__bg">
                    {!! getMediaurl_frontend($slide, 'web_image', '', asset('frontend/assets/img/banner/size-extra-large-wide/banner-home-1-img-1-extra-large-wide.jpg'),1351,380) !!}

                </div>
                <div class="banner__box banner__box--single-text-style-2">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="banner__content banner__content--center pos-absolute">
                                    <h6 class="banner__title  font--medium m-b-10">SPECIAL DISCOUNT</h6>
                                    <h1 class="banner__title banner__title--large font--regular text-capitalize">
                                        {{ $slide->text }}
                                    </h1>
                                    @php
                                        $url = 'javascript:void(0)';
                                        if ($slide->product_id != null) {
                                            $url = route('product', $slide->product_id);
                                        } elseif ($slide->market_id != null) {
                                            $url = route('market', $slide->market_id);
                                        }
                                    @endphp

                                    <a href="{{ $url }}"
                                        class="btn btn--large btn--radius btn--black btn--black-hover-green font--bold text-uppercase">{{ $slide->button }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- ::::::  End banner Section  ::::::  -->
        @endforeach

        <!-- ::::::  Start Testimonial Section  ::::::  -->
        <div class="testimonial m-t-100 pos-relative">
            <div class="testimonial__bg">
                <img src="{{ $app_banner1 }}" alt="" style="width:1351px;height:422px;">
            </div>
            <div class="testimonial__content pos-absolute absolute-center text-center">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-content__inner">
                                <h2>Our Client Say!</h2>
                            </div>
                            <div class="testimonial__slider default-slider">
                                @foreach ($testimonials as $testimonial)
                                    <div class="testimonial__content--slider ">
                                        <div class="testimonial__single">
                                            <p class="testimonial__desc">{!! $testimonial->description !!}</p>
                                            <div class="testimonial__info">
                                                {!! getMediaurl_frontend($testimonial, 'web_image', 'testimonial__info--user', asset('frontend/assets/img/testimonial/testimonial-home-1-img-1.png'),100,100) !!}
                                                <h5 class="testimonial__info--desig m-t-20">{!! $testimonial->name !!} </h5>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End Testimonial Section  ::::::  -->

        {{-- <!-- ::::::  Start  Blog News  ::::::  -->
        <div class="blog m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Start Section Title -->
                        <div class="section-content section-content--border m-b-35">
                            <h5 class="section-content__title">Our Latest News</h5>
                            <a href="{{ asset('/') }}frontend/blog-list-sidebar-left.html"
                                class="btn btn--icon-left btn--small btn--radius btn--transparent btn--border-green btn--border-green-hover-green font--regular text-capitalize">More
                                blogs <i class="fal fa-angle-right"></i></a>
                        </div> <!-- End Section Title -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="default-slider default-slider--hover-bg-red">
                            <div class="blog-feed-slider-3grid default-slider gap__col--30 ">
                                <!-- Start Single Blog Feed -->
                                <div class="blog-feed">
                                    <!-- Start Blog Feed Image -->
                                    <div class="blog-feed__img-box">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__img--link">
                                            <img class="img-fluid"
                                                src="{{ asset('/') }}frontend/assets/img/blog/feed/blog-feed-home-1-img-1.jpg"
                                                alt="">
                                        </a>
                                    </div> <!-- End  Blog Feed Image -->
                                    <!-- Start  Blog Feed Content -->
                                    <div class="blog-feed__content ">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__link">Consectetur adipisicing</a>

                                        <div class="blog-feed__post-meta">
                                            By
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--author">Partner 2020 /</span></a>
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--date">July 23, 2020</span></a>

                                        </div>
                                        <p class="blog-feed__excerpt">Lorem ipsum dolor sit amet, consectetur adipisicing
                                            elit, sed do eiusmod tempor incidid...</p>
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="btn btn--small btn--radius btn--green btn--green-hover-black font--regular text-uppercase text-capitalize">Continue
                                            Reading</a>
                                    </div> <!-- End  Blog Feed Content -->
                                </div> <!-- End Single Blog Feed -->
                                <!-- Start Single Blog Feed -->
                                <div class="blog-feed">
                                    <!-- Start Blog Feed Image -->
                                    <div class="blog-feed__img-box">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__img--link">
                                            <img class="img-fluid"
                                                src="{{ asset('/') }}frontend/assets/img/blog/feed/blog-feed-home-1-img-2.jpg"
                                                alt="">
                                        </a>
                                    </div> <!-- End  Blog Feed Image -->
                                    <!-- Start  Blog Feed Content -->
                                    <div class="blog-feed__content ">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__link">Consectetur adipisicing</a>

                                        <div class="blog-feed__post-meta">
                                            By
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--author">Partner 2020 /</span></a>
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--date">July 23, 2020</span></a>

                                        </div>
                                        <p class="blog-feed__excerpt">Lorem ipsum dolor sit amet, consectetur adipisicing
                                            elit, sed do eiusmod tempor incidid...</p>
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="btn btn--small btn--radius btn--green btn--green-hover-black font--regular text-uppercase text-capitalize">Continue
                                            Reading</a>
                                    </div> <!-- End  Blog Feed Content -->
                                </div> <!-- End Single Blog Feed -->
                                <!-- Start Single Blog Feed -->
                                <div class="blog-feed">
                                    <!-- Start Blog Feed Image -->
                                    <div class="blog-feed__img-box">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__img--link">
                                            <img class="img-fluid"
                                                src="{{ asset('/') }}frontend/assets/img/blog/feed/blog-feed-home-1-img-3.jpg"
                                                alt="">
                                        </a>
                                    </div> <!-- End  Blog Feed Image -->
                                    <!-- Start  Blog Feed Content -->
                                    <div class="blog-feed__content ">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__link">Consectetur adipisicing</a>

                                        <div class="blog-feed__post-meta">
                                            By
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--author">Partner 2020 /</span></a>
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--date">July 23, 2020</span></a>

                                        </div>
                                        <p class="blog-feed__excerpt">Lorem ipsum dolor sit amet, consectetur adipisicing
                                            elit, sed do eiusmod tempor incidid...</p>
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="btn btn--small btn--radius btn--green btn--green-hover-black font--regular text-uppercase text-capitalize">Continue
                                            Reading</a>
                                    </div> <!-- End  Blog Feed Content -->
                                </div> <!-- End Single Blog Feed -->
                                <!-- Start Single Blog Feed -->
                                <div class="blog-feed">
                                    <!-- Start Blog Feed Image -->
                                    <div class="blog-feed__img-box">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__img--link">
                                            <img class="img-fluid"
                                                src="{{ asset('/') }}frontend/assets/img/blog/feed/blog-feed-home-1-img-4.jpg"
                                                alt="">
                                        </a>
                                    </div> <!-- End  Blog Feed Image -->
                                    <!-- Start  Blog Feed Content -->
                                    <div class="blog-feed__content ">
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="blog-feed__link">Consectetur adipisicing</a>

                                        <div class="blog-feed__post-meta">
                                            By
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--author">Partner 2020 /</span></a>
                                            <a class="blog-feed__post-meta--link"
                                                href="{{ asset('/') }}frontend/blog-grid-sidebar-left.html"><span
                                                    class="blog-feed__post-meta--date">July 23, 2020</span></a>

                                        </div>
                                        <p class="blog-feed__excerpt">Lorem ipsum dolor sit amet, consectetur adipisicing
                                            elit, sed do eiusmod tempor incidid...</p>
                                        <a href="{{ asset('/') }}frontend/blog-single-sidebar-left.html"
                                            class="btn btn--small btn--radius btn--green btn--green-hover-black font--regular text-uppercase text-capitalize">Continue
                                            Reading</a>
                                    </div> <!-- End  Blog Feed Content -->
                                </div> <!-- End Single Blog Feed -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End  Blog News   ::::::  -->

        <!-- ::::::  Start Newsletter Section  ::::::  -->
        <div class="newsletter m-t-100 pos-relative">
            <div class="newsletter__bg">
                <img src="{{ $app_banner2 }}" alt="" style="width:1351px;height:267px;">
            </div>
            <div class="newsletter__content pos-absolute absolute-center text-center">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-content__inner">
                                <h2>Subscribe To Our Newsletter</h2>
                            </div>
                        </div>
                        <div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                            <form class="newsletter__form" action="#" method="post">
                                <div class="newsletter__form-content pos-relative">
                                    <label class="pos-absolute" for="newsletter-mail"><i class="icon-mail"></i></label>
                                    <input type="email" name="newsletter-mail" id="newsletter-mail"
                                        placeholder="Your mail address">
                                    <button class="text-uppercase pos-absolute" type="submit">Subscribe</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End newsletter Section  ::::::  --> --}}

        <!-- ::::::  Start  Company Logo Section  ::::::  -->
        <div class="company-logo m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="company-logo__area default-slider">
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="{{ asset('/') }}frontend/assets/img/company-logo/company-logo-1.png"
                                        alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="{{ asset('/') }}frontend/assets/img/company-logo/company-logo-2.png"
                                        alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="{{ asset('/') }}frontend/assets/img/company-logo/company-logo-3.png"
                                        alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="{{ asset('/') }}frontend/assets/img/company-logo/company-logo-4.png"
                                        alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="{{ asset('/') }}frontend/assets/img/company-logo/company-logo-5.png"
                                        alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="{{ asset('/') }}frontend/assets/img/company-logo/company-logo-6.png"
                                        alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="{{ asset('/') }}frontend/assets/img/company-logo/company-logo-7.png"
                                        alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End  Company Logo Section  ::::::  -->

    </main> <!-- :::::: End MainContainer Wrapper :::::: -->

@endsection
@push('scripts_lib')
@endpush
