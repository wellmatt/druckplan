<?php

use Faker\Factory as Faker;
use App\Models\Address;
use App\Repositories\AddressRepository;

trait MakeAddressTrait
{
    /**
     * Create fake instance of Address and save it in database
     *
     * @param array $addressFields
     * @return Address
     */
    public function makeAddress($addressFields = [])
    {
        /** @var AddressRepository $addressRepo */
        $addressRepo = App::make(AddressRepository::class);
        $theme = $this->fakeAddressData($addressFields);
        return $addressRepo->create($theme);
    }

    /**
     * Get fake instance of Address
     *
     * @param array $addressFields
     * @return Address
     */
    public function fakeAddress($addressFields = [])
    {
        return new Address($this->fakeAddressData($addressFields));
    }

    /**
     * Get fake data of Address
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAddressData($addressFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'active' => $fake->word,
            'businesscontact' => $fake->randomDigitNotNull,
            'name1' => $fake->word,
            'name2' => $fake->word,
            'address1' => $fake->word,
            'address2' => $fake->word,
            'zip' => $fake->word,
            'city' => $fake->word,
            'country' => $fake->randomDigitNotNull,
            'fax' => $fake->word,
            'phone' => $fake->word,
            'mobile' => $fake->word,
            'shoprel' => $fake->word,
            'is_default' => $fake->word
        ], $addressFields);
    }
}
