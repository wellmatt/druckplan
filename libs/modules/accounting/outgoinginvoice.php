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
require_once('libs/modules/commissioncontact/commissioncontact.class.php');


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

$filters= array("module"=>"",
				"type"=>"4",
				"requestId"=>"", 
				"cust_id"=> (int)$_REQUEST["filter_cust"],
				"payed_status"=> (int)$_REQUEST["payed_status"],
				"date_from"=>$filter_from,
				"date_to"=>$filter_to);


if($_REQUEST["exec"] == "save")
{
	foreach(array_keys($_REQUEST) AS $reqkey)
	{

		if(strpos($reqkey, "date_") !== false)
		{
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

if($_REQUEST["exec"] == "storno"){
	$storno_doc = new Document((int)$_REQUEST["invid"]);
	$storno_doc->setStornoDate(time());
	$ret = $storno_doc->save();
	$savemsg = getSaveMessage($ret);
}

$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);
$documents= Document::getDocuments($filters);
$sum_netto = 0;
$sum_brutto = 0;
?>

<script type="text/javascript">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('.date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
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
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
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
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
			}
     );
});
</script>
<form action="index.php?page=<?= $_REQUEST['page'] ?>" class="form-horizontal" method="post" name="xform_invoicesearch">
	<input type="hidden" name="subexec" value="search">
	<input type="hidden" name="mid" value="<?= $_REQUEST["mid"] ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Rechnungsausgang
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
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Kunde</label>
								<div class="col-sm-10">
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
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Status</label>
								<div class="col-sm-10">
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
						<div class="col-md-6">
							<div class="col-sm-2">
								<label for="control-label">Zeitraum</label>
							</div>
							<div class="col-sm-1">
								Von
							</div>
							<div class="col-sm-4">
								<input type="text" name="filter_from" id="filter_from" class="form-control date"
									   value="<?= date("d.m.Y", $filter_from) ?>"/>
							</div>
							<div class="col-sm-1">
								Bis
							</div>
							<div class="col-sm-4">
								<input type="text" name="filter_to" id="filter_to" class="form-control date"
									   value="<?= date("d.m.Y", $filter_to) ?>"/>
							</div>
						</div>
					</div>
					<span class="pull-right">
					<button class="btn btn-primary btn-success" type="submit">
						<?= $_LANG->get('Suche starten') ?>
					</button>
					</span>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Rechnungen
					</h3>
				</div>
				<div class="panel-body">
					<form action="index.php?page=<?= $_REQUEST['page'] ?>" class="form-horizontal" method="post"
						  name="idx_invcform">
						<input type="hidden" name="exec" value="save"/>
						<input type="hidden" name="payed_status" value="<?= $filters["payed_status"] ?>"/>
						<input type="hidden" name="filter_from" value="<?= date("d.m.Y", $filter_from) ?>"/>
						<input type="hidden" name="filter_to" value="<?= date("d.m.Y", $filter_to) ?>"/>
						<input type="hidden" name="filter_cust" value="<?= $filters["cust_id"] ?>"/>

						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
								<tr>
									<th class="content_row_header"><?= $_LANG->get('Re-Nr.') ?></th>
									<th class="content_row_header"><?= $_LANG->get('Re-Typ') ?></th>
									<th class="content_row_header"><?= $_LANG->get('Auftragsnr.') ?></th>
									<th class="content_row_header" align="right"><?= $_LANG->get('Brutto') ?></th>
									<th class="content_row_header" align="right"><?= $_LANG->get('Netto') ?></th>
									<th class="content_row_header"><?= $_LANG->get('Kunde') ?></th>
									<th class="content_row_header"><?= $_LANG->get('Provisionspartner') ?></th>
									<th class="content_row_header"><?= $_LANG->get('Provision') ?></th>
									<th class="content_row_header"><?= $_LANG->get('Titel') ?></th>
									<th class="content_row_header"><?= $_LANG->get('erstellt') ?></th>
									<th class="content_row_header"><?= $_LANG->get('F&auml;llig') ?></th>
									<th class="content_row_header"><?= $_LANG->get('Bezahlt') ?></th>
									<th class="content_row_header" align="center"><?= $_LANG->get('Optionen') ?></th>
								</tr>
								</thead>
								<tbody>

								<? // CSV-Datei der Rechnungen vorbereiten
								$csv_file = fopen('./docs/' . $_USER->getId() . '-Rechnungsausgang.csv', "w");
								//fwrite($csv_file, "Firma iPactor - �bersicht\n");

								// Tabellenkopf der CSV-Datei (Rechnungen) schreiben
								$csv_string .= "Re-Nr.; Auftragstitel; ";
								$csv_string .= "Betrag Netto ; MWST ; Brutto ;";
								$csv_string .= "Kunde; Debitor-Nr. ; Erstellt; Zahlbar bis; Bezahlt am; Bemerkung \n";

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
									$csv_string .= ";";
									if ($document->getStornoDate() > 0) {
										$csv_string .= " STORNO ";
									}
									$csv_string .= " \n";

									if ($document->getStornoDate() == 0) {
										$sum_netto += $document->getPriceNetto();
										$sum_brutto += $document->getPriceBrutto();
									}
									?>
									<tr class="<?= getRowColor($x) ?>" onmouseover="mark(this, 0)"
										onmouseout="mark(this, 1)">

										<td>
											<a href="#"
											   onclick="document.getElementById('idx_iframe_doc').src='libs/modules/documents/document.get.iframe.php?getDoc=<?= $document->getId() ?>&version=print'">
												<?= $document->getName() ?>
											</a>
											<input type="hidden" name="doc_existingid_<?= $x ?>"
												   name="doc_existingid_<?= $x ?>"
												   value="<?= (int)$document->getId() ?>"/>
											<? if ($document->getStornoDate() > 0) {
												?>
												<span class="glyphicons glyphicons-exclamation-sign"
													  title="<?= $_LANG->get('Storno am') . " " . date("d.m.Y", $document->getStornoDate()) ?>">
									</span>

											<? } ?>
										</td>
										<td class="content_row">
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
										</td>
										<td><?= printPrice($document->getPriceBrutto()) ?> <?= $_USER->getClient()->getCurrency() ?></td>
										<td><?= printPrice($document->getPriceNetto()) ?> <?= $_USER->getClient()->getCurrency() ?></td>
										<td>
											<?= $order->getCustomer()->getNameAsLine() ?>
											&nbsp;
										</td>
										<td>
											<?
											if ($order->getCustomer()->getCommissionpartner() > 0) {
												$tmp_bcontact = new CommissionContact($order->getCustomer()->getCommissionpartner());
												echo $tmp_bcontact->getName1();
											} else {
												echo 'kein';
											}
											?>
											&nbsp;
										</td>
										<td>
											<?
											if ($order->getCustomer()->getCommissionpartner() > 0) {
												$tmp_bcontact = new CommissionContact($order->getCustomer()->getCommissionpartner());
												echo printPrice(($document->getPriceBrutto() / 100 * $tmp_bcontact->getProvision())) ?> <?= $_USER->getClient()->getCurrency();
											} else {
												echo '';
											}
											?>
											&nbsp;
										</td>
										<td>
											<?= $order->getTitle() ?>
											&nbsp;
										</td>
										<td><?= date("d.m.Y", $document->getCreateDate()) ?></td>
										<td
											<?php
											$payable = $document->getPayable();
											$paynet = $order->getPaymentterm()->getNettodays();
											if ($paynet > 0){
												$payable = $payable + (60*60*24*$paynet);
											}

											if ($document->getPayed() == 0) {
												if (strtotime(date("d.m.Y 23:59:59", $payable)) > time())
													echo "style='color:green'";
												else echo "style='color:red'";
											} ?>>
											<?
											echo date("d.m.Y", $payable);
											?>&nbsp;
										</td>

										<td>
											<input type="text" name="date_<?= $x ?>" id="date_<?= $x ?>"
												   class="form-control date"
												   value="<? if ($document->getPayed() > 0) echo date("d.m.Y", $document->getPayed()); ?>"/>
										</td>
										<td>
											<!-- ul class="postnav_save_small_outinvc"><a href="#"
			onclick="document.getElementById('idx_iframe_doc').src='libs/modules/documents/document.get.iframe.php?getDoc=<?= $document->getId() ?>&version=print'">
			<?= $_LANG->get('Anzeigen') ?></a>
			</ul-->
											<ul class="postnav_save_small_outinvc">
												<a href="index.php?page=libs/modules/accounting/invoicewarning.php&exec=new&invid=<?= $document->getId() ?>"><?= $_LANG->get('Mahnung'); ?></a>
											</ul>
											<? if ($document->getStornoDate() == 0) {
												?>
												<ul class="postnav_save_small_outinvc">
													<a href="#"
													   onclick="askDel('index.php?page=<?= $_REQUEST['page'] ?>&exec=storno&invid=<?= $document->getId() ?>')"><?= $_LANG->get('Storno'); ?>&emsp;&ensp;</a>
												</ul>
											<? } else { ?>
												<br/>
											<? } ?>
										</td>
									</tr>
									<? $x++;
								} ?>
								<tr>
									<td><?= $_LANG->get('Gesamtsumme') ?></td>
									<td>
										<?= printPrice($sum_brutto); ?> <?= $_USER->getClient()->getCurrency() ?>
									</td>
									<td>
										<?= printPrice($sum_netto); ?> <?= $_USER->getClient()->getCurrency() ?>
									</td>
									<td>
										<a href="./docs/<?= $_USER->getId() ?>-Rechnungsausgang.csv" class="icon-link"
										   title="Rechnugen als CSV-Datei exportieren"><span
												class="glyphicons glyphicons-calculator">Export</span></a>
									</td>
									<td>&ensp;</td>
								</tr>
								</tbody>
							</table>
						</div>
				</div>
			</div>
			<br/>
			<? // Datei mit den offenen Rechnungen schliessen
			$csv_string .= ";" . $_LANG->get('Summe') . ":;" . printPrice($sum_netto) . ";" . printPrice($sum_brutto) . "; ; ;";
			$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
			fwrite($csv_file, $csv_string);
			fclose($csv_file); ?>
			<span class="pull-right">
				<button class="btn btn-primary btn-success" type="submit">
					<?= $_LANG->get('Speichern') ?>
				</button>
			</span>
			<iframe style="width:1px;height:1px;display:none" id="idx_iframe_doc" src=""></iframe>
		</div>
	</div>
</form>

