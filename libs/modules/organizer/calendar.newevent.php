<?
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
require_once 'libs/modules/notifications/notification.class.php';

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

	
$part_users_int = Array();
$part_users_ext = Array();
$users = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());
// $businesscontacts = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME, BusinessContact::FILTER_ALL, BusinessContact::LOADER_MEDIUM);

$_REQUEST["eventid"] = (int)$_REQUEST["eventid"];
$event = new Event($_REQUEST["eventid"]);

if (($_REQUEST["end"] - $_REQUEST["start"]) == 86400)
{
//     echo "monat geklickt!";
    $starttime = mktime(8,0,0,date('m',$_REQUEST["start"]),date('d',$_REQUEST["start"]),date('Y',$_REQUEST["start"]));
    $event->setBegin($starttime);
    $endtime = mktime(9,0,0,date('m',$_REQUEST["start"]),date('d',$_REQUEST["start"]),date('Y',$_REQUEST["start"]));
    $event->setEnd($endtime);
}

// Startzeit setzen
if($event->getBegin() == 0)
    $event->setBegin($_REQUEST["start"]); // -3600
if($event->getEnd() == 0)
    $event->setEnd($_REQUEST["end"]);

if ($_REQUEST["delete"])
{
    $savemsg = getSaveMessage($event->delete());
    $_REQUEST["exec"] = "";
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/organizer/calendar.php";</script>';
}

if($_REQUEST["subexec"] == "save")
{

	$int_partitipants = Array();
	foreach($_REQUEST['participant_int'] as $pint)
	{
		$int_partitipants[] = (int)$pint;
	}
	
	$ext_partitipants = Array();
	foreach($_REQUEST['participant_ext'] as $pext)
	{
		$ext_partitipants[] = (int)$pext;
	}
	
	$tmp_old_int_parts = Array();
	$all_int_partitipants = $event->getParticipants_Int();
	foreach ($all_int_partitipants as $tmp_int_part)
	{
	    $tmp_old_int_parts[] = $tmp_int_part;
	}
	
	$new_int_parts = array_diff($int_partitipants, $tmp_old_int_parts);
	
	
	if (count($event->getParticipantsInt()) > 0 && 
	    ($event->getBegin() != (int)strtotime($_REQUEST["event_from_date"]) || 
	    $event->getEnd() != (int)strtotime($_REQUEST["event_to_date"]) ||
	    $event->getDesc() != trim(addslashes($_REQUEST["event_desc"])) ||
	    $event->getAdress() != $_REQUEST["formatted_address"])
	    )
	{
	    foreach ($event->getParticipantsInt() as $tmp_int)
	    {
	        $tmp_user = new User($tmp_int);
// 	        echo "Notfy für Teilnehmer: ".$tmp_user->getNameAsLine()."</br>";
// 	        if ($tmp_user->getId() != $_USER->getId())
            $notes = Array();
            if ($event->getBegin() != (int)strtotime($_REQUEST["event_from_date"]) || $event->getEnd() != (int)strtotime($_REQUEST["event_to_date"]))
                $notes[] = "Zeit";
            if ($event->getDesc() != trim(addslashes($_REQUEST["event_desc"])))
                $notes[] = "Beschreibung";
            if ($event->getAdress() != $_REQUEST["formatted_address"])
                $notes[] = "Adresse";
            Notification::generateNotification($tmp_user, "Event", "ChangeEvent", $_USER->getNameAsLine2(), $event->getId(), "", $notes);
	    }
	}
	
	$event->setParticipantsInt($int_partitipants);
	$event->setParticipantsExt($ext_partitipants);

    $event_begin    = (int)strtotime($_REQUEST["event_from_date"]);
    $event_end    = (int)strtotime($_REQUEST["event_to_date"]);
    
    if (!$event->getUser())
        $event->setUser($_USER);
    $event->setBegin($event_begin);
    $event->setEnd($event_end);
    $event->setPublic((int)$_REQUEST["event_public"]);
    $event->setTitle(trim(addslashes($_REQUEST["event_title"])));
    $event->setDesc(trim(addslashes($_REQUEST["event_desc"])));
    $event->setAdress($_REQUEST["formatted_address"]);
    
	echo $_REQUEST["formatted_address"] . "</br>";
	
    $savemsg = getSaveMessage($event->save());
    echo $DB->getLastError();
    
    foreach ($new_int_parts as $new_part)
    {
        $tmp_user = new User($new_part);
	    if ($tmp_user->getId() != $_USER->getId())
            Notification::generateNotification($tmp_user, "Event", "NewEvent", $_USER->getNameAsLine2(), $event->getId());
    }
    
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/organizer/calendar.php";</script>';
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

<!-- Geopicker -->
<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script	type="text/javascript" src="../../../jscripts/jquery.geocomplete.js"></script>

<link rel="stylesheet" type="text/css" href="../../../jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="../../../jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script>
	$(function() {
		$( "#tabs" ).tabs({ selected: 0 });

		$('#event_from_date').datetimepicker({
			 lang:'de',
			 i18n:{
			  de:{
			   months:[
			    'Januar','Februar','März','April',
			    'Mai','Juni','Juli','August',
			    'September','Oktober','November','Dezember',
			   ],
			   dayOfWeek:[
			    "So.", "Mo", "Di", "Mi", 
			    "Do", "Fr", "Sa.",
			   ]
			  }
			 },
			 timepicker:true,
			 format:'d.m.Y H:i'
		});
		$('#event_to_date').datetimepicker({
			 lang:'de',
			 i18n:{
			  de:{
			   months:[
			    'Januar','Februar','März','April',
			    'Mai','Juni','Juli','August',
			    'September','Oktober','November','Dezember',
			   ],
			   dayOfWeek:[
			    "So.", "Mo", "Di", "Mi", 
			    "Do", "Fr", "Sa.",
			   ]
			  }
			 },
			 timepicker:true,
			 format:'d.m.Y H:i'
		});
	});
</script>
<script>
$(document).ready(function() {
    var table_users = $('#table_users').DataTable( {
        "paging": true,
		"stateSave": true,
		"pageLength": 20,
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
    var table_bcontacs = $('#table_bcontacs').DataTable( {
        "paging": true,
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "calendar.newevent.dt.ajax.php",
		"stateSave": true,
		"pageLength": 10,
		"columns": [
		            null,
		            null,
		          ],
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
<script language="javascript">

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('#event_from_date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );

	$('#event_to_date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
    
});

function removeParticipant(what, id)
{
    if (what == 'user')
    {
		var element = document.getElementById('span_participant_int_'+id);
		element.parentNode.removeChild(element);
		document.getElementById('chkb_'+id).checked = false;
        // document.getElementById('participant_int_'+id).disabled = true;
        // document.getElementById('span_participant_int_'+id).style.display = 'none';
    } else if (what == 'contactperson')
    {
		var element = document.getElementById('span_participant_ext_'+id);
		element.parentNode.removeChild(element);
		document.getElementById('chkb_'+id).checked = false;
        // document.getElementById('participant_ext_'+id).disabled = true;
        // document.getElementById('span_participant_ext_'+id).style.display = 'none';
    }
}

function add_user(element)
{
	if (!element.checked) {
		removeParticipant('user',element.value);
	} else {
		var name = document.getElementById('user_name_' + element.value).innerHTML;
		var addStr = '<span class="newmailToField" id="span_participant_int_'+ element.value +'"><span class="glyphicons glyphicons-user"></span>&nbsp;'+ name;
		addStr += '<span class="glyphicons glyphicons-remove pointer" onclick="removeParticipant(\'user\', '+ element.value +')" ></span>';
		addStr += '<input type="hidden" name="participant_int[]" id="participant_int[]" value="'+ element.value +'"></br></span>';
		document.getElementById('td_part_int').insertAdjacentHTML('BeforeEnd', addStr);
	}
}
function add_contactperson(element)
{
	if (!element.checked) {
		removeParticipant('contactperson',element.value);
	} else {
		var name = document.getElementById('contactperson_name_' + element.value).value;
		var addStr = '<span class="newmailToField" id="span_participant_ext_'+ element.value +'"><span class="glyphicons glyphicons-user"></span>&nbsp;'+ name;
		addStr += '<span class="glyphicons glyphicons-remove pointer" onclick="removeParticipant(\'contactperson\', '+ element.value +')" ></span>';
		addStr += '<input type="hidden" name="participant_ext[]" id="participant_ext[]" value="'+ element.value +'"></br></span>';
		document.getElementById('td_part_ext').insertAdjacentHTML('BeforeEnd', addStr);
	}
}

</script>
    <script>
      $(function(){
        
        var options = {
          map: ".map_canvas",
          details: "form",
          types: ["geocode", "establishment"]
        };
        
        $("#geocomplete").geocomplete(options)
          .bind("geocode:result", function(event, result){
            $.log("Result: " + result.formatted_address);
          })
          .bind("geocode:error", function(event, status){
            $.log("ERROR: " + status);
          })
          .bind("geocode:multiple", function(event, results){
            $.log("Multiple: " + results.length + " results found");
          });
        
		if(document.getElementById('formatted_address').value != "") {
			$("#geocomplete").val(document.getElementById('formatted_address').value).trigger("geocode");
		};
		
        $("#find").click(function(){
          $("#geocomplete").trigger("geocode");
        });
        
      });
    </script>
<style>
#geocomplete { 
  width: 200px
}
.map_canvas { 
  width: 300px; 
  height: 200px; 
  margin: 10px 20px 10px 0;
}
</style>
</head>
<body>

<form action="calendar.newevent.php" method="post" name="event_form">
<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="eventid" value="<?=$_REQUEST["eventid"]?>">
<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <h1><span class="glyphicons glyphicons-remove"></span> <?=$_LANG->get('Kalender');?> -
            <? if ($_REQUEST["id"]) echo $_LANG->get('Termin editieren'); else echo $_LANG->get('Neuer Termin')?></h1>
        </td>
        <td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<input type="submit" value="<?=$_LANG->get('Speichern')?>" class="text">
<? if($event->getId()) { 
//     echo '<input type="button" class="buttonRed" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&exec=delevent&id='.$event->getId().'\')" value="'.$_LANG->get('L&ouml;schen').'">';
    echo '<input type="submit" class="buttonRed" name="delete" value="'.$_LANG->get('L&ouml;schen').'">';
 } ?> 

<div class="demo">	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-0"><? echo $_LANG->get('&Uuml;bersicht');?></a></li>
			<li><a href="#tabs-1"><? echo $_LANG->get('Interne Teilnehmer');?></a></li>
			<li><a href="#tabs-2"><? echo $_LANG->get('Externe Teilnehmer');?></a></li> 
		</ul>

		<div id="tabs-0">
			<table width="100%">
				<colgroup>
					<col width="180">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Von')?></td>
					<td class="content_row_clear">
						<input name="event_from_date" id="event_from_date" value="<?=date('d.m.Y H:i', $event->getBegin())?>" style="width:200px;"
						class="text" id="event_from_date">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Bis')?></td>
					<td class="content_row_clear">
						<input name="event_to_date" id="event_to_date" value="<?=date('d.m.Y H:i', $event->getEnd())?>" style="width:200px;"
						class="text" id="event_to_date">
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Treffpunkt')?></td>
					<td class="content_row_clear">
						<input name="geocomplete" id="geocomplete" value="" style="width:250px;" class="text">
						<input name="formatted_address" id="formatted_address" type="hidden" value="<?=$event->getAdress()?>">
						<span class="glyphicons glyphicons-map" onclick="window.open('https://www.google.de/maps/place/'+document.getElementById('geocomplete').value,'_blank');" ></span>
						<div class="map_canvas"></div>
					</td>
				</tr>    
				<tr>
					<td class="content_row_header"><?=$_LANG->get('&Ouml;ffentlich')?></td>
					<td class="content_row_clear">
						<input type="radio" name="event_public" value="0" <? if($event->getPublic() == 0) echo "checked"?>> <?=$_LANG->get('Nein')?>
						<input type="radio" name="event_public" value="1" <? if($event->getPublic() == 1 || $event->getId() == 0) echo "checked"?>> <?=$_LANG->get('Ja')?>
					</td>
				</tr>    
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
					<td class="content_row_clear">
						<input name="event_title" class="text" style="width:300px" value="<?=$event->getTitle()?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?></td>
					<td class="content_row_clear">
						<textarea name="event_desc" class="text" style="width:300px;height:150px"><?=$event->getDesc()?></textarea>
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Interne Teilnehmer')?></td>
					<td class="content_row_clear" id="td_part_int">
					<?
						if (count($event->getParticipantsInt()) > 0 ) {
							foreach($event->getParticipantsInt() as $part_user)
							{
								$part_int = new User($part_user);
								$part_users_int[] = $part_int->getId();
								$addStr = '<span class="newmailToField" id="span_participant_int_'.$part_int->getId().'"><span class="glyphicons glyphicons-user"></span>&nbsp;'.$part_int->getFirstname().'&nbsp;'.$part_int->getLastname();
								$addStr .= '<span class="glyphicons glyphicons-remove pointer" onclick="removeParticipant(\'user\', '.$part_int->getId().')" ></span>';
								$addStr .= '<input type="hidden" name="participant_int[]" id="participant_int_'.$part_int->getId().'" value="'.$part_int->getId().'"></br></span>';
								echo $addStr;
							}
						} elseif ($event->getId() == 0)
						{
						    $part_int = $_USER;
						    $part_users_int[] = $part_int->getId();
						    $addStr = '<span class="newmailToField" id="span_participant_int_'.$part_int->getId().'"><span class="glyphicons glyphicons-user"></span>&nbsp;'.$part_int->getFirstname().'&nbsp;'.$part_int->getLastname();
						    $addStr .= '<span class="glyphicons glyphicons-remove pointer" onclick="removeParticipant(\'user\', '.$part_int->getId().')" ></span>';
						    $addStr .= '<input type="hidden" name="participant_int[]" id="participant_int_'.$part_int->getId().'" value="'.$part_int->getId().'"></br></span>';
						    echo $addStr;
						}
					?>
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Externe Teilnehmer')?></td>
					<td class="content_row_clear" id="td_part_ext">
					<?
						if (count($event->getParticipantsExt()) > 0 ) {
							foreach($event->getParticipantsExt() as $part_contact_person)
							{
								$part_ext = new ContactPerson($part_contact_person);
								$part_users_ext[] = $part_ext->getId();
								$addStr = '<span class="newmailToField" id="span_participant_ext_'.$part_ext->getId().'"><span class="glyphicons glyphicons-user"></span>&nbsp;'.$part_ext->getNameAsLine2().'&nbsp;';
								$addStr .= '<span class="glyphicons glyphicons-remove pointer" onclick="removeParticipant(\'contactperson\', '.$part_ext->getId().')" ></span>';
								$addStr .= '<input type="hidden" name="participant_ext[]" id="participant_ext_'.$part_ext->getId().'" value="'.$part_ext->getId().'"></br></span>';
								echo $addStr;
							}
						}
					?>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="tabs-1">
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
					<input type="checkbox" id="chkb_<?=$u->getId()?>" onclick="add_user(this)" <? if(in_array($u->getId(), $part_users_int)) echo " checked ";?> value="<?=$u->getId()?>">
					<span class="glyphicons glyphicons-user"></span>
					<?=$u->getLogin()?>
				</td>
				<td class="content_row_clear" id="user_name_<?=$u->getId()?>"><?=$u->getNameAsLine()?></td>
			</tr>
			<? } ?>
			</table>
			<br>
		</div>
		
		<div id="tabs-2">
			<h1><?=$_LANG->get('Gesch&auml;ftskontakte')?></h1>
			<table id="table_bcontacs" width="500">
			<thead>
				<tr>
					<th width="180"><?=$_LANG->get('Firma')?></th>
					<th width="180"><?=$_LANG->get('Kontakt Person')?></th>
				</tr>
			</thead>
			<?php /*?>
			<?foreach($businesscontacts as $c) { ?>
			<? 	$contactpersons = $c->getContactpersons();
				if (count($contactpersons)>0){
					foreach ($contactpersons as $contact){?>
					<tr>
						<td class="content_row_clear"><img src="../../../images/icons/building.png" /><?=$c->getNameAsLine()?></td>
						<td class="content_row_header" width="180"><input type="checkbox" onclick="add_contactperson(this)" value="<?=$contact->getId()?>"><?=$contact->getNameAsLine()?></td>
						<input type="hidden" name="contactperson_name_<?=$contact->getId()?>" id="contactperson_name_<?=$contact->getId()?>" value="<?=$contact->getNameAsLine()?>">
					</tr>
			<?		}
				}
			} ?>
			*/ ?>
			</table>
		</div>
	</div>
</div>
<br>
</form>