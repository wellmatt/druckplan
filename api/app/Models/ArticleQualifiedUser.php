<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ArticleQualifiedUser",
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
 *          property="user",
 *          description="user",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ArticleQualifiedUser extends Model
{

    public $table = 'article_qualified_users';
    
    public $timestamps = false;



    public $fillable = [
        'article',
        'user'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'article' => 'integer',
        'user' => 'integer'
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

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'article');
    }

    
}
