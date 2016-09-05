<?php

use Faker\Factory as Faker;
use App\Models\MachineDifficulty;
use App\Repositories\MachineDifficultyRepository;

trait MakeMachineDifficultyTrait
{
    /**
     * Create fake instance of MachineDifficulty and save it in database
     *
     * @param array $machineDifficultyFields
     * @return MachineDifficulty
     */
    public function makeMachineDifficulty($machineDifficultyFields = [])
    {
        /** @var MachineDifficultyRepository $machineDifficultyRepo */
        $machineDifficultyRepo = App::make(MachineDifficultyRepository::class);
        $theme = $this->fakeMachineDifficultyData($machineDifficultyFields);
        return $machineDifficultyRepo->create($theme);
    }

    /**
     * Get fake instance of MachineDifficulty
     *
     * @param array $machineDifficultyFields
     * @return MachineDifficulty
     */
    public function fakeMachineDifficulty($machineDifficultyFields = [])
    {
        return new MachineDifficulty($this->fakeMachineDifficultyData($machineDifficultyFields));
    }

    /**
     * Get fake data of MachineDifficulty
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineDifficultyData($machineDifficultyFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'machine_id' => $fake->randomDigitNotNull,
            'diff_id' => $fake->randomDigitNotNull,
            'diff_unit' => $fake->randomDigitNotNull,
            'value' => $fake->randomDigitNotNull,
            'percent' => $fake->randomDigitNotNull
        ], $machineDifficultyFields);
    }
}
