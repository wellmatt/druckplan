<?php

use Faker\Factory as Faker;
use App\Models\StorageBookEntry;
use App\Repositories\StorageBookEntryRepository;

trait MakeStorageBookEntryTrait
{
    /**
     * Create fake instance of StorageBookEntry and save it in database
     *
     * @param array $storageBookEntryFields
     * @return StorageBookEntry
     */
    public function makeStorageBookEntry($storageBookEntryFields = [])
    {
        /** @var StorageBookEntryRepository $storageBookEntryRepo */
        $storageBookEntryRepo = App::make(StorageBookEntryRepository::class);
        $theme = $this->fakeStorageBookEntryData($storageBookEntryFields);
        return $storageBookEntryRepo->create($theme);
    }

    /**
     * Get fake instance of StorageBookEntry
     *
     * @param array $storageBookEntryFields
     * @return StorageBookEntry
     */
    public function fakeStorageBookEntry($storageBookEntryFields = [])
    {
        return new StorageBookEntry($this->fakeStorageBookEntryData($storageBookEntryFields));
    }

    /**
     * Get fake data of StorageBookEntry
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStorageBookEntryData($storageBookEntryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'area' => $fake->randomDigitNotNull,
            'article' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'origin' => $fake->randomDigitNotNull,
            'origin_pos' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull,
            'alloc' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull
        ], $storageBookEntryFields);
    }
}
