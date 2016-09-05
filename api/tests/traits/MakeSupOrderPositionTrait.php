<?php

use Faker\Factory as Faker;
use App\Models\SupOrderPosition;
use App\Repositories\SupOrderPositionRepository;

trait MakeSupOrderPositionTrait
{
    /**
     * Create fake instance of SupOrderPosition and save it in database
     *
     * @param array $supOrderPositionFields
     * @return SupOrderPosition
     */
    public function makeSupOrderPosition($supOrderPositionFields = [])
    {
        /** @var SupOrderPositionRepository $supOrderPositionRepo */
        $supOrderPositionRepo = App::make(SupOrderPositionRepository::class);
        $theme = $this->fakeSupOrderPositionData($supOrderPositionFields);
        return $supOrderPositionRepo->create($theme);
    }

    /**
     * Get fake instance of SupOrderPosition
     *
     * @param array $supOrderPositionFields
     * @return SupOrderPosition
     */
    public function fakeSupOrderPosition($supOrderPositionFields = [])
    {
        return new SupOrderPosition($this->fakeSupOrderPositionData($supOrderPositionFields));
    }

    /**
     * Get fake data of SupOrderPosition
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupOrderPositionData($supOrderPositionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'suporder' => $fake->randomDigitNotNull,
            'article' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull,
            'colinvoice' => $fake->randomDigitNotNull
        ], $supOrderPositionFields);
    }
}
