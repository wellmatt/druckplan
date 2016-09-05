<?php

namespace App\Repositories;

use App\Models\StorageBookEntry;
use InfyOm\Generator\Common\BaseRepository;

class StorageBookEntryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'area',
        'article',
        'type',
        'origin',
        'origin_pos',
        'amount',
        'alloc',
        'crtdate',
        'crtuser'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StorageBookEntry::class;
    }
}
