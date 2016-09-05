<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MachineDifficulty",
 *      required={"machine_id","diff_id","diff_unit","value","percent"},
 *      @SWG\Property(
 *          property="machine_id",
 *          description="machine_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="diff_id",
 *          description="diff_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="diff_unit",
 *          description="diff_unit",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="percent",
 *          description="percent",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class MachineDifficulty extends Model
{

    public $table = 'machines_difficulties';
    
    public $timestamps = false;



    public $fillable = [
        'machine_id',
        'diff_id',
        'diff_unit',
        'value',
        'percent'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'machine_id' => 'integer',
        'diff_id' => 'integer',
        'diff_unit' => 'integer',
        'value' => 'float',
        'percent' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
