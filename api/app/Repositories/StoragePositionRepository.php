<?php

namespace App\Repositories;

use App\Models\StoragePosition;
use InfyOm\Generator\Common\BaseRepository;

class StoragePositionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'area',
        'article',
        'businesscontact',
        'amount',
        'min_amount',
        'respuser',
        'description',
        'note',
        'dispatch',
        'packaging',
        'allocation'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StoragePosition::class;
    }
}
