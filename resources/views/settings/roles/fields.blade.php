<!-- Name Field -->
<div class="form-group row col-6">
    {!! Form::label('name', 'Name:', ['class' => 'col-4 control-label text-right']) !!}
    <div class="col-8">
        {!! Form::text('name', null, ['class' => 'form-control','placeholder'=>
        (Lang::has("lang.role_name_placeholder")) ? trans("lang.role_name_placeholder") : "Name here"]) !!}
        <div class="form-text text-muted">
            {{(Lang::has("lang.role_name_help")) ? trans("lang.role_name_help") : "Insert the Name"}}
        </div>
    </div>
</div>
<input type="hidden" name="country_id" value="{{auth()->user()->country_id}}">

<!-- Guard Name Field -->
<div class="form-group row col-6">
    {!! Form::label('guard_name', 'Guard Name:', ['class' => 'col-4 control-label text-right']) !!}
    <div class="col-8">
        {!! Form::text('guard_name', null, ['class' => 'form-control','placeholder'=>
      (Lang::has("lang.role_name_placeholder")) ? trans("lang.role_guard_name_placeholder") : "Name guard_name"]) !!}
        <div class="form-text text-muted">
            {{(Lang::has("lang.role_guard_name_help")) ? trans("lang.role_guard_name_help") : "Insert the guard_name"}}
        </div>
    </div>
</div>

{{-- <div class="form-group row">
    {!! Form::label('country_id', trans('lang.app_country'), ['class' => 'col-3 control-label text-right']) !!}
    <div class="col-9">
        {!! Form::select('country_id', $countries, null, ['class' => 'select2 form-control']) !!}
        <div class="form-text text-muted">{{ trans('lang.app_setting_default_country_help') }}</div>
    </div>
</div> --}}


<!-- 'Boolean Default Field' -->
<div class="form-group row col-6">
    {!! Form::label('default', trans("lang.role_default"),['class' => 'col-4 control-label text-right']) !!}
    <div class="checkbox icheck">
        <label class="col-8 ml-2 form-check-inline">
            {!! Form::hidden('default', 0) !!}
            {!! Form::checkbox('default', 1, null) !!}
        </label>

    </div>
</div>



<!-- Submit Field -->
<div class="form-group col-sm-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.role')}}</button>
    <a href="{!! route('roles.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
