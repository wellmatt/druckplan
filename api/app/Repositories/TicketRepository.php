<?php

namespace App\Repositories;

use App\Models\Ticket;
use InfyOm\Generator\Common\BaseRepository;

class TicketRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'crtdate',
        'crtuser',
        'duedate',
        'closedate',
        'closeuser',
        'editdate',
        'number',
        'customer',
        'customer_cp',
        'assigned_user',
        'assigned_group',
        'state',
        'category',
        'priority',
        'source',
        'tourmarker',
        'planned_time'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Ticket::class;
    }
}
