<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans('lang.delivery_time_name'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('lang.delivery_time_name_placeholder')]) !!}
            <div class="form-text text-muted">
                {{ trans('lang.delivery_time_name_help') }}
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
</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
</div>
<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-save"></i>
        {{ trans('lang.save') }} {{ trans('lang.delivery_time') }}</button>
    <a href="{!! route('deliveryTimes.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('lang.cancel') }}</a>
</div>
