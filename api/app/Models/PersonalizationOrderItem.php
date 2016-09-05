<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PersonalizationOrderItem",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="persoid",
 *          description="persoid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="persoorderid",
 *          description="persoorderid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="persoitemid",
 *          description="persoitemid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
 *          type="string"
 *      )
 * )
 */
class PersonalizationOrderItem extends Model
{

    public $table = 'personalization_orderitems';
    
    public $timestamps = false;



    public $fillable = [
        'persoid',
        'persoorderid',
        'persoitemid',
        'value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'persoid' => 'integer',
        'persoorderid' => 'integer',
        'persoitemid' => 'integer',
        'value' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
