<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Paymentterm",
 *      required={"active","client","name1","comment","skonto_days1","skonto1","skonto_days2","skonto2","netto_days","shop_rel"},
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
 *          property="shop_rel",
 *          description="shop_rel",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Paymentterm extends Model
{

    public $table = 'paymentterms';
    
    public $timestamps = false;



    public $fillable = [
        'active',
        'client',
        'name1',
        'comment',
        'skonto_days1',
        'skonto1',
        'skonto_days2',
        'skonto2',
        'netto_days',
        'shop_rel'
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
        'shop_rel' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
