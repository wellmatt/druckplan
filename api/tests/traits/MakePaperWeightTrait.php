<?php

use Faker\Factory as Faker;
use App\Models\PaperWeight;
use App\Repositories\PaperWeightRepository;

trait MakePaperWeightTrait
{
    /**
     * Create fake instance of PaperWeight and save it in database
     *
     * @param array $paperWeightFields
     * @return PaperWeight
     */
    public function makePaperWeight($paperWeightFields = [])
    {
        /** @var PaperWeightRepository $paperWeightRepo */
        $paperWeightRepo = App::make(PaperWeightRepository::class);
        $theme = $this->fakePaperWeightData($paperWeightFields);
        return $paperWeightRepo->create($theme);
    }

    /**
     * Get fake instance of PaperWeight
     *
     * @param array $paperWeightFields
     * @return PaperWeight
     */
    public function fakePaperWeight($paperWeightFields = [])
    {
        return new PaperWeight($this->fakePaperWeightData($paperWeightFields));
    }

    /**
     * Get fake data of PaperWeight
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaperWeightData($paperWeightFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paper_id' => $fake->randomDigitNotNull,
            'weight' => $fake->word
        ], $paperWeightFields);
    }
}
