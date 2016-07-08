<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.09.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/documents/document.class.php');
require_once('libs/modules/collectiveinvoice/collectiveinvoice.class.php');
require_once('libs/modules/calculation/order.class.php');
require_once 'warnlevel.class.php';

if($_REQUEST["exec"] == "new"){
	include_once 'invoicewarning.new.php';
} else {
	
	$warnlevel = new Warnlevel((int)$_REQUEST["warn_id"]);
	$invoice = new Document((int)$_REQUEST["invid"]);
	
	/*
	if($_REQUEST["subexec"] == "create"){
		$warn_text = trim(addslashes($_REQUEST["warn_text"]));
	
		$payable = time() + $warnlevel->getDeadline()*24*60*60;
	
		$warning = new Document();
		$warning->setType(Document::TYPE_INVOICEWARNING);
		$warning->setPayable($payable);
		$warning->setRequestModule($invoice->getRequestModule());
		$warning->setRequestId($invoice->getRequestId());
		$warning->setPriceBrutto($invoice->getPriceBrutto());
		$warning->setPriceNetto($invoice->getPriceNetto());
	
		$warning->createDoc(Document::VERSION_PRINT);
		$saver = $warning->save();
	
		$savemsg = getSaveMessage($saver).$DB->getLastError();
	
		// MahnungsID an der Rechnung anhaengen
		if ($saver){
			$invoice->setWarningId($warning->getId());
			$invoice->save();
	
			// Weiterleitung, wenn das Speichern in der invoicewarning.new.php geschehen wuerde
			//echo "<script type=\"text/javascript\">location.href='index.php?id=".$_CONFIG->invoicewarningID."';</script>";
		}
	}*/
	
	if ((int)$_REQUEST["filter_from"] == 0){
		$this_month = date("m",time());
		$filter_from = mktime(12,0,0,$this_month,1);
	} else {
		$filter_from = strtotime($_REQUEST["filter_from"]);
	}
	if ((int)$_REQUEST["filter_to"] == 0){
		$filter_to = time();
	} else {
		$filter_to = strtotime($_REQUEST["filter_to"]." 23:59:59");
	}
	
	if($_REQUEST["exec"] == "save"){
		foreach(array_keys($_REQUEST) AS $reqkey){
			if(strpos($reqkey, "date_") !== false){
				$idx = substr($reqkey, strrpos($reqkey, "_") +1);
					
				if(!empty($_REQUEST["date_{$idx}"])) {
					$doc = new Document((int)$_REQUEST["doc_existingid_{$idx}"]);
					$doc_payed = strtotime($_REQUEST["date_{$idx}"]);
					$doc->setPayed($doc_payed);
					
				 $ret = $doc->save($idx);
				}
			}
			$savemsg = getSaveMessage($ret);
		}
	}
	
	$filters= array("module"=>"",
			"type"=>Document::TYPE_INVOICEWARNING,
			"requestId"=>"",
			"cust_id"=> (int)$_REQUEST["filter_cust"],
			"payed_status"=> (int)$_REQUEST["payed_status"],
			"date_from"=>$filter_from,
			"date_to"=>$filter_to);
	
	$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);
	$documents= Document::getDocuments($filters);
	$sum_netto = 0;
	$sum_brutto = 0;
	?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			<img src="<?= $_MENU->getIcon($_REQUEST['page']) ?>">
			Mahnungs&uuml;bersicht
				<span class="pull-right">
					<?= $savemsg ?>
				</span>
		</h3>
	</div>
	<div class="panel-body">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					Filteroptionen
				</h3>
			</div>
			<div class="panel-body">
				<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" class="form-horizontal"
					  name="xform_warninginvoice_search">
					<input type="hidden" name="subexec" value="search">
					<input type="hidden" name="mid" value="<?= $_REQUEST["mid"] ?>">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kunde</label>
								<div class="col-sm-9">
									<select type="text" id="filter_cust" name="filter_cust" onfocus="markfield(this,0)"
											onblur="markfield(this,1)" class="form-control">
										<option value="0">&lt; <?= $_LANG->get('Bitte w&auml;hlen') ?> &gt;</option>
										<? foreach ($allcustomer as $cust) { ?>
											<option value="<?= $cust->getId() ?>"
												<? if ($filters["cust_id"] == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine() ?></option>
										<? } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-2"><label for="control-label"><?= $_LANG->get('Zeitraum'); ?></label>
							</div>
							<div class="col-md-1"><label for="control-label">Von</label></div>
							<div class="col-md-4">
								<input type="text" name="filter_from" id="filter_from" class="form-control date"
									   value="<?= date("d.m.Y", $filter_from) ?>"/>
							</div>
							<div class="col-md-1"><label for="control-label">Bis</label></div>
							<div class="col-md-4">
								<input type="text" name="filter_to" id="filter_to" class="form-control date"
									   value="<?= date("d.m.Y", $filter_to) ?>"/>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Status</label>
								<div class="col-sm-9">
									<select type="text" id="payed_status" name="payed_status"
											onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
										<option value="0">&lt; <?= $_LANG->get('Bitte w&auml;hlen') ?> &gt;</option>
										<option value="1"
											<? if ($filters["payed_status"] == 1) echo "selected" ?>><?= $_LANG->get('offen') ?></option>
										<option value="2"
											<? if ($filters["payed_status"] == 2) echo "selected" ?>><?= $_LANG->get('bezahlt') ?></option>
									</select>
								</div>
							</div>
						</div>
						</br>
						<div class="col-md-6">
								   <span class="pull-right">
									   <button class="btn btn-md btn-success" type="submit">
										   <?= $_LANG->get('Suche starten') ?>
									   </button>
								   </span>
						</div>
					</div>
				</form>
			</div>
		</div>
		<form action="index.php?page=<?= $_REQUEST['page'] ?>" class="form-horizontal" method="post"
			  name="idx_invcform">
			<input type="hidden" name="exec" value="save"/>
			<input type="hidden" name="payed_status" value="<?= $filters["payed_status"] ?>"/>
			<input type="hidden" name="filter_from" value="<?= date("d.m.Y", $filter_from) ?>"/>
			<input type="hidden" name="filter_to" value="<?= date("d.m.Y", $filter_to) ?>"/>
			<input type="hidden" name="filter_cust" value="<?= $filters["cust_id"] ?>"/>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Mahnungen
					</h3>
				</div>
					<div class="table-responsive">
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
								<tr>
									<th><?= $_LANG->get('MA-Nr.') ?></th>
									<th><?= $_LANG->get('Re-Typ') ?></th>
									<th><?= $_LANG->get('Auftragsnr.') ?></th>
									<th><?= $_LANG->get('Kunde') ?></th>
									<th><?= $_LANG->get('erstellt') ?></th>
									<th><?= $_LANG->get('F&auml;llig') ?></th>
									<th><?= $_LANG->get('Bezahlt') ?></th>
									<th><?= $_LANG->get('Optionen') ?></th>
								</tr>
								</thead>
								<? // CSV-Datei der offenen Posten vorbereiten
								$csv_file = fopen('./docs/' . $_USER->getId() . '-Mahnungen.csv', "w");
								//fwrite($csv_file, "Firma iPactor - �bersicht\n");

								//Tabellenkopf der CSV-Datei (offene Posten) schreiben
								$csv_string .= "MA-Nr.; Auftragstitel; ";
								$csv_string .= "Betrag Netto ; MWST ; Brutto ;";
								$csv_string .= "Kunde; Debitor-Nr. ; Erstellt; Zahlbar bis; Bezahlt am \n";

								$x = 0;
								foreach ($documents as $document) {

									$order = null;
									if ($document->getRequestModule() == Document::REQ_MODULE_ORDER) {
										$order = new Order($document->getRequestId());
									} else if ($document->getRequestModule() == Document::REQ_MODULE_COLLECTIVEORDER) {
										$order = new CollectiveInvoice($document->getRequestId());
									}
									$tmp_mwst = $document->getPriceBrutto() - $document->getPriceNetto();
									$csv_string .= $document->getName() . ";" . $order->getTitle() . ";";
									$csv_string .= printPrice($document->getPriceNetto()) . ";" . printPrice($tmp_mwst) . ";" . printPrice($document->getPriceBrutto()) . ";";
									$csv_string .= $order->getCustomer()->getNameAsLine() . ";" . $order->getCustomer()->getDebitor() . ";";
									$csv_string .= date("d.m.Y", $document->getCreateDate()) . ";" . date("d.m.Y", $document->getPayable()) . ";";
									if ($document->getPayed() > 0) {
										$csv_string .= date("d.m.Y", $document->getPayed());
									}
									$csv_string .= " \n";

									$sum_netto += $document->getPriceNetto();
									$sum_brutto += $document->getPriceBrutto();
									?>
									<tbody>
									<tr class="<?= getRowColor($x) ?>" onmouseover="mark(this, 0)"
										onmouseout="mark(this, 1)">

										<td><?= $document->getName() ?>
											<input type="hidden" name="doc_existingid_<?= $x ?>"
												   name="doc_existingid_<?= $x ?>"
												   value="<?= (int)$document->getId() ?>"/>
										</td>
										<td>
											<? if ($document->getRequestModule() == Document::REQ_MODULE_ORDER) echo $_LANG->get('Kalkulation');
											if ($document->getRequestModule() == Document::REQ_MODULE_COLLECTIVEORDER) echo $_LANG->get('Sammel');
											?>
										</td>
										<td>
											<? if ($document->getRequestModule() == Document::REQ_MODULE_ORDER) {
												?>
												<a href="index.php?page=libs/modules/calculation/order.php&exec=edit&id=<?= $order->getId() ?>&step=4"><?= $order->getNumber() ?></a>
											<?
											}
											if ($document->getRequestModule() == Document::REQ_MODULE_COLLECTIVEORDER) {
												?>
												<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?= $order->getId() ?>"><?= $order->getNumber() ?></a>
											<? } ?>
											&nbsp;
										</td>
										<td>
											<?= $order->getCustomer()->getNameAsLine() ?>
											&nbsp;
										</td>
										<td><?= date("d.m.Y", $document->getCreateDate()) ?></td>
										<td
											<?php if ($document->getPayed() == 0) {
												if (strtotime(date("d.m.Y 23:59:59", $document->getPayable())) > time())
													echo "style='color:green'";
												else echo "style='color:red'";
											} ?>>
											<? echo date("d.m.Y", $document->getPayable()); ?>&nbsp;</td>

										<td>
											<input type="text" name="date_<?= $x ?>" id="date_<?= $x ?>"
												   class="form-control date"
												   value="<? if ($document->getPayed() > 0) echo date("d.m.Y", $document->getPayed()); ?>"/>
										</td>
										<td>
											<ul class="postnav_save_small_outinvc">
												<a href="#"
												   onclick="document.getElementById('idx_iframe_doc').src='libs/modules/documents/document.get.iframe.php?getDoc=<?= $document->getId() ?>&version=print'">
													<?= $_LANG->get('Anzeigen') ?>
												</a>
											</ul>
										</td>
									</tr>
									</tbody>
									<? $x++;
								} ?>
							</table>
						</div>
					</div>
				<span class="pull-right">
						<button class="btn btn-md btn-success" type="submit">
							<?= $_LANG->get('Suche starten') ?>
						</button>
				</span>
			</div>
		</form>
	</div>
</div>

	
	<script type="text/javascript"">
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
		
		$('.date').datepicker(
				{
					showOtherMonths: true,
					selectOtherMonths: true,
					dateFormat: 'dd.mm.yy',
	               // showOn: "button",
	              //  buttonImage: "images/icons/calendar-blue.png",
	              //  buttonImageOnly: true
				}
	     );
	});
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
		
		$('.filter_from').datepicker(
				{
					showOtherMonths: true,
					selectOtherMonths: true,
					dateFormat: 'dd.mm.yy',
	              //  showOn: "button",
	              //  buttonImage: "images/icons/calendar-blue.png",
	              //  buttonImageOnly: true
				}
	     );
	});
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
		
		$('.filter_to').datepicker(
				{
					showOtherMonths: true,
					selectOtherMonths: true,
					dateFormat: 'dd.mm.yy',
	            //    showOn: "button",
	            //    buttonImage: "images/icons/calendar-blue.png",
	            //    buttonImageOnly: true
				}
	     );
	});
	</script>


	<? // Datei mit den offenen Eingangsrechnungen schliessen
		$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
		fwrite($csv_file, $csv_string);
		fclose($csv_file); ?>

	<iframe style="width:1px;height:1px;display:none" id="idx_iframe_doc" src=""></iframe>

<?}?>
