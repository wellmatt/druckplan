<?php

namespace App\Repositories;

use App\Models\PersonalizationItem;
use InfyOm\Generator\Common\BaseRepository;

class PersonalizationItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'title',
        'xpos',
        'ypos',
        'height',
        'width',
        'boxtype',
        'personalization_id',
        'text_size',
        'justification',
        'font',
        'color_c',
        'color_m',
        'color_y',
        'color_k',
        'spacing',
        'dependency_id',
        'reverse',
        'predefined',
        'position',
        'readonly',
        'tab',
        'zzgroup',
        'sort'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PersonalizationItem::class;
    }
}
