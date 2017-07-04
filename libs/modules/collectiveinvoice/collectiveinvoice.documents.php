<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/documents/document.class.php');
require_once('libs/modules/organizer/nachricht.class.php');

// Pr�fung ob eine Reservierung oder eine Rechnung vorhanden sind
$docsofferconfirm = Document::getDocuments(Array("type" => Document::TYPE_OFFERCONFIRM, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));
$docsinvoice = Document::getDocuments(Array("type" => Document::TYPE_INVOICE, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));
$docsdelivery = Document::getDocuments(Array("type" => Document::TYPE_DELIVERY, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));
// ------

if((int)$_REQUEST["deleteDoc"] > 0){
    $doc = new Document((int)$_REQUEST["deleteDoc"]);
    $doc->delete();
}

if($_REQUEST["createDoc"]){

	if ($_REQUEST["createDoc"] == "invoice"){
		$tmpdocs = Document::getDocuments(Array("type" => Document::TYPE_INVOICE, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));
		if (count($tmpdocs)>0){
			die('Es kann nur eine Rechnung erstellt werden!');
		}
	}

    $doc = new Document();
    $doc->setRequestId($collectinv->getId());
    $doc->setRequestModule(Document::REQ_MODULE_COLLECTIVEORDER);

	if((int)$_REQUEST["letterhead"] > 0)
		$doc->setLetterhead((int)$_REQUEST["letterhead"]);
    
    if($_REQUEST["createDoc"] == "offer")
    {
        $doc->setType(Document::TYPE_OFFER);
        $collectinv->setStatus(2);
        $collectinv->save();
    }
    if($_REQUEST["createDoc"] == "offerconfirm")
    {
        $doc->setType(Document::TYPE_OFFERCONFIRM);
        $collectinv->setStatus(3);
        $collectinv->save();
    }
    if($_REQUEST["createDoc"] == "label")
        $doc->setType(Document::TYPE_LABEL);
    if($_REQUEST["createDoc"] == "factory")
        $doc->setType(Document::TYPE_FACTORY);
    if($_REQUEST["createDoc"] == "delivery") {
		$doc->setType(Document::TYPE_DELIVERY);
		$collectinv->setStatus(5);
		$collectinv->save();
	}
    if($_REQUEST["createDoc"] == "invoice")
    {
        $doc->setType(Document::TYPE_INVOICE);
        $collectinv->setStatus(7);
        $collectinv->save();
    }
    if($_REQUEST["createDoc"] == "revert")
    	$doc->setType(Document::TYPE_REVERT);
    if($_REQUEST["createDoc"] == "factory")
        $doc->setType(Document::TYPE_FACTORY);

    if($_REQUEST["createDoc"] == "factory" || $_REQUEST["createDoc"] == "label"){
        $doc->createDoc(Document::VERSION_PRINT, false, false);
    } else {
        $hash = $doc->createDoc(Document::VERSION_EMAIL);
        $doc->createDoc(Document::VERSION_PRINT, $hash);
    }
    $doc->save();
	if ($_REQUEST["createDoc"] == "invoice"){
		InvoiceOut::generate($doc->getName(),$collectinv,$collectinv->getPaymentterm(), $doc->getId());
	}
}

if ($_REQUEST["subexec"] == "doc_texts")
{
    $collectinv->setOffer_header($_REQUEST["offer_header"]);
    $collectinv->setOffer_footer($_REQUEST["offer_footer"]);
    $collectinv->setOfferconfirm_header($_REQUEST["offerconfirm_header"]);
    $collectinv->setOfferconfirm_footer($_REQUEST["offerconfirm_footer"]);
    $collectinv->setFactory_header($_REQUEST["factory_header"]);
    $collectinv->setFactory_footer($_REQUEST["factory_footer"]);
    $collectinv->setDelivery_header($_REQUEST["delivery_header"]);
    $collectinv->setDelivery_footer($_REQUEST["delivery_footer"]);
    $collectinv->setInvoice_header($_REQUEST["invoice_header"]);
    $collectinv->setInvoice_footer($_REQUEST["invoice_footer"]);
    $collectinv->setRevert_header($_REQUEST["revert_header"]);
    $collectinv->setRevert_footer($_REQUEST["revert_footer"]);
    $collectinv->save();
}

$margin_warning = false;
if ($collectinv->getId()>0 && $collectinv->getLocked() == 0){
	$margin_warning = Orderposition::checkMarginWarn($collectinv);
}

?>

<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>
<script>
	$(function() {
		$("a#newmail_hiddenclicker").fancybox({
			'type'          :   'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'width'         :   1024,
			'height'		:	800,
			'scrolling'     :   'yes',
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyNewMail(my_href) {
		var j1 = document.getElementById("newmail_hiddenclicker");
		j1.href = my_href;
		$('#newmail_hiddenclicker').trigger('click');
	}
</script>

<div id="newmail_hidden_clicker" style="display:none"><a id="newmail_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<link rel="stylesheet" href="css/documents.css" type="text/css">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="form_collectiveinvoices" class="form-horizontal" name="form_collectiveinvoices">
	<input 	type="hidden" name="exec" value="docs">
	<input 	type="hidden" name="subexec" value="doc_texts">
	<input 	type="hidden" name="ciid" value="<?php echo $collectinv->getId();?>">
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					Dokumentenverwaltung
					<span class="pull-right">
						<button type="button" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$collectinv->getId()?>';" class="btn btn-sm btn-default">
							<?=$_LANG->get('Zurück')?>
						</button>
						<button class="btn btn-sm btn-success" type="submit">
							<?=$_LANG->get('Speichern')?>
						</button>
					</span>
				</h3>
		  </div>
		  <div style="padding: 0;" class="panel-body">
			  <?
			  //---------------------------------------------------------------------------
			  // Dokumenten Header + Footer
			  //---------------------------------------------------------------------------
			  ?>
			  <div style="padding: 0;" id="tabs">

				  <ul>
					  <li><a href="#tabs-0"><? echo $_LANG->get('Angebot'); ?></a></li>
					  <li><a href="#tabs-1"><? echo $_LANG->get('Auftragsbestätigung'); ?></a></li>
					  <li><a href="#tabs-2"><? echo $_LANG->get('Auftragstasche'); ?></a></li>
					  <li><a href="#tabs-3"><? echo $_LANG->get('Lieferschein'); ?></a></li>
					  <li><a href="#tabs-4"><? echo $_LANG->get('Rechnung'); ?></a></li>
					  <li><a href="#tabs-5"><? echo $_LANG->get('Gutschrift'); ?></a></li>
				  </ul>
				  <div id="tabs-0"> <!-- Angebot -->
					  <table width="100%">
						  <tr>
							  <td>Header:</br><textarea name="offer_header" rows="4"
														cols="1"><?php echo $collectinv->getOffer_header() ?></textarea>
							  </td>
							  <td>Footer:</br><textarea name="offer_footer" rows="4"
														cols="1"><?php echo $collectinv->getOffer_footer() ?></textarea>
							  </td>
						  </tr>
					  </table>
				  </div>

				  <div id="tabs-1"> <!-- Auftragsbestätigung -->
					  <table width="100%">
						  <tr>
							  <td>Header:</br><textarea name="offerconfirm_header" rows="4"
														cols="1"><?php echo $collectinv->getOfferconfirm_header() ?></textarea>
							  </td>
							  <td>Footer:<textarea name="offerconfirm_footer" rows="4"
												   cols="1"><?php echo $collectinv->getOfferconfirm_footer() ?></textarea>
							  </td>
						  </tr>
					  </table>
				  </div>

				  <div id="tabs-2"> <!-- Auftragstasche -->
					  <table width="100%">
						  <tr>
							  <td>Header:</br><textarea name="factory_header" rows="4"
														cols="1"><?php echo $collectinv->getFactory_header() ?></textarea>
							  </td>
							  <td>Footer:<textarea name="factory_footer" rows="4"
												   cols="1"><?php echo $collectinv->getFactory_footer() ?></textarea>
							  </td>
						  </tr>
					  </table>
				  </div>

				  <div id="tabs-3"> <!-- Lieferschein -->
					  <table width="100%">
						  <tr>
							  <td>Header:</br><textarea name="delivery_header" rows="4"
														cols="1"><?php echo $collectinv->getDelivery_header() ?></textarea>
							  </td>
							  <td>Footer:<textarea name="delivery_footer" rows="4"
												   cols="1"><?php echo $collectinv->getDelivery_footer() ?></textarea>
							  </td>
						  </tr>
					  </table>
				  </div>

				  <div id="tabs-4"> <!-- Rechnung -->
					  <table width="100%">
						  <tr>
							  <td>Header:</br><textarea name="invoice_header" rows="4"
														cols="1"><?php echo $collectinv->getInvoice_header() ?></textarea>
							  </td>
							  <td>Footer:<textarea name="invoice_footer" rows="4"
												   cols="1"><?php echo $collectinv->getInvoice_footer() ?></textarea>
							  </td>
						  </tr>
					  </table>
				  </div>

				  <div id="tabs-5"> <!-- Gutschrift -->
					  <table width="100%">
						  <tr>
							  <td>Header:</br><textarea name="revert_header" rows="4"
														cols="1"><?php echo $collectinv->getRevert_header() ?></textarea>
							  </td>
							  <td>Footer:<textarea name="revert_footer" rows="4"
												   cols="1"><?php echo $collectinv->getRevert_footer() ?></textarea>
							  </td>
						  </tr>
					  </table>
				  </div>
			  </div>
		  </div>
	</div>
</form>
<?php
if ($margin_warning){
	?>
	<div class="alert alert-danger" role="alert" style="margin-bottom: 0px;">
		<strong>Warnung!</strong></br>
		Die Marge in diesem Vorgang ist unter der minimal Grenze! Nur jemand mit der nötigen Berechtigung kann Dokumente generieren!
	</div>
	<?php
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Dokumente
			<span class="pull-right">
				<button type="button" onclick="callBoxFancyNewMail('libs/modules/mail/mail.send.system.frame.php?fromColinv=<?php echo $collectinv->getId();?>');" class="btn btn-sm btn-default">Mail verschicken</button>
			</span>
		</h3>
	</div>
	<div class="panel-body" style="padding: 5px;">
		<div class="panel panel-default" style="margin-bottom: -2px;">
			<div class="panel-heading">
				<h3 class="panel-title" style="font-size: 16px;">
					Angebot
				</h3>
			</div>
			<div class="table-responsive" style="margin: 0px 0px 0px 0px;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="10%"><?= $_LANG->get('Dokumentenname') ?></th>
						<th width="10%"><?= $_LANG->get('Versch.') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt von') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt am') ?></th>
						<th width="10%"><?= $_LANG->get('Dokumente') ?></th>
					</tr>
					</thead>
					<tbody>
					<?
					//---------------------------------------------------------------------------
					// Angebot
					//---------------------------------------------------------------------------
					$docs = Document::getDocuments(Array("type" => Document::TYPE_OFFER, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
					<?
					if (count($docs) > 0) {
						foreach ($docs AS $doc) { ?>
							<tr class="<?= getRowColor(0) ?>">
								<td>
									<span class="ok"><?= $doc->getName() ?></span>
								</td>
								<td>
									<? if ($doc->getSent())
										echo '<img src="images/status/green_small.svg">';
									else
										echo '<img src="images/status/red_small.svg">'; ?>
								</td>
								<td>
									<?= $doc->getCreateUser()->getNameAsLine() ?>
								</td>
								<td>
									<?= date('d.m.Y H:m', $doc->getCreateDate()) ?>
								</td>
								<td>
									<table cellpaddin="0" cellspacing="0" width="100%">
										<tr>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=email"><?= $_LANG->get('E-Mail') ?></a>
												</ul>
											</td>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=print"><?= $_LANG->get('Print') ?></a>
												</ul>
											</td>
											<td width="30%">
												<ul class="postnav_text_del">
													<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&deleteDoc=<?= $doc->getId() ?>"><?= $_LANG->get('L&ouml;schen') ?></a>
												</ul>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<? $x++;
						}
					}
					?>
					<tr class="<?= getRowColor(0) ?>">
						<td>
							<!-- span class="error"><?= $_LANG->get('nicht vorhanden') ?></span--> &ensp;
						</td>
						<td>&nbsp;</td>
						<td>- - -</td>
						<td>- - -</td>
						<td>
							<?php if (!$margin_warning || $_USER->hasRightsByGroup('colinv_ignoremargin')){?>
							<ul class="postnav_text_save">
								<?php
								$letterheads = Letterhead::getAllForType(1);
								?>
								<select name="letterhead_offer" id="letterhead_offer" class="form-control"
										style="margin-bottom: 5px;">
									<?php
									foreach ($letterheads as $item) {
										if ($item->getStd() == 1)
											echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
										else
											echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
									}
									?>
								</select>
								<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&createDoc=offer"
								   onclick="$(this).attr('href',$(this).attr('href')+'&letterhead='+$('#letterhead_offer').val());"><?= $_LANG->get('Generieren') ?></a>
							</ul>
							<?php } ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default" style="margin-bottom: -2px;">
			<div class="panel-heading">
				<h3 class="panel-title" style="font-size: 16px;">
					Auftragsbest&auml;tigung
				</h3>
			</div>
			<div class="table-responsive" style="margin: 0px 0px 0px 0px;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="10%"><?= $_LANG->get('Dokumentenname') ?></th>
						<th width="10%"><?= $_LANG->get('Versch.') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt von') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt am') ?></th>
						<th width="10%"><?= $_LANG->get('Dokumente') ?></th>
					</tr>
					</thead>
					<tbody>
					<?
					//---------------------------------------------------------------------------
					// Angebotsbets�tigung
					//---------------------------------------------------------------------------
					$docs = Document::getDocuments(Array("type" => Document::TYPE_OFFERCONFIRM, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
					<?
					if (count($docs) > 0) {
						foreach ($docs AS $doc) { ?>
							<tr class="<?= getRowColor(0) ?>">
								<td>
									<span class="ok"><?= $doc->getName() ?></span>
								</td>
								<td>
									<? if ($doc->getSent())
										echo '<img src="images/status/green_small.svg">';
									else
										echo '<img src="images/status/red_small.svg">'; ?>
								</td>
								<td>
									<?= $doc->getCreateUser()->getNameAsLine() ?>
								</td>
								<td>
									<?= date('d.m.Y H:m', $doc->getCreateDate()) ?>
								</td>
								<td>
									<table cellpaddin="0" cellspacing="0" width="100%">
										<tr>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=email"><?= $_LANG->get('E-Mail') ?></a>
												</ul>
											</td>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=print"><?= $_LANG->get('Print') ?></a>
												</ul>
											</td>
											<td width="40%">
												<ul class="postnav_text_del">
													<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&deleteDoc=<?= $doc->getId() ?>"><?= $_LANG->get('L&ouml;schen') ?></a>
												</ul>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<? $x++;
						}
					}
					?>
					<tr class="<?= getRowColor(0) ?>">
						<td>
							<!-- span class="error"><?= $_LANG->get('nicht vorhanden') ?></span--> &ensp;
						</td>
						<td>&nbsp;</td>
						<td>- - -</td>
						<td>- - -</td>
						<td>
							<?php if (!$margin_warning || $_USER->hasRightsByGroup('colinv_ignoremargin')){?>
							<ul class="postnav_text_save">
								<?php
								$letterheads = Letterhead::getAllForType(Document::TYPE_OFFERCONFIRM);
								?>
								<select name="letterhead_offerconfirm" id="letterhead_offerconfirm" class="form-control"
										style="margin-bottom: 5px;">
									<?php
									foreach ($letterheads as $item) {
										if ($item->getStd() == 1)
											echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
										else
											echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
									}
									?>
								</select>
								<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&createDoc=offerconfirm"
								   onclick="$(this).attr('href',$(this).attr('href')+'&letterhead='+$('#letterhead_offerconfirm').val());"><?= $_LANG->get('Generieren') ?></a>
							</ul>
							<?php } ?>
						</td>

					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default" style="margin-bottom: -2px;">
			<div class="panel-heading">
				<h3 class="panel-title" style="font-size: 16px;">
					Auftragstasche
				</h3>
			</div>
			<div class="table-responsive" style="margin: 0px 0px 0px 0px;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="10%"><?= $_LANG->get('Dokumentenname') ?></th>
						<th width="10%"><?= $_LANG->get('Versch.') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt von') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt am') ?></th>
						<th width="10%"><?= $_LANG->get('Dokumente') ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					//---------------------------------------------------------------------------
					// Auftragstasche
					//---------------------------------------------------------------------------
					$docs = Document::getDocuments(Array("type" => Document::TYPE_FACTORY, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
					<tr class="<?= getRowColor(1) ?>">
						<td class="content_row" colspan="6"><b><?= $_LANG->get('') ?></b></td>
					</tr>
					<?
					if (count($docs) > 0) {
						foreach ($docs AS $doc) { ?>
							<tr class="<?= getRowColor(0) ?>">
								<td>
									<span class="ok"><?= $doc->getName() ?></span>
								</td>
								<td>
									<? if ($doc->getSent())
										echo '<img src="images/status/green_small.svg">';
									else
										echo '<img src="images/status/red_small.svg">'; ?>
								</td>
								<td>
									<?= $doc->getCreateUser()->getNameAsLine() ?>
								</td>
								<td>
									<?= date('d.m.Y H:m', $doc->getCreateDate()) ?>
								</td>
								<td>
									<table cellpaddin="0" cellspacing="0" width="100%">
										<tr>
											<td width="30%">
												<!-- ul class="postnav_text">
						<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=email"><?= $_LANG->get('E-Mail') ?></a>
					</ul--> &ensp;
											</td>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=print"><?= $_LANG->get('Print') ?></a>
												</ul>
											</td>
											<td width="40%">
												<ul class="postnav_text_del">
													<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&deleteDoc=<?= $doc->getId() ?>"><?= $_LANG->get('L&ouml;schen') ?></a>
												</ul>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<? $x++;
						}
					}
					?>
					<tr class="<?= getRowColor(0) ?>">
						<td>
							<!-- span class="error"><?= $_LANG->get('nicht vorhanden') ?></span--> &ensp;
						</td>
						<td>&nbsp;</td>
						<td>- - -</td>
						<td>- - -</td>
						<td>
							<?php if (!$margin_warning || $_USER->hasRightsByGroup('colinv_ignoremargin')){?>
							<ul class="postnav_text_save">
								<?php
								$letterheads = Letterhead::getAllForType(Document::TYPE_FACTORY);
								?>
								<select name="letterhead_factory" id="letterhead_factory" class="form-control"
										style="margin-bottom: 5px;">
									<?php
									foreach ($letterheads as $item) {
										if ($item->getStd() == 1)
											echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
										else
											echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
									}
									?>
								</select>
								<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&createDoc=factory"
								   onclick="$(this).attr('href',$(this).attr('href')+'&letterhead='+$('#letterhead_factory').val());"><?= $_LANG->get('Generieren') ?></a>
							</ul>
							<?php } ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default" style="margin-bottom: -2px;">
			<div class="panel-heading">
				<h3 class="panel-title" style="font-size: 16px;">
					Lieferschein
				</h3>
			</div>
			<div class="table-responsive" style="margin: 0px 0px 0px 0px;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="10%"><?= $_LANG->get('Dokumentenname') ?></th>
						<th width="10%"><?= $_LANG->get('Versch.') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt von') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt am') ?></th>
						<th width="10%"><?= $_LANG->get('Dokumente') ?></th>
					</tr>
					</thead>
					<tbody>
					<?php

					//---------------------------------------------------------------------------
					// Lieferschein
					//---------------------------------------------------------------------------
					$docs = Document::getDocuments(Array("type" => Document::TYPE_DELIVERY, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
					<?
					if (count($docs) > 0) {
						foreach ($docs AS $doc) { ?>
							<tr class="<?= getRowColor(0) ?>">
								<td>
									<span class="ok"><?= $doc->getName() ?></span>
								</td>
								<td>
									<? if ($doc->getSent())
										echo '<img src="images/status/green_small.svg">';
									else
										echo '<img src="images/status/red_small.svg">'; ?>
								</td>
								<td>
									<?= $doc->getCreateUser()->getNameAsLine() ?>
								</td>
								<td>
									<?= date('d.m.Y H:m', $doc->getCreateDate()) ?>
								</td>
								<td>
									<table cellpaddin="0" cellspacing="0" width="100%">
										<tr>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=email"><?= $_LANG->get('E-Mail') ?></a>
												</ul>
											</td>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=print"><?= $_LANG->get('Print') ?></a>
												</ul>
											</td>
											<td width="40%">
												<ul class="postnav_text_del">
													<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&deleteDoc=<?= $doc->getId() ?>"><?= $_LANG->get('L&ouml;schen') ?></a>
												</ul>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<? $x++;
						}
					}
					?>
					<tr class="<?= getRowColor(0) ?>">
						<td>
							<!-- span class="error"><?= $_LANG->get('nicht vorhanden') ?></span--> &ensp;
						</td>
						<td>&nbsp;</td>
						<td>- - -</td>
						<td>- - -</td>
						<td>
							<?php if (!$margin_warning || $_USER->hasRightsByGroup('colinv_ignoremargin')){?>
							<ul class="postnav_text_save">
								<?php
								$letterheads = Letterhead::getAllForType(Document::TYPE_DELIVERY);
								?>
								<select name="letterhead_delivery" id="letterhead_delivery" class="form-control"
										style="margin-bottom: 5px;">
									<?php
									foreach ($letterheads as $item) {
										if ($item->getStd() == 1)
											echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
										else
											echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
									}
									?>
								</select>
								<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&createDoc=delivery"
								   onclick="$(this).attr('href',$(this).attr('href')+'&letterhead='+$('#letterhead_delivery').val());"><?= $_LANG->get('Generieren') ?></a>
							</ul>
							<?php } ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default" style="margin-bottom: -2px;">
			<div class="panel-heading">
				<h3 class="panel-title" style="font-size: 16px;">
					Etiketten
				</h3>
			</div>
			<div class="table-responsive" style="margin: 0px 0px 0px 0px;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="10%"><?= $_LANG->get('Dokumentenname') ?></th>
						<th width="10%"><?= $_LANG->get('Versch.') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt von') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt am') ?></th>
						<th width="10%"><?= $_LANG->get('Dokumente') ?></th>
					</tr>
					</thead>
					<tbody>
					<?
					//---------------------------------------------------------------------------
					// Etiketten
					//---------------------------------------------------------------------------
					$docs = Document::getDocuments(Array("type" => Document::TYPE_LABEL, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
					<?
					if (count($docs) > 0) {
						foreach ($docs AS $doc) { ?>
							<tr class="<?= getRowColor(0) ?>">
								<td>
									<span class="ok"><?= $doc->getName() ?></span>
								</td>
								<td>
									<? if ($doc->getSent())
										echo '<img src="images/status/green_small.svg">';
									else
										echo '<img src="images/status/red_small.svg">'; ?>
								</td>
								<td>
									<?= $doc->getCreateUser()->getNameAsLine() ?>
								</td>
								<td>
									<?= date('d.m.Y H:m', $doc->getCreateDate()) ?>
								</td>
								<td>
									<table cellpaddin="0" cellspacing="0" width="100%">
										<tr>
											<td width="30%">
												<!--ul class="postnav_text">
						<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=email"><?= $_LANG->get('E-Mail') ?></a>
					</ul-->
											</td>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=print"><?= $_LANG->get('Print') ?></a>
												</ul>
											</td>
											<td width="40%">
												<ul class="postnav_text_del">
													<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&deleteDoc=<?= $doc->getId() ?>"><?= $_LANG->get('L&ouml;schen') ?></a>
												</ul>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<? $x++;
						}
					}
					?>
					<tr class="<?= getRowColor(0) ?>">
						<td colspan="4">
							<div class="form-horizontal">
								<div class="form-group">
									<label for="" class="col-xs-1 control-label">Menge</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="label_box_amount" id="label_box_amount" value="1">
									</div>
									<label for="" class="col-xs-2 control-label">Logo drucken</label>
									<div class="col-xs-1">
										<input type="checkbox" name="label_print_logo" id="label_print_logo" value="1"/>
									</div>
									<label for="" class="col-xs-1  control-label">Titel</label>
									<div class="col-xs-3">
										<input type="text" class="form-control" name="label_title" id="label_title"/>
									</div>
								</div>
							</div>
						</td>
						<td>
							<?php if (!$margin_warning || $_USER->hasRightsByGroup('colinv_ignoremargin')){?>
							<ul class="postnav_text_save">
								<?php
								$letterheads = Letterhead::getAllForType(Document::TYPE_LABEL);
								?>
								<select name="letterhead_label" id="letterhead_label" class="form-control"
										style="margin-bottom: 5px;">
									<?php
									foreach ($letterheads as $item) {
										if ($item->getStd() == 1)
											echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
										else
											echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
									}
									?>
								</select>
								<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&createDoc=label"
								   onclick="$(this).attr('href',$(this).attr('href')+'&letterhead='+$('#letterhead_label').val()+'&label_box_amount='+$('#label_box_amount').val()+'&label_print_logo='+$('#label_print_logo').val()+'&label_title='+$('#label_title').val());"><?= $_LANG->get('Generieren') ?></a>
							</ul>
							<?php } ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default" style="margin-bottom: -2px;">
			<div class="panel-heading">
				<h3 class="panel-title" style="font-size: 16px;">
					Rechnung
				</h3>
			</div>
			<div class="table-responsive" style="margin: 0px 0px 0px 0px;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="10%"><?= $_LANG->get('Dokumentenname') ?></th>
						<th width="10%"><?= $_LANG->get('Versch.') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt von') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt am') ?></th>
						<th width="10%"><?= $_LANG->get('Dokumente') ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					//---------------------------------------------------------------------------
					// Rechnung
					//---------------------------------------------------------------------------
					$docs = Document::getDocuments(Array("type" => Document::TYPE_INVOICE, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
					<?
					if (count($docs) > 0) {
						foreach ($docs AS $doc) { ?>
							<tr class="<?= getRowColor(0) ?> <?php if ($doc->getStornoDate() > 0) echo ' canceled ';?>">
								<td>
									<span class="ok"><?= $doc->getName() ?></span>
								</td>
								<td>
									<? if ($doc->getSent())
										echo '<img src="images/status/green_small.svg">';
									else
										echo '<img src="images/status/red_small.svg">'; ?>
									<?php if ($doc->getStornoDate()) {?>Storno: <?= date('d.m.Y H:m', $doc->getStornoDate()) ?><?php } ?>
								</td>
								<td>
									<?= $doc->getCreateUser()->getNameAsLine() ?>
								</td>
								<td>
									<?= date('d.m.Y H:m', $doc->getCreateDate()) ?>
								</td>
								<td>
									<table cellpaddin="0" cellspacing="0" width="100%">
										<tr>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=email"><?= $_LANG->get('E-Mail') ?></a>
												</ul>
											</td>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=print"><?= $_LANG->get('Print') ?></a>
												</ul>
											</td>
<!--											<td width="40%">-->
<!--												<ul class="postnav_text_del">-->
<!--													<a href="index.php?page=--><?//= $_REQUEST['page'] ?><!--&ciid=--><?//= $collectinv->getId() ?><!--&exec=docs&deleteDoc=--><?//= $doc->getId() ?><!--">--><?//= $_LANG->get('L&ouml;schen') ?><!--</a>-->
<!--												</ul>-->
<!--											</td>-->
										</tr>
									</table>
								</td>
							</tr>
							<? $x++;
						}
					}
					?>
					<tr class="<?= getRowColor(0) ?>">
						<td>
							<!-- span class="error"><?= $_LANG->get('nicht vorhanden') ?></span--> &ensp;
						</td>
						<td>&nbsp;</td>
						<td>- - -</td>
						<td>- - -</td>
						<td>
							<?php if ($collectinv->getLocked() == 0 && (!$margin_warning || $_USER->hasRightsByGroup('colinv_ignoremargin'))){?>
							<ul class="postnav_text_save">
								<?php
								$letterheads = Letterhead::getAllForType(Document::TYPE_INVOICE);
								?>
								<select name="letterhead_invoice" id="letterhead_invoice" class="form-control"
										style="margin-bottom: 5px;">
									<?php
									foreach ($letterheads as $item) {
										if ($item->getStd() == 1)
											echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
										else
											echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
									}
									?>
								</select>
								<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=docs&createDoc=invoice"
								   onclick="$(this).attr('href',$(this).attr('href')+'&letterhead='+$('#letterhead_invoice').val());"><?= $_LANG->get('Generieren') ?></a>
							</ul>
							<?php } ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default" style="margin-bottom: -2px;">
			<div class="panel-heading">
				<h3 class="panel-title" style="font-size: 16px;">
					Gutschrift
				</h3>
			</div>
			<div class="table-responsive" style="margin: 0px 0px 0px 0px;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="10%"><?= $_LANG->get('Dokumentenname') ?></th>
						<th width="10%"><?= $_LANG->get('Versch.') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt von') ?></th>
						<th width="10%"><?= $_LANG->get('Erstellt am') ?></th>
						<th width="10%"><?= $_LANG->get('Dokumente') ?></th>
					</tr>
					</thead>
					<tbody>
					<?
					//---------------------------------------------------------------------------
					// Gutschriften
					//---------------------------------------------------------------------------
					$docs = Document::getDocuments(Array("type" => Document::TYPE_REVERT, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>

					<?
					if (count($docs) > 0) {
						foreach ($docs AS $doc) { ?>
							<tr class="<?= getRowColor(0) ?> <?php if ($doc->getStornoDate() > 0) echo ' canceled ';?>">
								<td>
									<span class="ok"><?= $doc->getName() ?></span>
								</td>
								<td>
									<? if ($doc->getSent())
										echo '<img src="images/status/green_small.svg">';
									else
										echo '<img src="images/status/red_small.svg">'; ?>
									<?php if ($doc->getStornoDate()) {?>Storno: <?= date('d.m.Y H:m', $doc->getStornoDate()) ?><?php } ?>
								</td>
								<td>
									<?= $doc->getCreateUser()->getNameAsLine() ?>
								</td>
								<td>
									<?= date('d.m.Y H:m', $doc->getCreateDate()) ?>
								</td>
								<td>
									<table cellpaddin="0" cellspacing="0" width="100%">
										<tr>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=email"><?= $_LANG->get('E-Mail') ?></a>
												</ul>
											</td>
											<td width="30%">
												<ul class="postnav_text">
													<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?= $doc->getId() ?>&version=print"><?= $_LANG->get('Print') ?></a>
												</ul>
											</td>
<!--											<td width="40%">-->
<!--												<ul class="postnav_text_del">-->
<!--													<a href="index.php?page=--><?//= $_REQUEST['page'] ?><!--&ciid=--><?//= $collectinv->getId() ?><!--&exec=docs&deleteDoc=--><?//= $doc->getId() ?><!--">--><?//= $_LANG->get('L&ouml;schen') ?><!--</a>-->
<!--												</ul>-->
<!--											</td>-->
										</tr>
									</table>
								</td>
							</tr>
							<? $x++;
						}
					}
					?>
					<tr class="<?= getRowColor(0) ?>">
						<td>
							<!-- span class="error"><?= $_LANG->get('nicht vorhanden') ?></span--> &ensp;
						</td>
						<td>&nbsp;</td>
						<td>- - -</td>
						<td>- - -</td>
						<td>
							<ul class="postnav_text_save">
								<a href="index.php?page=<?= $_REQUEST['page'] ?>&ciid=<?= $collectinv->getId() ?>&exec=createNewRevert"><?= $_LANG->get('Generieren') ?></a>
							</ul>
						</td>

					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?
		//---------------------------------------------------------------------------
		// Ende Dokumente
		//---------------------------------------------------------------------------?>
	</div>
</div>
<iframe style="width:1px;height:1px;display:none" id="idx_iframe_doc" src=""></iframe>

<!-- TinyMCE -->
<script
	type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">


	tinyMCE.init({
// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,outdent,indent,blockquote,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "undo,redo,|,link,unlink,anchor,cleanup,code,|,forecolor,backcolor,|,sub,sup,|,tablecontrols",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		formats : {
			alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
			aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
			alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
			alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
			bold : {inline : 'span', 'classes' : 'bold'},
			italic : {inline : 'span', 'classes' : 'italic'},
			underline : {inline : 'span', 'classes' : 'underline', exact : true},
			strikethrough : {inline : 'del'}
		},

		paste_remove_styles: true, paste_auto_cleanup_on_paste : true, force_br_newlines: true, forced_root_block: '',
	});

</script>
<!-- /TinyMCE -->