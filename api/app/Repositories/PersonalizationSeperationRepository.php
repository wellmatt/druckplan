<?php

namespace App\Repositories;

use App\Models\PersonalizationSeperation;
use InfyOm\Generator\Common\BaseRepository;

class PersonalizationSeperationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sep_personalizationid',
        'sep_min',
        'sep_max',
        'sep_price',
        'sep_show'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PersonalizationSeperation::class;
    }
}
