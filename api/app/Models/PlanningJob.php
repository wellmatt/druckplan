<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PlanningJob",
 *      required={"object","type","opos","subobject","assigned_user","assigned_group","ticket","start","tplanned","tactual","state","artmach"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="object",
 *          description="object",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="opos",
 *          description="opos",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subobject",
 *          description="subobject",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assigned_user",
 *          description="assigned_user",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assigned_group",
 *          description="assigned_group",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticket",
 *          description="ticket",
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
 *          property="tplanned",
 *          description="tplanned",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="tactual",
 *          description="tactual",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="artmach",
 *          description="artmach",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PlanningJob extends Model
{

    public $table = 'planning_jobs';
    
    public $timestamps = false;



    public $fillable = [
        'object',
        'type',
        'opos',
        'subobject',
        'assigned_user',
        'assigned_group',
        'ticket',
        'start',
        'tplanned',
        'tactual',
        'state',
        'artmach'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'object' => 'integer',
        'opos' => 'integer',
        'subobject' => 'integer',
        'assigned_user' => 'integer',
        'assigned_group' => 'integer',
        'ticket' => 'integer',
        'start' => 'integer',
        'tplanned' => 'float',
        'tactual' => 'float',
        'artmach' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
