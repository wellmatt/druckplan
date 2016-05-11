<?php
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'thirdparty/phpfastcache/phpfastcache.php';

class DBMysql {
   private $conn = FALSE;
   private $dbhost = "";
   private $dbname = "";
   private $dbpass = "";
   private $dbuser = "";
   
   // Constructor
   function __construct() {
   }
   
   // connect to mysql
   function connect($conf) {
      $this->dbhost = $conf->host;
      $this->dbuser = $conf->user;
      $this->dbpass = $conf->pass;
      $this->dbname = $conf->name;
      
//      $this->conn = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass);
      $this->conn = mysqli_connect($this->dbhost,$this->dbuser,$this->dbpass);
      if ($this->conn)
         mysqli_select_db($this->conn,$this->dbname);
//         mysql_select_db($this->dbname, $this->conn);
      else
         return false;
      
//      mysql_set_charset("utf8", $this->conn);
      mysqli_set_charset($this->conn,'utf8');
      
      return true;
   }
   
   // Disconnect
   function disconnect() {
      mysqli_close($this->conn);
//      mysql_close($this->conn);
      $this->conn = false;
      
      return false;
   }
   
   // select-anweisung
   function select($sql) {
//      echo $sql . "</br>";
      $retval = Array();
//      $res = mysql_query($sql, $this->conn);
      $res = mysqli_query($this->conn,$sql);
      if ($res) {
//         while ($data = mysql_fetch_assoc($res))
         while ($data = mysqli_fetch_assoc($res))
         {
            $retval[] = $data;
         }
         return $retval;
      } else
         return false;
   }
   
   // falls keine Daten ausgelesen werden
   function no_result($sql) {
//      echo $sql . "</br>";
//      $res = mysql_query($sql);
      $res = mysqli_query($this->conn,$sql);
      if ($res)
         return true;
      else
         return false;
   }
   
   // num_rows
   function num_rows($sql) {
//      echo $sql . "</br>";
//      $res = mysql_query($sql, $this->conn);
      $res = mysqli_query($this->conn,$sql);
      if ($res)
//         return mysql_num_rows($res);
         return mysqli_num_rows($res);
      else
         return 0;
   }
   
   // get last error 
   function getLastError()
   {
//      return mysql_error();
      return mysqli_error($this->conn);
   }
   
}