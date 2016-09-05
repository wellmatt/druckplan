<?php

namespace App\Repositories;

use App\Models\PaperPrice;
use InfyOm\Generator\Common\BaseRepository;

class PaperPriceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paper_id',
        'weight_from',
        'weight_to',
        'size_width',
        'size_height',
        'quantity_from',
        'price',
        'weight'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaperPrice::class;
    }
}
