<?php

use Faker\Factory as Faker;
use App\Models\StorageGood;
use App\Repositories\StorageGoodRepository;

trait MakeStorageGoodTrait
{
    /**
     * Create fake instance of StorageGood and save it in database
     *
     * @param array $storageGoodFields
     * @return StorageGood
     */
    public function makeStorageGood($storageGoodFields = [])
    {
        /** @var StorageGoodRepository $storageGoodRepo */
        $storageGoodRepo = App::make(StorageGoodRepository::class);
        $theme = $this->fakeStorageGoodData($storageGoodFields);
        return $storageGoodRepo->create($theme);
    }

    /**
     * Get fake instance of StorageGood
     *
     * @param array $storageGoodFields
     * @return StorageGood
     */
    public function fakeStorageGood($storageGoodFields = [])
    {
        return new StorageGood($this->fakeStorageGoodData($storageGoodFields));
    }

    /**
     * Get fake data of StorageGood
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStorageGoodData($storageGoodFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'origin' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull
        ], $storageGoodFields);
    }
}
