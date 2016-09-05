<?php

namespace App\Repositories;

use App\Models\PersonalizationOrder;
use InfyOm\Generator\Common\BaseRepository;

class PersonalizationOrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'title',
        'persoid',
        'documentid',
        'customerid',
        'crtuser',
        'crtdate',
        'orderdate',
        'comment',
        'amount',
        'contact_person_id',
        'deliveryaddress_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PersonalizationOrder::class;
    }
}
