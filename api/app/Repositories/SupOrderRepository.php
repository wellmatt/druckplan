<?php

namespace App\Repositories;

use App\Models\SupOrder;
use InfyOm\Generator\Common\BaseRepository;

class SupOrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'number',
        'supplier',
        'title',
        'eta',
        'paymentterm',
        'status',
        'invoiceaddress',
        'deliveryaddress',
        'crtdate',
        'crtuser',
        'cpinternal',
        'cpexternal'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupOrder::class;
    }
}
