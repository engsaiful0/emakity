<?php

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Driver;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class DriverDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        if (auth()->user()->hasRole('client'))
            $query = $query->where('user_id', auth()->id());
        if (auth()->user()->hasRole('branch'))
            $query = $query->whereHas('user.country', function($q){
                return $q->where('countries.id',get_role_country_id('branch'));
            });
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('user.country.name', function ($driver) {
                return @$driver['user']['country']['name'];
            })
            ->editColumn('user.name', function ($driver) {
                return getLinksColumnByRouteName([@$driver->user], "users.edit", 'id', 'name');
            })
            ->editColumn('updated_at', function ($driver) {
                return getDateColumn(@$driver, 'updated_at');
            })
            ->editColumn('earning', function ($driver) {
                return getPriceColumn($driver,@$driver['markets'][0] ,'earning');
            })
            ->editColumn('delivery_fee', function ($driver) {
                return @$driver->delivery_fee . "%";
            })
            ->editColumn('available', function ($driver) {
                return getBooleanColumn(@$driver, 'available');
            })
            ->addColumn('action', 'drivers.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        if(auth()->check() && auth()->user()->hasRole('admin'))
        {
            $columns = [
                [
                    'data' => 'user.name',
                    'title' => trans('lang.driver_user_id'),
    
                ],
                [
                    'data' => 'user.country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'delivery_fee',
                    'title' => trans('lang.driver_delivery_fee'),
    
                ],
                [
                    'data' => 'total_orders',
                    'title' => trans('lang.driver_total_orders'),
    
                ],
                [
                    'data' => 'earning',
                    'title' => trans('lang.driver_earning'),
    
                ],
                [
                    'data' => 'available',
                    'title' => trans('lang.driver_available'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.driver_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'user.name',
                    'title' => trans('lang.driver_user_id'),
    
                ],
                [
                    'data' => 'delivery_fee',
                    'title' => trans('lang.driver_delivery_fee'),
    
                ],
                [
                    'data' => 'total_orders',
                    'title' => trans('lang.driver_total_orders'),
    
                ],
                [
                    'data' => 'earning',
                    'title' => trans('lang.driver_earning'),
    
                ],
                [
                    'data' => 'available',
                    'title' => trans('lang.driver_available'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.driver_updated_at'),
                    'searchable' => false,
                ]
            ];
        }


        $hasCustomField = in_array(Driver::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Driver::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.driver_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Driver $model)
    {
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch')){
            return $model->newQuery()->with("user.country")->select('drivers.*');
        }
        else if (auth()->user()->hasRole('manager')){
            // markets of this user
            $marketsIds = array_column(auth()->user()->markets->toArray(), 'id');

            return $model->newQuery()->with("user.country")
                ->join('driver_markets','driver_markets.user_id','=','drivers.user_id')
                ->whereIn('driver_markets.market_id',$marketsIds)
                ->distinct('driver_markets.user_id')
                ->select('drivers.*');
        }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['title'=>trans('lang.actions'),'width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'driversdatatable_' . time();
    }
}