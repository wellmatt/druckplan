<?php

use Faker\Factory as Faker;
use App\Models\Invoicerevert;
use App\Repositories\InvoicerevertRepository;

trait MakeInvoicerevertTrait
{
    /**
     * Create fake instance of Invoicerevert and save it in database
     *
     * @param array $invoicerevertFields
     * @return Invoicerevert
     */
    public function makeInvoicerevert($invoicerevertFields = [])
    {
        /** @var InvoicerevertRepository $invoicerevertRepo */
        $invoicerevertRepo = App::make(InvoicerevertRepository::class);
        $theme = $this->fakeInvoicerevertData($invoicerevertFields);
        return $invoicerevertRepo->create($theme);
    }

    /**
     * Get fake instance of Invoicerevert
     *
     * @param array $invoicerevertFields
     * @return Invoicerevert
     */
    public function fakeInvoicerevert($invoicerevertFields = [])
    {
        return new Invoicerevert($this->fakeInvoicerevertData($invoicerevertFields));
    }

    /**
     * Get fake data of Invoicerevert
     *
     * @param array $postFields
     * @return array
     */
    public function fakeInvoicerevertData($invoicerevertFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'rev_title' => $fake->word,
            'rev_number' => $fake->word,
            'rev_price_netto' => $fake->word,
            'rev_taxes_active' => $fake->word,
            'rev_payed' => $fake->word,
            'rev_payed_dat' => $fake->randomDigitNotNull,
            'rev_payable_dat' => $fake->randomDigitNotNull,
            'rev_crtusr' => $fake->randomDigitNotNull,
            'rev_crtdat' => $fake->randomDigitNotNull,
            'rev_companyid' => $fake->randomDigitNotNull,
            'rev_supplierid' => $fake->randomDigitNotNull
        ], $invoicerevertFields);
    }
}
