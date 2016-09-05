<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ArticlePricescale",
 *      required={"article","type","min","max","price","supplier","artnum"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="article",
 *          description="article",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="min",
 *          description="min",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="max",
 *          description="max",
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
 *          property="supplier",
 *          description="supplier",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="artnum",
 *          description="artnum",
 *          type="string"
 *      )
 * )
 */
class ArticlePricescale extends Model
{

    public $table = 'article_pricescale';
    
    public $timestamps = false;



    public $fillable = [
        'article',
        'type',
        'min',
        'max',
        'price',
        'supplier',
        'artnum'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'article' => 'integer',
        'min' => 'integer',
        'max' => 'integer',
        'price' => 'float',
        'supplier' => 'integer',
        'artnum' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function article()
    {
        return $this->belongsTo('App\Models\Article', 'id', 'article');
    }
    
}
