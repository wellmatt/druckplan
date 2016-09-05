<?php

use Faker\Factory as Faker;
use App\Models\PartsList;
use App\Repositories\PartsListRepository;

trait MakePartsListTrait
{
    /**
     * Create fake instance of PartsList and save it in database
     *
     * @param array $partsListFields
     * @return PartsList
     */
    public function makePartsList($partsListFields = [])
    {
        /** @var PartsListRepository $partsListRepo */
        $partsListRepo = App::make(PartsListRepository::class);
        $theme = $this->fakePartsListData($partsListFields);
        return $partsListRepo->create($theme);
    }

    /**
     * Get fake instance of PartsList
     *
     * @param array $partsListFields
     * @return PartsList
     */
    public function fakePartsList($partsListFields = [])
    {
        return new PartsList($this->fakePartsListData($partsListFields));
    }

    /**
     * Get fake data of PartsList
     *
     * @param array $postFields
     * @return array
     */
    public function fakePartsListData($partsListFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'price' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull
        ], $partsListFields);
    }
}
