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

$dir = 'images/icons/';
$icons = Array();
$dh = opendir($dir);
while ($file = readdir($dh))
{
    if(preg_match("/.+\.png/", $file))
        $icons[] = $file;
}

sort($icons);
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
<tr>
    <td class="content_header" colspan="3"><?=$_LANG->get('Icon ausw&auml;hlen')?></td>
</tr>
</table>

<? foreach ($icons as $i) { ?>
    <img src="../../../images/icons/<?=$i?>" width="25" height="25" class="pointer"
        onclick="parent.document.getElementById('img_menu_icon').src='images/icons/<?=$i?>';parent.document.getElementById('menu_icon').value='images/icons/<?=$i?>';parent.$.fancybox.close();">
<? } ?>

</body>
</html>