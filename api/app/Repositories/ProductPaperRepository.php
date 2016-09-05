<?php

namespace App\Repositories;

use App\Models\ProductPaper;
use InfyOm\Generator\Common\BaseRepository;

class ProductPaperRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'product_id',
        'paper_id',
        'weight',
        'part'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProductPaper::class;
    }
}
