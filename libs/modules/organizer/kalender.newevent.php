<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       07.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'libs/modules/businesscontact/contactperson.class.php';

$_REQUEST["id"] = (int)$_REQUEST["id"];
$event = new Event($_REQUEST["id"]);

// Startzeit setzen
if($_REQUEST["hour"])
    $_REQUEST["hour"] = explode(":", $_REQUEST["hour"]);
else
{
    $_REQUEST["hour"][0] = 7;
    $_REQUEST["hour"][1] = 0;
} 
    

$tmeStart = mktime((int)$_REQUEST["hour"][0], (int)$_REQUEST["hour"][1], 0, (int)$_REQUEST["cal_month"], (int)$_REQUEST["day"], (int)$_REQUEST["cal_year"]);

if($event->getBegin() == 0)
    $event->setBegin($tmeStart);
if($event->getEnd() == 0)
    $event->setEnd($tmeStart+3600);

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
	
	if (count($int_partitipants) > 0)
		$event->setParticipantsInt($int_partitipants);
		
	if (count($ext_partitipants) > 0)
		$event->setParticipantsExt($ext_partitipants);

    $_REQUEST["event_from_hour"]    = (int)$_REQUEST["event_from_hour"];
    $_REQUEST["event_from_minute"]  = (int)$_REQUEST["event_from_minute"];
    $_REQUEST["event_from_date"]    = explode(".", $_REQUEST["event_from_date"]);
    $event_begin    = (int)mktime($_REQUEST["event_from_hour"], $_REQUEST["event_from_minute"], 0, $_REQUEST["event_from_date"][1], $_REQUEST["event_from_date"][0], $_REQUEST["event_from_date"][2]);
    
    $_REQUEST["event_to_hour"]    = (int)$_REQUEST["event_to_hour"];
    $_REQUEST["event_to_minute"]  = (int)$_REQUEST["event_to_minute"];
    $_REQUEST["event_to_date"]    = explode(".", $_REQUEST["event_to_date"]);
    $event_end    = (int)mktime($_REQUEST["event_to_hour"], $_REQUEST["event_to_minute"], 0, $_REQUEST["event_to_date"][1], $_REQUEST["event_to_date"][0], $_REQUEST["event_to_date"][2]);
    
    if (!$event->getUser())
        $event->setUser($_USER);
    $event->setBegin($event_begin);
    $event->setEnd($event_end);
    $event->setPublic((int)$_REQUEST["event_public"]);
    $event->setTitle(trim(addslashes($_REQUEST["event_title"])));
    $event->setDesc(trim(addslashes($_REQUEST["event_desc"])));
    
    $savemsg = getSaveMessage($event->save());
    echo $DB->getLastError();
}

?>

<!-- FancyBox -->
<script
	type="text/javascript"
	src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script
	type="text/javascript"
	src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link
	rel="stylesheet" type="text/css"
	href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script language="javascript">

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('#event_from_date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );

	$('#event_to_date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
    
});

function removeParticipant(what, id)
{
    if (what == 'user')
    {
        document.getElementById('participant_int_'+id).disabled = true;
        document.getElementById('span_participant_int_'+id).style.display = 'none';
    } else if (what == 'contactperson')
    {
        document.getElementById('participant_ext_'+id).disabled = true;
        document.getElementById('span_participant_ext_'+id).style.display = 'none';
    }
}

$(document).ready(function() {

	$("a#add_int").fancybox({
	'type'    : 'iframe'
	});
	$("a#add_ext").fancybox({
	'type'    : 'iframe'
	});
});

</script>

<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Kalender');?> - 
            <? if ($_REQUEST["id"]) echo $_LANG->get('Termin editieren'); else echo $_LANG->get('Neuer Termin')?>
        </td>
        <td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<div class="box1">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="event_form">
<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=$_REQUEST["id"]?>">
<table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Von')?></td>
        <td class="content_row_clear">
            <input name="event_from_date" value="<?=date('d.m.Y', $event->getBegin())?>" style="width:80px;"
            class="text" id="event_from_date">&nbsp;
            <input name="event_from_hour" value="<?=date('H', $event->getBegin())?>" style="width:30px;" class="text"> :
            <input name="event_from_minute" value="<?=date('i', $event->getBegin())?>" style="width:30px;" class="text">
        </td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Bis')?></td>
        <td class="content_row_clear">
            <input name="event_to_date" value="<?=date('d.m.Y', $event->getEnd())?>" style="width:80px;"
            class="text" id="event_to_date">&nbsp;
            <input name="event_to_hour" value="<?=date('H', $event->getEnd())?>" style="width:30px;" class="text"> :
            <input name="event_to_minute" value="<?=date('i', $event->getEnd())?>" style="width:30px;" class="text">
        </td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('&Ouml;ffentlich')?></td>
        <td class="content_row_clear">
            <input type="radio" name="event_public" value="0" <? if($event->getPublic() == 0) echo "checked"?>> <?=$_LANG->get('Nein')?>
            <input type="radio" name="event_public" value="1" <? if($event->getPublic() == 1) echo "checked"?>> <?=$_LANG->get('Ja')?>
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
					$addStr = '<span class="newmailToField" id="span_participant_int_'.$part_int->getId().'"><img src="images/icons/user.png" />&nbsp;'.$part_int->getFirstname().'&nbsp;'.$part_int->getLastname();
					$addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeParticipant(\'user\', '.$part_int->getId().')" />';
					$addStr .= '<input type="hidden" name="participant_int[]" id="participant_int_'.$part_int->getId().'" value="'.$part_int->getId().'"></span>';
					echo $addStr;
				}
			}
		?>
		<a href="libs/modules/organizer/kalender.addpart.php"  class="icon-link"	id="add_int"><img src="images/icons/plus-white.png" title="<?=$_LANG->get('Hinzuf&uuml;gen')?>"> </a>
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
					$addStr = '<span class="newmailToField" id="span_participant_ext_'.$part_ext->getId().'"><img src="images/icons/user.png" />&nbsp;'.$part_ext->getNameAsLine2().'&nbsp;';
					$addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeParticipant(\'contactperson\', '.$part_ext->getId().')" />';
					$addStr .= '<input type="hidden" name="participant_ext[]" id="participant_ext_'.$part_ext->getId().'" value="'.$part_ext->getId().'"></span>';
					echo $addStr;
				}
			}
		?>
		<a href="libs/modules/organizer/kalender.addpart.php"  class="icon-link"	id="add_ext"><img src="images/icons/plus-white.png" title="<?=$_LANG->get('Hinzuf&uuml;gen')?>"> </a>
        </td>
    </tr>
</table>
</div>
<br>
<input type="submit" value="<?=$_LANG->get('Speichern')?>" class="text">
<? if($event->getId()) { 
    echo '<input type="button" class="buttonRed" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&exec=delevent&id='.$event->getId().'\')" value="'.$_LANG->get('L&ouml;schen').'">';
 } ?> 
</form>