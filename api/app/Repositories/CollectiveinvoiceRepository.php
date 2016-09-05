<?php

namespace App\Repositories;

use App\Models\Collectiveinvoice;
use InfyOm\Generator\Common\BaseRepository;

class CollectiveinvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return Collectiveinvoice::class;
    }
}
