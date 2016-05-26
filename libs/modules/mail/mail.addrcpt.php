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
   $addStr = '';
   if (isset($_REQUEST["add_user"]))
   {
       foreach($_REQUEST["add_user"] as $add)
       {
           $u = new User($add);
           $addStr .= $u->getEmail() . ', ';
       }
   }
       
   if (isset($_REQUEST["add_group"]))
   {    
       foreach($_REQUEST["add_group"] as $add)
       {
           $g = new Group($add);
           foreach ($g->getMembers() as $member)
           {
               if ($u->getEmail() != "")
                   $addStr .= $u->getEmail() . ', ';
           }
       }
   }
   
   if (isset($_REQUEST["add_businesscontact"]))
   {
       foreach($_REQUEST["add_businesscontact"] as $add)
       {
           $c = new businesscontact($add);
           $addStr .= $c->getEmail() . ', ';
       }
   }
   
   if (isset($_REQUEST["add_contactperson"]))
   {
   	foreach($_REQUEST["add_contactperson"] as $add)
   	{
   		$c = new ContactPerson($add);
   		$addStr .= $c->getEmail() . ', ';
   	}
   }
   $addStr = substr($addStr, 0, strlen($addStr)-2);
   $input = $_REQUEST["jselector"];
   $js = "if (parent.$('#{$input}').val()!=''){parent.$('#{$input}').val(parent.$('#{$input}').val()+', {$addStr}');}else{parent.$('#{$input}').val('{$addStr}');}parent.$.fancybox.close();";
//    $js = "parent.$('#{$input}').val(parent.$('#{$input}').val()+', {$addStr}');parent.$.fancybox.close();";
   echo '<script type="text/javascript">';
   echo $js;
   echo '</script>'; 
}


$users = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());
$groups = Group::getAllGroups(Group::ORDER_NAME);
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


<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/date-uk.js"></script>

<script type="text/javascript">
$(function() {
	$("#tabs").tabs();

	var table_users = $('#table_users').DataTable( {
        "paging": true,
		"stateSave": true,
		"pageLength": 10,
		"dom": 'flrtip',        
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"language": 
					{
						"emptyTable":     "Keine Daten vorhanden",
						"info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
						"infoEmpty": 	  "Keine Seiten vorhanden",
						"infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
						"infoPostFix":    "",
						"thousands":      ".",
						"lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
						"loadingRecords": "Lade...",
						"processing":     "Verarbeite...",
						"search":         "Suche:",
						"zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
						"paginate": {
							"first":      "Erste",
							"last":       "Letzte",
							"next":       "N&auml;chste",
							"previous":   "Vorherige"
						},
						"aria": {
							"sortAscending":  ": aktivieren um aufsteigend zu sortieren",
							"sortDescending": ": aktivieren um absteigend zu sortieren"
						}
					}
    } );

	var table_groups = $('#table_groups').DataTable( {
        "paging": true,
		"stateSave": true,
		"pageLength": 10,
		"dom": 'flrtip',        
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"language": 
					{
						"emptyTable":     "Keine Daten vorhanden",
						"info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
						"infoEmpty": 	  "Keine Seiten vorhanden",
						"infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
						"infoPostFix":    "",
						"thousands":      ".",
						"lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
						"loadingRecords": "Lade...",
						"processing":     "Verarbeite...",
						"search":         "Suche:",
						"zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
						"paginate": {
							"first":      "Erste",
							"last":       "Letzte",
							"next":       "N&auml;chste",
							"previous":   "Vorherige"
						},
						"aria": {
							"sortAscending":  ": aktivieren um aufsteigend zu sortieren",
							"sortDescending": ": aktivieren um absteigend zu sortieren"
						}
					}
    } );

	var table_bcons = $('#table_bcons').DataTable( {
        "paging": true,
		"stateSave": true,
		"pageLength": 10,
		"dom": 'flrtip',        
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"language": 
					{
						"emptyTable":     "Keine Daten vorhanden",
						"info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
						"infoEmpty": 	  "Keine Seiten vorhanden",
						"infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
						"infoPostFix":    "",
						"thousands":      ".",
						"lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
						"loadingRecords": "Lade...",
						"processing":     "Verarbeite...",
						"search":         "Suche:",
						"zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
						"paginate": {
							"first":      "Erste",
							"last":       "Letzte",
							"next":       "N&auml;chste",
							"previous":   "Vorherige"
						},
						"aria": {
							"sortAscending":  ": aktivieren um aufsteigend zu sortieren",
							"sortDescending": ": aktivieren um absteigend zu sortieren"
						}
					}
    } );
});
</script>

<!-- /jQuery -->

</head>
<body>

<form action="mail.addrcpt.php" method="post">
<input type="hidden" name="add" value="1"/>
<input type="hidden" name="jselector" value="<?php echo $_REQUEST["jselector"];?>"/>

<table width="100%">
<tr>
    <td class="content_row_header" align="right">
        <input type="submit" value="<?=$_LANG->get('Hinzuf&uuml;gen')?>">
    </td>
</tr>
</table>

<div id="tabs">
<ul>
	<li><a href="#tabs-1"><?=$_LANG->get('Benutzer')?></a></li>
	<li><a href="#tabs-2"><?=$_LANG->get('Gruppen')?></a></li>
	<li><a href="#tabs-3"><?=$_LANG->get('Gesch&auml;ftskontakte')?></a></li>
</ul>
<div id="tabs-1">
    <h1>Benutzer</h1>
    <table id="table_users" width="100%">
    <thead>
        <tr>
            <td class="content_row_header" width="20">&nbsp;</td>
            <td class="content_row_header" width="20">&nbsp;</td>
            <td class="content_row_header"><?=$_LANG->get('Login')?></td>
            <td class="content_row_header"><?=$_LANG->get('Name')?></td>
        </tr>
    </thead>
    <?foreach($users as $u) { ?>
    <tr>
        <td class="content_row_clear"><input type="checkbox" name="add_user[]" value="<?=$u->getId()?>"></td>
        <td class="content_row_clear"<span class="glyphicons glyphicons-user"></span></td>
        <td class="content_row_clear"><?=$u->getLogin()?></td>
        <td class="content_row_clear"><?=$u->getNameAsLine()?></td>
    </tr>
    <? } ?>
    </table>
</div>

<div id="tabs-2">
    <h1><?=$_LANG->get('Gruppen')?></h1>
    <table id="table_groups" width="100%">
    <thead>
        <tr>
            <td class="content_row_header" width="20">&nbsp;</td>
            <td class="content_row_header" width="20">&nbsp;</td>
            <td class="content_row_header"><?=$_LANG->get('Gruppe')?></td>
            <td class="content_row_header"><?=$_LANG->get('Mitglieder')?></td>
        </tr>
    </thead>
    <?foreach($groups as $g) { ?>
    <tr>
        <td class="content_row_clear"><input type="checkbox" name="add_group[]" value="<?=$g->getId()?>"></td>
        <td class="content_row_clear"><span class="glyphicons glyphicons-user"></span></td>
        <td class="content_row_clear"><?=$g->getName()?></td>
        <td class="content_row_clear">
            <? foreach($g->getMembers() as $m) echo $m->getLogin().", "?>
        </td>
    </tr>
    <? } ?>
    </table>
</div>

<div id="tabs-3">
    <h1><?=$_LANG->get('Gesch&auml;ftskontakte')?></h1>
    <table id="table_bcons" width="100%">
    <thead>
        <tr>
            <td class="content_row_header" width="20">&nbsp;</td>
            <td class="content_row_header" width="20">&nbsp;</td>
            <td class="content_row_header"><?=$_LANG->get('Name')?></td>
            <td class="content_row_header"><?=$_LANG->get('E-Mail')?></td>
        </tr>
    </thead>
    <?foreach($businesscontacts as $c) { ?>
    <tr>
        <td class="content_row_clear"><input type="checkbox" name="add_businesscontact[]" value="<?=$c->getId()?>"></td>
        <td class="content_row_clear"><span class="glyphicons glyphicons-building"></span></td>
        <td class="content_row_clear"><?=$c->getNameAsLine()?></td>
        <td class="content_row_clear"><?=$c->getEmail()?></td>
    </tr>
	<? 	$contactpersons = $c->getContactpersons(); 			//getAllBusinessContacts();
		if (count($contactpersons)>0){
			foreach ($contactpersons as $contact){?>
				<tr>
				<td class="content_row_clear"><input type="checkbox" name="add_contactperson[]" value="<?=$contact->getId()?>"></td>
				<td class="content_row_clear"><span class="glyphicons glyphicons-user"></span></td>
				<td class="content_row_clear"><?=$contact->getNameAsLine()?></td>
				<td class="content_row_clear"><?=$contact->getEmail()?></td>
				</tr>	
	<?		}
		}
	} ?>
    </table>
</div>

</div> <!-- /tabs -->
    
</form>
</body>
</html>