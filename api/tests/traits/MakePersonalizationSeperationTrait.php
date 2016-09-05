<?php

use Faker\Factory as Faker;
use App\Models\PersonalizationSeperation;
use App\Repositories\PersonalizationSeperationRepository;

trait MakePersonalizationSeperationTrait
{
    /**
     * Create fake instance of PersonalizationSeperation and save it in database
     *
     * @param array $personalizationSeperationFields
     * @return PersonalizationSeperation
     */
    public function makePersonalizationSeperation($personalizationSeperationFields = [])
    {
        /** @var PersonalizationSeperationRepository $personalizationSeperationRepo */
        $personalizationSeperationRepo = App::make(PersonalizationSeperationRepository::class);
        $theme = $this->fakePersonalizationSeperationData($personalizationSeperationFields);
        return $personalizationSeperationRepo->create($theme);
    }

    /**
     * Get fake instance of PersonalizationSeperation
     *
     * @param array $personalizationSeperationFields
     * @return PersonalizationSeperation
     */
    public function fakePersonalizationSeperation($personalizationSeperationFields = [])
    {
        return new PersonalizationSeperation($this->fakePersonalizationSeperationData($personalizationSeperationFields));
    }

    /**
     * Get fake data of PersonalizationSeperation
     *
     * @param array $postFields
     * @return array
     */
    public function fakePersonalizationSeperationData($personalizationSeperationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'sep_personalizationid' => $fake->randomDigitNotNull,
            'sep_min' => $fake->randomDigitNotNull,
            'sep_max' => $fake->randomDigitNotNull,
            'sep_price' => $fake->randomDigitNotNull,
            'sep_show' => $fake->word
        ], $personalizationSeperationFields);
    }
}
