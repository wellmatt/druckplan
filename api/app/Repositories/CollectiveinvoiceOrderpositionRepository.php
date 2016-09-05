<?php

namespace App\Repositories;

use App\Models\CollectiveinvoiceOrderposition;
use InfyOm\Generator\Common\BaseRepository;

class CollectiveinvoiceOrderpositionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'quantity',
        'price',
        'tax',
        'comment',
        'collectiveinvoice',
        'type',
        'inv_rel',
        'object_id',
        'rev_rel',
        'file_attach',
        'perso_order'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CollectiveinvoiceOrderposition::class;
    }
}
