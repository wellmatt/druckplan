<?php

use Faker\Factory as Faker;
use App\Models\BusinesscontactAttribute;
use App\Repositories\BusinesscontactAttributeRepository;

trait MakeBusinesscontactAttributeTrait
{
    /**
     * Create fake instance of BusinesscontactAttribute and save it in database
     *
     * @param array $businesscontactAttributeFields
     * @return BusinesscontactAttribute
     */
    public function makeBusinesscontactAttribute($businesscontactAttributeFields = [])
    {
        /** @var BusinesscontactAttributeRepository $businesscontactAttributeRepo */
        $businesscontactAttributeRepo = App::make(BusinesscontactAttributeRepository::class);
        $theme = $this->fakeBusinesscontactAttributeData($businesscontactAttributeFields);
        return $businesscontactAttributeRepo->create($theme);
    }

    /**
     * Get fake instance of BusinesscontactAttribute
     *
     * @param array $businesscontactAttributeFields
     * @return BusinesscontactAttribute
     */
    public function fakeBusinesscontactAttribute($businesscontactAttributeFields = [])
    {
        return new BusinesscontactAttribute($this->fakeBusinesscontactAttributeData($businesscontactAttributeFields));
    }

    /**
     * Get fake data of BusinesscontactAttribute
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBusinesscontactAttributeData($businesscontactAttributeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'businesscontact_id' => $fake->randomDigitNotNull,
            'attribute_id' => $fake->randomDigitNotNull,
            'item_id' => $fake->randomDigitNotNull,
            'value' => $fake->word,
            'inputvalue' => $fake->word
        ], $businesscontactAttributeFields);
    }
}
