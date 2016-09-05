<?php

namespace App\Repositories;

use App\Models\MachineLock;
use InfyOm\Generator\Common\BaseRepository;

class MachineLockRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'machineid',
        'start',
        'stop'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MachineLock::class;
    }
}
