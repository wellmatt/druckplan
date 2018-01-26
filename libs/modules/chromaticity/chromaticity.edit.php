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
	$chr->setPricekg(tofloat($_REQUEST["pricekg"]));
    $savemsg = getSaveMessage($chr->save());
}
?>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#chromaticity_form').submit();",'glyphicon-floppy-disk');

if ($chr->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$chr->getId()."');", 'glyphicon-trash', true);
}

echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Farbigkeit kopieren')?>
				<? if ($_REQUEST["exec"] == "edit" && $chr->getId() == 0) echo $_LANG->get('Farbigkeit anlegen')?>
				<? if ($_REQUEST["exec"] == "edit" && $chr->getId() != 0) echo $_LANG->get('Farbigkeit bearbeiten')?>
				<span class="pull-right"><?=$savemsg?></span>
			</h3>
	  </div>
	<div class="panel-body">
		<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="chromaticity_form" name="chromaticity_form"
			  class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.chr_name))">
			<input type="hidden" name="exec" value="edit">
			<input type="hidden" name="subexec" value="save">
			<input type="hidden" name="id" value="<?=$chr->getId()?>">


			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Bezeichnung</label>
				<div class="col-sm-3">
					<input id="chr_name" name="chr_name" type="text" class="form-control" value="<?=$chr->getName()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Farben Vorderseite</label>
				<div class="col-sm-3">
					<input name="chr_color_front" type="text" class="form-control" value="<?=$chr->getColorsFront()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Farben Rückseite</label>
				<div class="col-sm-3">
					<input name="chr_color_back"  type="text" class="form-control" value="<?=$chr->getColorsBack()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Schön- und Widerdruck</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input type="checkbox" name="chr_reverse"  class="form-control" value="1" <? if($chr->getReversePrinting()) echo "checked";?>>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Aufschlag auf Maschinenpreis</label>
				<div class="col-sm-3">
					<div class="input-group">
						<input name="chr_markup"type="text" class="form-control" value="<?=printPrice($chr->getMarkup())?>">
						<span class="input-group-addon">%</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Preis/Kg pro Farbton</label>
				<div class="col-sm-3">
					<div class="input-group">
						<input name="pricekg" type="text" class="form-control" value="<?=printPrice($chr->getPricekg())?>">
						<span class="input-group-addon">€</span>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>


