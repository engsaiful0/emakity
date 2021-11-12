@extends('frontend.layouts.app')
@push('css_lib')

@endpush
@section('content')
<div class="container-fluid">
    @include('flash::message')
</div>



    <div class="page-breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="page-breadcrumb__menu">
                        <li class="page-breadcrumb__nav"><a href="{{url('/')}}">Home</a></li>
                        <li class="page-breadcrumb__nav active">{{$market->name}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div> <!-- ::::::  End  Breadcrumb Section  ::::::  -->



    <main id="main-container" class="main-container">
        <div class="container">
            <div class="row">
                <!-- Start Rightside - Content -->
                <div class="col-12">
                    <div class="img-responsive">
                        <img src="assets/img/banner/size-wide/banner-shop-1-img-1-wide.jpg" alt="">
                    </div>


                    <div class="product-tab-area">
                        <div class="tab-content tab-animate-zoom">
                            <div class="tab-pane show active shop-grid" id="sort-grid">
                                <div class="row">



                                    @foreach ($market->products as $product)
                                        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">
                                            @include('frontend.product-structure',['product'=>$product])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </main>



@endsection
@push('scripts_lib')
@endpush
