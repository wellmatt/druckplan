<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StorageGood",
 *      required={"origin","type","crtdate","crtuser"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="origin",
 *          description="origin",
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
 *      )
 * )
 */
class StorageGood extends Model
{

    public $table = 'storage_goods';
    
    public $timestamps = false;



    public $fillable = [
        'origin',
        'type',
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
        'origin' => 'integer',
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
        'storagegoodpositions',
    );

    /**
     * @return mixed
     */
    public function storagegoodpositions()
    {
        return $this->hasMany('App\Models\StorageGoodPosition', 'goods', 'id');
    }

    
}
