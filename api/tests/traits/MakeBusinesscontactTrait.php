<?php

use Faker\Factory as Faker;
use App\Models\Businesscontact;
use App\Repositories\BusinesscontactRepository;

trait MakeBusinesscontactTrait
{
    /**
     * Create fake instance of Businesscontact and save it in database
     *
     * @param array $businesscontactFields
     * @return Businesscontact
     */
    public function makeBusinesscontact($businesscontactFields = [])
    {
        /** @var BusinesscontactRepository $businesscontactRepo */
        $businesscontactRepo = App::make(BusinesscontactRepository::class);
        $theme = $this->fakeBusinesscontactData($businesscontactFields);
        return $businesscontactRepo->create($theme);
    }

    /**
     * Get fake instance of Businesscontact
     *
     * @param array $businesscontactFields
     * @return Businesscontact
     */
    public function fakeBusinesscontact($businesscontactFields = [])
    {
        return new Businesscontact($this->fakeBusinesscontactData($businesscontactFields));
    }

    /**
     * Get fake data of Businesscontact
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBusinesscontactData($businesscontactFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'active' => $fake->word,
            'commissionpartner' => $fake->word,
            'customer' => $fake->word,
            'supplier' => $fake->word,
            'client' => $fake->randomDigitNotNull,
            'matchcode' => $fake->word,
            'name1' => $fake->word,
            'name2' => $fake->word,
            'address1' => $fake->word,
            'address2' => $fake->word,
            'zip' => $fake->word,
            'city' => $fake->word,
            'country' => $fake->randomDigitNotNull,
            'phone' => $fake->word,
            'fax' => $fake->word,
            'email' => $fake->word,
            'web' => $fake->word,
            'comment' => $fake->text,
            'language' => $fake->randomDigitNotNull,
            'payment_terms' => $fake->randomDigitNotNull,
            'discount' => $fake->randomDigitNotNull,
            'lector_id' => $fake->randomDigitNotNull,
            'shop_login' => $fake->word,
            'shop_pass' => $fake->word,
            'login_expire' => $fake->randomDigitNotNull,
            'ticket_enabled' => $fake->word,
            'personalization_enabled' => $fake->word,
            'branche' => $fake->word,
            'type' => $fake->word,
            'produkte' => $fake->word,
            'bedarf' => $fake->word,
            'priv_name1' => $fake->word,
            'priv_name2' => $fake->word,
            'priv_address1' => $fake->word,
            'priv_address2' => $fake->word,
            'priv_zip' => $fake->word,
            'priv_city' => $fake->word,
            'priv_country' => $fake->randomDigitNotNull,
            'priv_phone' => $fake->word,
            'priv_fax' => $fake->word,
            'priv_email' => $fake->word,
            'alt_name1' => $fake->word,
            'alt_name2' => $fake->word,
            'alt_address1' => $fake->word,
            'alt_address2' => $fake->word,
            'alt_zip' => $fake->word,
            'alt_city' => $fake->word,
            'alt_country' => $fake->randomDigitNotNull,
            'alt_phone' => $fake->word,
            'alt_fax' => $fake->word,
            'alt_email' => $fake->word,
            'cust_number' => $fake->word,
            'number_at_customer' => $fake->word,
            'enabled_article' => $fake->word,
            'debitor_number' => $fake->randomDigitNotNull,
            'kreditor_number' => $fake->randomDigitNotNull,
            'iban' => $fake->word,
            'bic' => $fake->word,
            'position_titles' => $fake->text,
            'notifymailadr' => $fake->text,
            'supervisor' => $fake->randomDigitNotNull,
            'tourmarker' => $fake->word,
            'notes' => $fake->text,
            'salesperson' => $fake->randomDigitNotNull
        ], $businesscontactFields);
    }
}
