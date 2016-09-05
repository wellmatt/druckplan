<?php

namespace App\Repositories;

use App\Models\Invoicetemplate;
use InfyOm\Generator\Common\BaseRepository;

class InvoicetemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invc_title',
        'invc_price_netto',
        'invc_taxes_active',
        'invc_crtusr',
        'invc_crtdat',
        'invc_companyid',
        'invc_supplierid'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Invoicetemplate::class;
    }
}
