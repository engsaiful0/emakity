<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Question Field -->
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
            isset($area->state)?[$area->state_id=>$area->state->name]:[]
            ,null, ['class' => 'select-state form-control','id'=>'change-state']) !!}
            <div class="form-text text-muted">{{ trans("lang.app_setting_default_state_help") }}</div>
        </div>
    </div>
    <div class="form-group row ">
        {!! Form::label('name', trans('lang.area_name'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('lang.area_name_placeholder')]) !!}
            <div class="form-text text-muted">{{ trans('lang.area_name_help') }}</div>
        </div>
    </div>

</div>

<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-save"></i>
        {{ trans('lang.save') }} {{ trans('lang.area') }}</button>
    <a href="{!! route('areas.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('lang.cancel') }}</a>
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
            @if(!isset($area) || auth()->user()->hasRole('branch'))
                $('#change-country').trigger('change');
            @endif
        });
    </script>
@endpush