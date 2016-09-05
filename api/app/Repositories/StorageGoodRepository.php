<?php

namespace App\Repositories;

use App\Models\StorageGood;
use InfyOm\Generator\Common\BaseRepository;

class StorageGoodRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'origin',
        'type',
        'crtdate',
        'crtuser'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StorageGood::class;
    }
}
