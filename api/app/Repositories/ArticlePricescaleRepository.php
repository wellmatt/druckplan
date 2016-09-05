<?php

namespace App\Repositories;

use App\Models\ArticlePricescale;
use InfyOm\Generator\Common\BaseRepository;

class ArticlePricescaleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'article',
        'type',
        'min',
        'max',
        'price',
        'supplier',
        'artnum'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ArticlePricescale::class;
    }
}
