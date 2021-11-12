@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans("lang.user_name"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.user_name_help") }}
            </div>
        </div>
    </div>
    <div class="form-group row ">
        {!! Form::label('language', trans("lang.app_setting_language"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('language', getAvailableLanguages(), null , ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.app_setting_language_help") }}</div>
        </div>
    </div>

    @if(!auth()->user()->hasRole('branch') && !auth()->user()->hasRole('manager'))
        <div class="form-group row">
            {!! Form::label('country_id', trans('lang.app_country'), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
                {!! Form::select('country_id',
                $countries
                ,null, ['class' => 'select-country form-control','id'=>'change-country']) !!}
                <div class="form-text text-muted">{{ trans("lang.app_setting_default_country_help") }}</div>
            </div>
        </div>
    @else
        {!! Form::hidden('country_id', auth()->user()->country_id,  ['class' => 'form-control','placeholder'=>  trans("lang.user_name_placeholder"),'id'=>'change-country']) !!}
    @endif
    <div class="form-group row">
        {!! Form::label('state_id', trans('lang.app_state'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('state_id',
           isset($user->state)?[$user->state_id=>$user->state->name]:[]
            ,null, ['class' => 'select-state form-control','id'=>'change-state']) !!}
            <div class="form-text text-muted">{{ trans("lang.app_setting_default_state_help") }}</div>
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('area_id', trans('lang.app_area'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('area_id',
            isset($user->area)?[$user->area_id=>$user->area->name]:[]
            ,null, ['class' => 'select-area form-control','id'=>'change-area']) !!}
            <div class="form-text text-muted">{{ trans("lang.app_setting_default_area_help") }}</div>
        </div>
    </div>

    <!-- Email Field -->
    <div class="form-group row ">
        {!! Form::label('email', trans("lang.user_email"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('email', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_email_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.user_email_help") }}
            </div>
        </div>
    </div>

    <!-- Password Field -->
    <div class="form-group row ">
        {!! Form::label('password', trans("lang.user_password"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::password('password', ['class' => 'form-control','placeholder'=>  trans("lang.user_password_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.user_password_help") }}
            </div>
        </div>
    </div>
</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- $FIELD_NAME_TITLE$ Field -->
    <div class="form-group row">
        {!! Form::label('avatar', trans("lang.user_avatar"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            <div style="width: 100%" class="dropzone avatar" id="avatar" data-field="avatar">
                <input type="hidden" name="avatar">
            </div>
            <a href="#loadMediaModal" data-dropzone="avatar" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>
            <div class="form-text text-muted w-50">
                {{ trans("lang.user_avatar_help") }}
            </div>
        </div>
    </div>
    @prepend('scripts')
    <script type="text/javascript">
        var user_avatar = '';
        @if(isset($user) && $user->hasMedia('avatar'))
            user_avatar = {
            name: "{!! $user->getFirstMedia('avatar')->name !!}",
            size: "{!! $user->getFirstMedia('avatar')->size !!}",
            type: "{!! $user->getFirstMedia('avatar')->mime_type !!}",
            collection_name: "{!! $user->getFirstMedia('avatar')->collection_name !!}"
        };
                @endif
        var dz_user_avatar = $(".dropzone.avatar").dropzone({
                url: "{!!url('uploads/store')!!}",
                addRemoveLinks: true,
                maxFiles: 1,
                init: function () {
                    @if(isset($user) && $user->hasMedia('avatar'))
                    dzInit(this, user_avatar, '{!! url($user->getFirstMediaUrl('avatar','thumb')) !!}')
                    @endif
                },
                accept: function (file, done) {
                    dzAccept(file, done, this.element, "{!!config('medialibrary.icons_folder')!!}");
                },
                sending: function (file, xhr, formData) {
                    dzSending(this, file, formData, '{!! csrf_token() !!}');
                },
                maxfilesexceeded: function (file) {
                    dz_user_avatar[0].mockFile = '';
                    dzMaxfile(this, file);
                },
                complete: function (file) {
                    dzComplete(this, file, user_avatar, dz_user_avatar[0].mockFile);
                    dz_user_avatar[0].mockFile = file;
                },
                removedfile: function (file) {
                    dzRemoveFile(
                        file, user_avatar, '{!! url("users/remove-media") !!}',
                        'avatar', '{!! isset($user) ? $user->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
                    );
                }
            });
        dz_user_avatar[0].mockFile = user_avatar;
        dropzoneFields['avatar'] = dz_user_avatar;
    </script>
@endprepend

<!-- Roles Field -->
    <div class="form-group row ">
        {!! Form::label('roles[]', trans("lang.user_role_id"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('roles[]', $role, $rolesSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
            <div class="form-text text-muted">{{ trans("lang.user_role_id_help") }}</div>
        </div>
    </div>

</div>
@if($customFields)
    {{--TODO generate custom field--}}
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.user')}}</button>
    <a href="{!! route('users.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>

@push('scripts_lib')
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
                @if(!isset($user) || auth()->user()->hasRole('branch'))
                    $('#change-country').trigger('change');
                    $('#change-state').trigger('change');                    
                @endif
        });
    </script>
@endpush
