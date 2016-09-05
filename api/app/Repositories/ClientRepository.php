<?php

namespace App\Repositories;

use App\Models\Client;
use InfyOm\Generator\Common\BaseRepository;

class ClientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return Client::class;
    }
}
