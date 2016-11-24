<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			28.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/attribute.class.php';

$bulk = new Bulkletter((int)$_REQUEST["bid"]);
$all_attributes = Attribute::getAllAttributesForCustomer();

if($_REQUEST["subexec"] == "save"){
	$bulk->setTitle(trim(addslashes($_REQUEST["bulk_title"])));
	$bulk->setText(trim(addslashes($_REQUEST["bulk_text"])));
	$bulk->setCustomerFilter($_REQUEST["bulk_filter"]);
	
	$tmp_attribs = Array();
	foreach ($_REQUEST["filter_attrib"] as $tmp_attrib){
	    if ($tmp_attrib != ""){
	       $tmp_attribs[] = $tmp_attrib;
	    }
	}
	$bulk->setCustomerAttrib($tmp_attribs);
	
	$ret_save = $bulk->save();
	$savemsg = getSaveMessage($ret_save)." ".$DB->getLastError();
	
	if($ret_save){
		$bulk->createDocument();
	}
}

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#bulkletter_edit').submit();",'glyphicon-floppy-disk');
if ($bulk->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&delid".$bulk->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="bulkletter_edit" id="bulkletter_edit"
	  class="form-horizontal" role="form">
	<input type="hidden" name="exec" value="edit">
	<input type="hidden" name="subexec" value="save">
	<input type="hidden" name="bid" value="<?=$bulk->getId()?>">
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Serienbrief erstellen')?>
					<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Serienbrief bearbeiten')?>
					<span class="pull-right"><?=$savemsg?></span>
				</h3>
		  </div>
		<div class="panel-body">

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Titel</label>
					<div class="col-sm-5">
						<input id="bulk_title" name="bulk_title" type="text" class="form-control" value="<?=$bulk->getTitle()?>" >
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Text</label>
					<div class="col-sm-5">
						<textarea id="bulk_text" name="bulk_text" type="text" class="form-control" ><?=$bulk->getText()?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Kunden-Filter</label>
					<div class="col-sm-5">
						<div class="input-group">
							<select name="bulk_filter" type="text" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								<option value="0" <? if($bulk->getCustomerFilter()==0) echo "selected";?>><?=$_LANG->get('Interessent')?></option>
								<option value="1" <? if($bulk->getCustomerFilter()==1) echo "selected";?>><?=$_LANG->get('Bestandskunde')?></option>
								<option value="2" <? if($bulk->getCustomerFilter()==2) echo "selected";?>><?=$_LANG->get('Alle Kunden')?></option>
								<option value="3" <? if($bulk->getCustomerFilter()==3) echo "selected";?>><?=$_LANG->get('Lieferanten')?></option>
								<option value="4" <? if($bulk->getCustomerFilter()==4) echo "selected";?>><?=$_LANG->get('Alle')?></option>
							</select>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Kunden-Merkmale</label>
					<div class="col-sm-5">
						<? if (count($bulk->getCustomerAttrib()) > 0) { foreach ($bulk->getCustomerAttrib() as $attrib){
							$tmp_expl_attrib = explode(",",$attrib);?>
							<select name="filter_attrib[]" type="text" class="form-control"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
								<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
								<?
								foreach ($all_attributes AS $attribute){
									$allitems = $attribute->getItems();
									foreach ($allitems AS $item){
										if ($item["id"] == $tmp_expl_attrib[1]){?>
											<option value="<?=$attribute->getId()?>,<?=$item["id"]?>" selected><?=$item["title"]?></option>
										<? } else { ?>
											<option value="<?=$attribute->getId()?>,<?=$item["id"]?>"><?=$item["title"]?></option>
										<?}
									}
								} ?>
							</select></br>
						<? }} ?>
						<select name="filter_attrib[]"type="text" class="form-control"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							<?
							foreach ($all_attributes AS $attribute){
								$allitems = $attribute->getItems();
								foreach ($allitems AS $item){?>
									<option value="<?=$attribute->getId()?>,<?=$item["id"]?>"><?=$item["title"]?></option>
								<?}
							} ?>
						</select>
					</div>
				</div>
				<?if ($bulk->getCrt_user()->getId() > 0){?>

					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Erstellt von</label>
						<div class="col-sm-5 form-text">
							<?if($bulk->getCrt_user()->getId() > 0) echo $bulk->getCrt_user()->getNameAsLine()?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Erstellt am</label>
						<div class="col-sm-5 form-text">
							<?if($bulk->getCrt_date() > 0) echo date('d.m.Y - H:i:s',$bulk->getCrt_date())?>
						</div>
					</div>
					<?if($bulk->getUpd_user()->getId() > 0){?>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Bearbeitet von</label>
							<div class="col-sm-5 form-text">
								<?=$bulk->getUpd_user()->getNameAsLine()?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Bearbeitet am</label>
							<div class="col-sm-5 form-text">
								<?if($bulk->getUpd_date() > 0) echo date('d.m.Y - H:i:s',$bulk->getUpd_date())?>
							</div>
						</div>
					<?}?>
					<div class="row">
						<div class="col-sm-2 control-label"><b>Download</b></div>
						<div class="col-sm-1 control-label" style="text-align: left;">
							<a href="<?=$bulk->getPdfLink(Document::VERSION_EMAIL)?>"
							   title="PDF mit Hintergrund"><?=$_LANG->get('E-Mail')?></a>
						</div>
						<div class="col-sm-1 control-label" style="text-align: left;">
							<a href="<?=$bulk->getPdfLink(Document::VERSION_PRINT)?>"
							   title="PDF ohne Hintergrund"><?=$_LANG->get('Print')?></a>
						</div>
					</div>
				<?}?>
		</div>
	</div>
</form>
