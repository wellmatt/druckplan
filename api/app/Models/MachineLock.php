<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MachineLock",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="machineid",
 *          description="machineid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="start",
 *          description="start",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stop",
 *          description="stop",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class MachineLock extends Model
{

    public $table = 'machines_locks';
    
    public $timestamps = false;



    public $fillable = [
        'machineid',
        'start',
        'stop'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'machineid' => 'integer',
        'start' => 'integer',
        'stop' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
