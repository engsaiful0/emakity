<?php

namespace App\DataTables;

use App\Models\FaqCategory;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class FaqCategoryDataTable extends DataTable
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
        if (auth()->user()->hasRole('branch') || auth()->user()->hasRole('manager'))
            $query = $query->where('country_id', get_role_country_id('branch'));
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
        ->editColumn('country.name', function ($faq_category) {
            return $faq_category['country']['name'];
        })
        ->editColumn('updated_at',function($faq_category){
            return getDateColumn($faq_category,'updated_at');
        })
        ->editColumn('name', function ($faq_category) {
            return $faq_category->name;
        })
            
        ->addColumn('action', 'faq_categories.datatables_actions')
        ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FaqCategory $model)
    {
        return $model->newQuery()->with('country')->select('faq_categories.*');
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
            ->addAction(['title'=>trans('lang.actions'),'width' => '80px', 'printable' => false ,'responsivePriority'=>'100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/'.app()->getLocale().'/datatable.json')
                        ),true)
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
                    'title' => trans('lang.faq_category_name'),
                
                ],
                [
                    'data' => 'country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.faq_category_updated_at'),
                    'searchable'=>false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'name',
                    'title' => trans('lang.faq_category_name'),
                
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.faq_category_updated_at'),
                    'searchable'=>false,
                ]
            ];            
        }


        $hasCustomField = in_array(FaqCategory::class, setting('custom_field_models',[]));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', FaqCategory::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.faq_category_' . $field->name),
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
        return 'faq_categoriesdatatable_' . time();
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename().'.pdf');
    }
}