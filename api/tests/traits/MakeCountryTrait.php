<?php

use Faker\Factory as Faker;
use App\Models\Country;
use App\Repositories\CountryRepository;

trait MakeCountryTrait
{
    /**
     * Create fake instance of Country and save it in database
     *
     * @param array $countryFields
     * @return Country
     */
    public function makeCountry($countryFields = [])
    {
        /** @var CountryRepository $countryRepo */
        $countryRepo = App::make(CountryRepository::class);
        $theme = $this->fakeCountryData($countryFields);
        return $countryRepo->create($theme);
    }

    /**
     * Get fake instance of Country
     *
     * @param array $countryFields
     * @return Country
     */
    public function fakeCountry($countryFields = [])
    {
        return new Country($this->fakeCountryData($countryFields));
    }

    /**
     * Get fake data of Country
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCountryData($countryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'country_name' => $fake->word,
            'country_name_int' => $fake->word,
            'country_code' => $fake->word,
            'country_active' => $fake->word
        ], $countryFields);
    }
}
