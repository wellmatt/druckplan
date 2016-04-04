<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'machine.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $machine = new Machine($_REQUEST["id"]);
    $machine->delete(); 
}

if($_REQUEST["exec"] == "copy" || $_REQUEST["exec"] == "edit")
{
    require_once 'machines.edit.php';
} else 
{

$machines = Machine::getAllMachines(Machine::ORDER_GROUP_NAME);

?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Maschinen')?></td>
      <td><?=$savemsg?></td>
      <td width="200" class="content_header" align="right"><a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/drill--plus.png"> <?=$_LANG->get('Maschine hinzuf&uuml;gen')?></a></td>
   </tr>
</table>
<div class="box1">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <colgroup>
        <col width="20">
        <col width="150">
        <col>
        <col width="70">
        <col width="100">
        <col width="100">
        <col width="100">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('ID')?></td>
        <td class="content_row_header"><?=$_LANG->get('Maschinengruppe')?></td>
        <td class="content_row_header"><?=$_LANG->get('Bezeichnung')?></td>
        <td class="content_row_header"><?=$_LANG->get('Preis')?></td>
        <td class="content_row_header"><?=$_LANG->get('Maschinentyp')?></td>
        <td class="content_row_header"><?=$_LANG->get('Einheit')?></td>
        <td class="content_row_header"><?=$_LANG->get('Importiert')?></td>
        <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
    </tr>
    <? $x = 0; 
    foreach($machines as $m)
    {?>
        <tr class=<?=getRowColor($x)?>>
            <td class="content_row"><?=$m->getId()?></td>
            <td class="content_row"><?=$m->getGroup()->getName()?></td>
            <td class="content_row"><?=$m->getName()?></td>
            <td class="content_row"><?=printPrice($m->getPrice(),4)?> <?=$_USER->getClient()->getCurrency()?></td>
            <td class="content_row">
            	<? if($m->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo $_LANG->get('Druckmaschine Offset')?>
                <? if($m->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo $_LANG->get('Druckmaschine Digital')?>
                <? if($m->getType() == Machine::TYPE_CTP) echo $_LANG->get('Computer To Plate')?>
                <? if($m->getType() == Machine::TYPE_FOLDER) echo $_LANG->get('Falzmaschine')?>
                <? if($m->getType() == Machine::TYPE_CUTTER) echo $_LANG->get('Schneidemaschine')?>
                <? if($m->getType() == Machine::TYPE_LASERCUTTER) echo $_LANG->get('Stanze / Laser-Stanze')?>
                <? if($m->getType() == Machine::TYPE_LAGENFALZ) echo $_LANG->get('Lagenfalz-/Zusammentragmaschine')?>
                <? if($m->getType() == Machine::TYPE_SAMMELHEFTER) echo $_LANG->get('Sammelhefter')?>
                <? if($m->getType() == Machine::TYPE_MANUELL) echo $_LANG->get('Manuelle Arbeit')?>
            	<? if($m->getType() == Machine::TYPE_OTHER) echo $_LANG->get('Andere')?>
			</td>
            <td class="content_row">
                <? if($m->getPriceBase() == Machine::PRICE_AUFLAGE) echo $_LANG->get('nach Auflage')?>
                <? if($m->getPriceBase() == Machine::PRICE_BOGEN) echo $_LANG->get('nach Bogen')?>
                <? if($m->getPriceBase() == Machine::PRICE_DRUCKPLATTE) echo $_LANG->get('nach Druckplatten')?>
                <? if($m->getPriceBase() == Machine::PRICE_MINUTE) echo $_LANG->get('nach Minute')?>
                <? if($m->getPriceBase() == Machine::PRICE_PAUSCHAL) echo $_LANG->get('pauschal')?>
                <? if($m->getPriceBase() == Machine::PRICE_VARIABEL) echo $_LANG->get('variabel')?>
            </td>
            <td class="content_row">
                <? if($m->getLectorId()) echo '<span class="error">Importiert</span>'; else echo '&nbsp;'?>
            </td>
            <td class="content_row">
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$m->getId()?>"><img src="images/icons/pencil.png"></a>
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$m->getId()?>"><img src="images/icons/scripts.png"></a>
                <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$m->getId()?>')"><img src="images/icons/cross-script.png"></a>
            </td>
        </tr>
        
    <? $x++; }
    ?>
</table>
</div>
<?  } ?>