<?php

namespace App\Repositories;

use App\Models\Invoicerevert;
use InfyOm\Generator\Common\BaseRepository;

class InvoicerevertRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'rev_title',
        'rev_number',
        'rev_price_netto',
        'rev_taxes_active',
        'rev_payed',
        'rev_payed_dat',
        'rev_payable_dat',
        'rev_crtusr',
        'rev_crtdat',
        'rev_companyid',
        'rev_supplierid'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Invoicerevert::class;
    }
}
