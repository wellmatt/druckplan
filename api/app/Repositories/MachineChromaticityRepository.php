<?php

namespace App\Repositories;

use App\Models\MachineChromaticity;
use InfyOm\Generator\Common\BaseRepository;

class MachineChromaticityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'machine_id',
        'chroma_id',
        'clickprice'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MachineChromaticity::class;
    }
}
