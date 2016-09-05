<?php

namespace App\Repositories;

use App\Models\PlanningJob;
use InfyOm\Generator\Common\BaseRepository;

class PlanningJobRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'object',
        'type',
        'opos',
        'subobject',
        'assigned_user',
        'assigned_group',
        'ticket',
        'start',
        'tplanned',
        'tactual',
        'state',
        'artmach'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PlanningJob::class;
    }
}
