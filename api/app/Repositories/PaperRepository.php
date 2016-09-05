<?php

namespace App\Repositories;

use App\Models\Paper;
use InfyOm\Generator\Common\BaseRepository;

class PaperRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'name',
        'comment',
        'type',
        'pricebase',
        'dilivermat',
        'glue',
        'thickness',
        'totalweight',
        'price_100kg',
        'price_1qm',
        'rolle',
        'volume'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Paper::class;
    }
}
