<?php

namespace App\Repositories;

use App\Models\Article;
use InfyOm\Generator\Common\BaseRepository;

class ArticleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'title',
        'description',
        'number',
        'tradegroup',
        'shoprel',
        'crtuser',
        'crtdate',
        'uptuser',
        'uptdate',
        'picture',
        'tax',
        'minorder',
        'maxorder',
        'orderunit',
        'orderunitweight',
        'shop_customer_rel',
        'shop_customer_id',
        'isworkhourart',
        'show_shop_price',
        'shop_needs_upload',
        'matchcode',
        'orderid',
        'usesstorage'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Article::class;
    }
}
