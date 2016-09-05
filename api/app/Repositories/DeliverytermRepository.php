<?php

namespace App\Repositories;

use App\Models\Deliveryterm;
use InfyOm\Generator\Common\BaseRepository;

class DeliverytermRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'active',
        'client',
        'name1',
        'comment',
        'charges',
        'shoprel',
        'tax'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Deliveryterm::class;
    }
}
