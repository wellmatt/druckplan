<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ArticlePicture",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="url",
 *          description="url",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="crtdate",
 *          description="crtdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="articleid",
 *          description="articleid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ArticlePicture extends Model
{

    public $table = 'article_pictures';
    
    public $timestamps = false;



    public $fillable = [
        'url',
        'crtdate',
        'articleid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'url' => 'string',
        'crtdate' => 'integer',
        'articleid' => 'integer'
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
        return $this->belongsTo('App\Models\Article', 'id', 'articleid');
    }

    
}
