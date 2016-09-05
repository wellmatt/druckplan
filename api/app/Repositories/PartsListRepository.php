<?php

namespace App\Repositories;

use App\Models\PartsList;
use InfyOm\Generator\Common\BaseRepository;

class PartsListRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'price',
        'crtdate',
        'crtuser'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PartsList::class;
    }
}
