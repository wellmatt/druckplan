<?php namespace App\Api\Transformers;

use App\Models\Collectiveinvoice;
use League\Fractal\TransformerAbstract;

class CollectiveinvoiceTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array.
     *
     * @param $item
     * @return array
     */
    public function transform(Collectiveinvoice $item)
    {
        return [
            'id' => (int)$item->id,
            'status' => (int)$item->status,
            'title' => (string)$item->title,
            'number' => (string)$item->number,
            'deliverycosts' => (float)$item->deliverycosts,
            'comment' => (string)$item->comment,
            'client' => (int)$item->client,
            'businesscontact' => (int)$item->businesscontact,
            'deliveryterm' => (int)$item->deliveryterm,
            'paymentterm' => (int)$item->paymentterm,
            'deliveryaddress' => (int)$item->deliveryaddress,
            'invoiceaddress' => (int)$item->invoiceaddress,
            'crtdate' => $item->crtdate,
            'crtuser' => $item->crtuser,
            'uptdate' => $item->uptdate,
            'uptuser' => $item->uptuser,
            'intent' => (string)$item->intent,
            'intern_contactperson' => (int)$item->intern_contactperson,
            'cust_message' => (string)$item->cust_message,
            'cust_sign' => (string)$item->cust_sign,
            'custContactperson' => (int)$item->custContactperson,
            'needs_planning' => (int)$item->needs_planning,
            'deliverydate' => $item->deliverydate,
            'rdyfordispatch' => (int)$item->rdyfordispatch,
            'ext_comment' => (string)$item->ext_comment,
            'thirdparty' => (int)$item->thirdparty,
            'thirdpartycomment' => (string)$item->thirdpartycomment,
            'ticket' => (int)$item->ticket,
            'offer_header' => (string)$item->offer_header,
            'offer_footer' => (string)$item->offer_footer,
            'offerconfirm_header' => (string)$item->offerconfirm_header,
            'offerconfirm_footer' => (string)$item->offerconfirm_footer,
            'factory_header' => (string)$item->factory_header,
            'factory_footer' => (string)$item->factory_footer,
            'delivery_header' => (string)$item->delivery_header,
            'delivery_footer' => (string)$item->delivery_footer,
            'invoice_header' => (string)$item->invoice_header,
            'invoice_footer' => (string)$item->invoice_footer,
            'revert_header' => (string)$item->revert_header,
            'revert_footer' => (string)$item->revert_footer,
        ];

    }
}
