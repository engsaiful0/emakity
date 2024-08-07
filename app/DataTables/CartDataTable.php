<?php

namespace App\DataTables;

use App\Models\Cart;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class CartDataTable extends DataTable
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
        // if (auth()->user()->hasRole('client'))
        //     $query = $query->where('user_id', auth()->id());
        // if (auth()->user()->hasRole('branch'))
        //     $query = $query->where('user_id', auth()->id());


        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
        ->editColumn('product.market.country.name', function ($cart) {
            return $cart['product']['market']['country']['name'];
        })
            ->editColumn('updated_at', function ($cart) {
                return getDateColumn($cart, 'updated_at');
            })
            ->editColumn('options', function ($cart) {
                return getLinksColumn($cart->options, 'options', 'id', 'name');
            })
            ->addColumn('action', 'carts.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Cart $model)
    {

        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->select('carts.*')->with("product.market.country")->with("user");
        } else {
            return $model->newQuery()->with("product.market.country")->with("user")
                ->where('carts.user_id', auth()->id())
                ->select('carts.*');
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
                    'data' => 'product.name',
                    'title' => trans('lang.cart_product_id'),
    
                ],
                [
                    'data' => 'product.market.country.name',
                    'title' => trans('lang.country'),
    
                ],
                [
                    'data' => 'user.name',
                    'title' => trans('lang.cart_user_id'),
    
                ],
                [
                    'data' => 'options',
                    'title' => trans('lang.cart_options'),
                    'searchable' => false,
                ],
                [
                    'data' => 'quantity',
                    'title' => trans('lang.cart_quantity'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.cart_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'product.name',
                    'title' => trans('lang.cart_product_id'),
    
                ],
                [
                    'data' => 'options',
                    'title' => trans('lang.cart_options'),
                    'searchable' => false,
                ],
                [
                    'data' => 'quantity',
                    'title' => trans('lang.cart_quantity'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.cart_updated_at'),
                    'searchable' => false,
                ]
            ]; 
        }


        $columns = array_filter($columns);
        $hasCustomField = in_array(Cart::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Cart::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.cart_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'cartsdatatable_' . time();
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
}