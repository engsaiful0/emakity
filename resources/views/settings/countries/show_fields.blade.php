<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('id', 'Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->id !!}</p>
  </div>
</div>

<!-- Name Field -->
<div class="form-group row col-6">
  {!! Form::label('name', 'Name:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->name !!}</p>
  </div>
</div>

<!-- Symbol Field -->
<div class="form-group row col-6">
  {!! Form::label('symbol', 'Symbol:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->symbol !!}</p>
  </div>
</div>

<!-- Code Field -->
<div class="form-group row col-6">
  {!! Form::label('code', 'Code:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->code !!}</p>
  </div>
</div>

<!-- Decimal Digits Field -->
<div class="form-group row col-6">
  {!! Form::label('decimal_digits', 'Decimal Digits:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->decimal_digits !!}</p>
  </div>
</div>

<!-- Rounding Field -->
<div class="form-group row col-6">
  {!! Form::label('rounding', 'Rounding:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->rounding !!}</p>
  </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
  {!! Form::label('created_at', 'Created At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->created_at !!}</p>
  </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
  {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $country->updated_at !!}</p>
  </div>
</div>

