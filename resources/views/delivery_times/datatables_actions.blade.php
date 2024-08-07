<div class='btn-group btn-group-sm'>
  @can('deliveryTimes.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('deliveryTimes.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('deliveryTimes.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.delivery_time_edit')}}" href="{{ route('deliveryTimes.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('deliveryTimes.destroy')
{!! Form::open(['route' => ['deliveryTimes.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
