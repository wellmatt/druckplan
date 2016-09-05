<?php

use Faker\Factory as Faker;
use App\Models\MachineLock;
use App\Repositories\MachineLockRepository;

trait MakeMachineLockTrait
{
    /**
     * Create fake instance of MachineLock and save it in database
     *
     * @param array $machineLockFields
     * @return MachineLock
     */
    public function makeMachineLock($machineLockFields = [])
    {
        /** @var MachineLockRepository $machineLockRepo */
        $machineLockRepo = App::make(MachineLockRepository::class);
        $theme = $this->fakeMachineLockData($machineLockFields);
        return $machineLockRepo->create($theme);
    }

    /**
     * Get fake instance of MachineLock
     *
     * @param array $machineLockFields
     * @return MachineLock
     */
    public function fakeMachineLock($machineLockFields = [])
    {
        return new MachineLock($this->fakeMachineLockData($machineLockFields));
    }

    /**
     * Get fake data of MachineLock
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineLockData($machineLockFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'machineid' => $fake->randomDigitNotNull,
            'start' => $fake->randomDigitNotNull,
            'stop' => $fake->randomDigitNotNull
        ], $machineLockFields);
    }
}
