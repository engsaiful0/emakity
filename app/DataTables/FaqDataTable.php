<?php

namespace App\DataTables;

use App\Models\Faq;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class FaqDataTable extends DataTable
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
            $query = $query->whereHas('faqCategory.country', function($q){
                return $q->where('countries.id',get_role_country_id('branch'));
            });
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
        ->editColumn('faqCategory.country.name', function ($faq) {
            return $faq['faqCategory']['country']['name'];
        })
        ->editColumn('question', function ($faq) {
            return getStripedHtmlColumn($faq, 'question');
        })
        ->editColumn('answer', function ($faq) {
            return getStripedHtmlColumn($faq, 'answer');
        })
        ->editColumn('faqCategory.name', function ($faq) {
            return $faq['faqCategory']['name'];
        })    
        ->editColumn('updated_at', function ($faq) {
                return getDateColumn($faq, 'updated_at');
            })
            ->addColumn('action', 'faqs.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Faq $model)
    {
        return $model->newQuery()->with("faqCategory.country")->select('faqs.*');
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
                    'data' => 'question',
                    'title' => trans('lang.faq_question'),
    
                ],
                [
                    'data' => 'answer',
                    'title' => trans('lang.faq_answer'),
    
                ],
                [
                    'data' => 'faqCategory.name',
                    'title' => trans('lang.faq_category_id'),
    
                ],
                [
                    'data' => 'faqCategory.country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.faq_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'question',
                    'title' => trans('lang.faq_question'),
    
                ],
                [
                    'data' => 'answer',
                    'title' => trans('lang.faq_answer'),
    
                ],
                [
                    'data' => 'faqCategory.name',
                    'title' => trans('lang.faq_category_id'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.faq_updated_at'),
                    'searchable' => false,
                ]
            ];
            
        }


        $hasCustomField = in_array(Faq::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Faq::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.faq_' . $field->name),
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
        return 'faqsdatatable_' . time();
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