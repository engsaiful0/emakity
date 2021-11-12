@if ($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_testimonials') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans('lang.testimonial_name'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('lang.testimonial_name_placeholder')]) !!}
            <div class="form-text text-muted">
                {{ trans('lang.testimonial_name_help') }}
            </div>
        </div>
    </div>
    @if(!auth()->user()->hasRole('branch') && !auth()->user()->hasRole('manager'))
        <div class="form-group row">
            {!! Form::label('country_id', trans('lang.app_country'), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
                {!! Form::select('country_id',
                $countries
                ,null, ['class' => 'select2 form-control','id'=>'change-country']) !!}
                <div class="form-text text-muted">{{ trans("lang.app_setting_default_country_help") }}</div>
            </div>
        </div>
    @else
        {!! Form::hidden('country_id', auth()->user()->country_id,  ['class' => 'form-control','placeholder'=>  trans("lang.user_name_placeholder"),'id'=>'change-country']) !!}
    @endif

    <!-- Description Field -->
    <div class="form-group row ">
        {!! Form::label('description', trans('lang.testimonial_description'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => trans('lang.testimonial_description_placeholder')]) !!}
            <div class="form-text text-muted">{{ trans('lang.testimonial_description_help') }}</div>
        </div>
    </div>
</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

    <!-- Image Field -->
    <div class="form-group row">
        {!! Form::label('image', trans('lang.testimonial_image'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            <div style="width: 100%" class="dropzone image" id="image" data-field="image">
                <input type="hidden" name="image">
            </div>
            <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal"
                class="btn btn-outline-{{ setting('theme_color', 'primary') }} btn-sm float-right mt-1">{{ trans('lang.media_select') }}</a>
            <div class="form-text text-muted w-50">
                {{ trans('lang.testimonial_image_help') }}
            </div>
        </div>
    </div>
    @prepend('scripts')
        <script type="text/javascript">
            var var15866134631720934041ble = '';
            @if (isset($testimonial) && $testimonial->hasMedia('image'))
                var15866134631720934041ble = {
                name: "{!! $testimonial->getFirstMedia('image')->name !!}",
                size: "{!! $testimonial->getFirstMedia('image')->size !!}",
                type: "{!! $testimonial->getFirstMedia('image')->mime_type !!}",
                collection_name: "{!! $testimonial->getFirstMedia('image')->collection_name !!}"};
            @endif
            var dz_var15866134631720934041ble = $(".dropzone.image").dropzone({
                url: "{!! url('uploads/store') !!}",
                addRemoveLinks: true,
                maxFiles: 1,
                init: function() {
                    @if (isset($testimonial) && $testimonial->hasMedia('image'))
                        dzInit(this,var15866134631720934041ble,'{!! url($testimonial->getFirstMediaUrl('image', 'thumb')) !!}')
                    @endif
                },
                accept: function(file, done) {
                    dzAccept(file, done, this.element, "{!! config('medialibrary.icons_folder') !!}");
                },
                sending: function(file, xhr, formData) {
                    dzSending(this, file, formData, '{!! csrf_token() !!}');
                },
                maxfilesexceeded: function(file) {
                    dz_var15866134631720934041ble[0].mockFile = '';
                    dzMaxfile(this, file);
                },
                complete: function(file) {
                    dzComplete(this, file, var15866134631720934041ble, dz_var15866134631720934041ble[0]
                        .mockFile);
                    dz_var15866134631720934041ble[0].mockFile = file;
                },
                removedfile: function(file) {
                    dzRemoveFile(
                        file, var15866134631720934041ble, '{!! url('testimonials/remove-media') !!}',
                        'image', '{!! isset($testimonial) ? $testimonial->id : 0 !!}', '{!! url('uplaods/clear') !!}',
                        '{!! csrf_token() !!}'
                    );
                }
            });
            dz_var15866134631720934041ble[0].mockFile = var15866134631720934041ble;
            dropzoneFields['image'] = dz_var15866134631720934041ble;

        </script>
    @endprepend

</div>

<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

    <!-- Image Field -->
    <div class="form-group row">
        {!! Form::label('web_image', trans('lang.product_web_image'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            <div style="width: 100%" class="dropzone web_image" id="web_image" data-field="web_image">
                <input type="hidden" name="web_image">
            </div>
            <a href="#loadMediaModal" data-dropzone="web_image" data-toggle="modal" data-target="#mediaModal"
                class="btn btn-outline-{{ setting('theme_color', 'primary') }} btn-sm float-right mt-1">{{ trans('lang.media_select') }}</a>
            <div class="form-text text-muted w-50">
                {{ trans('lang.product_web_image_help') }}
            </div>
        </div>
    </div>
    @prepend('scripts')
        <script type="text/javascript">
            var var15866134631720934041ble = '';
            @if (isset($testimonial) && $testimonial->hasMedia('web_image'))
                var15866134631720934041ble = {
                name: "{!! $testimonial->getFirstMedia('web_image')->name !!}",
                size: "{!! $testimonial->getFirstMedia('web_image')->size !!}",
                type: "{!! $testimonial->getFirstMedia('web_image')->mime_type !!}",
                collection_name: "{!! $testimonial->getFirstMedia('web_image')->collection_name !!}"};
            @endif
            var dz_var15866134631720934041ble = $(".dropzone.web_image").dropzone({
                url: "{!! url('uploads/store') !!}",
                addRemoveLinks: true,
                maxFiles: 1,
                init: function() {
                    @if (isset($testimonial) && $testimonial->hasMedia('web_image'))
                        dzInit(this,var15866134631720934041ble,'{!! url($testimonial->getFirstMediaUrl('web_image', 'thumb')) !!}')
                    @endif
                },
                accept: function(file, done) {
                    dzAccept(file, done, this.element, "{!! config('medialibrary.icons_folder') !!}");
                },
                sending: function(file, xhr, formData) {
                    dzSending(this, file, formData, '{!! csrf_token() !!}');
                },
                maxfilesexceeded: function(file) {
                    dz_var15866134631720934041ble[0].mockFile = '';
                    dzMaxfile(this, file);
                },
                complete: function(file) {
                    dzComplete(this, file, var15866134631720934041ble, dz_var15866134631720934041ble[0]
                        .mockFile);
                    dz_var15866134631720934041ble[0].mockFile = file;
                },
                removedfile: function(file) {
                    dzRemoveFile(
                        file, var15866134631720934041ble, '{!! url('testimonials/remove-media') !!}',
                        'web_image', '{!! isset($testimonial) ? $testimonial->id : 0 !!}', '{!! url('uplaods/clear') !!}',
                        '{!! csrf_token() !!}'
                    );
                }
            });
            dz_var15866134631720934041ble[0].mockFile = var15866134631720934041ble;
            dropzoneFields['web_image'] = dz_var15866134631720934041ble;

        </script>
    @endprepend

</div>
@if ($customFields)
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_testimonial_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-save"></i>
        {{ trans('lang.save') }} {{ trans('lang.testimonial') }}</button>
    <a href="{!! route('testimonials.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>
        {{ trans('lang.cancel') }}</a>
</div>
