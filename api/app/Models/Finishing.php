<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Finishing",
 *      required={"lector_id","status","name","beschreibung","kosten"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lector_id",
 *          description="lector_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="beschreibung",
 *          description="beschreibung",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="kosten",
 *          description="kosten",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class Finishing extends Model
{

    public $table = 'finishing';
    
    public $timestamps = false;



    public $fillable = [
        'lector_id',
        'status',
        'name',
        'beschreibung',
        'kosten'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'lector_id' => 'integer',
        'name' => 'string',
        'beschreibung' => 'string',
        'kosten' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
