<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PaperPrice",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="weight_from",
 *          description="weight_from",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="weight_to",
 *          description="weight_to",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="size_width",
 *          description="size_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="size_height",
 *          description="size_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quantity_from",
 *          description="quantity_from",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="weight",
 *          description="weight",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class PaperPrice extends Model
{

    public $table = 'papers_prices';
    
    public $timestamps = false;



    public $fillable = [
        'paper_id',
        'weight_from',
        'weight_to',
        'size_width',
        'size_height',
        'quantity_from',
        'price',
        'weight'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'paper_id' => 'integer',
        'weight_from' => 'integer',
        'weight_to' => 'integer',
        'size_width' => 'integer',
        'size_height' => 'integer',
        'quantity_from' => 'integer',
        'price' => 'float',
        'weight' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
