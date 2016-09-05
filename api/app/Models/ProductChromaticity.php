<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ProductChromaticity",
 *      required={"product_id","chromaticity_id"},
 *      @SWG\Property(
 *          property="product_id",
 *          description="product_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chromaticity_id",
 *          description="chromaticity_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ProductChromaticity extends Model
{

    public $table = 'products_chromaticity';
    
    public $timestamps = false;



    public $fillable = [
        'product_id',
        'chromaticity_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'product_id' => 'integer',
        'chromaticity_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
