<?
require_once("./config.php");
require_once("./libs/basic/mysql.php");
require_once("./libs/basic/debug.php");
require_once("./libs/basic/globalFunctions.php");
require_once("./libs/basic/user/user.class.php");
require_once("./libs/basic/groups/group.class.php");
require_once("./libs/basic/clients/client.class.php");
require_once("./libs/basic/translator/translator.class.php");
require_once("./libs/basic/countries/country.class.php");
require_once("./libs/basic/license/license.class.php");
require_once("./libs/modules/businesscontact/businesscontact.class.php");
//----------------------------------------------------------------------------------
//error_reporting($_CONFIG[$_CONFIG["_MODUS"]]["ERROR_REPORTING"]);

//----------------------------------------------------------------------------------
session_start();

//----------------------------------------------------------------------------------
session_start();

//----------------------------------------------------------------------------------
$DB = new DBMysql();
$DB->connect($_CONFIG->db);
$_DEBUG = new Debug();
$_LICENSE = new License();

$_REQUEST["hash"] =  trim(addslashes($_REQUEST["hash"]));
$_REQUEST["type"] = (int)$_REQUEST["type"];

if($_REQUEST["type"] == 1 || $_REQUEST["type"] == 0) {
    $sql = "SELECT * FROM ftpdownloads WHERE ftp_hash = '{$_REQUEST["hash"]}'";
    $download = $DB->select($sql);
    $download = $download[0];
    $filename = "ftp/".$_CONFIG[$_CONFIG["_MODUS"]]["FTP"]["CUSTOMER_DIR"]."/".$download["ftp_hash"].".zip";
} else if($_REQUEST["type"] == 2) {
    $sql = "SELECT * FROM ftpcustuploads WHERE ftp_hash = '{$_REQUEST["hash"]}'";
    $download = $DB->select($sql);
    $download = $download[0];
    $fileext = explode(".", $download["ftp_orgname"]);
    $fileext = $fileext[count($fileext) - 1];
    $filename = "ftp/cust_uploads/".$download["ftp_hash"].".".$fileext;
} else
    die("Ungültiger Parameter");

if(file_exists($filename))
{
   if($_REQUEST["type"] == 1 || $_REQUEST["type"] == 0)
   { 
       if (substr($download["ftp_orgname"], -3) != "zip")
          $download_filename = $download["ftp_orgname"].".zip";
       else
          $download_filename = $download["ftp_orgname"];
   } else
       $download_filename = $download["ftp_orgname"];
   
   header("Content-Type: {$_REQUEST["mime"]}");
   header("Content-disposition: attachment; filename=\"{$download_filename}\"");
   header('Expires: 0');
   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
   header('Pragma: no-cache');
   header('Content-Length: ' . filesize($filename));

   ob_clean();
   flush();
   if(file_exists($filename))
      readfile($filename);

   if($_REQUEST["type"] == 1 || $_REQUEST["type"] == 0)
   {
       $sql = "UPDATE ftpdownloads SET ftp_status = UNIX_TIMESTAMP() WHERE ftp_hash = '{$_REQUEST["hash"]}'";
       $DB->no_result($sql);
   }
} else {
   echo "Die angegebene Datei existiert nicht.<br>";
   echo $filename;
}
?>