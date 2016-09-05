<?php

use Faker\Factory as Faker;
use App\Models\PersonalizationOrderItem;
use App\Repositories\PersonalizationOrderItemRepository;

trait MakePersonalizationOrderItemTrait
{
    /**
     * Create fake instance of PersonalizationOrderItem and save it in database
     *
     * @param array $personalizationOrderItemFields
     * @return PersonalizationOrderItem
     */
    public function makePersonalizationOrderItem($personalizationOrderItemFields = [])
    {
        /** @var PersonalizationOrderItemRepository $personalizationOrderItemRepo */
        $personalizationOrderItemRepo = App::make(PersonalizationOrderItemRepository::class);
        $theme = $this->fakePersonalizationOrderItemData($personalizationOrderItemFields);
        return $personalizationOrderItemRepo->create($theme);
    }

    /**
     * Get fake instance of PersonalizationOrderItem
     *
     * @param array $personalizationOrderItemFields
     * @return PersonalizationOrderItem
     */
    public function fakePersonalizationOrderItem($personalizationOrderItemFields = [])
    {
        return new PersonalizationOrderItem($this->fakePersonalizationOrderItemData($personalizationOrderItemFields));
    }

    /**
     * Get fake data of PersonalizationOrderItem
     *
     * @param array $postFields
     * @return array
     */
    public function fakePersonalizationOrderItemData($personalizationOrderItemFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'persoid' => $fake->randomDigitNotNull,
            'persoorderid' => $fake->randomDigitNotNull,
            'persoitemid' => $fake->randomDigitNotNull,
            'value' => $fake->text
        ], $personalizationOrderItemFields);
    }
}
