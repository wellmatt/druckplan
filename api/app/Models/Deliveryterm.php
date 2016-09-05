<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Deliveryterm",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="client",
 *          description="client",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name1",
 *          description="name1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="charges",
 *          description="charges",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="shoprel",
 *          description="shoprel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tax",
 *          description="tax",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class Deliveryterm extends Model
{

    public $table = 'deliveryterms';
    
    public $timestamps = false;



    public $fillable = [
        'active',
        'client',
        'name1',
        'comment',
        'charges',
        'shoprel',
        'tax'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'client' => 'integer',
        'name1' => 'string',
        'comment' => 'string',
        'charges' => 'float',
        'shoprel' => 'integer',
        'tax' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
