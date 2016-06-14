<?php
class Group {
   private $name;
   private $description;
   private $members = Array();
   private $rights;
   private $id = 0;
   private $db;
   
   const ORDER_ID = "id";
   const ORDER_NAME = "group_name";
   
   const RIGHT_URLAUB = 1; // Urlaub genehmigen
   const RIGHT_MACHINE_SELECTION = 2; // Maschinenauswahl anzeigen
   const RIGHT_DETAILED_CALCULATION = 4; // Ausführliche Kalkulation anzeigen
   const RIGHT_SEE_TARGETTIME = 8; // Darf Sollzeiten sehen
   const RIGHT_PARTS_EDIT = 16; // Darf Teilauftr#ge planen
   const RIGHT_ALL_CALENDAR = 32; // Darf alle Kalender bearbeiten
   const RIGHT_SEE_ALL_CALENDAR = 131072; // Darf alle Kalender einsehen
   const RIGHT_EDIT_BC = 64; // Darf Geschäftskontakte bearbeiten
   const RIGHT_DELETE_BC = 128; // Darf Geschäftskontakte löschen
   const RIGHT_EDIT_CP = 256; // Darf Geschäftskontakte bearbeiten
   const RIGHT_DELETE_CP = 512; // Darf Geschäftskontakte löschen
   const RIGHT_DELETE_SCHEDULE = 1024;
   const RIGHT_DELETE_ORDER = 2048;
   const RIGHT_DELETE_COLINV = 4096;
   const RIGHT_COMBINE_COLINV = 8192;
   const RIGHT_TICKET_CHANGE_OWNER = 16384;
   const RIGHT_ASSO_DELETE = 32768;
   const RIGHT_NOTES_BC = 65536;
   const RIGHT_APPROVE_VACATION = 262144;
   const RIGHT_TICKET_EDIT_INTERNAL = 524288;
   const RIGHT_TICKET_EDIT_OFFICAL = 1048576;
   
   function __construct($id = 0, $adduser = true)
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
            $this->rights = $res[0]["group_rights"];
            $this->description = $res[0]["group_description"];
            
            if($adduser)
            {
               $sql = " SELECT * FROM user_groups WHERE group_id = {$this->id}";
               if ($this->db->num_rows($sql) > 0)
               {
                  $res = $this->db->select($sql);
                  foreach ($res as $r)
                     $this->members[] = new User($r["user_id"], false);
               }
            }
         }
      }
   }
   
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
      
      $sql = "SELECT id FROM groups WHERE group_status = 1 " .$filter;

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
   
   function getId() {
      return $this->id;
   }
   
   function getName() {
      return $this->name;
   }
   
   function getDescription () {
      return $this->description;
   }
   
   function getMembers() {
      return $this->members;
   }
   
   function hasRight($r) {
      return $this->rights & $r;
   }
   
   function setName($val) {
      $this->name = $val;
   }
   
   function setDescription($val) {
      $this->description = $val;
   }
   
   function addMember($val) {
      $this->members[] = $val;
   }
   
   function delMember($val) {
      $new = Array();
      foreach ($this->members as $m)
      {
         if ($m->getId() != $val->getId())
         {
            $new[] = $m;
         }
      }
      $this->members = $new;
   }
   
   function setRight($r, $v)
   {
      if ($v == 1 || $v === true)
      {
         $this->rights |= $r;
      } else
      {
         $this->rights &= ~$r;
      }
   }
   
   function save() {
      if($this->id > 0)
      {
         $sql = " UPDATE groups SET 
                     group_name = '{$this->name}',
                     group_description = '{$this->description}',
                     group_rights = '{$this->rights}'
                  WHERE id = {$this->id}";
         $res = $this->db->no_result($sql);
         
         $sql = " DELETE FROM user_groups WHERE group_id = {$this->id}";
         $this->db->no_result($sql);
         
         foreach ($this->members as $m)
         {
            $sql = " INSERT INTO user_groups 
                        (user_id, group_id)
                     VALUES
                        ({$m->getId()}, {$this->id})";
            $this->db->no_result($sql);
         }
      } else
      {
         $sql = " INSERT INTO groups
                     (group_name, group_description, group_status, group_rights)
                  VALUES
                     ('{$this->name}', '{$this->description}', 1, '{$this->rights}')";
         $res = $this->db->no_result($sql);
//          echo $sql . "</br>";
         if ($res)
         {
            $sql = " SELECT max(id) id FROM groups";
            $thisid = $this->db->select($sql);
            $this->id = $thisid[0]["id"];
            //echo "NEW ID = ".$this->id;
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
 
}
?>