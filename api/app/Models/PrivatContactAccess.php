<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PrivatContactAccess",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="prvtc_id",
 *          description="prvtc_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="userid",
 *          description="userid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PrivatContactAccess extends Model
{

    public $table = 'privatecontacts_access';
    
    public $timestamps = false;



    public $fillable = [
        'prvtc_id',
        'userid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'prvtc_id' => 'integer',
        'userid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
