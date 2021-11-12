<?php

/**
 * File name: Country.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Models;

use Eloquent as Model;

/**
 * Class Country
 * @package App\Models
 * @version October 22, 2019, 2:46 pm UTC
 *
 * @property string name
 * @property string symbol
 * @property string code
 * @property unsignedTinyInteger decimal_digits
 * @property unsignedTinyInteger rounding
 */
class State extends Model
{

  public $table = 'states';


  public $fillable = [
    'name',
    'country_id',
    'country_code',
    'iso2',
    'covered'
  ];

  /**
   * The attributes that should be casted to native types.
   *
   * @var array
   */
  protected $casts = [
    'name' => 'string',
    'country_id' => 'integer',
    'country_code' => 'string',
    'iso2' => 'string',
    'covered' => 'string'

  ];

  /**
   * Validation rules
   *
   * @var array
   */
  public static $rules = [
    'name' => 'required',
    'country_id' => 'required|exists:countries,id'
  ];




  
  public function country()
  {
    return $this->hasOne('App\Models\Country', 'id', 'country_id');
  }
  public function areas()
  {
      return $this->hasMany(\App\Models\Area::class, 'state_id', 'id');
  }
}
