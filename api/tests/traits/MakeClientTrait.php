<?php

use Faker\Factory as Faker;
use App\Models\Client;
use App\Repositories\ClientRepository;

trait MakeClientTrait
{
    /**
     * Create fake instance of Client and save it in database
     *
     * @param array $clientFields
     * @return Client
     */
    public function makeClient($clientFields = [])
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = App::make(ClientRepository::class);
        $theme = $this->fakeClientData($clientFields);
        return $clientRepo->create($theme);
    }

    /**
     * Get fake instance of Client
     *
     * @param array $clientFields
     * @return Client
     */
    public function fakeClient($clientFields = [])
    {
        return new Client($this->fakeClientData($clientFields));
    }

    /**
     * Get fake data of Client
     *
     * @param array $postFields
     * @return array
     */
    public function fakeClientData($clientFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'active' => $fake->word,
            'client_status' => $fake->word,
            'client_name' => $fake->word,
            'client_street1' => $fake->word,
            'client_street2' => $fake->word,
            'client_street3' => $fake->word,
            'client_postcode' => $fake->word,
            'client_city' => $fake->word,
            'client_phone' => $fake->word,
            'client_fax' => $fake->word,
            'client_email' => $fake->word,
            'client_website' => $fake->word,
            'client_bank_name' => $fake->word,
            'client_bank_blz' => $fake->word,
            'client_bank_kto' => $fake->word,
            'client_bank_iban' => $fake->word,
            'client_bank_bic' => $fake->word,
            'client_gericht' => $fake->word,
            'client_steuernummer' => $fake->word,
            'client_ustid' => $fake->word,
            'client_country' => $fake->randomDigitNotNull,
            'client_currency' => $fake->word,
            'client_decimal' => $fake->word,
            'client_thousand' => $fake->word,
            'client_taxes' => $fake->randomDigitNotNull,
            'client_margin' => $fake->randomDigitNotNull,
            'number_format_order' => $fake->word,
            'number_counter_order' => $fake->randomDigitNotNull,
            'number_format_colinv' => $fake->word,
            'number_counter_colinv' => $fake->randomDigitNotNull,
            'number_format_offer' => $fake->word,
            'number_counter_offer' => $fake->randomDigitNotNull,
            'number_format_offerconfirm' => $fake->word,
            'number_counter_offerconfirm' => $fake->randomDigitNotNull,
            'number_format_delivery' => $fake->word,
            'number_counter_delivery' => $fake->randomDigitNotNull,
            'number_format_paper_order' => $fake->word,
            'number_counter_paper_order' => $fake->randomDigitNotNull,
            'number_format_invoice' => $fake->word,
            'number_counter_invoice' => $fake->randomDigitNotNull,
            'number_format_revert' => $fake->word,
            'number_counter_revert' => $fake->randomDigitNotNull,
            'number_format_warning' => $fake->word,
            'number_counter_warning' => $fake->randomDigitNotNull,
            'number_format_work' => $fake->word,
            'number_counter_work' => $fake->randomDigitNotNull,
            'number_format_suporder' => $fake->word,
            'number_counter_suporder' => $fake->randomDigitNotNull,
            'number_counter_ticket' => $fake->randomDigitNotNull,
            'ticketnumberreset' => $fake->randomDigitNotNull,
            'number_counter_debitor' => $fake->randomDigitNotNull,
            'number_counter_creditor' => $fake->randomDigitNotNull,
            'number_counter_customer' => $fake->randomDigitNotNull,
            'client_bank2' => $fake->word,
            'client_bic2' => $fake->word,
            'client_iban2' => $fake->word,
            'client_bank3' => $fake->word,
            'client_bic3' => $fake->word,
            'client_iban3' => $fake->word
        ], $clientFields);
    }
}
