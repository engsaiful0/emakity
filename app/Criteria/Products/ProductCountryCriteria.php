<?php

namespace App\Criteria\Products;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;


/**
 * Class CountryCriteria.
 *
 * @package namespace App\Criteria\Markets;
 */
class ProductCountryCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */

         /**
     * @var array
     */
    private $request;

    /**
     * MarketsOfFieldsCriteria constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($model, RepositoryInterface $repository)
    {
        if(!$this->request->has('country_id') || $this->request->get('country_id')=='') {
            return $model;
        } else {
            $country_id=$this->request->get('country_id');
            return $model->whereHas('market',function($query) use ($country_id){
                $query->where('markets.country_id',$country_id);
            });
        }
    }
}