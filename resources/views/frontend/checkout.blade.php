@extends('frontend.layouts.app')
@push('css_lib')

@endpush
@section('content')
    @include('flash::message')


    <!-- ::::::  Start  Main Container Section  ::::::  -->
    <main id="main-container" class="main-container">
        <div class="container">
            {!! Form::model(@$deliveryAddress, ['route' => 'checkout.store', 'method' => 'POST']) !!}

            <div class="row">
                <!-- Start Client Shipping Address -->

                <div class="col-lg-7">
                    <div class="section-content">
                        <h5 class="section-content__title">Billing Details</h5>
                    </div>
                    <div class="row">

                        {!! Form::hidden('user_id', Auth::user()->id, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('lang.order_tax_placeholder')]) !!}
                        {!! Form::hidden('payment', getPayemntOptions(@$product->product->market, 'available_for_delivery'), ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('lang.order_tax_placeholder')]) !!}
                        {!! Form::hidden('order_status_id', 1) !!}
                        {!! Form::hidden('status', 'Waiting for Client') !!}
                        {!! Form::hidden('active', 1) !!}
                        {!! Form::hidden('tax', $product->product->market->default_tax) !!}
                        {!! Form::hidden('delivery_fee', $product->product->market->delivery_fee) !!}
                        {!! Form::hidden('delivery_address_id', $deliveryAddress->id) !!}
                        {!! Form::hidden('delivery_time_id', 1) !!}
                        <!-- Description Field -->
                        <div class="form-group row mb-2">
                            {!! Form::label('name', trans('lang.user_name'), ['class' => 'col-3 control-label text-right']) !!}
                            <div class="col-9">
                                {!! Form::text('name', auth()->user()->name, ['class' => 'form-control', 'placeholder' => trans('lang.app_setting_mail_username'), 'readonly']) !!}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            {!! Form::label('email', trans('lang.user_email'), ['class' => 'col-3 control-label text-right']) !!}
                            <div class="col-9">
                                {!! Form::text('email', auth()->user()->email, ['class' => 'form-control', 'placeholder' => trans('lang.app_setting_mail_useremail'), 'readonly']) !!}
                            </div>
                        </div>
                        <div class="form-group row mb-2 ">
                            {!! Form::label('description', trans('lang.delivery_address_description'), ['class' => 'col-3 control-label text-right']) !!}
                            <div class="col-9">
                                {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' => trans('lang.delivery_address_description_placeholder')]) !!}
                                <div class="form-text text-muted">
                                    {{ trans('lang.delivery_address_description_help') }}
                                </div>
                            </div>
                        </div>

                        <!-- Address Field -->
                        <div class="form-group row mb-2 ">
                            {!! Form::label('address', trans('lang.delivery_address_address'), ['class' => 'col-3 control-label text-right']) !!}
                            <div class="col-9">
                                {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => trans('lang.delivery_address_address_placeholder')]) !!}
                                <div class="form-text text-muted">
                                    {{ trans('lang.delivery_address_address_help') }}
                                </div>
                            </div>
                        </div>

                        <!-- Latitude Field -->
                        <div class="form-group row mb-2 ">
                            {!! Form::label('latitude', trans('lang.delivery_address_latitude'), ['class' => 'col-3 control-label text-right']) !!}
                            <div class="col-9">
                                {!! Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => trans('lang.delivery_address_latitude_placeholder')]) !!}
                                <div class="form-text text-muted">
                                    {{ trans('lang.delivery_address_latitude_help') }}
                                </div>
                            </div>
                        </div>

                        <!-- Longitude Field -->
                        <div class="form-group row mb-2 ">
                            {!! Form::label('longitude', trans('lang.delivery_address_longitude'), ['class' => 'col-3 control-label text-right']) !!}
                            <div class="col-9">
                                {!! Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => trans('lang.delivery_address_longitude_placeholder')]) !!}
                                <div class="form-text text-muted">
                                    {{ trans('lang.delivery_address_longitude_help') }}
                                </div>
                            </div>
                        </div>

                        <!-- 'Boolean Is Default Field' -->
                        <div class="form-group row mb-2 ">
                            {!! Form::label('is_default', trans('lang.delivery_address_is_default'), ['class' => 'col-3 control-label text-right']) !!}
                            <div class="col-9">

                                <div class="checkbox icheck">
                                    <label class="ml-2 form-check-inline">
                                        {!! Form::hidden('is_default', 0) !!}
                                        {!! Form::checkbox('is_default', 1, null) !!}
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                </div> <!-- End Client Shipping Address -->

                <!-- Start Order Wrapper -->
                <div class="col-lg-5">
                    <div class="your-order-section">
                        <div class="section-content">
                            <h5 class="section-content__title">Your order</h5>
                        </div>
                        <div class="your-order-box gray-bg m-t-40 m-b-30">
                            <div class="your-order-product-info">
                                <div class="your-order-top d-flex justify-content-between">
                                    <h6 class="your-order-top-left font--bold">Product</h6>
                                    <h6 class="your-order-top-left font--bold">Quantity</h6>
                                    <h6 class="your-order-top-right font--bold">Total</h6>
                                </div>
                                <ul class="your-order-middle">
                                    @php
                                        $amount = 0;
                                    @endphp
                                    @foreach ($products as $productOrder)
                                        @php
                                            $amount += getPriceValue($productOrder['product'], 'discount_price') * $productOrder['quantity'];
                                            $price = getPriceValue($productOrder['product'], 'discount_price') * $productOrder['quantity'];
                                        @endphp
                                        <li class="d-flex justify-content-between">
                                            <span
                                                class="your-order-middle-left font--semi-bold">{{ $productOrder['product']['name'] }}</span>
                                                <span
                                                class="your-order-middle-right font--semi-bold">{{ $productOrder['quantity'] }}</span>
                                                <span
                                                class="your-order-middle-right font--semi-bold">{{ $product->product->market->country->currency->symbol }}
                                                {{ getPriceValue($productOrder['product'], 'discount_price') * $productOrder['quantity'] }}</span>
                                        </li>
                                    @endforeach
                                    @php
                                        $amount += $product->product->market->delivery_fee;
                                        $amountWithTax = $amount + ($amount * $product->product->market->default_tax) / 100;
                                    @endphp

                                </ul>
                                <div class="total-shipping" style="border-top:0px;">
                                    <span>Payment Option</span>
                                    <ul class="shipping-cost m-t-10">
                                        <li>
                                            <label for="ship-standard">
                                                <input type="radio" class="shipping-select" name="shipping-cost" value="Standard" id="ship-standard" required><span>{{getPayemntOptions(@$product->product->market, 'available_for_delivery')}}</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="your-order-bottom d-flex justify-content-between">
                                    <h6 class="your-order-bottom-left font--bold">Shipping</h6>
                                    <span
                                        class="your-order-bottom-right font--semi-bold">{{ $product->product->market->country->currency->symbol }}
                                        {{ $product->product->market->delivery_fee }}</span>
                                </div>
                                <div class="your-order-bottom d-flex justify-content-between">
                                    <h6 class="your-order-bottom-left font--bold">Tax</h6>
                                    <span
                                        class="your-order-bottom-right font--semi-bold">{{ $product->product->market->default_tax }}
                                        %</span>
                                </div>
                                <div class="your-order-total d-flex justify-content-between">
                                    <h5 class="your-order-total-left font--bold">Total</h5>
                                    <h5 class="your-order-total-right font--bold">
                                        {{ $product->product->market->country->currency->symbol }} {{ $amountWithTax }}</h5>
                                </div>


                                {{-- <div class="payment-method">
                                    <div class="payment-accordion element-mrg">
                                        <div class="panel-group" id="accordion">
                                            <div class="panel payment-accordion">
                                                <div class="panel-heading" id="method-one">
                                                    <h4 class="panel-title">
                                                        <a class="collapsed d-flex justify-content-between align-items-center"
                                                            data-toggle="collapse" data-parent="#accordion" href="#method1"
                                                            aria-expanded="false">
                                                            Direct bank transfer <i class="far fa-chevron-down"></i>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="method1" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>Please send a check to Store Name, Store Street, Store Town,
                                                            Store State / County, Store Postcode.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel payment-accordion">
                                                <div class="panel-heading" id="method-two">
                                                    <h4 class="panel-title">
                                                        <a class="collapsed d-flex justify-content-between align-items-center"
                                                            data-toggle="collapse" data-parent="#accordion" href="#method2"
                                                            aria-expanded="false">
                                                            Check payments <i class="far fa-chevron-down"></i>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="method2" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>Please send a check to Store Name, Store Street, Store Town,
                                                            Store State / County, Store Postcode.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel payment-accordion">
                                                <div class="panel-heading" id="method-three">
                                                    <h4 class="panel-title">
                                                        <a class="collapsed d-flex justify-content-between align-items-center"
                                                            data-toggle="collapse" data-parent="#accordion" href="#method3"
                                                            aria-expanded="false">
                                                            Cash on delivery <i class="far fa-chevron-down"></i>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="method3" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>Please send a check to Store Name, Store Street, Store Town,
                                                            Store State / County, Store Postcode.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="text-center">
                            <button
                                class="btn btn--small btn--radius btn--green btn--green-hover-black btn--uppercase font--bold"
                                type="submit">PLACE ORDER</button>
                        </div>
                    </div>
                </div> <!-- End Order Wrapper -->
            </div>
            {!! Form::close() !!}
        </div>
    </main> <!-- ::::::  End  Main Container Section  ::::::  -->

@endsection
@push('scripts_lib')
@endpush
