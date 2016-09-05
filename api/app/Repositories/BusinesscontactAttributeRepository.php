<?php

namespace App\Repositories;

use App\Models\BusinesscontactAttribute;
use InfyOm\Generator\Common\BaseRepository;

class BusinesscontactAttributeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'businesscontact_id',
        'attribute_id',
        'item_id',
        'value',
        'inputvalue'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BusinesscontactAttribute::class;
    }
}
