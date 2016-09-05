<?php

namespace App\Repositories;

use App\Models\StorageArea;
use InfyOm\Generator\Common\BaseRepository;

class StorageAreaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'location',
        'corridor',
        'shelf',
        'line',
        'layer',
        'prio'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StorageArea::class;
    }
}
