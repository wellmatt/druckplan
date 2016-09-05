<?php

use Faker\Factory as Faker;
use App\Models\Invoiceemission;
use App\Repositories\InvoiceemissionRepository;

trait MakeInvoiceemissionTrait
{
    /**
     * Create fake instance of Invoiceemission and save it in database
     *
     * @param array $invoiceemissionFields
     * @return Invoiceemission
     */
    public function makeInvoiceemission($invoiceemissionFields = [])
    {
        /** @var InvoiceemissionRepository $invoiceemissionRepo */
        $invoiceemissionRepo = App::make(InvoiceemissionRepository::class);
        $theme = $this->fakeInvoiceemissionData($invoiceemissionFields);
        return $invoiceemissionRepo->create($theme);
    }

    /**
     * Get fake instance of Invoiceemission
     *
     * @param array $invoiceemissionFields
     * @return Invoiceemission
     */
    public function fakeInvoiceemission($invoiceemissionFields = [])
    {
        return new Invoiceemission($this->fakeInvoiceemissionData($invoiceemissionFields));
    }

    /**
     * Get fake data of Invoiceemission
     *
     * @param array $postFields
     * @return array
     */
    public function fakeInvoiceemissionData($invoiceemissionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'invc_title' => $fake->word,
            'invc_number' => $fake->word,
            'invc_price_netto' => $fake->word,
            'invc_taxes_active' => $fake->word,
            'invc_payed' => $fake->word,
            'invc_payed_dat' => $fake->randomDigitNotNull,
            'invc_payable_dat' => $fake->randomDigitNotNull,
            'invc_crtusr' => $fake->randomDigitNotNull,
            'invc_crtdat' => $fake->randomDigitNotNull,
            'invc_companyid' => $fake->randomDigitNotNull,
            'invc_supplierid' => $fake->randomDigitNotNull,
            'invc_orders' => $fake->text
        ], $invoiceemissionFields);
    }
}
