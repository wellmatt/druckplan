<?php

use Faker\Factory as Faker;
use App\Models\MachineWorkTime;
use App\Repositories\MachineWorkTimeRepository;

trait MakeMachineWorkTimeTrait
{
    /**
     * Create fake instance of MachineWorkTime and save it in database
     *
     * @param array $machineWorkTimeFields
     * @return MachineWorkTime
     */
    public function makeMachineWorkTime($machineWorkTimeFields = [])
    {
        /** @var MachineWorkTimeRepository $machineWorkTimeRepo */
        $machineWorkTimeRepo = App::make(MachineWorkTimeRepository::class);
        $theme = $this->fakeMachineWorkTimeData($machineWorkTimeFields);
        return $machineWorkTimeRepo->create($theme);
    }

    /**
     * Get fake instance of MachineWorkTime
     *
     * @param array $machineWorkTimeFields
     * @return MachineWorkTime
     */
    public function fakeMachineWorkTime($machineWorkTimeFields = [])
    {
        return new MachineWorkTime($this->fakeMachineWorkTimeData($machineWorkTimeFields));
    }

    /**
     * Get fake data of MachineWorkTime
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineWorkTimeData($machineWorkTimeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'machine' => $fake->randomDigitNotNull,
            'weekday' => $fake->randomDigitNotNull,
            'start' => $fake->randomDigitNotNull,
            'end' => $fake->randomDigitNotNull
        ], $machineWorkTimeFields);
    }
}
