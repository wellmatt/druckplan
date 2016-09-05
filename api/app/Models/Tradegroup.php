<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Tradegroup",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tradegroup_state",
 *          description="tradegroup_state",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tradegroup_title",
 *          description="tradegroup_title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tradegroup_desc",
 *          description="tradegroup_desc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tradegroup_shoprel",
 *          description="tradegroup_shoprel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tradegroup_parentid",
 *          description="tradegroup_parentid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Tradegroup extends Model
{

    public $table = 'tradegroup';
    
    public $timestamps = false;



    public $fillable = [
        'tradegroup_state',
        'tradegroup_title',
        'tradegroup_desc',
        'tradegroup_shoprel',
        'tradegroup_parentid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tradegroup_state' => 'integer',
        'tradegroup_title' => 'string',
        'tradegroup_desc' => 'string',
        'tradegroup_shoprel' => 'integer',
        'tradegroup_parentid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
