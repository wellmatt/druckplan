<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       21.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'chromaticity.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $chr = new Chromaticity($_REQUEST["id"]);
    $savemsg = getSaveMessage($chr->delete());
}

if($_REQUEST["exec"] == "copy" || $_REQUEST["exec"] == "edit")
{
    require_once 'chromaticity.edit.php';
} else
{
    $chromaticities = Chromaticity::getAllChromaticities(Chromaticity::ORDER_NAME);
?><div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Farbigkeit</h3>
    </div>
    <div class="panel-heading">
        <h3 class="panel-title">Farbigkeit hinzuf&uuml;gen</h3>
    </div>
    <div class="table-responsive">
        <br>
        <table id="cp_table" class="table table-hover">
            <thead>
            <tr>
                <td class="content_row_header"><?=$_LANG->get('ID')?></td>
                <td class="content_row_header"><?=$_LANG->get('Bezeichnung')?></td>
                <td class="content_row_header"><?=$_LANG->get('Vorderseite')?></td>
                <td class="content_row_header"><?=$_LANG->get('R&uuml;ckseite')?></td>
                <td class="content_row_header"><?=$_LANG->get('Sch&ouml;n-/Widerdruck')?></td>
                <td class="content_row_header"><?=$_LANG->get('Aufschlag')?></td>
                <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
            </tr>
            </thead>
        </table>
    </div>
</div>

<table width="100%">
    <tr>
        <td width="200" class="content_header"><img
                src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Farbigkeit')?>
        </td>
        <td><?=$savemsg?></td>
        <td width="200" class="content_header" align="right"><a class="icon-link"
                                                                href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img
                    src="images/icons/color--plus.png"> <?=$_LANG->get('Farbigkeit hinzuf&uuml;gen')?>
            </a></td>
    </tr>
</table>
<div class="box1">
<table width="100%"	border="0" cellpadding="0" cellspacing="0">
    <colgroup>
        <col width="20">
        <col>
        <col width="100">
        <col width="100">
        <col width="180">
        <col width="100">
        <col width="100">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('ID')?></td>
        <td class="content_row_header"><?=$_LANG->get('Bezeichnung')?></td>
        <td class="content_row_header"><?=$_LANG->get('Vorderseite')?></td>
        <td class="content_row_header"><?=$_LANG->get('R&uuml;ckseite')?></td>
        <td class="content_row_header"><?=$_LANG->get('Sch&ouml;n-/Widerdruck')?></td>
        <td class="content_row_header"><?=$_LANG->get('Aufschlag')?></td>
        <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
    </tr>
    <? $x = 1;
    foreach($chromaticities as $chr)
    {
    ?>
    <tr class="<?=getRowColor($x)?>">
        <td class="content_row"><?=$chr->getId()?></td>
        <td class="content_row"><?=$chr->getName()?></td>
        <td class="content_row"><?=$chr->getColorsFront()?></td>
        <td class="content_row"><?=$chr->getColorsBack()?></td>
        <td class="content_row">
        <?if($chr->getReversePrinting())
            echo $_LANG->get('Sch&ouml;n- und Widerdruck');
        else
            echo $_LANG->get('Sch&ouml;ndruck');
        ?></td>
        <td class="content_row"><?=printPrice($chr->getMarkup())?> %</td>
        <td class="content_row">
            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$chr->getId()?>"><img src="images/icons/pencil.png"></a>
            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$chr->getId()?>"><img src="images/icons/scripts.png"></a>
            <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$chr->getId()?>')"><img src="images/icons/cross-script.png"></a>
        </td>
    </tr>
    <?     
    $x++;}
    ?>
</table>
</div>
<? } ?>