@extends('authentication.layouts.auth.default')
@section('content')



    <div class="col-xl-8 col-lg-8 content-right" id="start">
        <div id="wizard_container">
            <div id="top-wizard">
                <span id="location"></span>
                <div id="progressbar"></div>
            </div>
            <!-- /top-wizard -->
            <div class="header__logo text-center">
                <a href="{{ url('/') }}" class="header__logo-link img-responsive">
                    <img class="header__logo-img img-fluid" src="{{ $app_logo }}" alt="" style="width:120px;height:120px;">
                </a>
            </div>
            <div class="card-body login-card-body">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">{{ __('auth.login_title') }}</p>

                    <form action="{{ url('/login') }}" method="post">
                        {!! csrf_field() !!}

                        <div class="input-group mb-3">
                            <input value="{{ old('email') }}" type="email"
                                class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                placeholder="{{ __('auth.email') }}" aria-label="{{ __('auth.email') }}" autocomplete="new-password">
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>

                        <div class="input-group mb-3">
                            <input value="{{ old('password') }}" type="password"
                                class="form-control  {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                                placeholder="{{ __('auth.password') }}" aria-label="{{ __('auth.password') }}" autocomplete="new-password">
                            @if ($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="row mb-2">
                            <div class="col-10">
                                <div class="checkbox icheck">
                                    <label> <input type="checkbox" name="remember"> {{ __('auth.remember_me') }}
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            {{-- <div class="col-2">
                                <button type="submit" class="submit">{{ __('auth.login') }}</button>
                            </div> --}}
                            <!-- /.col -->
                        </div>

                        <div class="social-auth-links text-center mb-3">
                            <button type="submit" class="submit btn-block">{{ __('auth.login') }}</button>
                            {{-- <a href="{{ url('login/facebook') }}" class="btn btn-primary btn-block btn-facebook">
                                <i class="fa fa-facebook mr-2"></i> {{ __('auth.login_facebook') }}
                            </a> --}}
                        </div>
                        @if (env('APP_DEMO', false))
                            <div class="row mb-2">
                                <div class="col-12 callout callout-success">
                                    <h6 class="text-bold">Admin</h6>
                                    <p><small>User: admin@demo.com | Password: 123456</small></p>
                                </div>
                                <!-- /.col -->
                            </div>
                            <div class="row mb-2">
                                <div class="col-12 callout callout-warning">
                                    <h6 class="text-bold">Manager</h6>
                                    <p><small>User: manager@demo.com | Password: 123456</small></p>
                                </div>
                                <!-- /.col -->
                            </div>
                            <div class="row mb-2">
                                <div class="col-12 callout callout-danger">
                                    <h6 class="text-bold">Client</h6>
                                    <p><small>User: client@demo.com | Password: 123456</small></p>
                                </div>
                                <!-- /.col -->
                            </div>
                        @endif

                    </form>

                    @if (setting('enable_facebook', false) || setting('enable_google', false) || setting('enable_twitter', false))
                        <div class="social-auth-links text-center mb-3">
                            <p style="text-transform: uppercase">- {{ __('lang.or') }} -</p>
                            @if (setting('enable_facebook', false))
                                <a href="{{ url('login/facebook') }}" class="btn btn-primary btn-block btn-facebook">
                                    <i class="fa fa-facebook mr-2"></i> {{ __('auth.login_facebook') }}
                                </a>
                            @endif
                            @if (setting('enable_google', false))
                                <a href="{{ url('login/google') }}" class="btn btn-danger btn-block btn-google"> <i
                                        class="fa fa-google-plus mr-2"></i> {{ __('auth.login_google') }}
                                </a>
                            @endif
                            @if (setting('enable_twitter', false))
                                <a href="{{ url('login/twitter') }}" class="btn btn btn-info btn-block btn-twitter"> <i
                                        class="fa fa-twitter mr-2"></i> {{ __('auth.login_twitter') }}
                                </a>
                            @endif
                        </div>
                        <!-- /.social-auth-links -->
                    @endif

                    <p class="mb-1 text-center">
                        <a href="{{ url('/password/reset') }}">{{ __('auth.forgot_password') }}</a>
                    </p>
                    <p class="mb-0 text-center">
                        <a href="{{ url('/register') }}" class="text-center">{{ __('auth.register_new_member') }}</a>
                    </p>
                </div>
            </div>
        </div>
        <!-- /Wizard container -->
    </div>

@endsection
