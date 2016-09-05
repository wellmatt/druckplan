<?php

namespace App\Repositories;

use App\Models\Formats;
use InfyOm\Generator\Common\BaseRepository;

class FormatsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'width',
        'height'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Formats::class;
    }
}
