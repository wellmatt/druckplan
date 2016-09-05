<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Ticket",
 *      required={"title","crtdate","crtuser","duedate","closedate","closeuser","editdate","number","customer","customer_cp","assigned_user","assigned_group","state","category","priority","source","tourmarker","planned_time"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="crtdate",
 *          description="crtdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtuser",
 *          description="crtuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="duedate",
 *          description="duedate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedate",
 *          description="closedate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closeuser",
 *          description="closeuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="editdate",
 *          description="editdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number",
 *          description="number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customer",
 *          description="customer",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customer_cp",
 *          description="customer_cp",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assigned_user",
 *          description="assigned_user",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assigned_group",
 *          description="assigned_group",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category",
 *          description="category",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="priority",
 *          description="priority",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="source",
 *          description="source",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tourmarker",
 *          description="tourmarker",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="planned_time",
 *          description="planned_time",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class Ticket extends Model
{

    public $table = 'tickets';
    
    public $timestamps = false;



    public $fillable = [
        'title',
        'crtdate',
        'crtuser',
        'duedate',
        'closedate',
        'closeuser',
        'editdate',
        'number',
        'customer',
        'customer_cp',
        'assigned_user',
        'assigned_group',
        'state',
        'category',
        'priority',
        'source',
        'tourmarker',
        'planned_time'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'crtdate' => 'integer',
        'crtuser' => 'integer',
        'duedate' => 'integer',
        'closedate' => 'integer',
        'closeuser' => 'integer',
        'editdate' => 'integer',
        'number' => 'string',
        'customer' => 'integer',
        'customer_cp' => 'integer',
        'assigned_user' => 'integer',
        'assigned_group' => 'integer',
        'category' => 'integer',
        'priority' => 'integer',
        'source' => 'string',
        'tourmarker' => 'string',
        'planned_time' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'ticketlogs',
    );

    /**
     * @return mixed
     */
    public function ticketlogs()
    {
        return $this->hasMany('App\Models\TicketLog', 'ticket', 'id');
    }

    
}
