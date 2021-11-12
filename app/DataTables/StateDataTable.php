<?php

namespace App\DataTables;

use App\Models\State;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class StateDataTable extends DataTable
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
        ->editColumn('country.name', function ($state) {
            return $state['country']['name'];
        })    
        ->editColumn('updated_at', function ($state) {
            return getDateColumn($state, 'updated_at');
        })
        ->addColumn('action', 'settings.states.datatables_actions')
        ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(State $model)
    {
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch'))
        {
            return $model->newQuery()->with('country')->select('states.*');
        }
        else
        {
            return $model->newQuery()->with('country')->where('country_id',auth()->user()->country_id)->select('states.*');
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
                    'title' => trans('lang.state_name'),
                    'searchable' => true,
                ],
                [
                    'data' => 'country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.state_updated_at'),
                    'searchable' => false,
                ]
            ];

        }
        else
        {
            $columns = [
                [
                    'data' => 'name',
                    'title' => trans('lang.state_name'),
                    'searchable' => true,
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.state_updated_at'),
                    'searchable' => false,
                ]
            ];
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
        return 'statesdatatable_' . time();
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