<?php

namespace App\DataTables;

use App\Models\Area;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class AreaDataTable extends DataTable
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
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
        ->editColumn('state.name', function ($area) {
            return $area['state']['name'];
        })
        ->editColumn('country.name', function ($area) {
            return $area['country']['name'];
        })    
        ->editColumn('updated_at', function ($area) {
                return getDateColumn($area, 'updated_at');
            })
            ->addColumn('action', 'settings.areas.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Area $model)
    {
        if(auth()->user()->hasRole('admin'))
        {
            return $model->newQuery()->with('country')->with('state')->select('areas.*');        
        }
        else
        {
            return $model->newQuery()->with('country')->with('state')->select('areas.*')->where('country_id',auth()->user()->country_id);
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
                    'data' => 'name',
                    'title' => trans('lang.area_name'),
                    'searchable' => true,
                ],
                [
                    'data' => 'state.name',
                    'title' => trans('lang.state'),
                    'searchable' => false,
                ],
                [
                    'data' => 'country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.area_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'name',
                    'title' => trans('lang.area_name'),
                    'searchable' => true,
                ],
                [
                    'data' => 'state.name',
                    'title' => trans('lang.state'),
                    'searchable' => false,
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.area_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        $columns = [
            [
                'data' => 'name',
                'title' => trans('lang.area_name'),
                'searchable' => true,
            ],
            [
                'data' => 'state.name',
                'title' => trans('lang.state'),
                'searchable' => false,
            ],
            (auth()->check() && auth()->user()->hasRole('admin')) ? [
                'data' => 'country.name',
                'title' => trans('lang.country'),
            ] : '',
            [
                'data' => 'updated_at',
                'title' => trans('lang.area_updated_at'),
                'searchable' => false,
            ]
        ];

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'areasdatatable_' . time();
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