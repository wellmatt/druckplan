<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Personalization",
 *      required={"title","comment","status","picture","article","customer","crtdate","crtuser","uptdate","uptuser","direction","format","format_width","format_height","type","picture2","linebyline","hidden","anschnitt","preview"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="picture",
 *          description="picture",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="article",
 *          description="article",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customer",
 *          description="customer",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtdate",
 *          description="crtdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtuser",
 *          description="crtuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uptdate",
 *          description="uptdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uptuser",
 *          description="uptuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="direction",
 *          description="direction",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="format",
 *          description="format",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_width",
 *          description="format_width",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="format_height",
 *          description="format_height",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="picture2",
 *          description="picture2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="hidden",
 *          description="hidden",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="anschnitt",
 *          description="anschnitt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="preview",
 *          description="preview",
 *          type="string"
 *      )
 * )
 */
class Personalization extends Model
{

    public $table = 'personalization';
    
    public $timestamps = false;



    public $fillable = [
        'title',
        'comment',
        'status',
        'picture',
        'article',
        'customer',
        'crtdate',
        'crtuser',
        'uptdate',
        'uptuser',
        'direction',
        'format',
        'format_width',
        'format_height',
        'type',
        'picture2',
        'linebyline',
        'hidden',
        'anschnitt',
        'preview'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'comment' => 'string',
        'picture' => 'string',
        'article' => 'integer',
        'customer' => 'integer',
        'crtdate' => 'integer',
        'crtuser' => 'integer',
        'uptdate' => 'integer',
        'uptuser' => 'integer',
        'direction' => 'integer',
        'format' => 'string',
        'format_width' => 'float',
        'format_height' => 'float',
        'picture2' => 'string',
        'hidden' => 'integer',
        'anschnitt' => 'float',
        'preview' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'personalizationitems',
        'personalizationseperations',
    );

    /**
     * @return mixed
     */
    public function personalizationitems()
    {
        return $this->hasMany('App\Models\PersonalizationItem', 'personalization_id', 'id');
    }

    /**
     * @return mixed
     */
    public function personalizationseperations()
    {
        return $this->hasMany('App\Models\PersonalizationSeperation', 'sep_personalizationid', 'id');
    }

    
}
