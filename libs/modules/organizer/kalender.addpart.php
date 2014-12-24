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


$users = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());
$businesscontacts = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME)
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<script language="javascript" src="jscripts/basic.js"></script>

<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>

<script type="text/javascript">
$(function() {
	$("#tabs").tabs();
});

function add_user(element)
{
	var name = document.getElementById('user_name_' + element.value).innerHTML;
	
    var addStr = '<span class="newmailToField" id="span_participant_int_'+ element.value +'"><img src="images/icons/user.png" />&nbsp;'+ name;
    addStr += '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeParticipant(\'user\', '+ element.value +')" />';
    addStr += '<input type="hidden" name="participant_int[]" id="participant_int[]" value="'+ element.value +'"></span>';
	
	window.parent.document.getElementById('td_part_int').insertAdjacentHTML('AfterBegin', addStr);
}
function add_contactperson(element)
{
	var name = document.getElementById('contactperson_name_' + element.value).innerHTML;
	
    var addStr = '<span class="newmailToField" id="span_participant_ext_'+ element.value +'"><img src="images/icons/user.png" />&nbsp;'+ name;
    addStr += '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeParticipant(\'contactperson\', '+ element.value +')" />';
    addStr += '<input type="hidden" name="participant_ext[]" id="participant_ext[]" value="'+ element.value +'"></span>';
	
	window.parent.document.getElementById('td_part_ext').insertAdjacentHTML('AfterBegin', addStr);
}
</script>

<!-- /jQuery -->

</head>
<body>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><?=$_LANG->get('Benutzer')?></a></li>
		<li><a href="#tabs-2"><?=$_LANG->get('Gesch&auml;ftskontakte')?></a></li>
	</ul>
	<div id="tabs-1">
		<h1>Benutzer</h1>
		<table width="500">
		<tr>
			<td class="content_row_header" width="20">&nbsp;</td>
			<td class="content_row_header" width="20">&nbsp;</td>
			<td class="content_row_header" width="180"><?=$_LANG->get('Login')?></td>
			<td class="content_row_header"><?=$_LANG->get('Name')?></td>
		</tr>
		<?foreach($users as $u) { ?>
		<tr>
			<td class="content_row_clear"><input type="checkbox" onclick="add_user(this)" value="<?=$u->getId()?>"></td>
			<td class="content_row_clear"><img src="../../../images/icons/user.png" /></td>
			<td class="content_row_clear"><?=$u->getLogin()?></td>
			<td class="content_row_clear" id="user_name_<?=$u->getId()?>"><?=$u->getNameAsLine()?></td>
		</tr>
		<? } ?>
		</table>
		<br>
	</div>

	<div id="tabs-2">
		<h1><?=$_LANG->get('Gesch&auml;ftskontakte')?></h1>
		<table width="500">
		<tr>
			<td class="content_row_header" width="20">&nbsp;</td>
			<td class="content_row_header" width="180"><?=$_LANG->get('Name')?></td>
			<td class="content_row_header" width="180">&nbsp;</td>
		</tr>
		<?foreach($businesscontacts as $c) { ?>
		<tr>
			<td class="content_row_clear"><img src="../../../images/icons/building.png" /></td>
			<td class="content_row_clear"><?=$c->getNameAsLine()?></td>
			<td class="content_row_header" width="180">&nbsp;</td>
		</tr>
		<? 	$contactpersons = $c->getContactpersons();
			if (count($contactpersons)>0){
				foreach ($contactpersons as $contact){?>
					<tr>
					<td class="content_row_header" width="20">&nbsp;</td>
					<td class="content_row_clear" align="right"><input type="checkbox" onclick="add_contactperson(this)" value="<?=$contact->getId()?>"></td>
					<td class="content_row_clear" id="contactperson_name_<?=$contact->getId()?>"><?=$contact->getNameAsLine()?></td>
		<?		}
			}
		} ?>
		</table>
	</div>
</div>
</body>
</html>