<?php

namespace App\DataTables;

use App\Models\Gallery;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class GalleryDataTable extends DataTable
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
            $query = $query->whereHas('market.country', function($q){
                return $q->where('countries.id',get_role_country_id('branch'));
            });
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('image', function ($gallery) {
                return getMediaColumn($gallery, 'image');
            })
            ->editColumn('market.country.name', function ($product) {
                return $product['market']['country']['name'];
            })
            ->editColumn('updated_at', function ($gallery) {
                return getDateColumn($gallery, 'updated_at');
            })
            ->addColumn('action', 'galleries.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Gallery $model)
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch')) {
            return $model->newQuery()->with("market.country")->select('galleries.*');
        } else {
            return $model->newQuery()->with("market.country")
                ->join("user_markets", "user_markets.market_id", "=", "galleries.market_id")
                ->where('user_markets.user_id', auth()->id())
                ->select('galleries.*');
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
                    'data' => 'description',
                    'title' => trans('lang.gallery_description'),
    
                ],
                [
                    'data' => 'market.country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'image',
                    'title' => trans('lang.gallery_image'),
                    'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
                ],
                [
                    'data' => 'market.name',
                    'title' => trans('lang.gallery_market_id'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.gallery_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'description',
                    'title' => trans('lang.gallery_description'),
    
                ],
                [
                    'data' => 'image',
                    'title' => trans('lang.gallery_image'),
                    'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
                ],
                [
                    'data' => 'market.name',
                    'title' => trans('lang.gallery_market_id'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.gallery_updated_at'),
                    'searchable' => false,
                ]
            ];
            
        }

        $hasCustomField = in_array(Gallery::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Gallery::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.gallery_' . $field->name),
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
        return 'galleriesdatatable_' . time();
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