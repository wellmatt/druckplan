<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Client",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="client_name",
 *          description="client_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_street1",
 *          description="client_street1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_street2",
 *          description="client_street2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_street3",
 *          description="client_street3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_postcode",
 *          description="client_postcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_city",
 *          description="client_city",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_phone",
 *          description="client_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_fax",
 *          description="client_fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_email",
 *          description="client_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_website",
 *          description="client_website",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bank_name",
 *          description="client_bank_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bank_blz",
 *          description="client_bank_blz",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bank_kto",
 *          description="client_bank_kto",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bank_iban",
 *          description="client_bank_iban",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bank_bic",
 *          description="client_bank_bic",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_gericht",
 *          description="client_gericht",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_steuernummer",
 *          description="client_steuernummer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_ustid",
 *          description="client_ustid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_country",
 *          description="client_country",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="client_currency",
 *          description="client_currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_decimal",
 *          description="client_decimal",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_thousand",
 *          description="client_thousand",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_taxes",
 *          description="client_taxes",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="client_margin",
 *          description="client_margin",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="number_format_order",
 *          description="number_format_order",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_order",
 *          description="number_counter_order",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_colinv",
 *          description="number_format_colinv",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_colinv",
 *          description="number_counter_colinv",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_offer",
 *          description="number_format_offer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_offer",
 *          description="number_counter_offer",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_offerconfirm",
 *          description="number_format_offerconfirm",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_offerconfirm",
 *          description="number_counter_offerconfirm",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_delivery",
 *          description="number_format_delivery",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_delivery",
 *          description="number_counter_delivery",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_paper_order",
 *          description="number_format_paper_order",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_paper_order",
 *          description="number_counter_paper_order",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_invoice",
 *          description="number_format_invoice",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_invoice",
 *          description="number_counter_invoice",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_revert",
 *          description="number_format_revert",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_revert",
 *          description="number_counter_revert",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_warning",
 *          description="number_format_warning",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_warning",
 *          description="number_counter_warning",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_work",
 *          description="number_format_work",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_work",
 *          description="number_counter_work",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_format_suporder",
 *          description="number_format_suporder",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_suporder",
 *          description="number_counter_suporder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_ticket",
 *          description="number_counter_ticket",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketnumberreset",
 *          description="ticketnumberreset",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_debitor",
 *          description="number_counter_debitor",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_creditor",
 *          description="number_counter_creditor",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="number_counter_customer",
 *          description="number_counter_customer",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="client_bank2",
 *          description="client_bank2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bic2",
 *          description="client_bic2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_iban2",
 *          description="client_iban2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bank3",
 *          description="client_bank3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_bic3",
 *          description="client_bic3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="client_iban3",
 *          description="client_iban3",
 *          type="string"
 *      )
 * )
 */
class Client extends Model
{

    public $table = 'clients';
    
    public $timestamps = false;



    public $fillable = [
        'active',
        'client_status',
        'client_name',
        'client_street1',
        'client_street2',
        'client_street3',
        'client_postcode',
        'client_city',
        'client_phone',
        'client_fax',
        'client_email',
        'client_website',
        'client_bank_name',
        'client_bank_blz',
        'client_bank_kto',
        'client_bank_iban',
        'client_bank_bic',
        'client_gericht',
        'client_steuernummer',
        'client_ustid',
        'client_country',
        'client_currency',
        'client_decimal',
        'client_thousand',
        'client_taxes',
        'client_margin',
        'number_format_order',
        'number_counter_order',
        'number_format_colinv',
        'number_counter_colinv',
        'number_format_offer',
        'number_counter_offer',
        'number_format_offerconfirm',
        'number_counter_offerconfirm',
        'number_format_delivery',
        'number_counter_delivery',
        'number_format_paper_order',
        'number_counter_paper_order',
        'number_format_invoice',
        'number_counter_invoice',
        'number_format_revert',
        'number_counter_revert',
        'number_format_warning',
        'number_counter_warning',
        'number_format_work',
        'number_counter_work',
        'number_format_suporder',
        'number_counter_suporder',
        'number_counter_ticket',
        'ticketnumberreset',
        'number_counter_debitor',
        'number_counter_creditor',
        'number_counter_customer',
        'client_bank2',
        'client_bic2',
        'client_iban2',
        'client_bank3',
        'client_bic3',
        'client_iban3'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'client_name' => 'string',
        'client_street1' => 'string',
        'client_street2' => 'string',
        'client_street3' => 'string',
        'client_postcode' => 'string',
        'client_city' => 'string',
        'client_phone' => 'string',
        'client_fax' => 'string',
        'client_email' => 'string',
        'client_website' => 'string',
        'client_bank_name' => 'string',
        'client_bank_blz' => 'string',
        'client_bank_kto' => 'string',
        'client_bank_iban' => 'string',
        'client_bank_bic' => 'string',
        'client_gericht' => 'string',
        'client_steuernummer' => 'string',
        'client_ustid' => 'string',
        'client_country' => 'integer',
        'client_currency' => 'string',
        'client_decimal' => 'string',
        'client_thousand' => 'string',
        'client_taxes' => 'float',
        'client_margin' => 'float',
        'number_format_order' => 'string',
        'number_counter_order' => 'integer',
        'number_format_colinv' => 'string',
        'number_counter_colinv' => 'integer',
        'number_format_offer' => 'string',
        'number_counter_offer' => 'integer',
        'number_format_offerconfirm' => 'string',
        'number_counter_offerconfirm' => 'integer',
        'number_format_delivery' => 'string',
        'number_counter_delivery' => 'integer',
        'number_format_paper_order' => 'string',
        'number_counter_paper_order' => 'integer',
        'number_format_invoice' => 'string',
        'number_counter_invoice' => 'integer',
        'number_format_revert' => 'string',
        'number_counter_revert' => 'integer',
        'number_format_warning' => 'string',
        'number_counter_warning' => 'integer',
        'number_format_work' => 'string',
        'number_counter_work' => 'integer',
        'number_format_suporder' => 'string',
        'number_counter_suporder' => 'integer',
        'number_counter_ticket' => 'integer',
        'ticketnumberreset' => 'integer',
        'number_counter_debitor' => 'integer',
        'number_counter_creditor' => 'integer',
        'number_counter_customer' => 'integer',
        'client_bank2' => 'string',
        'client_bic2' => 'string',
        'client_iban2' => 'string',
        'client_bank3' => 'string',
        'client_bic3' => 'string',
        'client_iban3' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
}
