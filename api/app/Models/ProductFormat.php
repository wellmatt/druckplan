<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ProductFormat",
 *      required={""},
 *      @SWG\Property(
 *          property="product_id",
 *          description="product_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="format_id",
 *          description="format_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ProductFormat extends Model
{

    public $table = 'products_formats';
    
    public $timestamps = false;



    public $fillable = [
        'product_id',
        'format_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'product_id' => 'integer',
        'format_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
