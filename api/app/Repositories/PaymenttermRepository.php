<?php

namespace App\Repositories;

use App\Models\Paymentterm;
use InfyOm\Generator\Common\BaseRepository;

class PaymenttermRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'active',
        'client',
        'name1',
        'comment',
        'skonto_days1',
        'skonto1',
        'skonto_days2',
        'skonto2',
        'netto_days',
        'shop_rel'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Paymentterm::class;
    }
}
