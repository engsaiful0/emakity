<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Making your Grocery a lot Easier">
    <meta name="author" content="Ansonika">

    <title>{{ setting('app_name') }} | {{ setting('app_short_description') }}</title>
    <!-- Tell the browser to be responsive to screen width -->

    <link rel="shortcut icon" href="{{ $app_logo }}" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="{{ $app_logo }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="{{ $app_logo }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="{{ $app_logo }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="{{ $app_logo }}">

    <!-- GOOGLE WEB FONT -->
    <link href="{{ asset('/') }}authentication/https://fonts.googleapis.com/css?family=Work+Sans:400,500,600"
        rel="stylesheet">

    <!-- BASE CSS -->
    <link href="{{ asset('/') }}authentication/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('/') }}authentication/css/menu.css" rel="stylesheet">
    <link href="{{ asset('/') }}authentication/css/style.css" rel="stylesheet">
    <link href="{{ asset('/') }}authentication/css/vendors.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="{{ asset('/') }}authentication/css/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

    <!-- MODERNIZR MENU -->
    @stack('styles')

</head>

<body class="hold-transition login-page">
    <div id="preloader">
        <div data-loader="circle-side"></div>
    </div>

    <div id="loader_form">
        <div data-loader="circle-side-2"></div>
    </div>



    <div class="container-fluid">
        <div class="row row-height">
            <div class="col-xl-4 col-lg-4 content-left">
                <div class="content-left-wrapper">
                    <a href="{{url('/')}}" id="logo"><img src="{{ $app_logo }}" alt=""
                            width="45" height="45"></a>
                    <div id="social">
                        <ul>
                            <li><a href="#0"><i class="icon-facebook"></i></a></li>
                            <li><a href="#0"><i class="icon-twitter"></i></a></li>
                            <li><a href="#0"><i class="icon-google"></i></a></li>
                            <li><a href="#0"><i class="icon-linkedin"></i></a></li>
                        </ul>
                    </div>
                    <!-- /social -->
                    <div>
                        <figure><img src="{{ asset('/') }}authentication/img/info_graphic_1.svg" alt=""
                                class="img-fluid" width="270" height="270"></figure>
                        <h2>We are Hiring</h2>
                        <p>Tation argumentum et usu, dicit viderer evertitur te has. Eu dictas concludaturque usu,
                            facete detracto patrioque an per, lucilius pertinacia eu vel.</p>
                        <a href="#start" class="btn_1 rounded mobile_btn yellow">Start Now!</a>
                    </div>
                </div>
                <!-- /content-left-wrapper -->
            </div>
            <!-- /content-left -->
            @yield('content')

            <!-- /content-right-->
        </div>
        <!-- /row-->
    </div>

    <!-- /container-fluid -->

    <div class="cd-overlay-nav">
        <span></span>
    </div>
    <!-- /cd-overlay-nav -->

    <div class="cd-overlay-content">
        <span></span>
    </div>


    <!-- Modal terms -->
    <div class="modal fade" id="terms-txt" tabindex="-1" role="dialog" aria-labelledby="termsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="termsLabel">Terms and conditions</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Lorem ipsum dolor sit amet, in porro albucius qui, in <strong>nec quod novum accumsan</strong>,
                        mei ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus,
                        pro ne quod dicunt sensibus.</p>
                    <p>Lorem ipsum dolor sit amet, in porro albucius qui, in nec quod novum accumsan, mei ludus tamquam
                        dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt
                        sensibus. Lorem ipsum dolor sit amet, <strong>in porro albucius qui</strong>, in nec quod novum
                        accumsan, mei ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum
                        sanctus, pro ne quod dicunt sensibus.</p>
                    <p>Lorem ipsum dolor sit amet, in porro albucius qui, in nec quod novum accumsan, mei ludus tamquam
                        dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt
                        sensibus.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn_1" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- COMMON SCRIPTS -->

    <script src="{{ asset('/') }}authentication/js/jquery-3.5.1.min.js"></script>
    <script src="{{ asset('/') }}authentication/js/modernizr.js"></script>
    <script src="{{ asset('/') }}authentication/js/common_scripts.min.js"></script>
    <script src="{{ asset('/') }}authentication/js/velocity.min.js"></script>
    <script src="{{ asset('/') }}authentication/js/common_functions.js"></script>
    <script src="{{ asset('/') }}authentication/js/file-validator.js"></script>

    <!-- Wizard script-->
    <script src="{{ asset('/') }}authentication/js/func_1.js"></script>
    <script src="{{asset('plugins/select2/select2.min.js')}}"></script>
    @stack('scripts')

</body>

</html>
