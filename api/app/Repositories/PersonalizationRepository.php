<?php

namespace App\Repositories;

use App\Models\Personalization;
use InfyOm\Generator\Common\BaseRepository;

class PersonalizationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'comment',
        'status',
        'picture',
        'article',
        'customer',
        'crtdate',
        'crtuser',
        'uptdate',
        'uptuser',
        'direction',
        'format',
        'format_width',
        'format_height',
        'type',
        'picture2',
        'linebyline',
        'hidden',
        'anschnitt',
        'preview'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Personalization::class;
    }
}
