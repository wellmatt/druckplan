<?php

use Faker\Factory as Faker;
use App\Models\Finishing;
use App\Repositories\FinishingRepository;

trait MakeFinishingTrait
{
    /**
     * Create fake instance of Finishing and save it in database
     *
     * @param array $finishingFields
     * @return Finishing
     */
    public function makeFinishing($finishingFields = [])
    {
        /** @var FinishingRepository $finishingRepo */
        $finishingRepo = App::make(FinishingRepository::class);
        $theme = $this->fakeFinishingData($finishingFields);
        return $finishingRepo->create($theme);
    }

    /**
     * Get fake instance of Finishing
     *
     * @param array $finishingFields
     * @return Finishing
     */
    public function fakeFinishing($finishingFields = [])
    {
        return new Finishing($this->fakeFinishingData($finishingFields));
    }

    /**
     * Get fake data of Finishing
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFinishingData($finishingFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'lector_id' => $fake->randomDigitNotNull,
            'status' => $fake->word,
            'name' => $fake->word,
            'beschreibung' => $fake->word,
            'kosten' => $fake->randomDigitNotNull
        ], $finishingFields);
    }
}
