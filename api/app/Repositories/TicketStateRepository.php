<?php

namespace App\Repositories;

use App\Models\TicketState;
use InfyOm\Generator\Common\BaseRepository;

class TicketStateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'protected',
        'colorcode'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TicketState::class;
    }
}
