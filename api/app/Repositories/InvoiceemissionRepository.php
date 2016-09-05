<?php

namespace App\Repositories;

use App\Models\Invoiceemission;
use InfyOm\Generator\Common\BaseRepository;

class InvoiceemissionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invc_title',
        'invc_number',
        'invc_price_netto',
        'invc_taxes_active',
        'invc_payed',
        'invc_payed_dat',
        'invc_payable_dat',
        'invc_crtusr',
        'invc_crtdat',
        'invc_companyid',
        'invc_supplierid',
        'invc_orders'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Invoiceemission::class;
    }
}
