<?php

use Faker\Factory as Faker;
use App\Models\StoragePosition;
use App\Repositories\StoragePositionRepository;

trait MakeStoragePositionTrait
{
    /**
     * Create fake instance of StoragePosition and save it in database
     *
     * @param array $storagePositionFields
     * @return StoragePosition
     */
    public function makeStoragePosition($storagePositionFields = [])
    {
        /** @var StoragePositionRepository $storagePositionRepo */
        $storagePositionRepo = App::make(StoragePositionRepository::class);
        $theme = $this->fakeStoragePositionData($storagePositionFields);
        return $storagePositionRepo->create($theme);
    }

    /**
     * Get fake instance of StoragePosition
     *
     * @param array $storagePositionFields
     * @return StoragePosition
     */
    public function fakeStoragePosition($storagePositionFields = [])
    {
        return new StoragePosition($this->fakeStoragePositionData($storagePositionFields));
    }

    /**
     * Get fake data of StoragePosition
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStoragePositionData($storagePositionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'area' => $fake->randomDigitNotNull,
            'article' => $fake->randomDigitNotNull,
            'businesscontact' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull,
            'min_amount' => $fake->randomDigitNotNull,
            'respuser' => $fake->randomDigitNotNull,
            'description' => $fake->text,
            'note' => $fake->text,
            'dispatch' => $fake->word,
            'packaging' => $fake->word,
            'allocation' => $fake->randomDigitNotNull
        ], $storagePositionFields);
    }
}
