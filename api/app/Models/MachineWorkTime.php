<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MachineWorkTime",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="machine",
 *          description="machine",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="weekday",
 *          description="weekday",
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
 *          property="end",
 *          description="end",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class MachineWorkTime extends Model
{

    public $table = 'machines_worktimes';
    
    public $timestamps = false;



    public $fillable = [
        'machine',
        'weekday',
        'start',
        'end'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'machine' => 'integer',
        'weekday' => 'integer',
        'start' => 'integer',
        'end' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
