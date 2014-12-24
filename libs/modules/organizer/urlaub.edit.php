<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'nachricht.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];
$vacation = new Urlaub($_REQUEST["id"]);
if(!$vacation->getId())
    $vacation->setUseddays(1);

if($_REQUEST["exec"] == "newvacation")
{
    $vacation->setBegin(mktime(0,0,0,$_SESSION["vac_month"], $_REQUEST["day"], $_SESSION["vac_year"]));
    $vacation->setEnd(mktime(0,0,0,$_SESSION["vac_month"], $_REQUEST["day"], $_SESSION["vac_year"]));
    $vacation->setUser(new User((int)$_REQUEST["uid"]));
    $vacation->setState(1);
}

if($_REQUEST["subexec"] == "save")
{
    if ($vacation->getState() != Urlaub::STATE_APPROVED || $_REQUEST["exec"] == "newvacation")
    {
        // Diese Dinge nur ändern wenn Status nicht Approved ist.
        $_REQUEST["vac_from"]       = explode(".", $_REQUEST["vac_from"]);
        $_REQUEST["vac_from"]       = (int)mktime(0, 0, 0, $_REQUEST["vac_from"][1], $_REQUEST["vac_from"][0], $_REQUEST["vac_from"][2]);
        $_REQUEST["vac_to"]       = explode(".", $_REQUEST["vac_to"]);
        $_REQUEST["vac_to"]       = (int)mktime(0, 0, 0, $_REQUEST["vac_to"][1], $_REQUEST["vac_to"][0], $_REQUEST["vac_to"][2]);
        $vacation->setUser(new User((int)$_REQUEST["uid"]));
        $vacation->setNotes(trim(addslashes($_REQUEST["vac_note"])));
        $vacation->setReason((int)$_REQUEST["vac_type"]);
        $vacation->setUseddays((int)$_REQUEST["vac_days"]);
        $vacation->setBegin($_REQUEST["vac_from"]);
        $vacation->setEnd($_REQUEST["vac_to"]);
    }
    $vacation->setState((int)$_REQUEST["vac_state"]);
    $res = $vacation->save();
    $savemsg = getSaveMessage($res);
    
    if($res && $_REQUEST["id"] == 0)
    {
        $text = '<p>'.$_LANG->get('Hallo').',</p>
        <p></p>
        <p>'.$_LANG->get('eine neue Urlaubsanfrage ist eingegangen von Mitarbeiter').' '.$vacation->getUser()->getNameAsLine().':</p>
        <p></p>
        <p>
        <table>
            <tr><td><b>'.$_LANG->get('von').'</b></td><td>'.date('d.m.Y', $vacation->getBegin()).'</td></tr>
            <tr><td><b>'.$_LANG->get('bis').'</b></td><td>'.date('d.m.Y', $vacation->getEnd()).'</td></tr>
            <tr><td><b>'.$_LANG->get('Verwendete Urlaubstage').'</b></td><td>'.$vacation->getUseddays().'</td></tr>
            <tr><td><b>'.$_LANG->get('Grund').'</b></td><td>';
        if($vacation->getReason() == Urlaub::TYPE_URLAUB) $text .= $_LANG->get('Urlaub');
        if($vacation->getReason() == Urlaub::TYPE_KRANKHEIT) $text .= $_LANG->get('Krankheit');
        if($vacation->getReason() == Urlaub::TYPE_UEBERSTUNDEN) $text .= $_LANG->get('&Uuml;berstunden');
        if($vacation->getReason() == Urlaub::TYPE_SONSTIGES) $text .= $_LANG->get('Sonstiges');
        $text .= '</td></tr>
            <tr><td><b>'.$_LANG->get('Bemerkungen').'</b></td><td>'.nl2br($vacation->getNotes()).'</td></tr>
        </table></p>
        <p></p>';
        // Nachricht an die verschicken, die Urlaub genehmigen dürfen.
        $users = User::getAllUser();
        $to = Array();
        foreach($users as $u)
        {
            if($u->hasRightsByGroup(Group::RIGHT_URLAUB))
            {
                $to[] = $u;
            }
        }
        $msg = new Nachricht();
        $msg->setTo($to);
        $msg->setText($text);
        $msg->setSubject("".$_LANG->get('Neuer Urlaubsantrag von')." ".$vacation->getUser()->getNameAsLine());
        $msg->setFrom($vacation->getUser());
        $msg->send();
    }
}


?>

<script language="javascript">
function calcDays()
{
	if (document.getElementById('vac_type').value == 1)
	{
    	var dat1 = document.getElementById('vac_from').value;
    	var dat2 = document.getElementById('vac_to').value;
    
    	if(dat1 != '' && dat2 != '')
    	{
    		$.post("libs/modules/organizer/calcdays.ajax.php", { from: dat1, to: dat2 },
    				   function(data) {
    				     document.getElementById('vac_days').value = data;
    				   });
    	}
	} else
	{
		document.getElementById('vac_days').value = 0;
	}
}

function checkDates()
{
	var year1 = document.getElementById('vac_from').value.substr(6);
	var year2 = document.getElementById('vac_to').value.substr(6);

	if (year1 != year2)
	{
		alert('<?=$_LANG->get('Bitte fuer das naechste Jahr getrennt Urlaub beantragen.')?>');
		document.getElementById('vac_to').value = '31.12.'+year1;
	}
}

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#vac_from').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
	
	$('#vac_to').datepicker(
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

</script>



<link rel="stylesheet" type="text/css" href="./css/urlaub.css" />
<table width="100%">
	<tr>
		<td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
		    <? if ($_REQUEST["exec"] == "newvacation") echo $_LANG->get('Urlaub eintragen'); else echo $_LANG->get('Urlaub &auml;ndern'); ?>
		</td>
		<td class="content_header" align="right"><?=$savemsg?></td>
	</tr>
</table>

<div class="box1">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="vacation_form">
<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=$vacation->getId()?>">
<input type="hidden" name="uid" value="<?=$vacation->getUser()->getId()?>">
<table width="100%">
<colgroup>
    <col width="150">
    <col>
</colgroup>

<tr>
    <td class="content_row_header"><?=$_LANG->get('Von')?></td>
    <td class="content_row_clear">
        <? if ($vacation->getState() == Urlaub::STATE_APPROVED) { ?>
            <?=date('d.m.Y', $vacation->getBegin())?>
        <?  } else { ?>
            <input name="vac_from" id="vac_from" style="width:80px"
            class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
            onchange="calcDays();checkDates()" value="<?=date('d.m.Y', $vacation->getBegin())?>">
        <? } ?>
    </td>
</tr>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Bis')?></td>
    <td class="content_row_clear">
        <? if ($vacation->getState() == Urlaub::STATE_APPROVED) { ?>
            <?=date('d.m.Y', $vacation->getEnd())?>
        <?  } else { ?>    
            <input name="vac_to" id="vac_to" style="width:80px"
            class="text" onchange="calcDays();checkDates()" value="<?=date('d.m.Y', $vacation->getEnd())?>">
        <? } ?>
    </td>
</tr>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
    <td class="content_row_clear">
        <? if ($vacation->getState() == Urlaub::STATE_APPROVED) { 
            switch ($vacation->getReason())
            {
                case Urlaub::TYPE_URLAUB:
                    echo $_LANG->get('Urlaub'); break;
                case Urlaub::TYPE_KRANKHEIT:
                    echo $_LANG->get('Krank'); break;
                case Urlaub::TYPE_UEBERSTUNDEN:
                    echo $_LANG->get('&Uuml;berstunden'); break;
                case Urlaub::TYPE_SONSTIGES:
                    echo $_LANG->get('Sonstiges'); break;    
            } 
        } else { ?>
            <select name="vac_type" id="vac_type" style="width:300px" class="text" onchange="calcDays()">
                <option value="<?=Urlaub::TYPE_URLAUB?>" <?if($vacation->getReason() == Urlaub::TYPE_URLAUB) echo "selected";?>><?=$_LANG->get('Urlaub')?></option>
                <option value="<?=Urlaub::TYPE_UEBERSTUNDEN?>" <?if($vacation->getReason() == Urlaub::TYPE_UEBERSTUNDEN) echo "selected";?>><?=$_LANG->get('&Uuml;berstunden')?></option>
                <option value="<?=Urlaub::TYPE_KRANKHEIT?>" <?if($vacation->getReason() == Urlaub::TYPE_KRANKHEIT) echo "selected";?>><?=$_LANG->get('Krank')?></option>
                <option value="<?=Urlaub::TYPE_SONSTIGES?>" <?if($vacation->getReason() == Urlaub::TYPE_SONSTIGES) echo "selected";?>><?=$_LANG->get('Sonstiges')?></option>
            </select>
        <? } ?>
    </td>
</tr>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Verbrauchte Tage')?></td>
    <td class="content_row_clear">
        <? if ($vacation->getState() == Urlaub::STATE_APPROVED) {
            echo $vacation->getUseddays()." Tage";
        } else { ?>
            <input name="vac_days" id="vac_days" style="width:80px" class="text" value="<?=$vacation->getUseddays()?>"> Tage
        <?  } ?>
    </td>
</tr>
<tr>
    <td class="content_row_header" valign="top"><?=$_LANG->get('Bemerkungen')?></td>
    <td class="content_row_clear">
        <? if ($vacation->getState() == Urlaub::STATE_APPROVED) {
            echo nl2br($vacation->getNotes());
        } else { ?>
            <textarea name="vac_note" style="width:300px;height:150px" class="text" <?=$rdly?>><?=$vacation->getNotes()?></textarea>
        <?  } ?>
    </td>
</tr>
<? if($_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_URLAUB)) { ?>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Status')?></td>
    <td class="content_row_clear">
        <select name="vac_state" id="vac_state" style="width:300px" class="text" <?=$rdly?>>
            <option value="<?=Urlaub::STATE_UNSEEN?>" <?if($vacation->getState() == Urlaub::STATE_UNSEEN) echo "selected";?>><?=$_LANG->get('Offen')?></option>
            <option value="<?=Urlaub::STATE_WAIT?>" <?if($vacation->getState() == Urlaub::STATE_WAIT) echo "selected";?>><?=$_LANG->get('Wartend')?></option>
            <option value="<?=Urlaub::STATE_APPROVED?>" <?if($vacation->getState() == Urlaub::STATE_APPROVED) echo "selected";?>><?=$_LANG->get('Genehmigt')?></option>
        </select>
    </td>
</tr>
<?  }
echo "</table></div><br><br>";
if($vacation->getState() != Urlaub::STATE_APPROVED || $_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_URLAUB))
{
    echo '<input value="'.$_LANG->get('Speichern').'" type="submit">&nbsp;';
    echo '<input type="button" class="buttonRed" value="'.$_LANG->get('L&ouml;schen').'" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&exec=delvacation&id='.$vacation->getId().'\')">';
} ?>
</form>
