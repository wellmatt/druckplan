<?
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

$_REQUEST["id"] = (int)$_REQUEST["id"];
$_REQUEST["display"] = (int)$_REQUEST["display"];

if($_REQUEST["display"] == 1)
{
    $sql = "INSERT INTO menu_status
                (user_id, menu_elements_id, display)
            VALUES
                ({$_USER->getId()}, {$_REQUEST["id"]}, 1)";
    $DB->no_result($sql);
} else 
{
    $sql = "DELETE FROM menu_status
            WHERE user_id = {$_USER->getId()}
                AND menu_elements_id = {$_REQUEST["id"]}";
    $DB->no_result($sql);
}
echo $sql;
echo $DB->getLastError();
?>