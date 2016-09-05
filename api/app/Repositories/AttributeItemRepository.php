<?php

namespace App\Repositories;

use App\Models\AttributeItem;
use InfyOm\Generator\Common\BaseRepository;

class AttributeItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'attribute_id',
        'title',
        'input'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AttributeItem::class;
    }
}
