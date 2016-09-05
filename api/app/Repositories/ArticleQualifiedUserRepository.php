<?php

namespace App\Repositories;

use App\Models\ArticleQualifiedUser;
use InfyOm\Generator\Common\BaseRepository;

class ArticleQualifiedUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'article',
        'user'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ArticleQualifiedUser::class;
    }
}
