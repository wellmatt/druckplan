<?php

use Faker\Factory as Faker;
use App\Models\MachineChromaticity;
use App\Repositories\MachineChromaticityRepository;

trait MakeMachineChromaticityTrait
{
    /**
     * Create fake instance of MachineChromaticity and save it in database
     *
     * @param array $machineChromaticityFields
     * @return MachineChromaticity
     */
    public function makeMachineChromaticity($machineChromaticityFields = [])
    {
        /** @var MachineChromaticityRepository $machineChromaticityRepo */
        $machineChromaticityRepo = App::make(MachineChromaticityRepository::class);
        $theme = $this->fakeMachineChromaticityData($machineChromaticityFields);
        return $machineChromaticityRepo->create($theme);
    }

    /**
     * Get fake instance of MachineChromaticity
     *
     * @param array $machineChromaticityFields
     * @return MachineChromaticity
     */
    public function fakeMachineChromaticity($machineChromaticityFields = [])
    {
        return new MachineChromaticity($this->fakeMachineChromaticityData($machineChromaticityFields));
    }

    /**
     * Get fake data of MachineChromaticity
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineChromaticityData($machineChromaticityFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'machine_id' => $fake->randomDigitNotNull,
            'chroma_id' => $fake->randomDigitNotNull,
            'clickprice' => $fake->randomDigitNotNull
        ], $machineChromaticityFields);
    }
}
