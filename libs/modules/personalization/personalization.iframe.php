<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.07.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
//error_reporting(-1);
//ini_set('display_errors', 1);
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

$picture = (int)$_REQUEST["picture"];

if($_FILES)
{
    $filename = $_USER->getClient()->getId().'.perbg_'.md5(time().$_FILES["picture"]["name"]).".pdf";
    if(move_uploaded_file($_FILES["picture"]["tmp_name"], "docs/personalization/".$filename))
    {
        ?>
        <script language="javascript">
            parent.document.getElementById('perso_picture<?=$picture?>').value = '<?=$filename?>';
            // parent.document.getElementById('picture_origname').value = '<?=$_FILES["picture"]["name"] ?>';
            parent.document.getElementById('td_picture<?=$picture?>').innerHTML = '<iframe width="400" height="300" scrolling="no" src="libs/modules/personalization/personalization.preview.php?pdffile=<?=$filename?>" style="overflow:hidden;"></iframe> <br/><br/> Neues Bild - Bitte speichern';
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
<form enctype="multipart/form-data" action="personalization.iframe.php" method="post">
	<!-- b><?=$_LANG->get('Bild ausw&auml;hlen');?>:</b><br/>
	Hier werden dann die Bilder des Artikels zur Auswahl aufgelistet
	<hr/>
	<b><?=$_LANG->get('Oder Bild hochladen');?>:</b--><br/>
	<input type="hidden" name="picture" value="<?=$picture?>" />
    <input type="file" name="picture" accept="application/pdf">
    <input type="submit" value="<?=$_LANG->get('Ausw&auml;hlen')?>">
</form>
<b><?=$_LANG->get('Hinweis')?>:</b> <?=$_LANG->get('Bilder ohne Interlacing empfolen. Andernfalls kann es zu Darstellungsfehlern kommen.')?>
</body>
</html>