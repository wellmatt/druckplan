<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StoragePosition",
 *      required={"area","article","businesscontact","amount","min_amount","respuser","description","note","dispatch","packaging","allocation"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="area",
 *          description="area",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="article",
 *          description="article",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="businesscontact",
 *          description="businesscontact",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="min_amount",
 *          description="min_amount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="respuser",
 *          description="respuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="note",
 *          description="note",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="dispatch",
 *          description="dispatch",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="packaging",
 *          description="packaging",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="allocation",
 *          description="allocation",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class StoragePosition extends Model
{

    public $table = 'storage_positions';
    
    public $timestamps = false;



    public $fillable = [
        'area',
        'article',
        'businesscontact',
        'amount',
        'min_amount',
        'respuser',
        'description',
        'note',
        'dispatch',
        'packaging',
        'allocation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'area' => 'integer',
        'article' => 'integer',
        'businesscontact' => 'integer',
        'amount' => 'integer',
        'min_amount' => 'integer',
        'respuser' => 'integer',
        'description' => 'string',
        'note' => 'string',
        'dispatch' => 'string',
        'packaging' => 'string',
        'allocation' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
