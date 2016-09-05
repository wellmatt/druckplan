<?php

namespace App\Repositories;

use App\Models\PrivatContactAccess;
use InfyOm\Generator\Common\BaseRepository;

class PrivatContactAccessRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'prvtc_id',
        'userid'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PrivatContactAccess::class;
    }
}
