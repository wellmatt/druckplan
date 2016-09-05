<?php

use Faker\Factory as Faker;
use App\Models\Chromaticity;
use App\Repositories\ChromaticityRepository;

trait MakeChromaticityTrait
{
    /**
     * Create fake instance of Chromaticity and save it in database
     *
     * @param array $chromaticityFields
     * @return Chromaticity
     */
    public function makeChromaticity($chromaticityFields = [])
    {
        /** @var ChromaticityRepository $chromaticityRepo */
        $chromaticityRepo = App::make(ChromaticityRepository::class);
        $theme = $this->fakeChromaticityData($chromaticityFields);
        return $chromaticityRepo->create($theme);
    }

    /**
     * Get fake instance of Chromaticity
     *
     * @param array $chromaticityFields
     * @return Chromaticity
     */
    public function fakeChromaticity($chromaticityFields = [])
    {
        return new Chromaticity($this->fakeChromaticityData($chromaticityFields));
    }

    /**
     * Get fake data of Chromaticity
     *
     * @param array $postFields
     * @return array
     */
    public function fakeChromaticityData($chromaticityFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'colors_front' => $fake->word,
            'colors_back' => $fake->word,
            'reverse_printing' => $fake->word,
            'markup' => $fake->randomDigitNotNull,
            'pricekg' => $fake->randomDigitNotNull
        ], $chromaticityFields);
    }
}
