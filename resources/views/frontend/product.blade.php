@extends('frontend.layouts.app')
@push('css_lib')
    <style>
        /* Rating Star Widgets Style */
        .rating-stars ul {
            list-style-type: none;
            padding: 0;

            -moz-user-select: none;
            -webkit-user-select: none;
        }

        .rating-stars ul>li.star {
            display: inline-block;

        }

        /* Idle State of the stars */
        .rating-stars ul>li.star>i.fa {
            font-size: 2.5em;
            /* Change the size of the stars */
            color: #ccc;
            /* Color on idle state */
        }

        /* Hover state of the stars */
        .rating-stars ul>li.star.hover>i.fa {
            color: #FF912C;
        }

        /* Selected state of the stars */
        .rating-stars ul>li.star.selected>i.fa {
            color: #FFCC36;
        }

    </style>
@endpush
@section('content')
    <div class="container-fluid">
        @include('flash::message')
    </div>
    <!-- :::::: Start Main Container Wrapper :::::: -->
    <main id="main-container" class="main-container">

        <!-- Start Product Details Gallery -->
        <div class="product-details">
            <div class="container">
                <div class="row">
                    <div class="col-md-5">
                        <div
                            class="product-gallery-box product-gallery-box--tab d-flex align-items-center flex-row-reverse m-b-60">
                            <div class="product-image--large product-image--large-vertical float-80-per m-l-20">
                                {!! getMediaurl_frontend($product, 'image', 'product__img img-fluid',asset('frontend/assets/img/product/gallery/gallery-large/product-gallery-large-1.jpg'),700,700) !!}
                            </div>
                            <div id="gallery-zoom"
                                class="product-image--thumb product-image--thumb-vertical pos-relative float-20-per">
                                <a class="zoom-active"
                                    data-image="assets/img/product/gallery/gallery-large/product-gallery-large-1.jpg"
                                    data-zoom-image="assets/img/product/gallery/gallery-large/product-gallery-large-1.jpg">
                                    {!! getMediaurl_frontend($product, 'image', 'product__img img-fluid',asset('frontend/assets/img/product/gallery/gallery-large/product-gallery-large-1.jpg'),700,700) !!}

                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="product-details-box m-b-60">
                            <h4 class="font--regular m-b-20">{{ $product->name }}</h4>
                            @include('frontend.layouts.components.product_review',['rate'=>($product->rate!=null)?((int)$product->rate):0])
                            <div class="product__price m-t-5">
                                <span class="product__price product__price--large">
                                    @if(!$product->market->country->currency_right)
                                        {{ $product->market->country->currency->symbol }} {{ $product->discount_price }}
                                    @else
                                        {{ $product->discount_price }} {{ $product->market->country->currency->symbol }}
                                    @endif
                                    @if(!$product->market->country->currency_right)
                                        <del> {{ $product->market->country->currency->symbol }} {{ $product->price }} </del>
                                    @else
                                        <del>{{ $product->price }} {{ $product->market->country->currency->symbol }} </del>
                                    @endif
                                </span>
                                <span class="product__tag m-l-15 btn--tiny btn--green">-34%</span>
                            </div>
                            <div class="product__desc m-t-25 m-b-30">
                                <p>{{ $product->description }}</p>
                            </div>
                            <div class="product-var p-tb-30">
                                <div class="product-quantity product-var__item">
                                    <ul class="product-modal-group">
                                        <li><a href="#modalSizeGuide" data-toggle="modal"
                                                class="link--gray link--icon-left"><i
                                                    class="fal fa-money-check-edit"></i>Size Guide</a></li>
                                        <li><a href="#modalShippinginfo" data-toggle="modal"
                                                class="link--gray link--icon-left"><i
                                                    class="fal fa-truck-container"></i>Shipping</a></li>
                                        <li><a href="#modalProductAsk" data-toggle="modal"
                                                class="link--gray link--icon-left"><i class="fal fa-envelope"></i>Ask About
                                                This product</a></li>
                                    </ul>
                                </div>
                                <form class="quantity-scale m-l-20" action="{{ route('carts.store') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="product-quantity product-var__item d-flex align-items-center">
                                        <span class="product-var__text">Quantity: </span>
                                        <div class="value-button" id="decrease" onclick="decreaseValue()">-</div>
                                        <input type="number" id="number" value="1" name='quantity' />
                                        <div class="value-button" id="increase" onclick="increaseValue()">+</div>
                                        <input type="hidden" value="{{ $product->id }}" name='product_id' />
                                        <input type="hidden" value="{{ auth()->id() }}" name='user_id' />

                                    </div>
                                    <div class="product-var__item">
                                        @if($product->in_stock > 0)        
                                        <input type="submit" value="Add to cart"
                                            class="btn btn--long btn--radius-tiny btn--green btn--green-hover-black btn--uppercase btn--weight m-r-20" />
                                        @endif

                                        <a href="wishlist.html"
                                            class="btn btn--round btn--round-size-small btn--green btn--green-hover-black"><i
                                                class="fas fa-heart"></i></a>
                                    </div>
                                </form>

                                <div class="product-var__item">
                                    <span class="product-var__text">Guaranteed safe checkout </span>
                                    <ul class="payment-icon m-t-5">
                                        <li><img src="assets/img/icon/payment/paypal.svg" alt=""></li>
                                        <li><img src="assets/img/icon/payment/amex.svg" alt=""></li>
                                        <li><img src="assets/img/icon/payment/ipay.svg" alt=""></li>
                                        <li><img src="assets/img/icon/payment/visa.svg" alt=""></li>
                                        <li><img src="assets/img/icon/payment/shoify.svg" alt=""></li>
                                        <li><img src="assets/img/icon/payment/mastercard.svg" alt=""></li>
                                        <li><img src="assets/img/icon/payment/gpay.svg" alt=""></li>
                                    </ul>
                                </div>
                                <div class="product-var__item d-flex align-items-center">
                                    <span class="product-var__text">Share: </span>
                                    <ul class="product-social m-l-20">
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Product Details Gallery -->

        <!-- Start Product Details Tab -->
        <div class="product-details-tab-area">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="product-details-content">
                            <ul class="tablist tablist--style-black tablist--style-title tablist--style-gap-30 nav">
                                <li><a class="nav-link active" data-toggle="tab" href="#product-desc">Description</a></li>
                                <li><a class="nav-link" data-toggle="tab" href="#product-review">Reviews</a></li>
                            </ul>
                            <div class="product-details-tab-box">
                                <div class="tab-content">
                                    <!-- Start Tab - Product Description -->
                                    <div class="tab-pane show active" id="product-desc">
                                        <div class="para__content">
                                            <p class="para__text">{{ $product->description }}
                                            </p>
                                        </div>
                                    </div> <!-- End Tab - Product Description -->


                                    <!-- Start Tab - Product Review -->
                                    <div class="tab-pane " id="product-review">

                                        <ul class="comment">
                                            @foreach ($product->productReviews as $review)
                                                <li class="comment__list">
                                                    <div class="comment__wrapper">
                                                        <div class="comment__img">
                                                            {!! getMediaurl_frontend($review['user'], 'image', '',asset('frontend/assets/img/user/image-1.png'),78,78) !!}

                                                        </div>
                                                        <div class="comment__content">
                                                            <div class="comment__content-top">
                                                                <div class="comment__content-left">
                                                                    <h6 class="comment__name">
                                                                        {{ $review['user']['name'] }}
                                                                    </h6>
                                                                    @include('frontend.layouts.components.product_review',['rate'=>($review->rate!=null)?((int)$review->rate):0])

                                                                </div>
                                                            </div>

                                                            <div class="para__content">
                                                                <p class="para__text">{{ $review->review }} </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach

                                        </ul>

                                        <!-- Start Add Review Form-->
                                        <div class="review-form m-t-40">
                                            <div class="section-content">
                                                <h6 class="font--bold text-uppercase">ADD A REVIEW</h6>
                                                <p class="section-content__desc">Your email address will not be published.
                                                    Required fields are marked *</p>
                                            </div>
                                            <form class="form-box" method="POST"
                                                action="{{ route('productReviews.store') }}">
                                                <div class="row">
                                                    {{ csrf_field() }}



                                                    <!-- Review Field -->
                                                    <div class="form-group row ">
                                                        {!! Form::label('review', trans('lang.product_review_review'), ['class' => 'col-3 control-label text-right']) !!}
                                                        <div class="col-9">
                                                            {!! Form::textarea('review', null, ['class' => 'form-control', 'placeholder' => trans('lang.product_review_review_placeholder')]) !!}
                                                            <div class="form-text text-muted">
                                                                {{ trans('lang.product_review_review_help') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- Rate Field -->
                                                    <div class="form-group row ">
                                                        {!! Form::label('rate', trans('lang.product_review_rate'), ['class' => 'col-3 control-label text-right']) !!}
                                                        <div class="col-9">
                                                            <div class='rating-stars text-center'>
                                                                <ul id='stars'>
                                                                    <li class='star' title='Poor' data-value='1'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='Fair' data-value='2'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='Good' data-value='3'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='Excellent' data-value='4'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='WOW!!!' data-value='5'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            {!! Form::hidden('rate', null, ['id' => 'rate_value', 'class' => 'form-control', 'placeholder' => trans('lang.product_review_rate_placeholder')]) !!}
                                                            <div class="form-text text-muted">
                                                                {{ trans('lang.product_review_rate_help') }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @auth 
                                                    @if(!in_array(
            'admin',
            auth()->user()->getRoleNames()->toArray(),
        ))
                                                        <!-- User Id Field -->
                                                        <div class="form-group row ">
                                                            {!! Form::label('user_id', trans('lang.product_review_user_id'), ['class' => 'col-3 control-label text-right']) !!}
                                                            <div class="col-9">
                                                                {!! Form::select('user_id', $user, null, ['class' => 'select2 form-control']) !!}
                                                                <div class="form-text text-muted">
                                                                    {{ trans('lang.product_review_user_id_help') }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @else
                                                        {!! Form::hidden('user_id', auth()->id(), ['class' => 'form-control', 'placeholder' => trans('lang.cart_quantity_placeholder')]) !!}
                                                    
                                                    @endauth
                                                    {!! Form::hidden('product_id', $product->id, ['class' => 'form-control', 'placeholder' => trans('lang.cart_quantity_placeholder')]) !!}


                                                    <div class="col-12 text-right">
                                                        <button
                                                            class="btn btn--box btn--small btn--black btn--black-hover-green btn--uppercase font--bold m-t-30"
                                                            type="submit">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div> <!-- End Add Review Form- -->
                                    </div> <!-- Start Tab - Product Review -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End Product Details Tab -->

        <!-- ::::::  Start  Product Style - Default Section  ::::::  -->
        <div class="product m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Start Section Title -->
                        <div class="section-content section-content--border m-b-35">
                            <h5 class="section-content__title">Related Product
                            </h5>
                            <a href="shop-sidebar-grid-left.html"
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

        <!-- ::::::  Start  Company Logo Section  ::::::  -->
        <div class="company-logo m-t-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="company-logo__area default-slider">
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="assets/img/company-logo/company-logo-1.png" alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="assets/img/company-logo/company-logo-2.png" alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="assets/img/company-logo/company-logo-3.png" alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="assets/img/company-logo/company-logo-4.png" alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="assets/img/company-logo/company-logo-5.png" alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="assets/img/company-logo/company-logo-6.png" alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                            <!-- Start Single Company Logo Item -->
                            <div class="company-logo__item">
                                <a href="#" class="company-logo__link">
                                    <img src="assets/img/company-logo/company-logo-7.png" alt="" class="company-logo__img">
                                </a>
                            </div> <!-- End Single Company Logo Item -->
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ::::::  End  Company Logo Section  ::::::  -->

    </main> <!-- :::::: End MainContainer Wrapper :::::: -->


@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            /* 1. Visualizing things on Hover - See next part for action on click */
            $('#stars li').on('mouseover', function() {
                var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

                // Now highlight all the stars that's not after the current hovered star
                $(this).parent().children('li.star').each(function(e) {
                    if (e < onStar) {
                        $(this).addClass('hover');
                    } else {
                        $(this).removeClass('hover');
                    }
                });

            }).on('mouseout', function() {
                $(this).parent().children('li.star').each(function(e) {
                    $(this).removeClass('hover');
                });
            });


            /* 2. Action to perform on click */
            $('#stars li').on('click', function() {
                var onStar = parseInt($(this).data('value'), 10); // The star currently selected
                var stars = $(this).parent().children('li.star');

                for (i = 0; i < stars.length; i++) {
                    $(stars[i]).removeClass('selected');
                }

                for (i = 0; i < onStar; i++) {
                    $(stars[i]).addClass('selected');
                }

                // JUST RESPONSE (Not needed)
                var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
                $("#rate_value").val(ratingValue);

            });


        });

    </script>
@endpush
