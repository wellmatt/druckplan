<?php

namespace App\Repositories;

use App\Models\Chromaticity;
use InfyOm\Generator\Common\BaseRepository;

class ChromaticityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'colors_front',
        'colors_back',
        'reverse_printing',
        'markup',
        'pricekg'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Chromaticity::class;
    }
}
