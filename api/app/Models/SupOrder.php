<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupOrder",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number",
 *          description="number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplier",
 *          description="supplier",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="eta",
 *          description="eta",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentterm",
 *          description="paymentterm",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceaddress",
 *          description="invoiceaddress",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryaddress",
 *          description="deliveryaddress",
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
 *          property="crtuser",
 *          description="crtuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cpinternal",
 *          description="cpinternal",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cpexternal",
 *          description="cpexternal",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SupOrder extends Model
{

    public $table = 'suporders';
    
    public $timestamps = false;



    public $fillable = [
        'number',
        'supplier',
        'title',
        'eta',
        'paymentterm',
        'status',
        'invoiceaddress',
        'deliveryaddress',
        'crtdate',
        'crtuser',
        'cpinternal',
        'cpexternal'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'number' => 'string',
        'supplier' => 'integer',
        'title' => 'string',
        'eta' => 'integer',
        'paymentterm' => 'integer',
        'invoiceaddress' => 'integer',
        'deliveryaddress' => 'integer',
        'crtdate' => 'integer',
        'crtuser' => 'integer',
        'cpinternal' => 'integer',
        'cpexternal' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'suporderpositions',
    );

    /**
     * @return mixed
     */
    public function suporderpositions()
    {
        return $this->hasMany('App\Models\SupOrderPosition', 'suporder', 'id');
    }

    
}
