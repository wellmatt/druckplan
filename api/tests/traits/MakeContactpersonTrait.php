<?php

use Faker\Factory as Faker;
use App\Models\Contactperson;
use App\Repositories\ContactpersonRepository;

trait MakeContactpersonTrait
{
    /**
     * Create fake instance of Contactperson and save it in database
     *
     * @param array $contactpersonFields
     * @return Contactperson
     */
    public function makeContactperson($contactpersonFields = [])
    {
        /** @var ContactpersonRepository $contactpersonRepo */
        $contactpersonRepo = App::make(ContactpersonRepository::class);
        $theme = $this->fakeContactpersonData($contactpersonFields);
        return $contactpersonRepo->create($theme);
    }

    /**
     * Get fake instance of Contactperson
     *
     * @param array $contactpersonFields
     * @return Contactperson
     */
    public function fakeContactperson($contactpersonFields = [])
    {
        return new Contactperson($this->fakeContactpersonData($contactpersonFields));
    }

    /**
     * Get fake data of Contactperson
     *
     * @param array $postFields
     * @return array
     */
    public function fakeContactpersonData($contactpersonFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'active' => $fake->word,
            'businesscontact' => $fake->randomDigitNotNull,
            'title' => $fake->word,
            'name1' => $fake->word,
            'name2' => $fake->word,
            'address1' => $fake->word,
            'address2' => $fake->word,
            'zip' => $fake->word,
            'city' => $fake->word,
            'country' => $fake->randomDigitNotNull,
            'phone' => $fake->word,
            'mobil' => $fake->word,
            'fax' => $fake->word,
            'email' => $fake->word,
            'web' => $fake->word,
            'comment' => $fake->text,
            'main_contact' => $fake->word,
            'active_adress' => $fake->word,
            'alt_name1' => $fake->word,
            'alt_name2' => $fake->word,
            'alt_address1' => $fake->word,
            'alt_address2' => $fake->word,
            'alt_zip' => $fake->word,
            'alt_city' => $fake->word,
            'alt_country' => $fake->randomDigitNotNull,
            'alt_phone' => $fake->word,
            'alt_fax' => $fake->word,
            'alt_mobil' => $fake->word,
            'alt_email' => $fake->word,
            'priv_name1' => $fake->word,
            'priv_name2' => $fake->word,
            'priv_address1' => $fake->word,
            'priv_address2' => $fake->word,
            'priv_zip' => $fake->word,
            'priv_city' => $fake->word,
            'priv_country' => $fake->randomDigitNotNull,
            'priv_phone' => $fake->word,
            'priv_fax' => $fake->word,
            'priv_mobil' => $fake->word,
            'priv_email' => $fake->word,
            'shop_login' => $fake->word,
            'shop_pass' => $fake->word,
            'enabled_ticket' => $fake->word,
            'enabled_article' => $fake->word,
            'enabled_personalization' => $fake->word,
            'enabled_marketing' => $fake->word,
            'birthdate' => $fake->randomDigitNotNull,
            'notifymailadr' => $fake->text
        ], $contactpersonFields);
    }
}
