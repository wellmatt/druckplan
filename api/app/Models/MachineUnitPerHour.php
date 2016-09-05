<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MachineUnitPerHour",
 *      required={"machine_id","units_from","units_amount"},
 *      @SWG\Property(
 *          property="machine_id",
 *          description="machine_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="units_from",
 *          description="units_from",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="units_amount",
 *          description="units_amount",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class MachineUnitPerHour extends Model
{

    public $table = 'machines_unitsperhour';
    
    public $timestamps = false;



    public $fillable = [
        'machine_id',
        'units_from',
        'units_amount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'machine_id' => 'integer',
        'units_from' => 'integer',
        'units_amount' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
