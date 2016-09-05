<?php

namespace App\Repositories;

use App\Models\PaperWeight;
use InfyOm\Generator\Common\BaseRepository;

class PaperWeightRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paper_id',
        'weight'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaperWeight::class;
    }
}
