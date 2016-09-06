<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MachineQualifiedUser",
 *      required={"machine","user"},
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
 *          property="user",
 *          description="user",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class MachineQualifiedUser extends Model
{

    public $table = 'machines_qualified_users';
    
    public $timestamps = false;



    public $fillable = [
        'machine',
        'user'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'machine' => 'integer',
        'user' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
