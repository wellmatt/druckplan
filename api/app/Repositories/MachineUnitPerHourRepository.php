<?php

namespace App\Repositories;

use App\Models\MachineUnitPerHour;
use InfyOm\Generator\Common\BaseRepository;

class MachineUnitPerHourRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'machine_id',
        'units_from',
        'units_amount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MachineUnitPerHour::class;
    }
}
