<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupOrderPosition",
 *      required={"suporder","article","amount","colinvoice"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="suporder",
 *          description="suporder",
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
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="colinvoice",
 *          description="colinvoice",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SupOrderPosition extends Model
{

    public $table = 'suporders_positions';
    
    public $timestamps = false;



    public $fillable = [
        'suporder',
        'article',
        'amount',
        'colinvoice'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'suporder' => 'integer',
        'article' => 'integer',
        'amount' => 'float',
        'colinvoice' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
