<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';


Class GroupUser extends Model{
    public $_table = 'group_users';

    public $group;
    public $user;


    protected function bootClasses()
    {
        $this->user = new User($this->user);
        $this->group = new Group($this->group);
    }


    /**
     * @param $group Group
     * @return GroupUser[]
     */
    public static function getGroupUsersForGroup(Group $group)
    {
        $ret = self::fetch([
            [
                "column" => "group",
                "value" => $group->getId()
            ]
        ]);
        return $ret;
    }


    /**
     * @param $group Group
     * @return User[]
     */
    public static function getUsersForGroup(Group $group)
    {
        $users = [];
        $ret = self::fetch([
            [
                "column" => "`group`",
                "value" => $group->getId()
            ]
        ]);
        foreach ($ret as $item) {
            if ($item->getId() > 0){
                $users[] = $item->getUser();
            }
        }
        return $users;
    }


    /**
     * @param $user User
     * @return User[]
     */
    public static function getGroupsForUser(User $user)
    {
        $groups = [];
        $ret = self::fetch([
            [
                "column" => "user",
                "value" => $user->getId()
            ]
        ]);
        foreach ($ret as $item) {
            if ($item->getId() > 0){
                $groups[] = $item->getGroup();
            }
        }
        return $groups;
    }

    /**
     * @param Group $group
     * @return array
     */
    public static function getUserIdsForGroup(Group $group)
    {
        $ids = [];
        $users = self::getUsersForGroup($group);
        foreach ($users as $user) {
            $ids[] = $user->getId();
        }
        return $ids;
    }

    /**
     * @param User $user
     * @return array
     */
    public static function getGroupIdsForUser(User $user)
    {
        $ids = [];
        $groups = self::getGroupsForUser($user);
        foreach ($groups as $group) {
            $ids[] = $group->getId();
        }
        return $ids;
    }


    /**
     * @param $group Group
     */
    public static function wipeForGroup(Group $group)
    {
        $ret = self::fetch([
            [
                "column" => "`group`",
                "value" => $group->getId()
            ]
        ]);
        foreach ($ret as $item) {
            $item->delete();
        }
    }


    /**
     * @param $user User
     */
    public static function wipeForUser(User $user)
    {
        $ret = self::fetch([
            [
                "column" => "user",
                "value" => $user->getId()
            ]
        ]);
        foreach ($ret as $item) {
            $item->delete();
        }
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}