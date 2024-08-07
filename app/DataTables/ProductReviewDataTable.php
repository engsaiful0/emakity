<?php
/**
 * File name: ProductReviewDataTable.php
 * Last modified: 2020.05.04 at 09:04:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Criteria\ProductReviews\OrderProductReviewsOfUserCriteria;
use App\Criteria\ProductReviews\ProductReviewsOfUserCriteria;
use App\Models\CustomField;
use App\Models\ProductReview;
use App\Repositories\ProductReviewRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class ProductReviewDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];
    private $productReviewRepo;
    private $myReviews;


    public function __construct(ProductReviewRepository $productReviewRepo)
    {
        $this->productReviewRepo = $productReviewRepo;
        $this->myReviews = $this->productReviewRepo->getByCriteria(new ProductReviewsOfUserCriteria(auth()->id()))->pluck('id')->toArray();
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function dataTable($query)
    {
        if (auth()->user()->hasRole('client'))
        $query = $query->where('user_id', auth()->id());
        if (auth()->user()->hasRole('branch'))
        $query = $query->whereHas('product.market.country', function($q){
            return $q->where('countries.id',get_role_country_id('branch'));
        });
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
        ->editColumn('product.market.country.name', function ($cart) {
            return $cart['product']['market']['country']['name'];
        })
            ->editColumn('updated_at', function ($product_review) {
                return getDateColumn($product_review, 'updated_at');
            })
            ->addColumn('action', function ($product_review) {
                return view('product_reviews.datatables_actions', ['id' => $product_review->id, 'myReviews' => $this->myReviews])->render();
            })
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function query(ProductReview $model)
    {
        $this->productReviewRepo->resetCriteria();
        $this->productReviewRepo->pushCriteria(new OrderProductReviewsOfUserCriteria(auth()->id()));
        return $this->productReviewRepo->with("user")->with("product.market.country")->newQuery();
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
                    'data' => 'review',
                    'title' => trans('lang.product_review_review'),
    
                ],
                [
                    'data' => 'product.market.country.name',
                    'title' => trans('lang.country'),
    
                ],
                [
                    'data' => 'rate',
                    'title' => trans('lang.product_review_rate'),
    
                ],
                [
                    'data' => 'user.name',
                    'title' => trans('lang.product_review_user_id'),
    
                ],
                [
                    'data' => 'product.name',
                    'title' => trans('lang.product_review_product_id'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.product_review_updated_at'),
                    'searchable' => false,
                ]
            ];
        }
        else
        {
            $columns = [
                [
                    'data' => 'review',
                    'title' => trans('lang.product_review_review'),
    
                ],
                [
                    'data' => 'rate',
                    'title' => trans('lang.product_review_rate'),
    
                ],
                [
                    'data' => 'user.name',
                    'title' => trans('lang.product_review_user_id'),
    
                ],
                [
                    'data' => 'product.name',
                    'title' => trans('lang.product_review_product_id'),
    
                ],
                [
                    'data' => 'updated_at',
                    'title' => trans('lang.product_review_updated_at'),
                    'searchable' => false,
                ]
            ];   
        }

        $hasCustomField = in_array(ProductReview::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', ProductReview::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.product_review_' . $field->name),
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
        return 'product_reviewsdatatable_' . time();
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