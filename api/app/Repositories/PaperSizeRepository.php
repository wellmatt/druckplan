<?php

namespace App\Repositories;

use App\Models\PaperSize;
use InfyOm\Generator\Common\BaseRepository;

class PaperSizeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paper_id',
        'width',
        'height'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaperSize::class;
    }
}
