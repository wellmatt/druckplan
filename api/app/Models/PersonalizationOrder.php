<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PersonalizationOrder",
 *      required={""},
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
 *          property="persoid",
 *          description="persoid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentid",
 *          description="documentid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerid",
 *          description="customerid",
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
 *          property="crtdate",
 *          description="crtdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="orderdate",
 *          description="orderdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contact_person_id",
 *          description="contact_person_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryaddress_id",
 *          description="deliveryaddress_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PersonalizationOrder extends Model
{

    public $table = 'personalization_orders';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'title',
        'persoid',
        'documentid',
        'customerid',
        'crtuser',
        'crtdate',
        'orderdate',
        'comment',
        'amount',
        'contact_person_id',
        'deliveryaddress_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'persoid' => 'integer',
        'documentid' => 'integer',
        'customerid' => 'integer',
        'crtuser' => 'integer',
        'crtdate' => 'integer',
        'orderdate' => 'integer',
        'comment' => 'string',
        'amount' => 'integer',
        'contact_person_id' => 'integer',
        'deliveryaddress_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'personalizationorderitems',
    );

    /**
     * @return mixed
     */
    public function personalizationorderitems()
    {
        return $this->hasMany('App\Models\PersonalizationOrderItem', 'persoorderid', 'id');
    }

    
}
