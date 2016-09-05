<?php

use Faker\Factory as Faker;
use App\Models\PersonalizationItem;
use App\Repositories\PersonalizationItemRepository;

trait MakePersonalizationItemTrait
{
    /**
     * Create fake instance of PersonalizationItem and save it in database
     *
     * @param array $personalizationItemFields
     * @return PersonalizationItem
     */
    public function makePersonalizationItem($personalizationItemFields = [])
    {
        /** @var PersonalizationItemRepository $personalizationItemRepo */
        $personalizationItemRepo = App::make(PersonalizationItemRepository::class);
        $theme = $this->fakePersonalizationItemData($personalizationItemFields);
        return $personalizationItemRepo->create($theme);
    }

    /**
     * Get fake instance of PersonalizationItem
     *
     * @param array $personalizationItemFields
     * @return PersonalizationItem
     */
    public function fakePersonalizationItem($personalizationItemFields = [])
    {
        return new PersonalizationItem($this->fakePersonalizationItemData($personalizationItemFields));
    }

    /**
     * Get fake data of PersonalizationItem
     *
     * @param array $postFields
     * @return array
     */
    public function fakePersonalizationItemData($personalizationItemFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'title' => $fake->word,
            'xpos' => $fake->randomDigitNotNull,
            'ypos' => $fake->randomDigitNotNull,
            'height' => $fake->randomDigitNotNull,
            'width' => $fake->randomDigitNotNull,
            'boxtype' => $fake->randomDigitNotNull,
            'personalization_id' => $fake->randomDigitNotNull,
            'text_size' => $fake->randomDigitNotNull,
            'justification' => $fake->word,
            'font' => $fake->word,
            'color_c' => $fake->word,
            'color_m' => $fake->word,
            'color_y' => $fake->word,
            'color_k' => $fake->word,
            'spacing' => $fake->randomDigitNotNull,
            'dependency_id' => $fake->randomDigitNotNull,
            'reverse' => $fake->word,
            'predefined' => $fake->word,
            'position' => $fake->word,
            'readonly' => $fake->word,
            'tab' => $fake->randomDigitNotNull,
            'zzgroup' => $fake->word,
            'sort' => $fake->randomDigitNotNull
        ], $personalizationItemFields);
    }
}
