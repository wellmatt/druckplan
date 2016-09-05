<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TicketLog",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="crtusr",
 *          description="crtusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="date",
 *          description="date",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="entry",
 *          description="entry",
 *          type="string"
 *      )
 * )
 */
class TicketLog extends Model
{

    public $table = 'tickets_logs';
    
    public $timestamps = false;



    public $fillable = [
        'ticket',
        'crtusr',
        'date',
        'entry'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'ticket' => 'integer',
        'crtusr' => 'integer',
        'date' => 'integer',
        'entry' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
