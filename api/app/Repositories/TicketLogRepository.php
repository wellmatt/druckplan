<?php

namespace App\Repositories;

use App\Models\TicketLog;
use InfyOm\Generator\Common\BaseRepository;

class TicketLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ticket',
        'crtusr',
        'date',
        'entry'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TicketLog::class;
    }
}
