<?php

use Faker\Factory as Faker;
use App\Models\StorageArea;
use App\Repositories\StorageAreaRepository;

trait MakeStorageAreaTrait
{
    /**
     * Create fake instance of StorageArea and save it in database
     *
     * @param array $storageAreaFields
     * @return StorageArea
     */
    public function makeStorageArea($storageAreaFields = [])
    {
        /** @var StorageAreaRepository $storageAreaRepo */
        $storageAreaRepo = App::make(StorageAreaRepository::class);
        $theme = $this->fakeStorageAreaData($storageAreaFields);
        return $storageAreaRepo->create($theme);
    }

    /**
     * Get fake instance of StorageArea
     *
     * @param array $storageAreaFields
     * @return StorageArea
     */
    public function fakeStorageArea($storageAreaFields = [])
    {
        return new StorageArea($this->fakeStorageAreaData($storageAreaFields));
    }

    /**
     * Get fake data of StorageArea
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStorageAreaData($storageAreaFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'description' => $fake->text,
            'location' => $fake->word,
            'corridor' => $fake->word,
            'shelf' => $fake->word,
            'line' => $fake->word,
            'layer' => $fake->word,
            'prio' => $fake->word
        ], $storageAreaFields);
    }
}
