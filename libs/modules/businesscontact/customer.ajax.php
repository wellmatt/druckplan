<?
require_once("../classes/menu.php");
require_once("../classes/page.php");
require_once("../classes/mysql.php");
require_once("../config.php");
//----------------------------------------------------------------------------------
error_reporting($_CONFIG[$_CONFIG["_MODUS"]]["ERROR_REPORTING"]);

//----------------------------------------------------------------------------------
session_start();

//----------------------------------------------------------------------------------
$CON = new CMYSQL($_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["NAME"],
                  $_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["HOST"],
                  $_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["USER"],
                  $_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["PASS"]);
$CON->connect();

$_REQUEST["giveme"]     = trim(addslashes($_REQUEST["giveme"]));
$_REQUEST["cid"]        = (int)$_REQUEST["cid"];

if ($_REQUEST["giveme"] == "emaillist")
{
   $addr_vorhanden = false;
   $sql = " SELECT cust_email, cust_company, cust_firstname, cust_lastname
            FROM customer WHERE id = {$_REQUEST["cid"]}";
   $contacts = $CON->select($sql);
   
   if (trim($contacts[0]["cust_email"]) != "")
   {
      echo '<input type="checkbox" name="mails[]" value="'.$contacts[0]["cust_email"].'">'.$contacts[0]["cust_company"].' &lt;'.$contacts[0]["cust_email"].'&gt;<br>';
      $addr_vorhanden = true;
   }
   
   $sql = " SELECT add_email, add_firstname, add_lastname
            FROM customer_contacts WHERE add_cust_id = {$_REQUEST["cid"]}";
   $contacts = $CON->select($sql);
   
   foreach ($contacts as $contact)
   {
      echo '<input type="checkbox" name="mails[]" value="'.$contact["add_email"].'">'.$contact["add_firstname"].' '.$contact["add_lastname"].' &lt;'.$contact["add_email"].'&gt;<br>';
      $addr_vorhanden = true;
   }
   if ($addr_vorhanden == false)
      echo "Keine E-Mailadressen f&uuml;r diesen Kunden hinterlegt";
}


if ($_REQUEST["giveme"] == "confidence"){
	
	$sql = " select conf_step
            from user_customer
            where
            user_id = {$_SESSION['user_id']} AND
            cust_id = {$_REQUEST['cid']}";
     $conf = $CON->select($sql);
     $conf = $conf[0]['conf_step'];
     if($conf!=0) echo $conf;
     else echo  4;
   

}
?>