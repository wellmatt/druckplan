<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Attribute",
 *      required={""},
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
 *          property="module",
 *          description="module",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="objectid",
 *          description="objectid",
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
 *          property="crtdate",
 *          description="crtdate",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Attribute extends Model
{

    public $table = 'attributes';
    
    public $timestamps = false;



    public $fillable = [
        'state',
        'title',
        'comment',
        'module',
        'objectid',
        'crtuser',
        'crtdate',
        'enable_customer',
        'enable_contacts',
        'enable_colinv'
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
        'module' => 'integer',
        'objectid' => 'integer',
        'crtuser' => 'integer',
        'crtdate' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'attributeitems',
    );

    /**
     * @return mixed
     */
    public function attributeitems()
    {
        return $this->hasMany('App\Models\AttributeItem', 'attribute_id', 'id');
    }
    
}
