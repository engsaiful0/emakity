<!-- Start Single Default Product -->
<div class="product__box product__default--single text-center">
    <!-- Start Product Image -->
    <div class="product__img-box  pos-relative">
        <a href="{{ route('product', $product->id) }}" class="product__img--link">
            {!! getMediaurl_frontend($product, 'web_image', 'product__img img-fluid',asset('frontend/assets/img/product/size-normal/product-home-1-img-1.jpg'),480,268) !!}
        </a>
        <div class="product__label product__label--sale-dis">{{getDiscountPercent($product)}}%</div>
        @if($product->in_stock <= 0)        
            <div class="product__label product__label--sale-dis" style="left: 150px !important">Out of Stock</div>
        @endif

        <ul class="product__action--link pos-absolute">
            @if($product->in_stock > 0)
            <li><a href="#modalAddCart" data-quantity="1" data-user_id="{{ @\Auth::user()->id }}"
                    data-product_id="{{ $product->id }}" data-toggle="modal"><i class="icon-shopping-cart"></i></a>
            </li>
            @endif
            <li><a href="#modalFavorite" data-user_id="{{ @\Auth::user()->id }}"
                data-product_id="{{ $product->id }}" data-toggle="modal"><i class="icon-heart"></i></a></li>
            <li><a href="{{ route('product', $product->id) }}" data-id="{{ $product->id }}" data-toggle="modal"><i
                        class="icon-eye"></i></a></li>
        </ul> <!-- End Product Action Link -->
    </div> <!-- End Product Image -->
    <!-- Start Product Content -->
    <div class="product__content m-t-20">
        @include('frontend.layouts.components.product_review',['rate'=>($product->rate!=null)?((int)$product->rate):0])

        <a href="{{ route('product', $product->id) }}" class="product__link">{{ $product->name }}</a>
        <div class="product__price m-t-5">
            <span class="product__price">
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
        </div>
    </div> <!-- End Product Content -->
</div> <!-- End Single Default Product -->
