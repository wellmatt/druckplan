<?php

namespace App\Repositories;

use App\Models\TicketCategory;
use InfyOm\Generator\Common\BaseRepository;

class TicketCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'protected',
        'sort'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TicketCategory::class;
    }
}
