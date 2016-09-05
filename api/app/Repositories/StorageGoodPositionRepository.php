<?php

namespace App\Repositories;

use App\Models\StorageGoodPosition;
use InfyOm\Generator\Common\BaseRepository;

class StorageGoodPositionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'goods',
        'article',
        'amount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StorageGoodPosition::class;
    }
}
