<?php

use Faker\Factory as Faker;
use App\Models\MachineQualifiedUser;
use App\Repositories\MachineQualifiedUserRepository;

trait MakeMachineQualifiedUserTrait
{
    /**
     * Create fake instance of MachineQualifiedUser and save it in database
     *
     * @param array $machineQualifiedUserFields
     * @return MachineQualifiedUser
     */
    public function makeMachineQualifiedUser($machineQualifiedUserFields = [])
    {
        /** @var MachineQualifiedUserRepository $machineQualifiedUserRepo */
        $machineQualifiedUserRepo = App::make(MachineQualifiedUserRepository::class);
        $theme = $this->fakeMachineQualifiedUserData($machineQualifiedUserFields);
        return $machineQualifiedUserRepo->create($theme);
    }

    /**
     * Get fake instance of MachineQualifiedUser
     *
     * @param array $machineQualifiedUserFields
     * @return MachineQualifiedUser
     */
    public function fakeMachineQualifiedUser($machineQualifiedUserFields = [])
    {
        return new MachineQualifiedUser($this->fakeMachineQualifiedUserData($machineQualifiedUserFields));
    }

    /**
     * Get fake data of MachineQualifiedUser
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineQualifiedUserData($machineQualifiedUserFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'machine' => $fake->randomDigitNotNull,
            'user' => $fake->randomDigitNotNull
        ], $machineQualifiedUserFields);
    }
}
