<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';

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

if($_FILES)
{
    $filename = md5(time().$_FILES["picture"]["name"]).".png";
    if(move_uploaded_file($_FILES["picture"]["tmp_name"], "images/foldtypes/".$filename))
    {
        ?>
        <script language="javascript">
            parent.document.getElementById('picture').value = '<?=$filename?>';
            parent.document.getElementById('picture_show').innerHTML = '<img src="images/foldtypes/<?=$filename?>">';
            parent.$.fancybox.close();
        </script>
        <?
    }
}

?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<script language="javascript" src="jscripts/basic.js"></script>
</head>
<body>
<h1><?=$_LANG->get('Bild &auml;ndern')?></h1>
<form enctype="multipart/form-data" action="picture.iframe.php" method="post">
    <input type="file" name="picture">
    <input type="submit" value="<?=$_LANG->get('Ausw&auml;hlen')?>">
</form>
</body>
</html>