<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       26.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'schedule.class.php';

if($_REQUEST["selday"])
    $_SESSION["delivery_selday"] = trim(addslashes($_REQUEST["selday"]));
else
    if(!$_SESSION["delivery_selday"])
        $_SESSION["delivery_selday"] = "all";

$scheds = Schedule::getAllSchedules(Schedule::ORDER_DELIVERY_DATE, Schedule::STATUS_ORDER_OPEN);

foreach($scheds as $s)
{
    if($s->getStatus() == 1)
    {
        if($_SESSION["delivery_selday"] == "all")
            $data[$s->getDeliveryDate()][$s->getDeliveryterms()->getName1()][] = $s;
        else 
            if($s->getDeliveryDate() == $_SESSION["delivery_selday"])
                $data[$s->getDeliveryDate()][$s->getDeliveryterms()->getName1()][] = $s;
    }
    $days[$s->getDeliveryDate()] = 1;
}
?>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
		    <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Auslieferungsplan')?>
		</td>
		<td align="right"><?=$savemsg?></td>
		<td align="right" style="padding-right:5px">
            <ul class="postnav_save">
                <a href="libs/modules/schedule/deliveryplan.doc.php?day=<?=$_SESSION["delivery_selday"]?>" target="_blank"><?=$_LANG->get('PDF-Anzeige')?></a>
            </ul>
        </td>
	</tr>
</table>

<select class="text" style="width:150px" name="day"
onchange="location.href='index.php?page=<?=$_REQUEST['page']?>&selday=' +this.value"
onfocus="markfield(this,0)" onblur="markfield(this,1)">
  <option value="all" <? if($_SESSION["delivery_selday"] == "all") echo "selected"?>><?=$_LANG->get('Alle Tage')?></option>
  <?
  foreach(array_keys($days) AS $day)
  {  ?>
     <option value="<?=$day?>" <? if($_SESSION["delivery_selday"] == $day) echo "selected"?>><?=date('d.m.Y', $day)?></option><?
  }
  ?>
</select>
<br><br>

<? 
foreach (array_keys($data) as $day)
{
    ?>
    <h1 class="content_header" ><img src="./images/icons/calendar-blue.png"> &nbsp; <?=date('d.m.Y', $day)?></h1>

    <?
    foreach(array_keys($data[$day]) as $location)
    {
        echo '<b class="content_message" style="padding-left:25px">'.$_LANG->get('Versandart').': '.$location.'</b>';
        echo '<br><br>';
        
        ?>
        <div class="box2" style="width:1300px">
        <table border="0" class="content_table" cellpadding="3" cellspacing="0" width="100%" style="table-layout:fixed">
            <colgroup>
                <col width="45">
                <col width="80">
                <col width="110">
                <col>
                <col>
                <col width="60">
                <col>
                <col>
                <col> 
            </colgroup>
            <tr>
                <td class="content_tbl_header" colspan="9"></td>
            </tr>
            <tr>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('ID')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Ersteller')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Auftr.-Nr.')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Kunde')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Objekt')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Auflage')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Farben')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Lieferort')?></nobr></td>
                <td class="content_row_subheader"><nobr><?=$_LANG->get('Bemerkungen')?></nobr></td>
            </tr>
            <? 
            $x = 0;
            foreach($data[$day][$location] as $s) {
            ?>
            <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)"
                onclick="location.href='index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$s->getId()?>'">
                <td class="content_row" valign="top"><?=$s->getId()?></td>
                <td class="content_row" valign="top"><?=$s->getCreateUser()?>&nbsp;</td>
                <td class="content_row" valign="top"><a href="index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$s->getId()?>"><?=$s->getNumber()?></a></td>
                <td class="content_row" valign="top"><?=$s->getCustomer()->getNameAsLine()?></td>
                <td class="content_row" valign="top"><?=$s->getObject()?>&nbsp;</td>
                <td class="content_row" valign="top"><?=$s->getAmount()?>&nbsp;</td>
                <td class="content_row" valign="top"><?=$s->getColors()?>&nbsp;</td>
                <td class="content_row" valign="top"><nobr><?=$s->getDeliveryLocation()?>&nbsp;</nobr></td>
                <td class="content_row" valign="top"><?=$s->getNotes()?>&nbsp;</td>
            </tr>
            <? $x++;} ?>
        </table>
        </div>     
        <br><br> 
        <? 
    }
}
?>