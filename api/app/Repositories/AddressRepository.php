<?php

namespace App\Repositories;

use App\Models\Address;
use InfyOm\Generator\Common\BaseRepository;

class AddressRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'active',
        'businesscontact',
        'name1',
        'name2',
        'address1',
        'address2',
        'zip',
        'city',
        'country',
        'fax',
        'phone',
        'mobile',
        'shoprel',
        'is_default'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Address::class;
    }
}
