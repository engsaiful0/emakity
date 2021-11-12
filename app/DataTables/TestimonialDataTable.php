<?php
/**
 * File name: TestimonialDataTable.php
 * Last modified: 2020.05.04 at 09:04:18
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Testimonial;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class TestimonialDataTable extends DataTable
{
    /**
     * custom testimonials columns
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
            $query = $query->where('country_id', get_role_country_id('branch'));
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('country.name', function ($testimonial) {
                return $testimonial['country']['name'];
            })
            ->editColumn('image', function ($testimonial) {
                return getMediaColumn($testimonial, 'image');
            })
            ->editColumn('updated_at', function ($testimonial) {
                return getDateColumn($testimonial, 'updated_at');
            })
            ->addColumn('action', 'testimonials.datatables_actions')
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
                    'data' => 'name',
                    'title' => trans('lang.testimonial_name'),
    
                ],
                [
                    'data' => 'country.name',
                    'title' => trans('lang.country'),
                ],
                [
                    'data' => 'description',
                    'title' => trans('lang.testimonial_description'),
    
                ],
                [
                    'data' => 'image',
                    'title' => trans('lang.testimonial_image'),
                    'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.testimonial_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'name',
                    'title' => trans('lang.testimonial_name'),
    
                ],
                [
                    'data' => 'description',
                    'title' => trans('lang.testimonial_description'),
    
                ],
                [
                    'data' => 'image',
                    'title' => trans('lang.testimonial_image'),
                    'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.testimonial_updated_at'),
                    'searchable' => false,
                ]
            ];
        }

        $columns = array_filter($columns);

        $hasCustomField = in_array(Testimonial::class, setting('custom_testimonial_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_testimonial_model', Testimonial::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $testimonial) {
                array_splice($columns, $testimonial->order - 1, 0, [[
                    'data' => 'custom_testimonials.' . $testimonial->name . '.view',
                    'title' => trans('lang.testimonial_' . $testimonial->name),
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
    public function query(Testimonial $model)
    {
        return $model->newQuery()->with('country');
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
        return 'testimonialsdatatable_' . time();
    }
}