<?php

namespace App\Repositories;

use App\Models\ProductChromaticity;
use InfyOm\Generator\Common\BaseRepository;

class ProductChromaticityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'product_id',
        'chromaticity_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProductChromaticity::class;
    }
}
