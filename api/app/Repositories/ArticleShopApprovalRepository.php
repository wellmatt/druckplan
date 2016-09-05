<?php

namespace App\Repositories;

use App\Models\ArticleShopApproval;
use InfyOm\Generator\Common\BaseRepository;

class ArticleShopApprovalRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'article',
        'bc',
        'cp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ArticleShopApproval::class;
    }
}
