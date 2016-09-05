<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Collectiveinvoice",
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
 *          property="number",
 *          description="number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="deliverycosts",
 *          description="deliverycosts",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client",
 *          description="client",
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
 *          property="deliveryterm",
 *          description="deliveryterm",
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
 *          property="deliveryaddress",
 *          description="deliveryaddress",
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
 *          property="uptdate",
 *          description="uptdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uptuser",
 *          description="uptuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="intent",
 *          description="intent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="intern_contactperson",
 *          description="intern_contactperson",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cust_message",
 *          description="cust_message",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="cust_sign",
 *          description="cust_sign",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="custContactperson",
 *          description="custContactperson",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliverydate",
 *          description="deliverydate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ext_comment",
 *          description="ext_comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="thirdpartycomment",
 *          description="thirdpartycomment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticket",
 *          description="ticket",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="offer_header",
 *          description="offer_header",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="offer_footer",
 *          description="offer_footer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="offerconfirm_header",
 *          description="offerconfirm_header",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="offerconfirm_footer",
 *          description="offerconfirm_footer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="factory_header",
 *          description="factory_header",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="factory_footer",
 *          description="factory_footer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="delivery_header",
 *          description="delivery_header",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="delivery_footer",
 *          description="delivery_footer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoice_header",
 *          description="invoice_header",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoice_footer",
 *          description="invoice_footer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="revert_header",
 *          description="revert_header",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="revert_footer",
 *          description="revert_footer",
 *          type="string"
 *      )
 * )
 */
class Collectiveinvoice extends Model
{

    public $table = 'collectiveinvoice';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'title',
        'number',
        'deliverycosts',
        'comment',
        'client',
        'businesscontact',
        'deliveryterm',
        'paymentterm',
        'deliveryaddress',
        'invoiceaddress',
        'crtdate',
        'crtuser',
        'uptdate',
        'uptuser',
        'intent',
        'intern_contactperson',
        'cust_message',
        'cust_sign',
        'custContactperson',
        'needs_planning',
        'deliverydate',
        'rdyfordispatch',
        'ext_comment',
        'thirdparty',
        'thirdpartycomment',
        'ticket',
        'offer_header',
        'offer_footer',
        'offerconfirm_header',
        'offerconfirm_footer',
        'factory_header',
        'factory_footer',
        'delivery_header',
        'delivery_footer',
        'invoice_header',
        'invoice_footer',
        'revert_header',
        'revert_footer'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'number' => 'string',
        'deliverycosts' => 'float',
        'comment' => 'string',
        'client' => 'integer',
        'businesscontact' => 'integer',
        'deliveryterm' => 'integer',
        'paymentterm' => 'integer',
        'deliveryaddress' => 'integer',
        'invoiceaddress' => 'integer',
        'crtdate' => 'integer',
        'crtuser' => 'integer',
        'uptdate' => 'integer',
        'uptuser' => 'integer',
        'intent' => 'string',
        'intern_contactperson' => 'integer',
        'cust_message' => 'string',
        'cust_sign' => 'string',
        'custContactperson' => 'integer',
        'deliverydate' => 'integer',
        'ext_comment' => 'string',
        'thirdpartycomment' => 'string',
        'ticket' => 'integer',
        'offer_header' => 'string',
        'offer_footer' => 'string',
        'offerconfirm_header' => 'string',
        'offerconfirm_footer' => 'string',
        'factory_header' => 'string',
        'factory_footer' => 'string',
        'delivery_header' => 'string',
        'delivery_footer' => 'string',
        'invoice_header' => 'string',
        'invoice_footer' => 'string',
        'revert_header' => 'string',
        'revert_footer' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    protected $with = array(
        'collectiveinvoiceattributes',
        'collectiveinvoicorderpositions'
    );

    /**
     * @return mixed
     */
    public function collectiveinvoiceattributes()
    {
        return $this->hasMany('App\Models\CollectiveinvoiceAttribute', 'collectiveinvoice_id', 'id');
    }

    /**
     * @return mixed
     */
    public function collectiveinvoicorderpositions()
    {
        return $this->hasMany('App\Models\CollectiveinvoiceOrderposition', 'collectiveinvoice', 'id');
    }


}
