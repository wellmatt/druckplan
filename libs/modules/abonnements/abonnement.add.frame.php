<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

// error_reporting(-1);
// ini_set('display_errors', 1);

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
require_once 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/organizer/event.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/abonnements/abonnement.class.php';

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
$classname = $_REQUEST["module"];
$object = new $classname((int)$_REQUEST["objectid"]);

if($_REQUEST["subexec"] == "save")
{
    $tmp_old_abos = Array();
    $all_abos = Abonnement::getAbonnementsForObject($classname, $object->getId());
    foreach ($all_abos as $tmp_abo)
    {
        $tmp_old_abos[] = $tmp_abo->getAbouser()->getId();
        $tmp_abo->delete();
    }
    
    $tmp_array = $_REQUEST["abo_users"];
    
    if ($classname == "Ticket")
    {
        $logentry = "";
        $removed_abos = array_diff($tmp_old_abos, $tmp_array);
//         print_r($removed_abos);
        foreach ($removed_abos as $rabo)
        {
            $tmp_user = new User($rabo);
            $logentry .= 'Abonnement entfernt: ' . $tmp_user->getNameAsLine() . '</br>';
        }
        $new_abos = array_diff($tmp_array, $tmp_old_abos);
//         print_r($new_abos);
        foreach ($new_abos as $nabo)
        {
            $tmp_user = new User($nabo);
            $logentry .= 'Abonnement hinzugefügt: ' . $tmp_user->getNameAsLine() . '</br>';
        }
        
        $tmp_ticket = new Ticket($object->getId());
        $ticketlog = new TicketLog();
        $ticketlog->setCrtusr($_USER);
        $ticketlog->setDate(time());
        $ticketlog->setTicket($tmp_ticket);
        $ticketlog->setEntry($logentry);
        $ticketlog->save();
    }
    
    foreach ($tmp_array as $abouser){
        $tmp_user = new User($abouser);
        if (!Abonnement::hasAbo($object,$tmp_user)){
            $abo = new Abonnement();
            $abo->setAbouser($tmp_user);
            $abo->setModule($classname);
            $abo->setObjectid($object->getId());
            $abo->save();
        }
    }
    echo '<script language="JavaScript">parent.Abo_Refresh(); parent.$.fancybox.close();</script>'; // parent.location.href=parent.location.href;
}

?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>


<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<!-- /jQuery -->

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>


<script language="javascript" src="../../../jscripts/basic.js"></script>
<script language="javascript" src="../../../jscripts/loadingscreen.js"></script>
<!-- FancyBox -->
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script>
$(document).ready(function() {
    var table_users = $('#table_users').DataTable( {
        "paging": true,
		"stateSave": true,
		"pageLength": -1,
		"lengthMenu": [ [-1], ["Alle"] ],
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

<form action="abonnement.add.frame.php" method="post" name="abo_form">
<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="module" value="<?=$_REQUEST["module"]?>">
<input type="hidden" name="objectid" value="<?=$_REQUEST["objectid"]?>">
<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <h1><img src="../../../images/icons/alarm-clock.png"> <?=$_LANG->get('Abonnements bearbeiten');?></h1>
        </td>
        <td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<input type="submit" value="<?=$_LANG->get('Speichern')?>" class="text">

<div class="box1">
<h1>Benutzer</h1>
<table id="table_users" width="500">
    <thead>
        <tr>
            <th width="180"><?=$_LANG->get('Login')?></th>
			<th><?=$_LANG->get('Name')?></th>
		</tr>
	</thead>
	<?foreach($users as $u) { ?>
	<tr>
		<td class="content_row_clear">
			<input type="checkbox" name="abo_users[]" id="chkb_<?=$u->getId()?>" <? if(Abonnement::hasAbo($object,$u)) echo " checked ";?> value="<?=$u->getId()?>">
			<img src="../../../images/icons/user.png" />
			<?=$u->getLogin()?>
		</td>
		<td class="content_row_clear" id="user_name_<?=$u->getId()?>"><?=$u->getNameAsLine()?></td>
	</tr>
	<? } ?>
	</table>
	<br>
</div>
</form>