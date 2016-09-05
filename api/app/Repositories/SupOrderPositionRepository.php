<?php

namespace App\Repositories;

use App\Models\SupOrderPosition;
use InfyOm\Generator\Common\BaseRepository;

class SupOrderPositionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'suporder',
        'article',
        'amount',
        'colinvoice'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupOrderPosition::class;
    }
}
