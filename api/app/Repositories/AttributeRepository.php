<?php

namespace App\Repositories;

use App\Models\Attribute;
use InfyOm\Generator\Common\BaseRepository;

class AttributeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'state',
        'title',
        'comment',
        'module',
        'objectid',
        'crtuser',
        'crtdate',
        'enable_customer',
        'enable_contacts',
        'enable_colinv'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Attribute::class;
    }
}
