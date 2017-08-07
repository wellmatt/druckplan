<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';


class Role extends Model{
    public $_table = 'roles';

    public $name = '';
    public $description = '';
    public $_permissions = [];


    /**
     * @return Permission[]
     */
    public function getPermissions()
    {
        if (count($this->_permissions) > 0)
            return $this->_permissions;
        else {
            $perms = RolePermission::getPermissionsForRole($this);
            $this->_permissions = $perms;
            return $this->_permissions;
        }
    }

    /**
     * @return string[]
     */
    public function getSlugs()
    {
        if (count($this->_permissions) == 0)
            $this->getPermissions();
        $slugs = [];
        foreach ($this->_permissions as $permission) {
            $slugs[] = $permission->getSlug();
        }
        return $slugs;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}