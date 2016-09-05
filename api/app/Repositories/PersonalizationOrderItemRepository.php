<?php

namespace App\Repositories;

use App\Models\PersonalizationOrderItem;
use InfyOm\Generator\Common\BaseRepository;

class PersonalizationOrderItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'persoid',
        'persoorderid',
        'persoitemid',
        'value'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PersonalizationOrderItem::class;
    }
}
