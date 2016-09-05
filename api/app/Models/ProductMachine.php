<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ProductMachine",
 *      required={"product_id","machine_id","default","minimum","maximum"},
 *      @SWG\Property(
 *          property="product_id",
 *          description="product_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="machine_id",
 *          description="machine_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="minimum",
 *          description="minimum",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="maximum",
 *          description="maximum",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ProductMachine extends Model
{

    public $table = 'products_machines';
    
    public $timestamps = false;



    public $fillable = [
        'product_id',
        'machine_id',
        'default',
        'minimum',
        'maximum'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'product_id' => 'integer',
        'machine_id' => 'integer',
        'minimum' => 'integer',
        'maximum' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
