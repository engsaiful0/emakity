<?php

namespace App\Repositories;

use App\Models\Testimonial;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TestimonialRepository
 * @package App\Repositories
 * @version April 11, 2020, 1:57 pm UTC
 *
 * @method Testimonial findWithoutFail($id, $columns = ['*'])
 * @method Testimonial find($id, $columns = ['*'])
 * @method Testimonial first($columns = ['*'])
*/
class TestimonialRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Testimonial::class;
    }
}
