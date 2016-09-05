<?php

namespace App\Repositories;

use App\Models\TicketPriority;
use InfyOm\Generator\Common\BaseRepository;

class TicketPriorityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'value',
        'protected'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TicketPriority::class;
    }
}
