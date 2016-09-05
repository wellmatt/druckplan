<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CollectiveinvoiceOrderposition",
 *      required={"status","quantity","price","tax","comment","collectiveinvoice","type","inv_rel","object_id","rev_rel","file_attach","perso_order"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quantity",
 *          description="quantity",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="tax",
 *          description="tax",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="collectiveinvoice",
 *          description="collectiveinvoice",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inv_rel",
 *          description="inv_rel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="object_id",
 *          description="object_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_rel",
 *          description="rev_rel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="file_attach",
 *          description="file_attach",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="perso_order",
 *          description="perso_order",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CollectiveinvoiceOrderposition extends Model
{

    public $table = 'collectiveinvoice_orderposition';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'quantity',
        'price',
        'tax',
        'comment',
        'collectiveinvoice',
        'type',
        'inv_rel',
        'object_id',
        'rev_rel',
        'file_attach',
        'perso_order'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'quantity' => 'float',
        'price' => 'float',
        'tax' => 'integer',
        'comment' => 'string',
        'collectiveinvoice' => 'integer',
        'type' => 'integer',
        'inv_rel' => 'integer',
        'object_id' => 'integer',
        'rev_rel' => 'integer',
        'file_attach' => 'integer',
        'perso_order' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
