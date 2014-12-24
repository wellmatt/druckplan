<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$finishing = new Finishing($_REQUEST["id"]);
if($_REQUEST["exec"] == "copy")
{
	$finishing->clearID();
}
if($_REQUEST["subexec"] == "save")
{
	$finishing->setName(trim(addslashes($_REQUEST["finishing_name"])));
	$finishing->setBeschreibung(trim(addslashes($_REQUEST["finishing_beschreibung"])));
	$finishing->setKosten((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["finishing_kosten"]))));
	$savemsg = getSaveMessage($finishing->save()).$DB->getLastError();
}
?>
<table width="100%">
<tr>
<td width="200" class="content_header">
<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Lacke hinzuf&uuml;gen')?>
<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Lacke &auml;ndern')?>
<?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Lacke kopieren')?>
</td>
<td align="right"><?=$savemsg?></td>
</tr>
</table>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="paper_form" onSubmit="return checkform(new Array(this.finishing_name))">
	<div class="box1">
		<input name="exec" value="edit" type="hidden">
		<input type="hidden" name="subexec" value="save">
		<input name="id" value="<?=$finishing->getId()?>" type="hidden">
		<table width="100%">
		    <colgroup>
		        <col width="200">
		        <col>
		    </colgroup>
		    <tr>
		        <td class="content_row_header"><?=$_LANG->get('Name')?> *</td>
		        <td class="content_row_clear">
		            <input id="finishing_name" name="finishing_name" value="<?=$finishing->getName()?>" class="text" style="width:300px">
		        </td>
		     <td valign="top">
				<table width="500" cellpadding="0" cellspacing="0" border="0">
				    <colgroup>
				        <col width="180">
				        <col>
				    </colgroup>
				    <? if($finishing->getLectorId() != 0) { ?>
				    <tr>
				        <td class="content_row_header"><span class="error"><?=$_LANG->get('Importiert von Lector')?></span></td>
				        <td class="content_row_clear">
				            Lector-ID: <?=$finishing->getLectorId()?>
				        </td>
				    </tr>
				    <? } ?>
				</table>      
		    </tr>
		    <tr>
		        <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
		        <td class="content_row_clear">
		            <input name="finishing_beschreibung" value="<?=$finishing->getBeschreibung()?>" class="text" style="width:300px">      
		        </td>   
		    </tr>
		     <tr>
		        <td class="content_row_header"><?=$_LANG->get('Kosten')?></td>
		        <td class="content_row_clear">
		            <input name="finishing_kosten" value="<?=printPrice($finishing->getKosten())?>" class="text" style="width:60px"> <?=$_USER->getClient()->getCurrency() ?>      
		        </td>   
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