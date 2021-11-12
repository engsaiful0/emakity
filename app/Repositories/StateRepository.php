<?php

namespace App\Repositories;

use App\Models\State;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StateRepository
 * @package App\Repositories
 * @version August 29, 2019, 9:39 pm UTC
 *
 * @method State findWithoutFail($id, $columns = ['*'])
 * @method State find($id, $columns = ['*'])
 * @method State first($columns = ['*'])
*/
class StateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'country_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return State::class;
    }
}
