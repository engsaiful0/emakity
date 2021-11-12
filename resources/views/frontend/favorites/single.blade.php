<div class="row">
    <div class="col-md-7">
        <div class="row">
            <div class="col-md-4">
                <div class="modal__product-img">
                    {!! getMediaurl_frontend($product->product, 'web_image', 'product__img img-fluid',asset('frontend/assets/img/product/size-normal/product-home-1-img-1.jpg'),480,268) !!}                                                

                </div>
            </div>
            <div class="col-md-8">
                <div class="link--green link--icon-left"><i
                        class="fal fa-check-square"></i>Added to favourite successfully!</div>
                <div class="modal__product-cart-buttons m-tb-15">                    
                    <a href="{{route('favorites.index')}}"
                        class="btn btn--box  btn--tiny btn--green btn--green-hover-black btn--uppercase">View
                        Favorite</a>
                    <a href="{{route('cart')}}"
                        class="btn btn--box  btn--tiny btn--green btn--green-hover-black btn--uppercase">View
                        Cart</a>
                    <a href="{{route('checkout.index')}}"
                        class="btn btn--box  btn--tiny btn--green btn--green-hover-black btn--uppercaset">Checkout</a>
                </div>
            </div>
        </div>
    </div>
</div>