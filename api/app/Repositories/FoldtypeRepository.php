<?php

namespace App\Repositories;

use App\Models\Foldtype;
use InfyOm\Generator\Common\BaseRepository;

class FoldtypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'name',
        'beschreibung',
        'vertical',
        'horizontal',
        'picture',
        'breaks'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Foldtype::class;
    }
}
