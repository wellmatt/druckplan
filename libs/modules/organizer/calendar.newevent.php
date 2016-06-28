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

$_REQUEST["eventid"] = (int)$_REQUEST["eventid"];
$event = new Event($_REQUEST["eventid"]);


if (($_REQUEST["end"] - $_REQUEST["start"]) == 86400)
{
    $starttime = mktime(8,0,0,date('m',$_REQUEST["start"]),date('d',$_REQUEST["start"]),date('Y',$_REQUEST["start"]));
    $event->setBegin($starttime);
    $endtime = mktime(9,0,0,date('m',$_REQUEST["start"]),date('d',$_REQUEST["start"]),date('Y',$_REQUEST["start"]));
    $event->setEnd($endtime);
}

// Startzeit setzen
if($event->getBegin() == 0)
    $event->setBegin(strtotime($_REQUEST["start"]));
if($event->getEnd() == 0)
    $event->setEnd(strtotime($_REQUEST["end"]));

if ($_REQUEST["delete"])
{
    $savemsg = getSaveMessage($event->delete());
    $_REQUEST["exec"] = "";
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/organizer/calendar.php";</script>';
}

if($_REQUEST["subexec"] == "save")
{
	$ext_partitipants = Array();
	$checkboxes = isset($_POST['parts_ext']) ? $_POST['parts_ext'] : array();
	foreach($checkboxes as $value) {
		if((int)$value > 0)
			$ext_partitipants[] = (int)$value;
	}

	$int_partitipants = Array();
	$checkboxes = isset($_POST['int_users']) ? $_POST['int_users'] : array();
	foreach($checkboxes as $value) {
		if((int)$value > 0)
			$int_partitipants[] = (int)$value;
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


	<!-- MegaNavbar -->
	<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
	<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
	<!-- /MegaNavbar -->

	<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
	<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
	<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>

	<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
	<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
	<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
	<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />
	<link rel="stylesheet" type="text/css" href="../../../css/main.css" />

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
	<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
</head>


	<form action="calendar.newevent.php" method="post" name="event_form" class="form-horizontal">
		<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
		<input type="hidden" name="subexec" value="save">
		<input type="hidden" name="eventid" value="<?=$_REQUEST["eventid"]?>">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Kalender Termin<?php if($event->getId()==0)echo ' - Neu'; else echo ' - '.$event->getTitle();?></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-9">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Termin bearbeiten</h2>
							</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Gesprächspartner</label>
									<div class="col-sm-9" id="cpdiv">
										<?php
										if ($event->getId()>0){
											if (count($event->getParticipantsExt()>0)){
												foreach ($event->getParticipantsExt() as $item) {
													$cp = new ContactPerson($item);
													?>
													<div class="input-group">
														<input type="text" class="form-control cpautoinput" value="<?php echo $cp->getBusinessContact()->getNameAsLine().' - '.$cp->getNameAsLine();?>">
														<input type="hidden" name="parts_ext[]" value="<?php echo $item;?>">
														<div class="input-group-addon pointer" onclick="$(this).parent().remove();">
															<span class="glyphicons glyphicons-remove" style="font-size: 12px;"></span>
														</div>
													</div>
													<?php
												}
											}
										}
										?>
										<div class="input-group">
											<input type="text" class="form-control cpautoinput">
											<input type="hidden" name="parts_ext[]" value="">
											<div class="input-group-addon pointer" onclick="resetCp(this);">
												<span class="glyphicons glyphicons-remove" style="font-size: 12px;"></span>
											</div>
											<div class="input-group-addon pointer" onclick="addCp();">
												<span class="glyphicons glyphicons-plus" style="font-size: 12px;"></span>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Titel</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" name="event_title" id="event_title" value="<?=$event->getTitle()?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Typ</label>
									<div class="col-sm-9">
										<input type="radio" name="event_public" value="0" <? if($event->getPublic() == 0) echo "checked"?>/> Privat
										<input type="radio" name="event_public" value="1" <? if($event->getPublic() == 1 || $event->getId() == 0) echo "checked"?>/> Öffentlich
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Notiz</label>
									<div class="col-sm-9">
										<textarea class="form-control" name="event_desc" id="event_desc""><?=$event->getDesc()?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Mitarbeiter</h2>
							</div>
							<div class="panel-body">
								<?php
								foreach ($users as $user) {
									if (in_array($user->getId(),$event->getParticipants_Int()))
										echo '<input name="int_users[]" value="'.$user->getId().'" type="checkbox" checked> '.$user->getNameAsLine().'<br>';
									else
										echo '<input name="int_users[]" value="'.$user->getId().'" type="checkbox"> '.$user->getNameAsLine().'<br>';
								}
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Datum, Zeit und Ort</h2>
							</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Von</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" name="event_from_date" id="event_from_date" value="<?=date('d.m.Y H:i', $event->getBegin())?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Bis</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" name="event_to_date" id="event_to_date" value="<?=date('d.m.Y H:i', $event->getEnd())?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Treffpunkt</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" name="formatted_address" id="formatted_address" type="hidden" value="<?=$event->getAdress()?>">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<input type="submit" value="<?=$_LANG->get('Speichern')?>" class="text">
						<? if($event->getId()) {
							echo '<input type="submit" class="buttonRed" name="delete" value="'.$_LANG->get('L&ouml;schen').'">';
						} ?>

					</div>
				</div>
			</div>
		</div>
	</form>



<script>
	$(function() {
		var options = {
			source: "../../../libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer_and_cp",
			minLength: 2,
			focus: function( event, ui ) {
				$( this ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( this ).val( ui.item.label );
				$( this ).parent().children('input:hidden').val( ui.item.cid );
				return false;
			}
		};

		$("input.cpautoinput").live("keydown.autocomplete", function() {
			$(this).autocomplete(options);
		});
	});

	function addCp(){
		var inputHTML = '<div class="input-group">' +
			'<input type="text" class="form-control cpautoinput">' +
			'<input type="hidden" name="parts_ext[]" value="">' +
			'<div class="input-group-addon pointer" onclick="resetCp(this);">' +
			'<span class="glyphicons glyphicons-remove" style="font-size: 12px;"></span></div>' +
			'<div class="input-group-addon pointer" onclick="addCp();">' +
			'<span class="glyphicons glyphicons-plus" style="font-size: 12px;"></span></div></div>';
		$(inputHTML).appendTo('#cpdiv');
		$("#cpdiv > input.cpautoinput:last").focus();
	}

	function resetCp(selector){
		$( selector ).parent().children('input').each(function(){
			$(this).val('');
		});
	}
</script>
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
<script language="javascript">
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);

		$('#event_from_date').datepicker(
				{
					showOtherMonths: true,
					selectOtherMonths: true,
					dateFormat: 'dd.mm.yy',
				}
		 );

		$('#event_to_date').datepicker(
				{
					showOtherMonths: true,
					selectOtherMonths: true,
					dateFormat: 'dd.mm.yy',
				}
		 );

	});
</script>