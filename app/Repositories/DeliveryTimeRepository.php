<?php

namespace App\Repositories;

use App\Models\DeliveryTime;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DeliveryTimeRepository
 * @package App\Repositories
 * @version August 29, 2019, 9:38 pm UTC
 *
 * @method DeliveryTime findWithoutFail($id, $columns = ['*'])
 * @method DeliveryTime find($id, $columns = ['*'])
 * @method DeliveryTime first($columns = ['*'])
*/
class DeliveryTimeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeliveryTime::class;
    }
}
