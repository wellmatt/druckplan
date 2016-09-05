<?php

namespace App\Repositories;

use App\Models\PaperSupplier;
use InfyOm\Generator\Common\BaseRepository;

class PaperSupplierRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paper_id',
        'supplier_id',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaperSupplier::class;
    }
}
