<?php

namespace App\Repositories;

use App\Models\ArticleOrderamount;
use InfyOm\Generator\Common\BaseRepository;

class ArticleOrderamountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'article_id',
        'amount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ArticleOrderamount::class;
    }
}
