<?php

namespace App\Repositories;

use App\Models\PartsListItem;
use InfyOm\Generator\Common\BaseRepository;

class PartsListItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'partslist',
        'article',
        'amount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PartsListItem::class;
    }
}
