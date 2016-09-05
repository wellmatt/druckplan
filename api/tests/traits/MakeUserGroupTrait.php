<?php

use Faker\Factory as Faker;
use App\Models\UserGroup;
use App\Repositories\UserGroupRepository;

trait MakeUserGroupTrait
{
    /**
     * Create fake instance of UserGroup and save it in database
     *
     * @param array $userGroupFields
     * @return UserGroup
     */
    public function makeUserGroup($userGroupFields = [])
    {
        /** @var UserGroupRepository $userGroupRepo */
        $userGroupRepo = App::make(UserGroupRepository::class);
        $theme = $this->fakeUserGroupData($userGroupFields);
        return $userGroupRepo->create($theme);
    }

    /**
     * Get fake instance of UserGroup
     *
     * @param array $userGroupFields
     * @return UserGroup
     */
    public function fakeUserGroup($userGroupFields = [])
    {
        return new UserGroup($this->fakeUserGroupData($userGroupFields));
    }

    /**
     * Get fake data of UserGroup
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUserGroupData($userGroupFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->randomDigitNotNull,
            'group_id' => $fake->randomDigitNotNull
        ], $userGroupFields);
    }
}
