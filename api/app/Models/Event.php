<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Event",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="begin",
 *          description="begin",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="end",
 *          description="end",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="participants_ext",
 *          description="participants_ext",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="participants_int",
 *          description="participants_int",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="adress",
 *          description="adress",
 *          type="string"
 *      )
 * )
 */
class Event extends Model
{

    public $table = 'events';
    
    public $timestamps = false;



    public $fillable = [
        'user_id',
        'public',
        'title',
        'description',
        'begin',
        'end',
        'participants_ext',
        'participants_int',
        'adress'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'begin' => 'integer',
        'end' => 'integer',
        'participants_ext' => 'string',
        'participants_int' => 'string',
        'adress' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    protected $with = array(
        'eventparticipants',
    );

    /**
     * @return mixed
     */
    public function eventparticipants()
    {
        return $this->hasMany('App\Models\EventParticipant', 'event', 'id');
    }

    
}
