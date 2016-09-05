<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ProductPaper",
 *      required={"product_id","paper_id","weight","part"},
 *      @SWG\Property(
 *          property="product_id",
 *          description="product_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_id",
 *          description="paper_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="weight",
 *          description="weight",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ProductPaper extends Model
{

    public $table = 'products_papers';
    
    public $timestamps = false;



    public $fillable = [
        'product_id',
        'paper_id',
        'weight',
        'part'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'product_id' => 'integer',
        'paper_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
