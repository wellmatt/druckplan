<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'permission.class.php';
require_once 'role.permission.class.php';
require_once 'role.class.php';
require_once 'group.role.class.php';
require_once 'group.user.class.php';

class Group {
    private $name;
    private $description;
    private $id = 0;
    private $db;
    private $_rights = [];
    private $_rights_slugs = [];

    const ORDER_ID = "id";
    const ORDER_NAME = "group_name";

    function __construct($id = 0)
    {
      global $DB;
      $this->db = $DB;

      if ($id > 0)
      {
         $sql = " SELECT * FROM groups WHERE id = {$id} AND group_status = 1";
         if ($this->db->num_rows($sql))
         {
            $res = $this->db->select($sql);
            $this->id = $res[0]["id"];
            $this->name = $res[0]["group_name"];
            $this->description = $res[0]["group_description"];
         }
      }
    }

    /**
    * @param int $order
    * @param int $client
    * @return Group[]
    */
    static function getAllGroups($order = 1, $client = 0) {
      global $DB;
      $groups = Array();

      $sql = "SELECT id FROM groups WHERE group_status = 1";
      if ($client > 0)
         $sql .= " AND client = {$client}";
      $sql .= " ORDER BY {$order}";

      if ($DB->num_rows($sql))
      {
         $res = $DB->select($sql);
         foreach ($res as $r)
         {
            $groups[] = new Group($r["id"]);
         }
      }
      return $groups;
    }

    static function getAllGroupsFiltered($filter = null) {
      global $DB;
      $groups = Array();

      $sql = "SELECT id FROM groups WHERE group_status = 1 " . $filter;

      if ($DB->num_rows($sql))
      {
         $res = $DB->select($sql);
         foreach ($res as $r)
         {
            $groups[] = new Group($r["id"]);
         }
      }
      return $groups;
    }

    /**
    * @param User $user
    * @return bool
    */
    public function hasMember(User $user)
    {
        $ids = GroupUser::getUserIdsForGroup($this);
        if (in_array($user->getId(),$ids))
            return true;
        else
            return false;
    }

    /**
     * @return Permission[]
     */
    public function getAllRights()
    {
        if (count($this->_rights)>0)
            return $this->_rights;
        else {
            $rightids = [];
            $rights = [];
            $slugs = [];
            $roles = GroupRole::getRolesForGroup($this);
            foreach ($roles as $role) {
                $perms = RolePermission::getPermissionsForRole($role);
                foreach ($perms as $perm) {
                    if (!in_array($perm->getId(),$rightids)){
                        $rightids[] = $perm->getId();
                        $rights[] = $perm;
                        $slugs[] = $perm->getSlug();
                    }
                }
            }
            $this->_rights = $rights;
            $this->_rights_slugs = $slugs;
            return $this->_rights;
        }
    }


    function save() {
        if($this->id > 0)
        {
            $sql = " UPDATE groups SET 
                     group_name = '{$this->name}',
                     group_description = '{$this->description}' 
                  WHERE id = {$this->id}";
            $res = $this->db->no_result($sql);
        } else
        {
            $sql = " INSERT INTO groups
                     (group_name, group_description, group_status)
                  VALUES
                     ('{$this->name}', '{$this->description}', 1)";
            $res = $this->db->no_result($sql);
            if ($res)
            {
                $sql = " SELECT max(id) id FROM groups";
                $thisid = $this->db->select($sql);
                $this->id = $thisid[0]["id"];
            }
        }

        if ($res)
            return true;
        else
            return false;
    }

    function delete() {
        $sql = "UPDATE groups SET group_status = 0 WHERE id = {$this->id}";
        $res = $this->db->no_result($sql);
        unset($this);
        if ($res)
            return true;
        else
            return false;
    }
   
    function getId() {
      return $this->id;
    }

    function getName() {
      return $this->name;
    }

    function getDescription () {
      return $this->description;
    }

    /**
    * @return User[]
    */
    function getMembers() {
      return GroupUser::getUsersForGroup($this);
    }

    function setName($val) {
      $this->name = $val;
    }

    function setDescription($val) {
      $this->description = $val;
    }
}