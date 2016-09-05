<?php

use Faker\Factory as Faker;
use App\Models\Invoicetemplate;
use App\Repositories\InvoicetemplateRepository;

trait MakeInvoicetemplateTrait
{
    /**
     * Create fake instance of Invoicetemplate and save it in database
     *
     * @param array $invoicetemplateFields
     * @return Invoicetemplate
     */
    public function makeInvoicetemplate($invoicetemplateFields = [])
    {
        /** @var InvoicetemplateRepository $invoicetemplateRepo */
        $invoicetemplateRepo = App::make(InvoicetemplateRepository::class);
        $theme = $this->fakeInvoicetemplateData($invoicetemplateFields);
        return $invoicetemplateRepo->create($theme);
    }

    /**
     * Get fake instance of Invoicetemplate
     *
     * @param array $invoicetemplateFields
     * @return Invoicetemplate
     */
    public function fakeInvoicetemplate($invoicetemplateFields = [])
    {
        return new Invoicetemplate($this->fakeInvoicetemplateData($invoicetemplateFields));
    }

    /**
     * Get fake data of Invoicetemplate
     *
     * @param array $postFields
     * @return array
     */
    public function fakeInvoicetemplateData($invoicetemplateFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'invc_title' => $fake->word,
            'invc_price_netto' => $fake->word,
            'invc_taxes_active' => $fake->word,
            'invc_crtusr' => $fake->randomDigitNotNull,
            'invc_crtdat' => $fake->randomDigitNotNull,
            'invc_companyid' => $fake->randomDigitNotNull,
            'invc_supplierid' => $fake->randomDigitNotNull
        ], $invoicetemplateFields);
    }
}
