<!-- Start Modal Add cart -->
<div class="modal fade" id="modalAddCart" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body medias-items">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col text-right">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"> <i class="fal fa-times"></i></span>
                            </button>
                        </div>
                    </div>
                    <div id='show_data'></div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Modal Add cart -->



<!-- Start Modal Add cart -->
<div class="modal fade" id="modalFavorite" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body medias-items">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col text-right">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"> <i class="fal fa-times"></i></span>
                            </button>
                        </div>
                    </div>
                    <div id='show_data_favorites'></div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Modal Add cart -->


<div class="modal fade" id="filterSearch" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body medias-items">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col text-right">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"> <i class="fal fa-times"></i></span>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('home') }}" method="get">
                        {{-- <div class="d-flex justify-content-between flex-wrap m-tb-20">
                                <label for="account-remember">
                                    <span>Remember me</span>
                                    <input type="checkbox-radio" name="account-remember" id="account-remember" checked>
                                </label>
                            </div>
                            <div class="d-flex justify-content-between flex-wrap m-tb-20">
                                <label for="account-remember">
                                    <span>Remember me</span>
                                    <input type="checkbox-radio" name="account-remember" id="account-remember" checked>
                                </label>
                            </div> --}}
                        <h3>Delivery or Pickup</h3>
                        <label class="input-container">Delivery
                            <input type="radio" name="first_query" value="delivery">
                            <div class="checkmark"></div>
                        </label>
                        <label class="input-container">Pickup
                            <input type="radio" checked="checked" name="first_query" value="pickup" checked>
                            <div class="checkmark"></div>
                        </label>
                        <h3>Opened Markets</h3>
                        <label class="input-container">Open
                            <input type="checkbox" name="second_query" value="open">
                            <div class="checkmark"></div>
                        </label>
                        <h3>Fields</h3>
                        <label class="input-container">All
                            <input type="checkbox" class="check-fields" checked>
                            <div class="checkmark"></div>
                        </label>
                        @foreach ($app_fields as $field)
                            <label class="input-container">{{ $field->name }}
                                <input type="checkbox" name="third_query[]" class='uncheck-fields'
                                    value="{{ $field->id }}" checked>
                                <div class="checkmark"></div>
                            </label>
                        @endforeach

                        <input type="reset" value="Reset"
                            class="btn btn--box btn--medium btn--radius btn--black btn--black-hover-green btn--uppercase font--semi-bold" />
                        <input type="submit" value="Filter"
                            class="btn btn--box btn--medium btn--radius btn--black btn--black-hover-green btn--uppercase font--semi-bold" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Modal Add cart -->

<!-- Start Modal Quickview cart -->
<div class="modal fade" id="modalQuickView" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col text-right">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"> <i class="fal fa-times"></i></span>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-gallery-box m-b-60">
                                <div class="modal-product-image--large">
                                    <img class="img-fluid"
                                        src="{{ asset('/') }}frontend/assets/img/product/gallery/gallery-large/product-gallery-large-1.jpg"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="product-details-box">
                                <h5 class="title title--normal m-b-20">Aliquam lobortis</h5>
                                <div class="product__price">
                                    <span class="product__price-del">$35.90</span>
                                    <span class="product__price-reg">$31.69</span>
                                </div>
                                <ul class="product__review m-t-15">
                                    <li class="product__review--fill"><i class="icon-star"></i></li>
                                    <li class="product__review--fill"><i class="icon-star"></i></li>
                                    <li class="product__review--fill"><i class="icon-star"></i></li>
                                    <li class="product__review--fill"><i class="icon-star"></i></li>
                                    <li class="product__review--blank"><i class="icon-star"></i></li>
                                </ul>
                                <div class="product__desc m-t-25 m-b-30">
                                    <p>On the other hand, we denounce with righteous indignation and dislike men who
                                        are so beguiled and demoralized by the charms of pleasure of the moment, so
                                        blinded by desire, that they cannot foresee the pain and trouble that are
                                        bound to ensue; and equal blame belongs to those who fail in their duty
                                        through weakness of will</p>
                                </div>

                                <div class="product-var p-t-30">
                                    <div class="product-quantity product-var__item d-flex align-items-center flex-wrap">
                                        <span class="product-var__text">Quantity: </span>
                                        <form class="modal-quantity-scale m-l-20">
                                            <div class="value-button" id="modal-decrease"
                                                onclick="decreaseValueModal()">-</div>
                                            <input type="number" id="modal-number" value="1" />
                                            <div class="value-button" id="modal-increase"
                                                onclick="increaseValueModal()">+</div>
                                        </form>
                                    </div>
                                </div>

                                <div class="product-links">
                                    <div class="product-social m-tb-30">
                                        <span>SHARE THIS PRODUCT</span>
                                        <ul class="product-social-link">
                                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                            <li><a href="#"><i class="fab fa-pinterest"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Modal Quickview cart -->



@push('scripts')

    <script type="text/javascript">
        $('#modalAddCart').on('show.bs.modal', function(e) {

            var product_id = $(e.relatedTarget).data('product_id');
            var user_id = $(e.relatedTarget).data('user_id');
            var quantity = $(e.relatedTarget).data('quantity');
            console.log(product_id);
            $.ajax({
                url: '{{ route('carts.store') }}',
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: product_id,
                    user_id: user_id,
                    quantity: quantity
                },
                success: function(response) {
                    $('#show_data').html('');
                    $('#show_data').html(response.data);
                },
                error: function(xhr) {
                    if (xhr.status == 500) {

                        $('#show_data').html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        ${xhr.responseJSON.message}
                                    </div>`);
                    }
                    if (xhr.status == 401) {

                        window.location.href = '{{ route('login') }}';
                    }
                }
            });

        });


        $('#modalFavorite').on('show.bs.modal', function(e) {

            var product_id = $(e.relatedTarget).data('product_id');
            var user_id = $(e.relatedTarget).data('user_id');
            console.log(product_id);
            $.ajax({
                url: '{{ route('favorites.store') }}',
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: product_id,
                    user_id: user_id
                },
                success: function(response) {
                    console.log(response);
                    $('#show_data_favorites').html('');
                    $('#show_data_favorites').html(response.data);
                },
                error: function(xhr) {
                    if (xhr.status == 500) {

                        $('#show_data_favorites').html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${xhr.responseJSON.message}
                        </div>`);
                    }
                    if (xhr.status == 401) {

                        window.location.href = '{{ route('login') }}';
                    }
                }
            });

        });

        $(document).ready(function() {

            $('.check-fields').click(function() {
                if ($(this).is(':checked')) {
                    $('.uncheck-fields').attr('checked', 'checked');
                } else {
                    $('.uncheck-fields').removeAttr('checked');

                }
            });
        })

    </script>
@endpush
