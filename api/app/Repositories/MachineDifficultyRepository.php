<?php

namespace App\Repositories;

use App\Models\MachineDifficulty;
use InfyOm\Generator\Common\BaseRepository;

class MachineDifficultyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'machine_id',
        'diff_id',
        'diff_unit',
        'value',
        'percent'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MachineDifficulty::class;
    }
}
