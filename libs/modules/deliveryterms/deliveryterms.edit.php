<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/taxkeys/taxkey.class.php';

$delterm = new DeliveryTerms($_REQUEST["did"]);

if($_REQUEST["subexec"] == "save"){
	$delterm->setCharges((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["dt_charges"]))));
	$delterm->setTaxkey(new TaxKey((int)$_REQUEST["dt_tax"]));
	$delterm->setName1(trim(addslashes($_REQUEST["dt_name"])));
	$delterm->setComment(trim(addslashes($_REQUEST["dt_comment"])));
	$delterm->setRevenueaccount(new RevenueaccountCategory((int)$_REQUEST["revenueaccount"]));
	
	if($_CONFIG->shopActivation){
		$delterm->setShoprel((int)$_REQUEST["dt_shoprel"]);
	} else {
		$delterm->setShoprel(0);
	}
	
	$savemsg = getSaveMessage($delterm->save()).$DB->getLastError();
}?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#user_form').submit();",'glyphicon-floppy-disk');

if ($delterm->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$delterm->getId()."');", 'glyphicon-trash', true);
}

echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="user_form" name="user_form"
	  class="form-horizontal" role="form"  onSubmit="return checkform(new Array(this.deliv_name, this.deliv_comment))">
	<input type="hidden" name="exec" value="edit" />
	<input type="hidden" name="subexec" value="save" />
	<input type="hidden" name="did" value="<?=$delterm->getId()?>" />
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
						<?if($_REQUEST["id"]){
							echo $_LANG->get('Lieferarten bearbeiten');
						} else {
							echo $_LANG->get('Lieferarten anlegen');
						}?>
					<span class="pull-right"><?=$savemsg?></span>
				</h3>
		  </div>
		<div class="panel-body">

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-4">
						<input id="deliv_name" name="dt_name" type="text" class="form-control" value="<?=$delterm->getName1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Beschreibung</label>
					<div class="col-sm-4">
						<input id="deliv_comment" name="dt_comment" type="text" class="form-control" value="<?=$delterm->getComment()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Kosten</label>
					<div class="col-sm-4">
						<div class="input-group">
							<input name="dt_charges"  type="text" class="form-control" value="<?=printPrice($delterm->getCharges())?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<span class="input-group-addon">€</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Umsatzsteuer</label>
					<div class="col-sm-4">
						<select id="dt_tax" name="dt_tax" class="form-control">
							<?php if ($delterm->getTaxkey()->getId() > 0) echo '<option value="'.$delterm->getTaxkey()->getId().'">'.$delterm->getTaxkey()->getValue().'%</option>';?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Erlöskonto</label>
					<div class="col-sm-4">
						<select name="revenueaccount" id="revenueaccount" class="form-control">
							<option
								value="0">- Nicht Überschreiben -</option>
							<?php
							$racs = RevenueaccountCategory::getAll();
							foreach ($racs as $rac) { ?>
								<?php
								if ($delterm->getId() > 0) {
									if ($rac->getId() == $delterm->getRevenueaccount()->getId()) { ?>
										<option
											value="<?php echo $delterm->getRevenueaccount()->getId(); ?>"
											selected><?php echo $delterm->getRevenueaccount()->getTitle(); ?></option>
									<?php } else { ?>
										<option
											value="<?php echo $rac->getId(); ?>"><?php echo $rac->getTitle(); ?></option>
									<?php } ?>
								<?php } else {?>
									<option
										value="<?php echo $rac->getId(); ?>"><?php echo $rac->getTitle(); ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
				<?/***if($_CONFIG->shopActivation){?>
				<tr>
				<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
				<td class="content_row_clear">
				<input 	id="dt_shoprel" name="dt_shoprel" class="text" type="checkbox"
				value="1" <?if ($delterm->getShoprel() == 1) echo "checked"; ?>>
				</td>
				</tr>
				<?}**/?>
		</div>
	</div>
</form>

<script>
	$(function () {
		$("#dt_tax").select2({
			ajax: {
				url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_taxkey",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						term: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, params) {
					// parse the results into the format expected by Select2
					// since we are using custom formatting functions we do not need to
					// alter the remote JSON data, except to indicate that infinite
					// scrolling can be used
					params.page = params.page || 1;

					return {
						results: data,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			minimumInputLength: 0,
			language: "de",
			multiple: false,
			allowClear: false,
			tags: false
		}).val(<?php echo $delterm->getTaxkey()->getId();?>).trigger('change');
	});
</script>


<script>
	$(function() {
		$("#revenueaccount").select2();
	});
</script>
