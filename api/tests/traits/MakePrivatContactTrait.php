<?php

use Faker\Factory as Faker;
use App\Models\PrivatContact;
use App\Repositories\PrivatContactRepository;

trait MakePrivatContactTrait
{
    /**
     * Create fake instance of PrivatContact and save it in database
     *
     * @param array $privatContactFields
     * @return PrivatContact
     */
    public function makePrivatContact($privatContactFields = [])
    {
        /** @var PrivatContactRepository $privatContactRepo */
        $privatContactRepo = App::make(PrivatContactRepository::class);
        $theme = $this->fakePrivatContactData($privatContactFields);
        return $privatContactRepo->create($theme);
    }

    /**
     * Get fake instance of PrivatContact
     *
     * @param array $privatContactFields
     * @return PrivatContact
     */
    public function fakePrivatContact($privatContactFields = [])
    {
        return new PrivatContact($this->fakePrivatContactData($privatContactFields));
    }

    /**
     * Get fake data of PrivatContact
     *
     * @param array $postFields
     * @return array
     */
    public function fakePrivatContactData($privatContactFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'crtuser' => $fake->randomDigitNotNull,
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
            'birthdate' => $fake->randomDigitNotNull,
            'alt_title' => $fake->word,
            'alt_name1' => $fake->word,
            'alt_name2' => $fake->word,
            'alt_address1' => $fake->word,
            'alt_address2' => $fake->word,
            'alt_zip' => $fake->word,
            'alt_city' => $fake->word,
            'alt_country' => $fake->randomDigitNotNull,
            'alt_email' => $fake->word,
            'alt_phone' => $fake->word,
            'alt_mobil' => $fake->word,
            'alt_fax' => $fake->word,
            'alt_web' => $fake->word
        ], $privatContactFields);
    }
}
