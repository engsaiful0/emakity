@extends('authentication.layouts.auth.default')
@section('content')


    <div class="col-xl-8 col-lg-8 content-right" id="start">
        <div id="wizard_container">
            <div id="top-wizard">
                <span id="location"></span>
                <div id="progressbar"></div>
            </div>
            <!-- /top-wizard -->
            <div class="card-body login-card-body">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">{{ __('auth.reset_password_title') }}</p>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{ route('password.request') }}" method="post">
                        {!! csrf_field() !!}
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="input-group mb-3">
                            <input value="{{ old('email') }}" type="email"
                                class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                placeholder="{{ __('auth.email') }}" aria-label="{{ __('auth.email') }}">
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>

                        <div class="input-group mb-3">
                            <input value="{{ old('password') }}" type="password"
                                class="form-control  {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                                placeholder="{{ __('auth.password') }}" aria-label="{{ __('auth.password') }}">
                            @if ($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="input-group mb-3">
                            <input value="{{ old('password_confirmation') }}" type="password"
                                class="form-control  {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                name="password_confirmation" placeholder="{{ __('auth.password_confirmation') }}"
                                aria-label="{{ __('auth.password_confirmation') }}">
                            @if ($errors->has('password_confirmation'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>

                        <div class="row mb-2">
                            <div class="col-8">
                                <button type="submit" class="submit">{{ __('auth.reset_password') }}</button>
                            </div>
                        </div>


                    </form>


                    <p class="mb-1 text-center">
                        <a href="{{ url('/login') }}">{{ __('auth.remember_password') }}</a>
                    </p>
                </div>
            </div>
        </div>
        <!-- /Wizard container -->
    </div>

@endsection
