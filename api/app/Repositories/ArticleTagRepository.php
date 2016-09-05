<?php

namespace App\Repositories;

use App\Models\ArticleTag;
use InfyOm\Generator\Common\BaseRepository;

class ArticleTagRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'article',
        'tag'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ArticleTag::class;
    }
}
