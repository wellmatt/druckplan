<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Order",
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
 *          property="businesscontact_id",
 *          description="businesscontact_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_id",
 *          description="product_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="notes",
 *          description="notes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="delivery_address_id",
 *          description="delivery_address_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoice_address_id",
 *          description="invoice_address_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="delivery_terms_id",
 *          description="delivery_terms_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payment_terms_id",
 *          description="payment_terms_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="delivery_date",
 *          description="delivery_date",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="delivery_cost",
 *          description="delivery_cost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="text_offer",
 *          description="text_offer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="text_offerconfirm",
 *          description="text_offerconfirm",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="text_invoice",
 *          description="text_invoice",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="cust_contactperson",
 *          description="cust_contactperson",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtdat",
 *          description="crtdat",
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
 *          property="upddat",
 *          description="upddat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updusr",
 *          description="updusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="collectiveinvoice_id",
 *          description="collectiveinvoice_id",
 *          type="integer",
 *          format="int32"
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
 *          property="inv_amount",
 *          description="inv_amount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliv_amount",
 *          description="deliv_amount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="label_box_amount",
 *          description="label_box_amount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="label_title",
 *          description="label_title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="productname",
 *          description="productname",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="paper_order_boegen",
 *          description="paper_order_boegen",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="paper_order_price",
 *          description="paper_order_price",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="paper_order_supplier",
 *          description="paper_order_supplier",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_order_calc",
 *          description="paper_order_calc",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="beilagen",
 *          description="beilagen",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="articleid",
 *          description="articleid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Order extends Model
{

    public $table = 'orders';
    
    public $timestamps = false;



    public $fillable = [
        'number',
        'status',
        'businesscontact_id',
        'product_id',
        'title',
        'notes',
        'delivery_address_id',
        'invoice_address_id',
        'delivery_terms_id',
        'payment_terms_id',
        'delivery_date',
        'delivery_cost',
        'text_offer',
        'text_offerconfirm',
        'text_invoice',
        'cust_contactperson',
        'crtdat',
        'crtusr',
        'upddat',
        'updusr',
        'collectiveinvoice_id',
        'intern_contactperson',
        'cust_message',
        'cust_sign',
        'inv_amount',
        'inv_price_update',
        'deliv_amount',
        'label_logo_active',
        'label_box_amount',
        'label_title',
        'show_product',
        'productname',
        'show_price_per_thousand',
        'paper_order_boegen',
        'paper_order_price',
        'paper_order_supplier',
        'paper_order_calc',
        'beilagen',
        'articleid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'number' => 'string',
        'businesscontact_id' => 'integer',
        'product_id' => 'integer',
        'title' => 'string',
        'notes' => 'string',
        'delivery_address_id' => 'integer',
        'invoice_address_id' => 'integer',
        'delivery_terms_id' => 'integer',
        'payment_terms_id' => 'integer',
        'delivery_date' => 'integer',
        'delivery_cost' => 'float',
        'text_offer' => 'string',
        'text_offerconfirm' => 'string',
        'text_invoice' => 'string',
        'cust_contactperson' => 'integer',
        'crtdat' => 'integer',
        'crtusr' => 'integer',
        'upddat' => 'integer',
        'updusr' => 'integer',
        'collectiveinvoice_id' => 'integer',
        'intern_contactperson' => 'integer',
        'cust_message' => 'string',
        'cust_sign' => 'string',
        'inv_amount' => 'integer',
        'deliv_amount' => 'integer',
        'label_box_amount' => 'integer',
        'label_title' => 'string',
        'productname' => 'string',
        'paper_order_boegen' => 'string',
        'paper_order_price' => 'string',
        'paper_order_supplier' => 'integer',
        'paper_order_calc' => 'integer',
        'beilagen' => 'string',
        'articleid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'ordercalculations',
    );

    /**
     * @return mixed
     */
    public function ordercalculations()
    {
        return $this->hasMany('App\Models\OrderCalculation', 'order_id', 'id');
    }

    
}
