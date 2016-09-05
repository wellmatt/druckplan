<?php

use Faker\Factory as Faker;
use App\Models\PartsListItem;
use App\Repositories\PartsListItemRepository;

trait MakePartsListItemTrait
{
    /**
     * Create fake instance of PartsListItem and save it in database
     *
     * @param array $partsListItemFields
     * @return PartsListItem
     */
    public function makePartsListItem($partsListItemFields = [])
    {
        /** @var PartsListItemRepository $partsListItemRepo */
        $partsListItemRepo = App::make(PartsListItemRepository::class);
        $theme = $this->fakePartsListItemData($partsListItemFields);
        return $partsListItemRepo->create($theme);
    }

    /**
     * Get fake instance of PartsListItem
     *
     * @param array $partsListItemFields
     * @return PartsListItem
     */
    public function fakePartsListItem($partsListItemFields = [])
    {
        return new PartsListItem($this->fakePartsListItemData($partsListItemFields));
    }

    /**
     * Get fake data of PartsListItem
     *
     * @param array $postFields
     * @return array
     */
    public function fakePartsListItemData($partsListItemFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'partslist' => $fake->randomDigitNotNull,
            'article' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull
        ], $partsListItemFields);
    }
}
