<?php

namespace App\Repositories;

use App\Models\MachineWorkTime;
use InfyOm\Generator\Common\BaseRepository;

class MachineWorkTimeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'machine',
        'weekday',
        'start',
        'end'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MachineWorkTime::class;
    }
}
