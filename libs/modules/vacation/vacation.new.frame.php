<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

 error_reporting(-1);
 ini_set('display_errors', 1);

require_once 'vacation.user.class.php';
require_once 'vacation.entry.class.php';
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
require_once 'libs/modules/organizer/caldav.event.class.php';

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

$users = User::getAllUser(User::ORDER_NAME);

$_REQUEST["eventid"] = (int)$_REQUEST["eventid"];
$vacation = new VacationEntry($_REQUEST["eventid"]);

// Startzeit setzen
if($vacation->getStart() == 0)
	$vacation->setStart($_REQUEST["start"]);
if($vacation->getEnd() == 0)
	$vacation->setEnd($_REQUEST["end"]-1);
if($vacation->getDays() == 0)
	$vacation->setDays(floor(($vacation->getEnd()+1-$vacation->getStart())/60/60/24));

if ($_REQUEST["delete"])
{
    $savemsg = getSaveMessage($vacation->delete());
    $_REQUEST["exec"] = "";
	$_REQUEST["subexec"] = "";
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/vacation/vacation.user.php";</script>';
}

if($_REQUEST["subexec"] == "save")
{
	$vacation->setStart(strtotime($_REQUEST["vac_start"]));
	$vacation->setEnd(strtotime($_REQUEST["vac_end"])+86399);
	$vacation->setDays($_REQUEST["vac_days"]);
	$vacation->setUser(new User($_REQUEST["vac_user"]));
	$vacation->setState($_REQUEST["vac_state"]);
	$vacation->setType($_REQUEST["vac_type"]);
	$vacation->setComment($_REQUEST["vac_comment"]);
	$res = $vacation->save();

	if ($res && $vacation->getState() == VacationEntry::STATE_APPROVED){

		$params = [
			'start' => CalDavEvent::convertTimestamp($vacation->getStart()),
			'end' => CalDavEvent::convertTimestamp($vacation->getEnd()),
			'summary' => "Urlaub: ".$vacation->getUser()->getNameAsLine(),
			'location' => '',
			'descr' => $vacation->getComment(),
			'uid' => "vac-".$vacation->getId()."-".time(),
		];
		$calevent = new CalDavEvent($params);
//        prettyPrint($calevent->generate());
		$calres = $calevent->saveToGlobalCal();
		if (is_a($calres,"CalDAVObject")){
			echo "Kalendereintrag erfolgreich erstell!<br>";
		}
//        prettyPrint($calres);
	}

    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/vacation/vacation.user.php";</script>';
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
<!-- MegaNavbar -->
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!-- /MegaNavbar -->
<!-- Glyphicons -->
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />
<!-- /Glyphicons -->
<!-- FancyBox -->
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../../jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="../../../jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script language="javascript">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('#vac_start').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "../../../images/icons/calendar-blue.png",
//                buttonImageOnly: true
			}
     );
	$('#vac_end').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "../../../images/icons/calendar-blue.png",
//                buttonImageOnly: true
			}
     );
});
</script>

</head>
<body>

<form action="vacation.new.frame.php" method="post" name="event_form">
<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="eventid" value="<?=$_REQUEST["eventid"]?>">
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					Urlaub  -
					<? if ($_REQUEST["id"]) echo $_LANG->get('editieren'); else echo $_LANG->get('Neu')?>
					<span class="pull-right">
						<button class="btn btn-xs btn-success" type="submit">
							<?=$_LANG->get('Speichern')?>
						</button>
						<? if($vacation->getId() && ($vacation->getState() == VacationEntry::STATE_OPEN || $_USER->hasRightsByGroup(Permission::vacation_grant))) {?>
						<button class="btn btn-xs btn-danger" type="submit">
							<?=$_LANG->get('L&ouml;schen')?>
						</button>
						<?php  } ?>
						<?=$savemsg?>
					</span>
				</h3>
		  </div>
		  <div class="panel-body">
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Benutzer</label>
				  <div class="col-sm-4">
					  <select name="vac_user" id="vac_user" class="form-control">
						  <?php
						  if (!$_USER->hasRightsByGroup(Permission::vacation_grant))
						  {
							  echo '<option value="' . $_USER->getId() . '" selected>' . $_USER->getNameAsLine() . '</option>';
						  } else {
							  foreach ($users as $user) {
								  if ($vacation->getUser()->getId() == 0 && $user->getId() == $_USER->getId()) {
									  echo '<option value="' . $user->getId() . '" selected>' . $user->getNameAsLine() . '</option>';
								  } elseif ($vacation->getUser()->getId() == $user->getId()) {
									  echo '<option value="' . $user->getId() . '" selected>' . $user->getNameAsLine() . '</option>';
								  } else {
									  echo '<option value="' . $user->getId() . '">' . $user->getNameAsLine() . '</option>';
								  }
							  }
						  }
						  ?>
					  </select>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Von</label>
				  <div class="col-sm-4">
					  <input name="vac_start" id="vac_start" value="<?=date('d.m.Y', $vacation->getStart())?>" class="form-control">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Bis</label>
				  <div class="col-sm-4">
					  <input name="vac_end" id="vac_end" value="<?=date('d.m.Y', $vacation->getEnd())?>" class="form-control">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Tage</label>
				  <div class="col-sm-4">
					  <input name="vac_days" type="number" value="<?=$vacation->getDays()?>" step="0.5" class="form-control">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Status</label>
				  <div class="col-sm-4">
					  <select name="vac_state" class="form-control">
						  <?php
						  if (!$_USER->hasRightsByGroup(Permission::vacation_grant))
						  {
							  if ($vacation->getId()>0)
							  {
								  if ($vacation->getState()==VacationEntry::STATE_OPEN)
									  echo '<option value="'.VacationEntry::STATE_OPEN.'" selected>Offen</option>';
								  else
									  echo '<option value="'.VacationEntry::STATE_APPROVED.'" selected>Genehmigt</option>';
							  } else {
								  echo '<option value="'.VacationEntry::STATE_OPEN.'" selected>Offen</option>';
							  }
						  } else {
							  ?>
							  <option value="<? echo VacationEntry::STATE_OPEN;?>" <?php if ($vacation->getState() == VacationEntry::STATE_OPEN) echo ' selected ';?>>Offen</option>
							  <option value="<? echo VacationEntry::STATE_APPROVED;?>" <?php if ($vacation->getState() == VacationEntry::STATE_APPROVED) echo ' selected ';?>>Genehmigt</option>
						  <?php } ?>
					  </select>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Typ</label>
				  <div class="col-sm-4">
					  <select name="vac_type" class="form-control">
						  <option value="<? echo VacationEntry::TYPE_URLAUB;?>" <?php if ($vacation->getType() == VacationEntry::TYPE_URLAUB) echo ' selected ';?>>Urlaub</option>
						  <option value="<? echo VacationEntry::TYPE_KRANKHEIT;?>" <?php if ($vacation->getType() == VacationEntry::TYPE_KRANKHEIT) echo ' selected ';?>>Krankheit</option>
						  <option value="<? echo VacationEntry::TYPE_UEBERSTUNDEN;?>" <?php if ($vacation->getType() == VacationEntry::TYPE_UEBERSTUNDEN) echo ' selected ';?>>Überstunden</option>
						  <option value="<? echo VacationEntry::TYPE_SONSTIGES;?>" <?php if ($vacation->getType() == VacationEntry::TYPE_SONSTIGES) echo ' selected ';?>>Sonstiges</option>
					  </select>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Kommentar</label>
				  <div class="col-sm-4">
					  <textarea name="vac_comment" class="form-control"><?=$vacation->getComment()?></textarea>
				  </div>
			  </div>
		  </div>
	</div>
<br>
</form>