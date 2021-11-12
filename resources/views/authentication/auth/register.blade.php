@extends('authentication.layouts.auth.default')
@push('styles')

@endpush
@section('content')
    @php
        $countries = \App\Models\Country::where('active', 1)->get();
    @endphp
    <div class="col-xl-8 col-lg-8 content-right" id="start">
        <div id="wizard_container">
            <div id="top-wizard">
                <span id="location"></span>
                <div id="progressbar"></div>
            </div>
            <!-- /top-wizard -->
            <div class="card-body login-card-body">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">{{ __('auth.register_new_member') }}</p>

                    <form action="{{ url('/register') }}" method="post">
                        {!! csrf_field() !!}

                        <div class="input-group mb-3">
                            <input value="{{ old('name') }}" type="name"
                                class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
                                placeholder="{{ __('auth.name') }}" aria-label="{{ __('auth.name') }}" autocomplete="new-password">
                            @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

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

                        <div class="input-group mb-3">
                            <input value="{{ old('password_confirmation') }}" type="password"
                                class="form-control  {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                name="password_confirmation" placeholder="{{ __('auth.password_confirmation') }}"
                                aria-label="{{ __('auth.password_confirmation') }}" autocomplete="new-password">
                            @if ($errors->has('password_confirmation'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <select id="change-country" name="country_id" class="form-control select-country" autocomplete="new-password">
                                        <option value=""></option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <select id="change-state" name="state_id" class="form-control select-state" autocomplete="new-password">
                                        <option value=""></option>
            
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <select name="area_id" class="form-control select-area" autocomplete="new-password">
                                        <option value=""></option>
            
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-7">
                                <div class="checkbox icheck">
                                    <label> <input type="checkbox" name="remember"> {{ __('auth.remember_me') }}
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-5">
                                <button type="submit" class="submit">{{ __('auth.register') }}</button>
                            </div>
                            <!-- /.col -->
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

                    <p class="mb-0 text-center">
                        <a href="{{ url('/login') }}" class="text-center">{{ __('auth.already_member') }}</a>
                    </p>
                </div>
            </div>
        </div>
        <!-- /Wizard container -->
    </div>

@endsection

@push('scripts')
  <!-- select2 -->
<script>
            $(document).ready(function() {
            $('.select-country').select2({
                placeholder: "Select country",
            });

            $('.select-state').select2({
                placeholder: "Select state",
            });

            $('.select-area').select2({
                placeholder: "Select Area",
            });

            $('.select-country').trigger('change');
            $('.select-state').trigger('change');
            $('#change-country').change(function() {
                var id = $(this).val();
                $.get("{{ route('get-states-ajax') }}?country_id=" + id, function(data) {
                    $('select[name ="state_id"]').empty();
                    $('select[name ="state_id"]').append(
                        '<option value=""></option>');
                    for (let index = 0; index < data.length; index++) {
                        const element = data[index];

                        $('select[name ="state_id"]').append('<option value="' +
                            element['id'] + '">' + element['name'] + '</option>');
                    }


                });
            });
            $('#change-state').change(function() {
                var id = $(this).val();

                $.get("{{ route('get-areas-ajax') }}?state_id=" + id, function(data) {
                    $('select[name ="area_id"]').empty();
                    $('select[name ="area_id"]').append(
                        '<option value=""></option>');
                    for (let index = 0; index < data.length; index++) {
                        const element = data[index];
                        $('select[name ="area_id"]').append('<option value="' +
                            element['id'] + '">' + element['name'] + '</option>');
                    }


                });
            });
        });
</script>
@endpush
