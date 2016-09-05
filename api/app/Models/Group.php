<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Group",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="group_name",
 *          description="group_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="group_description",
 *          description="group_description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="group_rights",
 *          description="group_rights",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Group extends Model
{

    public $table = 'groups';
    
    public $timestamps = false;



    public $fillable = [
        'group_name',
        'group_description',
        'group_status',
        'group_rights'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'group_name' => 'string',
        'group_description' => 'string',
        'group_rights' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
