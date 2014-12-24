<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       07.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$event = new Event((int)$_REQUEST["id"]);
?>
<link rel="stylesheet" type="text/css" href="./css/calendar.css" />
<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Kalender');?> - <?=$_LANG->get('Detailansicht');?>
        </td>
        <td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
<input type="hidden" name="exec" value="showday">
<input type="hidden" name="day" value="<?=$_REQUEST["day"]?>">
<table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Von')?></td>
        <td class="content_row_clear"><?=date('d.m.Y - H:i', $event->getBegin())?> Uhr</td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Bis')?></td>
        <td class="content_row_clear"><?=date('d.m.Y - H:i', $event->getEnd())?> Uhr</td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('&Ouml;ffentlich')?></td>
        <td class="content_row_clear"><? if($event->getPublic() == 1) echo "Ja"; else echo "Nein"?></td>
    </tr>    
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Titel')?></td>
        <td class="content_row_clear"><?=$event->getTitle()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?></td>
        <td class="content_row_clear"><?=$event->getDesc()?></td>
    </tr>
</table>
<input type="submit" value="<?=$_LANG->get('Zur&uuml;ck')?>">
</form>