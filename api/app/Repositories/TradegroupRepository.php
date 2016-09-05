<?php

namespace App\Repositories;

use App\Models\Tradegroup;
use InfyOm\Generator\Common\BaseRepository;

class TradegroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tradegroup_state',
        'tradegroup_title',
        'tradegroup_desc',
        'tradegroup_shoprel',
        'tradegroup_parentid'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tradegroup::class;
    }
}
