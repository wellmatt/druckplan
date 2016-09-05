<?php

use Faker\Factory as Faker;
use App\Models\MachineUnitPerHour;
use App\Repositories\MachineUnitPerHourRepository;

trait MakeMachineUnitPerHourTrait
{
    /**
     * Create fake instance of MachineUnitPerHour and save it in database
     *
     * @param array $machineUnitPerHourFields
     * @return MachineUnitPerHour
     */
    public function makeMachineUnitPerHour($machineUnitPerHourFields = [])
    {
        /** @var MachineUnitPerHourRepository $machineUnitPerHourRepo */
        $machineUnitPerHourRepo = App::make(MachineUnitPerHourRepository::class);
        $theme = $this->fakeMachineUnitPerHourData($machineUnitPerHourFields);
        return $machineUnitPerHourRepo->create($theme);
    }

    /**
     * Get fake instance of MachineUnitPerHour
     *
     * @param array $machineUnitPerHourFields
     * @return MachineUnitPerHour
     */
    public function fakeMachineUnitPerHour($machineUnitPerHourFields = [])
    {
        return new MachineUnitPerHour($this->fakeMachineUnitPerHourData($machineUnitPerHourFields));
    }

    /**
     * Get fake data of MachineUnitPerHour
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineUnitPerHourData($machineUnitPerHourFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'machine_id' => $fake->randomDigitNotNull,
            'units_from' => $fake->randomDigitNotNull,
            'units_amount' => $fake->randomDigitNotNull
        ], $machineUnitPerHourFields);
    }
}
