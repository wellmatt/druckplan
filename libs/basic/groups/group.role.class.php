<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';


Class GroupRole extends Model{
    public $_table = 'group_roles';

    public $group;
    public $role;


    protected function bootClasses()
    {
        $this->role = new Role($this->role);
        $this->group = new Group($this->group);
    }


    /**
     * @param $group Group
     * @return GroupRole[]
     */
    public static function getGroupRolesForGroup(Group $group)
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
     * @return Role[]
     */
    public static function getRolesForGroup(Group $group)
    {
        $roles = [];
        $ret = self::fetch([
            [
                "column" => "`group`",
                "value" => $group->getId()
            ]
        ]);
        foreach ($ret as $item) {
            if ($item->getId() > 0){
                $roles[] = $item->getRole();
            }
        }
        return $roles;
    }

    /**
     * @param Group $group
     * @return array
     */
    public static function getRoleIdsForGroup(Group $group)
    {
        $ids = [];
        $roles = self::getRolesForGroup($group);
        foreach ($roles as $role) {
            $ids[] = $role->getId();
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
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}