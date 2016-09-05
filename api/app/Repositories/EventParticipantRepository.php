<?php

namespace App\Repositories;

use App\Models\EventParticipant;
use InfyOm\Generator\Common\BaseRepository;

class EventParticipantRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'event',
        'participant',
        'type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EventParticipant::class;
    }
}
