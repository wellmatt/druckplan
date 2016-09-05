<?php

namespace App\Repositories;

use App\Models\CollectiveinvoiceAttribute;
use InfyOm\Generator\Common\BaseRepository;

class CollectiveinvoiceAttributeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'collectiveinvoice_id',
        'attribute_id',
        'item_id',
        'value',
        'inputvalue'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CollectiveinvoiceAttribute::class;
    }
}
