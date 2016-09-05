<?php

namespace App\Repositories;

use App\Models\ProductMachine;
use InfyOm\Generator\Common\BaseRepository;

class ProductMachineRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'product_id',
        'machine_id',
        'default',
        'minimum',
        'maximum'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProductMachine::class;
    }
}
