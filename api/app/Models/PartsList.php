<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PartsList",
 *      required={"title","price","crtdate","crtuser"},
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
 *          property="price",
 *          description="price",
 *          type="number",
 *          format="float"
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
 *      )
 * )
 */
class PartsList extends Model
{

    public $table = 'partslists';
    
    public $timestamps = false;



    public $fillable = [
        'title',
        'price',
        'crtdate',
        'crtuser'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'price' => 'float',
        'crtdate' => 'integer',
        'crtuser' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'partslistitems',
    );

    /**
     * @return mixed
     */
    public function partslistitems()
    {
        return $this->hasMany('App\Models\PartsListItem', 'partslist', 'id');
    }

    
}
