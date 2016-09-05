<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ArticleShopApproval",
 *      required={""},
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
 *          property="bc",
 *          description="bc",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cp",
 *          description="cp",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ArticleShopApproval extends Model
{

    public $table = 'article_shop_approval';
    
    public $timestamps = false;



    public $fillable = [
        'article',
        'bc',
        'cp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'article' => 'integer',
        'bc' => 'integer',
        'cp' => 'integer'
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

    public function businesscontact()
    {
        return $this->belongsTo('App\Models\Businesscontact', 'id', 'bc');
    }

    public function contactperson()
    {
        return $this->belongsTo('App\Models\Contactperson', 'id', 'cp');
    }

    
}
