<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       21.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$chr = new Chromaticity($_REQUEST["id"]);

// 
if($_REQUEST["exec"] == "copy")
    $chr->clearId();

if($_REQUEST["subexec"] == "save")
{
    $chr->setName(trim(addslashes($_REQUEST["chr_name"])));
    $chr->setColorsFront((int)$_REQUEST["chr_color_front"]);
    $chr->setColorsBack((int)$_REQUEST["chr_color_back"]);
    $chr->setReversePrinting((int)$_REQUEST["chr_reverse"]);
    $chr->setMarkup((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["chr_markup"]))));
    $savemsg = getSaveMessage($chr->save());
}
?>

<table width="100%">
    <tr>
        <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
            <? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Farbigkeit kopieren')?>
            <? if ($_REQUEST["exec"] == "edit" && $chr->getId() == 0) echo $_LANG->get('Farbigkeit anlegen')?>
            <? if ($_REQUEST["exec"] == "edit" && $chr->getId() != 0) echo $_LANG->get('Farbigkeit bearbeiten')?>
        </td>
        <td align="right"><?=$savemsg?></td>
    </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="chromaticity_form" onSubmit="return checkform(new Array(this.chr_name))">
<input type="hidden" name="exec" value="edit">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=$chr->getId()?>">
<div class="box1">
	<table width="500">
	    <colgroup>
	        <col width="180">
	        <col>
	    </colgroup>
	    <tr>
	        <td class="content_row_header"><?=$_LANG->get('Bezeichnung')?> *</td>
	        <td class="content_row_clear"><input id="chr_name" name="chr_name" style="width:300px" class="text" value="<?=$chr->getName()?>"></td>
	    </tr>
	    <tr>
	        <td class="content_row_header"><?=$_LANG->get('Farben Vorderseite')?></td>
	        <td class="content_row_clear"><input name="chr_color_front" style="width:60px" class="text" value="<?=$chr->getColorsFront()?>"></td>
	    </tr>
	    <tr>
	        <td class="content_row_header"><?=$_LANG->get('Farben R&uuml;ckseite')?></td>
	        <td class="content_row_clear"><input name="chr_color_back" style="width:60px" class="text" value="<?=$chr->getColorsBack()?>"></td>
	    </tr>
	    <tr>
	        <td class="content_row_header"><?=$_LANG->get('Sch&ouml;n- und Widerdruck')?></td>
	        <td class="content_row_clear"><input type="checkbox" name="chr_reverse" value="1" <? if($chr->getReversePrinting()) echo "checked";?>></td>
	    </tr>
	    <tr>
	        <td class="content_row_header"><?=$_LANG->get('Aufschlag auf Maschinenpreis')?></td>
	        <td class="content_row_clear"><input name="chr_markup" style="width:60px" class="text" value="<?=printPrice($chr->getMarkup())?>"> %</td>
	    </tr>

	</table>
</div>
<br/>
<?// Speicher & Navigations-Button ?>
<table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup> 
    <tr>
        <td class="content_row_header">
        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
        </td>
        <td class="content_row_clear" align="right">
        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
        </td>
    </tr>
</table>
</form>