<?php

namespace App\Repositories;

use App\Models\ArticlePicture;
use InfyOm\Generator\Common\BaseRepository;

class ArticlePictureRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'url',
        'crtdate',
        'articleid'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ArticlePicture::class;
    }
}
