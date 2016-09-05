<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PersonalizationSeperation",
 *      required={"sep_personalizationid","sep_min","sep_max","sep_price","sep_show"},
 *      @SWG\Property(
 *          property="sep_personalizationid",
 *          description="sep_personalizationid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sep_min",
 *          description="sep_min",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sep_max",
 *          description="sep_max",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sep_price",
 *          description="sep_price",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class PersonalizationSeperation extends Model
{

    public $table = 'personalization_seperation';
    
    public $timestamps = false;



    public $fillable = [
        'sep_personalizationid',
        'sep_min',
        'sep_max',
        'sep_price',
        'sep_show'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'sep_personalizationid' => 'integer',
        'sep_min' => 'integer',
        'sep_max' => 'integer',
        'sep_price' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
