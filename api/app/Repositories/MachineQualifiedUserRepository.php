<?php

namespace App\Repositories;

use App\Models\MachineQualifiedUser;
use InfyOm\Generator\Common\BaseRepository;

class MachineQualifiedUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'machine',
        'user'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MachineQualifiedUser::class;
    }
}
