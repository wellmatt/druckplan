<?php

namespace App\Repositories;

use App\Models\Contactperson;
use InfyOm\Generator\Common\BaseRepository;

class ContactpersonRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'active',
        'businesscontact',
        'title',
        'name1',
        'name2',
        'address1',
        'address2',
        'zip',
        'city',
        'country',
        'phone',
        'mobil',
        'fax',
        'email',
        'web',
        'comment',
        'main_contact',
        'active_adress',
        'alt_name1',
        'alt_name2',
        'alt_address1',
        'alt_address2',
        'alt_zip',
        'alt_city',
        'alt_country',
        'alt_phone',
        'alt_fax',
        'alt_mobil',
        'alt_email',
        'priv_name1',
        'priv_name2',
        'priv_address1',
        'priv_address2',
        'priv_zip',
        'priv_city',
        'priv_country',
        'priv_phone',
        'priv_fax',
        'priv_mobil',
        'priv_email',
        'shop_login',
        'shop_pass',
        'enabled_ticket',
        'enabled_article',
        'enabled_personalization',
        'enabled_marketing',
        'birthdate',
        'notifymailadr'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Contactperson::class;
    }
}
