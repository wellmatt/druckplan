<?php

namespace App\Repositories;

use App\Models\TicketSource;
use InfyOm\Generator\Common\BaseRepository;

class TicketSourceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'default'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TicketSource::class;
    }
}
