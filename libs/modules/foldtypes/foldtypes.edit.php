<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$ft = new Foldtype($_REQUEST["id"]);

// Falls kopieren, ID l�schen -> Maschine wird neu angelegt
if($_REQUEST["exec"] == "copy")
    $ft->clearId();

if($_REQUEST["subexec"] == "save")
{
    $ft->setName(trim(addslashes($_REQUEST["foldtype_name"])));
    $ft->setDescription(trim(addslashes($_REQUEST["foldtype_description"])));
    $ft->setVertical((int)$_REQUEST["foldtype_vertical"]);
    $ft->setHorizontal((int)$_REQUEST["foldtype_horizontal"]);
    $ft->setPicture(trim(addslashes($_REQUEST["picture"])));
    $ft->setBreaks((int)$_REQUEST["foldtype_breaks"]);
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

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
		<a href="#top" class="menu_item">Seitenanfang</a>
		<a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
		<a href="#" class="menu_item" onclick="$('#foldtype_form').submit();">Speichern</a>
	</div>
</div>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				<? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Falzart kopieren')?>
				<? if ($_REQUEST["exec"] == "edit" && $ft->getId() == 0) echo $_LANG->get('Falzart anlegen')?>
				<? if ($_REQUEST["exec"] == "edit" && $ft->getId() != 0) echo $_LANG->get('Falzart bearbeiten')?>
			</h3>
	  </div>

	<div class="panel-body">
		<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="foldtype_form" name="foldtype_form"
			  class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.foldtype_name,this.foldtype_description))">
			<input type="hidden" name="exec" value="edit">
			<input type="hidden" name="subexec" value="save">
			<input type="hidden" name="id" value="<?=$ft->getId()?>">
			<input type="hidden" name="picture" id="picture" value="<?=$ft->getPicture()?>">

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Bezeichnung</label>
				<div class="col-sm-10">
					<input name="foldtype_name" class="form-control" type="text" value="<?=$ft->getName()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Beispielbild</label>
				<div class="col-sm-10">
					<a href="libs/modules/foldtypes/picture.iframe.php" id="picture_select" class="products"><input type="button" class="button" value="<?=$_LANG->get('ändern')?>"></a>
					<? if($ft->getPicture() != "") {?>
						<input type="button" class="buttonRed" value="<?=$_LANG->get('L&ouml;schen')?>" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$ft->getId()?>&deletePicture=1'">
					<? } ?>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Beschreibung</label>
				<div class="col-sm-10">
					<textarea name="foldtype_description" type="text" class="form-control"><?=$ft->getDescription()?></textarea>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Falzen vertikal</label>
				<div class="col-sm-10">
					<input name="foldtype_vertical" class="form-control" type="text" value="<?=$ft->getVertical()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Falzen horizontal</label>
				<div class="col-sm-10">
					<input name="foldtype_horizontal" class="form-control" type="text" value="<?=$ft->getHorizontal()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Anz. Brüche</label>
				<div class="col-sm-10">
					<input name="foldtype_breaks" class="form-control" type="text" value="<?=$ft->getBreaks();?>">
				</div>
			</div>
		</form>
	</div>
</div>