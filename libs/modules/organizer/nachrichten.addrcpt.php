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

if ($_REQUEST["add"] == 1)
{
   echo '<script language="javascript">';
   $addStr = '';
   if (isset($_REQUEST["add_user"]))
   {
       foreach($_REQUEST["add_user"] as $add)
       {
           $u = new User($add);
           $addStr .= '<span class="newmailToField" id="touserfield_'.$u->getId().'"><img src="images/icons/user.png" />&nbsp;'.$u->getNameAsLine().'&nbsp;&nbsp;';
           $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\\\'user\\\', '.$u->getId().')" />';
           $addStr .= '<input type="hidden" name="mail_touser_'.$u->getId().'" id="mail_touser_'.$u->getId().'" value="1"></span>';
       }
   }
       
   if (isset($_REQUEST["add_group"]))
   {    
       foreach($_REQUEST["add_group"] as $add)
       {
           $g = new Group($add);
           $addStr .= '<span class="newmailToField" id="togroupfield_'.$g->getId().'"><img src="images/icons/users.png" />&nbsp;'.$g->getName().'&nbsp;&nbsp;';
           $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\\\'group\\\', '.$g->getId().')" />';
           $addStr .= '<input type="hidden" name="mail_togroup_'.$g->getId().'" id="mail_togroup_'.$g->getId().'" value="1"></span>';
       }
   }
   
   if (isset($_REQUEST["add_usercontact"]))
   {
       foreach($_REQUEST["add_usercontact"] as $add)
       {
           $c = new UserContact($add);
           $addStr .= '<span class="newmailToField" id="tousercontactfield_'.$c->getId().'"><img src="images/icons/card-address.png" />&nbsp;'.$c->getNameAsLine().'&nbsp;&nbsp;';
           $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\\\'usercontact\\\', '.$c->getId().')" />';
           $addStr .= '<input type="hidden" name="mail_tousercontact_'.$c->getId().'" id="mail_tousercontact_'.$c->getId().'" value="1"></span>';
       }
   }
   
   if (isset($_REQUEST["add_businesscontact"]))
   {
       foreach($_REQUEST["add_businesscontact"] as $add)
       {
           $c = new businesscontact($add);
           $addStr .= '<span class="newmailToField" id="tobusinesscontactfield_'.$c->getId().'"><img src="images/icons/building.png" />&nbsp;'.$c->getNameAsLine().'&nbsp;&nbsp;';
           $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\\\'businesscontact\\\', '.$c->getId().')" />';
           $addStr .= '<input type="hidden" name="mail_tobusinesscontact_'.$c->getId().'" id="mail_tobusinesscontact_'.$c->getId().'" value="1"></span>';
       }
   }
   
   if (isset($_REQUEST["add_contactperson"]))
   {
   	foreach($_REQUEST["add_contactperson"] as $add)
   	{
   		$c = new ContactPerson($add);
   		$addStr .= '<span class="newmailToField" id="tocontactpersonfield_'.$c->getId().'"><img src="images/icons/user-business.png" />&nbsp;'.$c->getNameAsLine().'&nbsp;&nbsp;';
   		$addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\\\'contactperson\\\', '.$c->getId().')" />';
   		$addStr .= '<input type="hidden" name="mail_tocontactperson_'.$c->getId().'" id="mail_tocontactperson_'.$c->getId().'" value="1"></span>';
   	}
   }
    
   
   $js = "parent.document.getElementById('td_mail_to').insertAdjacentHTML('AfterBegin', '{$addStr}');";
   echo $js;
   
   echo '</script>'; 
}


$users = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());
$groups = Group::getAllGroups(Group::ORDER_NAME);
$usercontacts = UserContact::getAllUserContacts(UserContact::ORDER_NAME);
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
</script>

<!-- /jQuery -->

</head>
<body>
<div id="tabs">
<ul>
	<li><a href="#tabs-1"><?=$_LANG->get('Benutzer / Gruppen')?></a></li>
	<li><a href="#tabs-2"><?=$_LANG->get('Eigene Kontakte')?></a></li>
	<li><a href="#tabs-3"><?=$_LANG->get('Gesch&auml;ftskontakte')?></a></li>
</ul>
<form action="nachrichten.addrcpt.php" method="post">
<input type="hidden" name="add" value="1">
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
        <td class="content_row_clear"><input type="checkbox" name="add_user[]" value="<?=$u->getId()?>"></td>
        <td class="content_row_clear"><img src="../../../images/icons/user.png" /></td>
        <td class="content_row_clear"><?=$u->getLogin()?></td>
        <td class="content_row_clear"><?=$u->getNameAsLine()?></td>
    </tr>
    <? } ?>
    </table>
    <br>
    <h1><?=$_LANG->get('Gruppen')?></h1>
    <table width="500">
    <tr>
        <td class="content_row_header" width="20">&nbsp;</td>
        <td class="content_row_header" width="20">&nbsp;</td>
        <td class="content_row_header" width="180"><?=$_LANG->get('Gruppe')?></td>
        <td class="content_row_header"><?=$_LANG->get('Mitglieder')?></td>
    </tr>
    <?foreach($groups as $g) { ?>
    <tr>
        <td class="content_row_clear"><input type="checkbox" name="add_group[]" value="<?=$g->getId()?>"></td>
        <td class="content_row_clear"><img src="../../../images/icons/users.png" /></td>
        <td class="content_row_clear"><?=$g->getName()?></td>
        <td class="content_row_clear">
            <? foreach($g->getMembers() as $m) echo $m->getLogin().", "?>
        </td>
    </tr>
    <? } ?>
    </table>
</div>
<div id="tabs-2">
    <h1><?=$_LANG->get('Kontakte')?></h1>
    <table width="500">
    <tr>
        <td class="content_row_header" width="20">&nbsp;</td>
        <td class="content_row_header" width="20">&nbsp;</td>
        <td class="content_row_header" width="180"><?=$_LANG->get('Name')?></td>
        <td class="content_row_header"><?=$_LANG->get('E-Mail')?></td>
    </tr>
    <?foreach($usercontacts as $c) { ?>
    <tr>
        <td class="content_row_clear"><input type="checkbox" name="add_usercontact[]" value="<?=$c->getId()?>"></td>
        <td class="content_row_clear"><img src="../../../images/icons/card-address.png" /></td>
        <td class="content_row_clear"><?=$c->getNameAsLine()?></td>
        <td class="content_row_clear"><?=$c->getEmail()?></td>
    </tr>
    <? } ?>
    </table>
</div>

<div id="tabs-3">
    <h1><?=$_LANG->get('Gesch&auml;ftskontakte')?></h1>
    <table width="500">
    <tr>
        <td class="content_row_header" width="20">&nbsp;</td>
        <td class="content_row_header" width="20">&nbsp;</td>
        <td class="content_row_header" width="180"><?=$_LANG->get('Name')?></td>
        <td class="content_row_header"><?=$_LANG->get('E-Mail')?></td>
    </tr>
    <?foreach($businesscontacts as $c) { ?>
    <tr>
        <td class="content_row_clear"><input type="checkbox" name="add_businesscontact[]" value="<?=$c->getId()?>"></td>
        <td class="content_row_clear"><img src="../../../images/icons/building.png" /></td>
        <td class="content_row_clear"><?=$c->getNameAsLine()?></td>
        <td class="content_row_clear"><?=$c->getEmail()?></td>
    </tr>
	<? 	$contactpersons = $c->getContactpersons(); 			//getAllBusinessContacts();
		if (count($contactpersons)>0){
			foreach ($contactpersons as $contact){?>
				<tr>
				<td class="content_row_clear">&ensp;</td>
				<td class="content_row_clear">
					<input type="checkbox" name="add_contactperson[]" value="<?=$contact->getId()?>">
				</td>
				<td class="content_row_clear">
					<img src="../../../images/icons/user-business.png" /> <?=$contact->getNameAsLine()?>
					</td>
				<td class="content_row_clear"><?=$contact->getEmail()?></td>
				</tr>	
	<?		}
		}
	} ?>
    </table>
</div>

</div> <!-- /tabs -->
<table width="500">
<tr>
    <td class="content_row_header" width="20">&nbsp;</td>
    <td class="content_row_header" width="20">&nbsp;</td>
    <td class="content_row_header" width="180">&nbsp;</td>
    <td class="content_row_header" align="right">
        <input type="submit" value="<?=$_LANG->get('Hinzuf&uuml;gen')?>">
    </td>
</tr>
</table>
    
</form>
</body>
</html>