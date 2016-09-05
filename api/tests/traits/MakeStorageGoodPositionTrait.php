<?php

use Faker\Factory as Faker;
use App\Models\StorageGoodPosition;
use App\Repositories\StorageGoodPositionRepository;

trait MakeStorageGoodPositionTrait
{
    /**
     * Create fake instance of StorageGoodPosition and save it in database
     *
     * @param array $storageGoodPositionFields
     * @return StorageGoodPosition
     */
    public function makeStorageGoodPosition($storageGoodPositionFields = [])
    {
        /** @var StorageGoodPositionRepository $storageGoodPositionRepo */
        $storageGoodPositionRepo = App::make(StorageGoodPositionRepository::class);
        $theme = $this->fakeStorageGoodPositionData($storageGoodPositionFields);
        return $storageGoodPositionRepo->create($theme);
    }

    /**
     * Get fake instance of StorageGoodPosition
     *
     * @param array $storageGoodPositionFields
     * @return StorageGoodPosition
     */
    public function fakeStorageGoodPosition($storageGoodPositionFields = [])
    {
        return new StorageGoodPosition($this->fakeStorageGoodPositionData($storageGoodPositionFields));
    }

    /**
     * Get fake data of StorageGoodPosition
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStorageGoodPositionData($storageGoodPositionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'goods' => $fake->randomDigitNotNull,
            'article' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull
        ], $storageGoodPositionFields);
    }
}
