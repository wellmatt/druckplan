<?php

namespace App\Repositories;

use App\Models\PrivatContact;
use InfyOm\Generator\Common\BaseRepository;

class PrivatContactRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'crtuser',
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
        'birthdate',
        'alt_title',
        'alt_name1',
        'alt_name2',
        'alt_address1',
        'alt_address2',
        'alt_zip',
        'alt_city',
        'alt_country',
        'alt_email',
        'alt_phone',
        'alt_mobil',
        'alt_fax',
        'alt_web'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PrivatContact::class;
    }
}
