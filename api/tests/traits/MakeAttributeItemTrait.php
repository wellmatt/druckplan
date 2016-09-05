<?php

use Faker\Factory as Faker;
use App\Models\AttributeItem;
use App\Repositories\AttributeItemRepository;

trait MakeAttributeItemTrait
{
    /**
     * Create fake instance of AttributeItem and save it in database
     *
     * @param array $attributeItemFields
     * @return AttributeItem
     */
    public function makeAttributeItem($attributeItemFields = [])
    {
        /** @var AttributeItemRepository $attributeItemRepo */
        $attributeItemRepo = App::make(AttributeItemRepository::class);
        $theme = $this->fakeAttributeItemData($attributeItemFields);
        return $attributeItemRepo->create($theme);
    }

    /**
     * Get fake instance of AttributeItem
     *
     * @param array $attributeItemFields
     * @return AttributeItem
     */
    public function fakeAttributeItem($attributeItemFields = [])
    {
        return new AttributeItem($this->fakeAttributeItemData($attributeItemFields));
    }

    /**
     * Get fake data of AttributeItem
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAttributeItemData($attributeItemFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'attribute_id' => $fake->word,
            'title' => $fake->word,
            'input' => $fake->word
        ], $attributeItemFields);
    }
}
