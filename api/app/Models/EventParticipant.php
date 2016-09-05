<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="EventParticipant",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="event",
 *          description="event",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="participant",
 *          description="participant",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class EventParticipant extends Model
{

    public $table = 'events_participants';
    
    public $timestamps = false;



    public $fillable = [
        'event',
        'participant',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'event' => 'integer',
        'participant' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
