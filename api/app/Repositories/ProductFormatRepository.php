<?php

namespace App\Repositories;

use App\Models\ProductFormat;
use InfyOm\Generator\Common\BaseRepository;

class ProductFormatRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'product_id',
        'format_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProductFormat::class;
    }
}
