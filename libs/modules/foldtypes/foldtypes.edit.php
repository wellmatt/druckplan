<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$ft = new Foldtype($_REQUEST["id"]);

// Falls kopieren, ID löschen -> Maschine wird neu angelegt
if($_REQUEST["exec"] == "copy")
    $ft->clearId();

if($_REQUEST["subexec"] == "save")
{
    $ft->setName(trim(addslashes($_REQUEST["foldtype_name"])));
    $ft->setDescription(trim(addslashes($_REQUEST["foldtype_description"])));
    $ft->setVertical((int)$_REQUEST["foldtype_vertical"]);
    $ft->setHorizontal((int)$_REQUEST["foldtype_horizontal"]);
    $ft->setPicture(trim(addslashes($_REQUEST["picture"])));
    $savemsg = getSaveMessage($ft->save());
}

?>
<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		$("a#picture_select").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>
<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
          <? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Falzart kopieren')?>
          <? if ($_REQUEST["exec"] == "edit" && $ft->getId() == 0) echo $_LANG->get('Falzart anlegen')?>
          <? if ($_REQUEST["exec"] == "edit" && $ft->getId() != 0) echo $_LANG->get('Falzart bearbeiten')?>
      </td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="foldtype_form" onSubmit="return checkform(new Array(this.foldtype_name,this.foldtype_description))">
	<input type="hidden" name="exec" value="edit">
	<input type="hidden" name="subexec" value="save">
	<input type="hidden" name="id" value="<?=$ft->getId()?>">
	<input type="hidden" name="picture" id="picture" value="<?=$ft->getPicture()?>">
	<div class="box1">
		<table width="100%">
		    <colgroup>
		        <col width="180">
		        <col width="300">
		        <col width="180">
		        <col width="300">
		    </colgroup>
		    <tr>
		        <td class="content_row_header"><?=$_LANG->get('Bezeichnung')?> *</td>
		        <td class="content_row_clear">
		            <input name="foldtype_name" value="<?=$ft->getName()?>" style="width:300px;" class="text">
		        </td>
		        <td class="content_row_header" rowspan="2" valign="top">
		            <?=$_LANG->get('Beispielbild')?> *<br>
		            <a href="libs/modules/foldtypes/picture.iframe.php" id="picture_select" class="products"><input type="button" class="button" value="<?=$_LANG->get('Ändern')?>"></a>
		            <? if($ft->getPicture() != "") {?>
		                <input type="button" class="buttonRed" value="<?=$_LANG->get('L&ouml;schen')?>" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$ft->getId()?>&deletePicture=1'">
		            <? } ?>
		        </td>
		        <td class="content_row_clear" rowspan="2" valign="top" id="picture_show">
		            <img src="images/foldtypes/<?=$ft->getPicture()?>">&nbsp;
		        </td> 
		    </tr>
		    <tr>
		        <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?> *</td>
		        <td class="content_row_clear">
		            <textarea name="foldtype_description" style="width:300px;height:100px" class="text"><?=$ft->getDescription()?></textarea>
		        </td>
		    </tr>
		    <tr>
		        <td class="content_row_header"><?=$_LANG->get('Falzen vertikal')?></td>
		        <td class="content_row_clear">
		            <input name="foldtype_vertical" value="<?=$ft->getVertical()?>" style="width:60px;" class="text">
		        </td>
		    </tr>
		    <tr>
		        <td class="content_row_header"><?=$_LANG->get('Falzen horizontal')?></td>
		        <td class="content_row_clear">
		            <input name="foldtype_horizontal" value="<?=$ft->getHorizontal()?>" style="width:60px;" class="text">
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