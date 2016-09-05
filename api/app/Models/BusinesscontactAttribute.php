<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BusinesscontactAttribute",
 *      required={""},
 *      @SWG\Property(
 *          property="businesscontact_id",
 *          description="businesscontact_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="attribute_id",
 *          description="attribute_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="item_id",
 *          description="item_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inputvalue",
 *          description="inputvalue",
 *          type="string"
 *      )
 * )
 */
class BusinesscontactAttribute extends Model
{

    public $table = 'businesscontact_attributes';
    
    public $timestamps = false;



    public $fillable = [
        'businesscontact_id',
        'attribute_id',
        'item_id',
        'value',
        'inputvalue'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'businesscontact_id' => 'integer',
        'attribute_id' => 'integer',
        'item_id' => 'integer',
        'inputvalue' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    
}
