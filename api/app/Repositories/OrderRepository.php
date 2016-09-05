<?php

namespace App\Repositories;

use App\Models\Order;
use InfyOm\Generator\Common\BaseRepository;

class OrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return Order::class;
    }
}
