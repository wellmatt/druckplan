<?php

namespace App\Repositories;

use App\Models\MachineGroup;
use InfyOm\Generator\Common\BaseRepository;

class MachineGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'position',
        'type',
        'lector_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MachineGroup::class;
    }
}
