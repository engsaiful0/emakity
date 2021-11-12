<?php

namespace App\DataTables;

use App\Models\Coupon;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class CouponDataTable extends DataTable
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
            $query = $query->where('country_id',get_role_country_id('branch'));        
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('country.name', function ($coupon) {
                return $coupon['country']['name'];
            })
            ->editColumn('updated_at', function ($coupon) {
                return getDateColumn($coupon, 'updated_at');
            })
            ->editColumn('expires_at', function ($coupon) {
                return getDateColumn($coupon, 'expires_at');
            })
            ->editColumn('enabled', function ($coupon) {
                return getBooleanColumn($coupon, 'enabled');
            })
            ->editColumn('discount', function ($coupon) {
                if($coupon['discount_type'] == 'percent'){
                    return $coupon['discount'] . "%";
                }
                return getPriceColumn($coupon, 'discount');
            })
            ->addColumn('action', 'coupons.datatables_actions')
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
                    'data' => 'code',
                    'title' => trans('lang.coupon_code'),
    
                ],
                [
                    'data' => 'country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'discount',
                    'title' => trans('lang.coupon_discount'),
    
                ],
                [
                    'data' => 'description',
                    'title' => trans('lang.coupon_description'),
    
                ],
                [
                    'data' => 'expires_at',
                    'title' => trans('lang.coupon_expires_at'),
    
                ],
                [
                    'data' => 'enabled',
                    'title' => trans('lang.coupon_enabled'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.coupon_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'code',
                    'title' => trans('lang.coupon_code'),
    
                ],
                [
                    'data' => 'discount',
                    'title' => trans('lang.coupon_discount'),
    
                ],
                [
                    'data' => 'description',
                    'title' => trans('lang.coupon_description'),
    
                ],
                [
                    'data' => 'expires_at',
                    'title' => trans('lang.coupon_expires_at'),
    
                ],
                [
                    'data' => 'enabled',
                    'title' => trans('lang.coupon_enabled'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.coupon_updated_at'),
                    'searchable' => false,
                ]
            ];
        }


        $hasCustomField = in_array(Coupon::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Coupon::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.coupon_' . $field->name),
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
    public function query(Coupon $model)
    {
        if (auth()->user()->hasRole('manager')){
            $markets = $model->join("discountables", "discountables.coupon_id", "=", "coupons.id")
                ->join("user_markets", "user_markets.market_id", "=", "discountables.discountable_id")
                ->where('discountable_type','App\\Models\\Market')
                ->where("user_markets.user_id",auth()->id())->select("coupons.*");

            $products = $model->with('country')->join("discountables", "discountables.coupon_id", "=", "coupons.id")
                ->join("products", "products.id", "=", "discountables.discountable_id")
                ->where('discountable_type','App\\Models\\Product')
                ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
                ->where("user_markets.user_id",auth()->id())
                ->select("coupons.*")
                ->union($markets);
            return $products;
        }
        else{
            return $model->newQuery()->with('country')->select("coupons.*");
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
                        ), true),
                    'order' => [ [5, 'desc'] ],
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
        return 'couponsdatatable_' . time();
    }
}