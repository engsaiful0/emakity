<?php

namespace App\Criteria\Markets;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;


/**
 * Class CurrencyCriteria.
 *
 * @package namespace App\Criteria\Markets;
 */
class MarketCurrencyCriteria implements CriteriaInterface
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
        if(!$this->request->has('currency_id') || $this->request->get('currency_id')=='') {
            return $model;
        } else {
            return $model->where('markets.currency_id',$this->request->get('currency_id'));
        }
    }
}
