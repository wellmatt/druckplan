<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StorageArea",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="corridor",
 *          description="corridor",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shelf",
 *          description="shelf",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="line",
 *          description="line",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="layer",
 *          description="layer",
 *          type="string"
 *      )
 * )
 */
class StorageArea extends Model
{

    public $table = 'storage_areas';
    
    public $timestamps = false;



    public $fillable = [
        'name',
        'description',
        'location',
        'corridor',
        'shelf',
        'line',
        'layer',
        'prio'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'location' => 'string',
        'corridor' => 'string',
        'shelf' => 'string',
        'line' => 'string',
        'layer' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'storagebookentries',
        'storagepositions',
    );

    /**
     * @return mixed
     */
    public function storagebookentries()
    {
        return $this->hasMany('App\Models\StorageBookEntry', 'area', 'id');
    }

    /**
     * @return mixed
     */
    public function storagepositions()
    {
        return $this->hasMany('App\Models\StoragePosition', 'area', 'id');
    }

    
}
