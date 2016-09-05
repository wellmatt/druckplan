<?php

namespace App\Repositories;

use App\Models\Businesscontact;
use InfyOm\Generator\Common\BaseRepository;

class BusinesscontactRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'active',
        'commissionpartner',
        'customer',
        'supplier',
        'client',
        'matchcode',
        'name1',
        'name2',
        'address1',
        'address2',
        'zip',
        'city',
        'country',
        'phone',
        'fax',
        'email',
        'web',
        'comment',
        'language',
        'payment_terms',
        'discount',
        'lector_id',
        'shop_login',
        'shop_pass',
        'login_expire',
        'ticket_enabled',
        'personalization_enabled',
        'branche',
        'type',
        'produkte',
        'bedarf',
        'priv_name1',
        'priv_name2',
        'priv_address1',
        'priv_address2',
        'priv_zip',
        'priv_city',
        'priv_country',
        'priv_phone',
        'priv_fax',
        'priv_email',
        'alt_name1',
        'alt_name2',
        'alt_address1',
        'alt_address2',
        'alt_zip',
        'alt_city',
        'alt_country',
        'alt_phone',
        'alt_fax',
        'alt_email',
        'cust_number',
        'number_at_customer',
        'enabled_article',
        'debitor_number',
        'kreditor_number',
        'iban',
        'bic',
        'position_titles',
        'notifymailadr',
        'supervisor',
        'tourmarker',
        'notes',
        'salesperson'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Businesscontact::class;
    }
}
