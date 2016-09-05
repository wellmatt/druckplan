<?php

namespace App\Repositories;

use App\Models\Finishing;
use InfyOm\Generator\Common\BaseRepository;

class FinishingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'lector_id',
        'status',
        'name',
        'beschreibung',
        'kosten'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Finishing::class;
    }
}
