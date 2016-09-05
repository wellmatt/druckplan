<?php

namespace App\Repositories;

use App\Models\Product;
use InfyOm\Generator\Common\BaseRepository;

class ProductRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'state',
        'name',
        'description',
        'picture',
        'pages_from',
        'pages_to',
        'pages_step',
        'has_content',
        'has_addcontent',
        'has_envelope',
        'factor_width',
        'factor_height',
        'taxes',
        'grant_paper',
        'type',
        'text_offer',
        'text_offerconfirm',
        'text_invoice',
        'text_processing',
        'shop_rel',
        'tradegroup',
        'is_individual',
        'has_addcontent2',
        'has_addcontent3',
        'load_dummydata',
        'singleplateset',
        'blockplateset'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Product::class;
    }
}
