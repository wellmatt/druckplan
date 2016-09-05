<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Country",
 *      required={"country_name","country_name_int","country_code","country_active"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="country_name",
 *          description="country_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="country_name_int",
 *          description="country_name_int",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="country_code",
 *          description="country_code",
 *          type="string"
 *      )
 * )
 */
class Country extends Model
{

    public $table = 'countries';
    
    public $timestamps = false;



    public $fillable = [
        'country_name',
        'country_name_int',
        'country_code',
        'country_active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'country_name' => 'string',
        'country_name_int' => 'string',
        'country_code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
