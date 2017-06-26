<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';


Class RolePermission extends Model{
    public $_table = 'role_permissions';

    public $role;
    public $permission;

    protected function bootClasses()
    {
        $this->role = new Role($this->role);
        $this->permission = new Permission($this->permission);
    }


    /**
     * @param $role Role
     * @return RolePermission[]
     */
    public static function getRolePermissionsForRole(Role $role)
    {
        $ret = self::fetch([
            [
                "column" => "role",
                "value" => $role->getId()
            ]
        ]);
        return $ret;
    }


    /**
     * @param $role Role
     * @return Permission[]
     */
    public static function getPermissionsForRole(Role $role)
    {
        $perms = [];
        $ret = self::fetch([
            [
                "column" => "role",
                "value" => $role->getId()
            ]
        ]);
        foreach ($ret as $item) {
            if ($item->getId() > 0){
                $perms[] = $item->getPermission();
            }
        }
        return $perms;
    }


    /**
     * @param $role Role
     */
    public static function wipeForRole(Role $role)
    {
        $ret = self::fetch([
            [
                "column" => "role",
                "value" => $role->getId()
            ]
        ]);
        foreach ($ret as $item) {
            $item->delete();
        }
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

    /**
     * @return Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param Permission $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }
}