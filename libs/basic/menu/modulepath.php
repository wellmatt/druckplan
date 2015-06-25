<?
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false)
    die("Login failed");

$dir = "libs/modules/";
// Basic module
$modules = Array();

// Spezielle Module von Hand setzen
$modules[] = 'libs/basic/clients/client.php';
$modules[] = 'libs/basic/groups/groups.php';
$modules[] = 'libs/basic/menu/menuconfig.php';
$modules[] = 'libs/basic/user/user.php';
$modules[] = 'libs/basic/license/license.php';
$modules[] = 'libs/basic/countries/country.php';
$modules[] = 'libs/basic/memcache.admin.php';

$dh = opendir($dir);
while($subdir = readdir($dh))
{
    $dh2 = opendir($dir.$subdir);
    while ($files = readdir($dh2))
    {
        if (preg_match("/.+\.php/", $files) && !preg_match("/.+\.class\.php/", $files))
        {
            $modules[] = "libs/modules/".$subdir."/".$files;
        }
    }
    closedir($dh2);
}
closedir($dh);
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<script language="javascript" src="jscripts/basic.js"></script>

</head>
<body>
<table width="480">
<colgroup>
    <col width="20">
    <col>
    <col width="80">
</colgroup>
<tr>
    <td class="content_header" colspan="3"><?=$_LANG->get('Modul ausw&auml;hlen')?></td>
</tr>
<? foreach ($modules as $m) { ?>
<tr>
    <td class="content_row">&nbsp;</td>
    <td class="content_row"><?=$m?></td>
    <td class="content_row">
        <input type="button" class="button pointer" value="<?=$_LANG->get('Auswahl')?>"
            onclick="parent.document.getElementById('span_menu_path').innerHTML='<?=$m?>   ';parent.document.getElementById('menu_path').value='<?=$m?>';parent.$.fancybox.close();"></td>
</tr>
<? } ?>
</table>


</body>
</html>