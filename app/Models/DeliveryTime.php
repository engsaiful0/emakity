<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class FaqCategory
 * @package App\Models
 * @version August 29, 2019, 9:38 pm UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection Faq
 * @property string name
 */
class DeliveryTime extends Model
{

    public $table = 'delivery_times';
    


    public $fillable = [
        'name',
        'country_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id', 'id');
    }
}
