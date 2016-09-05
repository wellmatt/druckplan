<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ArticleTag",
 *      required={"article","tag"},
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
 *          property="tag",
 *          description="tag",
 *          type="string"
 *      )
 * )
 */
class ArticleTag extends Model
{

    public $table = 'article_tags';
    
    public $timestamps = false;



    public $fillable = [
        'article',
        'tag'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'article' => 'integer',
        'tag' => 'string'
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
