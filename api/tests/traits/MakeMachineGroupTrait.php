<?php

use Faker\Factory as Faker;
use App\Models\MachineGroup;
use App\Repositories\MachineGroupRepository;

trait MakeMachineGroupTrait
{
    /**
     * Create fake instance of MachineGroup and save it in database
     *
     * @param array $machineGroupFields
     * @return MachineGroup
     */
    public function makeMachineGroup($machineGroupFields = [])
    {
        /** @var MachineGroupRepository $machineGroupRepo */
        $machineGroupRepo = App::make(MachineGroupRepository::class);
        $theme = $this->fakeMachineGroupData($machineGroupFields);
        return $machineGroupRepo->create($theme);
    }

    /**
     * Get fake instance of MachineGroup
     *
     * @param array $machineGroupFields
     * @return MachineGroup
     */
    public function fakeMachineGroup($machineGroupFields = [])
    {
        return new MachineGroup($this->fakeMachineGroupData($machineGroupFields));
    }

    /**
     * Get fake data of MachineGroup
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineGroupData($machineGroupFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'position' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'lector_id' => $fake->randomDigitNotNull
        ], $machineGroupFields);
    }
}
