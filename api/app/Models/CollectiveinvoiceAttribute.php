<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CollectiveinvoiceAttribute",
 *      required={""},
 *      @SWG\Property(
 *          property="collectiveinvoice_id",
 *          description="collectiveinvoice_id",
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
class CollectiveinvoiceAttribute extends Model
{

    public $table = 'collectiveinvoice_attributes';
    
    public $timestamps = false;



    public $fillable = [
        'collectiveinvoice_id',
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
        'collectiveinvoice_id' => 'integer',
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
