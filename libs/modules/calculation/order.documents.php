<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       24.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/documents/document.class.php');
require_once('libs/modules/organizer/nachricht.class.php');
require_once('libs/modules/paper/paper.class.php');

$all_papers = Paper::getAllPapers(Paper::ORDER_NAME);

if((int)$_REQUEST["deleteDoc"] > 0)
{
    $doc = new Document((int)$_REQUEST["deleteDoc"]);
    $doc->delete();
}

if($_REQUEST["createDoc"])
{
    $doc = new Document();
    $doc->setRequestId($order->getId());
    $doc->setRequestModule(Document::REQ_MODULE_ORDER);
    
    if($_REQUEST["createDoc"] == "offer"){
		$doc->setType(Document::TYPE_OFFER);
		$order->setStatus(2);
		$order->save();
	}
    if($_REQUEST["createDoc"] == "offerconfirm"){
        $doc->setType(Document::TYPE_OFFERCONFIRM);
		$order->setStatus(3);
		$order->save();
	}
    if($_REQUEST["createDoc"] == "factory"){
        $doc->setType(Document::TYPE_FACTORY);
    }
    if($_REQUEST["createDoc"] == "paper_order"){
		// echo "Debug paper_order_paper: " . $_REQUEST["paper_order_paper"];
		if($_REQUEST["paper_order_paper"] != "0" && $_REQUEST["paper_order_boegen"] != "" && $_REQUEST["paper_order_price"] != "" && $_REQUEST["paper_order_supplier"] != "") {
			$doc->setType(Document::TYPE_PAPER_ORDER);
			$doc->setPaperOrderPid($_REQUEST["paper_order_paper"]);
			$order->setPaperOrderBoegen($_REQUEST["paper_order_boegen"]);
			$order->setPaperOrderPrice($_REQUEST["paper_order_price"]);
			$order->setPaperOrderSupplier($_REQUEST["paper_order_supplier"]);
			$order->save();
		} else {
		?> 
		<script language="javascript">alert("Es wurden nicht alle erforderlichen Angaben gemacht");</script>
		<?
		}
    }
    if($_REQUEST["createDoc"] == "delivery"){
    	$doc->setType(Document::TYPE_DELIVERY);
    	$order->setDeliveryAmount((int)$_REQUEST["delivery_amount"]);
    	$order->save();
    }
    if($_REQUEST["createDoc"] == "invoice"){
    	$doc->setType(Document::TYPE_INVOICE);
    	$order->setInvoiceAmount((int)$_REQUEST["invoice_amount"]);
    	$order->setInvoicePriceUpdate((int)$_REQUEST["invoice_update_price"]);
    	$order->save();
    }
    if($_REQUEST["createDoc"] == "label"){
    	$doc->setType(Document::TYPE_LABEL);
    	$order->setLabelTitle(trim(addslashes($_REQUEST["label_title"])));
    	$order->setLabelBoxAmount((int)$_REQUEST["label_box_amount"]);
    	$order->setLabelPalletAmount((int)$_REQUEST["label_pallet_amount"]);
    	$order->setLabelLogoActive((int)$_REQUEST["label_print_logo"]);
    	$order->save();
    }
    
    if($_REQUEST["createDoc"] == "factory" || $_REQUEST["createDoc"] == "label"){
        $doc->createDoc(Document::VERSION_PRINT, false, false);
    } else {
        $hash = $doc->createDoc(Document::VERSION_EMAIL);
        $doc->createDoc(Document::VERSION_PRINT, $hash);
    }
    $doc->save();
}

/*
 * Datei per Mail verschicken
*/

$_REQUEST["mail_subject"] = trim($_REQUEST["mail_subject"]);
$_REQUEST["mail_body"] = trim($_REQUEST["mail_body"]);
$nachricht = new Nachricht();

if ($_REQUEST["subexec"] == "send")
{

    // Nachricht mit werten fuellen
    $nachricht->setFrom($_USER);
    $nachricht->setSubject($_REQUEST["mail_subject"]);
    $nachricht->setText($_REQUEST["mail_body"]);

    $to = Array();
    foreach(array_keys($_REQUEST) as $key)
    {
        if (preg_match("/mail_touser_(?P<id>\d+)/", $key, $match))
        {
            $to[] = new User($match["id"]);
        } else if (preg_match("/mail_togroup_(?P<id>\d+)/", $key, $match))
        {
            $to[] = new Group($match["id"]);
        } else if (preg_match("/mail_tousercontact_(?P<id>\d+)/", $key, $match))
        {
            $to[] = new UserContact($match["id"]);
        } else if (preg_match("/mail_tobusinesscontact_(?P<id>\d+)/", $key, $match))
        {
            $to[] = new BusinessContact($match["id"]);
        } else if (preg_match("/mail_tocontactperson_(?P<id>\d+)/", $key, $match))
        {
            $to[] = new ContactPerson($match["id"]);
        }

    }
    $nachricht->setTo($to);

    $attach = Array();
    foreach(array_keys($_REQUEST) as $key)
    {
        if (preg_match("/mail_attach_(?P<id>\d+)/", $key, $match))
        {
            $attach[] = new Document($match["id"]);
        } 
    }
    $nachricht->setAttachments($attach);
    
    if ($nachricht->send())
    {
        foreach($attach as $file)
            $file->setSent(1);
        $savemsg = getSaveMessage(true);
    } else
        $savemsg = getSaveMessage(false);
} else
    $nachricht->setTo(Array($order->getCustomer()));

if((int)$_REQUEST["updateTexts"] == 1)
{
    $order->setTextOffer(trim(addslashes($_REQUEST["text_offer"])));
    $order->setTextOfferconfirm(trim(addslashes($_REQUEST["text_offerconfirm"])));
    $order->setTextInvoice(trim(addslashes($_REQUEST["text_invoice"])));
    $order->setShowProduct((int)$_REQUEST["order_show_productdetails"]);
    $order->setShowPricePer1000((int)$_REQUEST["order_show_priceperthousand"]);
    $order->setProductName(trim(addslashes($_REQUEST["order_productname"])));
    $savemsg = getSaveMessage($order->save());
}
?>

<script language="javascript">
function Update_Paper_Order(paperid)
{
	var select = document.getElementById("paper_order_supplier");
	document.getElementById('paper_order_boegen').value = "";
	document.getElementById('paper_order_price').value = "";
	select.options.length = 0;
	if (paperid != 0) {
		$.post("libs/modules/calculation/order.ajax.php", 
				{exec: 'getPaperOrder', paperid: paperid, orderid: <?=$order->getId()?>}, 
				function(data) {
					var teile = data.split("-+-+-");
					// Work on returned data
					document.getElementById('paper_order_boegen').value = teile[0];
					document.getElementById('paper_order_price').value = teile[1];
					paper_supplier = JSON.parse(teile[2]);
					paper_supplier.forEach(function(entry) {
						var supplier = entry.split("+|+");
						select.options[select.options.length] = new Option(supplier[1], supplier[0]);
					});
				});	
	}
}

function sendPaperOrder()
{
	var boegen = document.getElementById('paper_order_boegen').value;
	var price = document.getElementById('paper_order_price').value;
	var calc = document.getElementById('paper_order_calc').value;
	var supplier = document.getElementById('paper_order_supplier').value;
	
	if (calc != "0" && boegen != "" && price != "" && supplier != "") {
		document.getElementById('createDoc').value='paper_order';
		document.getElementById('form_create_document').submit();
	} else {
		alert("Es wurden nicht alle erforderlichen Angaben gemacht!");
	}
}
</script>
<link rel="stylesheet" href="css/documents.css" type="text/css">

<div class="box1">
<table width="100%">
    <colgroup>
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Kundennummer')?>:</td>
        <td class="content_row_clear"><?=$order->getCustomer()->getCustomernumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Vorgang')?>:</td>
        <td class="content_row_clear"><?=$order->getNumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Telefon')?></td>
        <td class="content_row_clear"><?=$order->getCustomer()->getPhone()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Name')?>:</td>
        <td class="content_row_clear" valign="top"><?=nl2br($order->getCustomer()->getNameAsLine())?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Adresse')?>:</td>
        <td class="content_row_clear"  valign="top"><?=nl2br($order->getCustomer()->getAddressAsLine())?></td>
        <td class="content_row_header"  valign="top"><?=$_LANG->get('E-Mail')?></td>
        <td class="content_row_clear" valign="top"><?=$order->getCustomer()->getEmail()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Produkt')?>:</td>
        <td class="content_row_clear" valign="top"><?=$order->getProduct()->getName()?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?>:</td>
        <td class="content_row_clear" valign="top" colspan="3"><?=$order->getProduct()->getDescription()?></td>
    </tr>
</table>
</div>
<br>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="text_form">
<input name="step" value="6" type="hidden">
<input name="exec" value="edit" type="hidden">
<input name="id" value="<?=$order->getId()?>" type="hidden">
<input name="updateTexts" value="1" type="hidden">

<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="33%"><b><?=$_LANG->get('Zusatztexte')?></b></td>
        <td></td>
        <td width="33%" align="right"><?=$savemsg?></td>
    </tr>
</table>

<div class="box2">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="28%"><?=$_LANG->get('Angebot')?></td>
        <td width="28%"><?=$_LANG->get('Auftragsbest&auml;tigung')?></td>
        <td width="28%"><?=$_LANG->get('Rechnung')?></td>
        <td width="16%">&emsp;</td>
    </tr>
    <tr>
        <td>
            <textarea name="text_offer" class="text" style="width:320px;height:100px;"><?=$order->getTextOffer()?></textarea>
        </td>
        <td>
            <textarea name="text_offerconfirm" class="text" style="width:330px;height:100px;"><?=$order->getTextOfferconfirm()?></textarea>
        </td>
        <td>
            <textarea name="text_invoice" class="text" style="width:320px;height:100px;"><?=$order->getTextInvoice()?></textarea>
        </td>
	</tr>
	<tr>
        <td valign="top" align="left">
        	<input type="checkbox" value="1" name="order_show_productdetails" type="text" 
					<?if ($order->getShowProduct() == 1) echo 'checked="checked"';?>
					title="<?=$_LANG->get('Produktdetails auf den Dokumenten ausweisen (Abmessungen, Papier, Farbikeit)');?>">
        	<span title="<?$_LANG->get('Produktdetails in den Dokumenten anzeigen');?>">
        	<?=$_LANG->get('Produktdetails anzeigen');?>
        	</span>
		</td>
		<td>
        	<input type="checkbox" value="1" name="order_show_priceperthousand" 
					<?if ($order->getShowPricePer1000() == 1) echo 'checked="checked"';?>
					title="<?=$_LANG->get('Preis pro 1000 Stk. auf den Dokumenten ausweisen');?>">
        	<span title="<?$_LANG->get('Preis pro 1000 auf den Dokumenten ausweisen');?>">
        	<?=$_LANG->get('Preis pro 1000 anzeigen');?>
        	</span>
		</td>
		<td>
        	<span title="<?$_LANG->get('Name des Produktes in den Dokumenten');?>">
        		<?=$_LANG->get('Produktbezeichnung');?>:
        	</span>
        		<input type="text" class="text" value="<?=$order->getProductName()?>" name="order_productname" id="order_productname" 
					title="<?=$_LANG->get('Produktbezeichnung auf den Dokumenten &uuml;berschreiben');?>" style="width:180px;">
        </td>
    </tr>
</table>
</div>

<table width="100%">
    <tr>
        <td>
        </td>
        <td align="center" width="200">
            <!-- ul class="graphicalButton pointer" onclick="document.text_form.submit();">
                <?=$_LANG->get('Speichern')?>
            </ul-->
            <input type="submit" value="<?= $_LANG->get('Speichern') ?>">
        </td>
    </tr>
</table>
</form>
<br>

<h1><?=$_LANG->get('Dokumente')?></h1>

<? // Form fuer die Eingabefelder der Dokumente?>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="form_create_document" id="form_create_document">
<input type="hidden" name="id"value="<?=$order->getId()?>">
<input type="hidden" name="step" value="6">
<input type="hidden" name="exec" value="edit">
<input type="hidden" name="createDoc" id="createDoc" value="">

<div class="box2">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<colgroup>
    <col width="130">
    <col>
    <col width="60">
    <col width="150">
    <col width="100">
    <col width="250">
</colgroup>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
    <td class="content_row_header"><?=$_LANG->get('Dokumentenname')?></td>
    <td class="content_row_header"><?=$_LANG->get('Versch.')?></td>
    <td class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
    <td class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
    <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
</tr>
<? 
$x=0;
//---------------------------------------------------------------------------
// Angebot
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_OFFER, "requestId" => $order->getId(), "module" => Document::REQ_MODULE_ORDER));?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Angebot')?></b></td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){ ?>
		<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&ensp;</td>
		<td class="content_row_clear">
			<span class="ok"><?=$doc->getName()?></span>
		</td>
		<td class="content_row_clear">
		<? 	if($doc->getSent())
	    	    echo '<img src="images/status/green_small.gif">';
	    	else
	        	echo '<img src="images/status/red_small.gif">'; ?>
	    </td>
		<td class="content_row_clear">
			<?=$doc->getCreateUser()->getNameAsLine()?>
		</td>
		<td class="content_row_clear">
			<?=date('d.m.Y H:m', $doc->getCreateDate())?>
		</td>
		<td class="content_row_clear">
			<table cellpaddin="0" cellspacing="0" width="100%">
				<tr>
					<td width="30%">
			    		<ul class="postnav_text">
			    			<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=email"><?=$_LANG->get('E-Mail')?></a>
			    		</ul>
	    		</td>
	    		<td width="30%">
	    			<ul class="postnav_text">
	    				<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$_LANG->get('Print')?></a>
	    			</ul>
				</td>
				<td width="40%">
					<ul class="postnav_text_del">
						<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
					</ul>
				</td>
				</tr>
			</table>
		</td>
		</tr>
	<?	$x++;
	}
}
?>
<tr class="<?=getRowColor(0)?>">
	<td class="content_row_clear">&emsp;</td>
	<td class="content_row_clear">
		    <!-- span class="error"><?=$_LANG->get('nicht vorhanden')?></span--> &ensp;
	</td>
	<td class="content_row_clear">&nbsp;</td>
	<td class="content_row_clear">- - -</td>
	<td class="content_row_clear">- - -</td>
	<td class="content_row_clear">
		<ul class="postnav_text_save">
			<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&createDoc=offer"><?=$_LANG->get('Generieren')?></a>
		</ul>
	</td>
	
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}

//---------------------------------------------------------------------------
// Angebotsbetsaetigung
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_OFFERCONFIRM, "requestId" => $order->getId(), "module" => Document::REQ_MODULE_ORDER));?>

<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Auftragsbest&auml;tigung')?></b></td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){ ?>
		<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&ensp;</td>
		<td class="content_row_clear">
			<span class="ok"><?=$doc->getName()?></span>
		</td>
		<td class="content_row_clear">
		<? 	if($doc->getSent())
	    	    echo '<img src="images/status/green_small.gif">';
	    	else
	        	echo '<img src="images/status/red_small.gif">'; ?>
	    </td>
		<td class="content_row_clear">
			<?=$doc->getCreateUser()->getNameAsLine()?>
		</td>
		<td class="content_row_clear">
			<?=date('d.m.Y H:m', $doc->getCreateDate())?>
		</td>
		<td class="content_row_clear">
			<table cellpaddin="0" cellspacing="0" width="100%">
				<tr>
					<td width="30%">
			    		<ul class="postnav_text">
			    			<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=email"><?=$_LANG->get('E-Mail')?></a>
			    		</ul>
	    		</td>
	    		<td width="30%">
	    			<ul class="postnav_text">
	    				<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$_LANG->get('Print')?></a>
	    			</ul>
				</td>
				<td width="40%">
					<ul class="postnav_text_del">
						<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
					</ul>
				</td>
				</tr>
			</table>
		</td>
		</tr>
	<?	$x++;
	}
}
?>
<tr class="<?=getRowColor(0)?>">
	<td class="content_row_clear">&emsp;</td>
	<td class="content_row_clear">
		    <!-- span class="error"><?=$_LANG->get('nicht vorhanden')?></span--> &ensp;
	</td>
	<td class="content_row_clear">&nbsp;</td>
	<td class="content_row_clear">- - -</td>
	<td class="content_row_clear">- - -</td>
	<td class="content_row_clear">
		<ul class="postnav_text_save">
			<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&createDoc=offerconfirm"><?=$_LANG->get('Generieren')?></a>
		</ul>
	</td>
	
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}

//---------------------------------------------------------------------------
// Drucktasche
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_FACTORY, "requestId" => $order->getId(), "module" => Document::REQ_MODULE_ORDER));?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Drucktasche')?></b></td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){ ?>
		<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&ensp;</td>
		<td class="content_row_clear">
			<span class="ok"><?=$doc->getName()?></span>
		</td>
		<td class="content_row_clear">
		<? 	if($doc->getSent())
	    	    echo '<img src="images/status/green_small.gif">';
	    	else
	        	echo '<img src="images/status/red_small.gif">'; ?>
	    </td>
		<td class="content_row_clear">
			<?=$doc->getCreateUser()->getNameAsLine()?>
		</td>
		<td class="content_row_clear">
			<?=date('d.m.Y H:m', $doc->getCreateDate())?>
		</td>
		<td class="content_row_clear">
			<table cellpaddin="0" cellspacing="0" width="100%">
				<tr>
					<td width="30%">
			    		<!-- ul class="postnav_text">
			    			<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=email"><?=$_LANG->get('E-Mail')?></a>
			    		</ul--> &ensp;
	    			</td>
	    		<td width="30%">
	    			<ul class="postnav_text">
	    				<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$_LANG->get('Print')?></a>
	    			</ul>
				</td>
				<td width="40%">
					<ul class="postnav_text_del">
						<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
					</ul>
				</td>
				</tr>
			</table>
		</td>
		</tr>
	<?	$x++;
	}
}
?>
	<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&emsp;</td>
		<td class="content_row_clear">
			    <!-- span class="error"><?=$_LANG->get('nicht vorhanden')?></span--> &ensp;
		</td>
		<td class="content_row_clear">&nbsp;</td>
		<td class="content_row_clear">- - -</td>
		<td class="content_row_clear">- - -</td>
		<td class="content_row_clear">
			<ul class="postnav_text_save">
				<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&createDoc=factory"><?=$_LANG->get('Generieren')?></a>
			</ul>
		</td>
		
	</tr>
<?

if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}
//---------------------------------------------------------------------------
// Papier Bestellung by ascherer
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_PAPER_ORDER, "requestId" => $order->getId(), "module" => Document::REQ_MODULE_ORDER));?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Papier Bestellung')?></b></td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){ 
		$tmp_paper = new Paper($doc->getPaperOrderPid());
		?>
		<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&ensp;</td>
		<td class="content_row_clear">
			<span class="ok"><?=$doc->getName()?></span>&emsp; &emsp; <?=$tmp_paper->getName()?>
		</td>
		<td class="content_row_clear">
		<? 	if($doc->getSent())
	    	    echo '<img src="images/status/green_small.gif">';
	    	else
	        	echo '<img src="images/status/red_small.gif">'; ?>
	    </td>
		<td class="content_row_clear">
			<?=$doc->getCreateUser()->getNameAsLine()?>
		</td>
		<td class="content_row_clear">
			<?=date('d.m.Y H:m', $doc->getCreateDate())?>
		</td>
		<td class="content_row_clear">
			<table cellpaddin="0" cellspacing="0" width="100%">
				<tr>
					<td width="30%">
			    		<ul class="postnav_text">
			    			<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=email"><?=$_LANG->get('E-Mail')?></a>
			    		</ul>
	    			</td>
					<td width="30%">
						<ul class="postnav_text">
							<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$_LANG->get('Print')?></a>
						</ul>
					</td>
					<td width="40%">
						<ul class="postnav_text_del">
							<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
						</ul>
					</td>
				</tr>
			</table>
		</td>
		</tr>
	<?	$x++;
	}
}
?>
	<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear"></td>
		<td class="content_row_clear" colspan="4">
			<?=$_LANG->get('Papier')?> <select id="paper_order_paper" name="paper_order_paper" style="width:100px" onfocus="markfield(this,0)" onblur="markfield(this,1)" onchange="Update_Paper_Order(this.options[this.selectedIndex].value)" class="text">
				<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
			<? 
			$calc_papers = Array();
			foreach(Calculation::getAllCalculations($order) as $calc) {
				if ($calc->getState() == 1) {
					if($calc->getPaperContent()->getId() > 0) {
						if(!in_array($calc->getPaperContent()->getId(),$calc_papers) && count($calc_papers) > 0)
							$calc_papers[] = $calc->getPaperContent()->getId();
						else if (count($calc_papers) == 0)
							$calc_papers[] = $calc->getPaperContent()->getId();
					}
					if($calc->getPaperAddContent()->getId() > 0) {
						if(!in_array($calc->getPaperAddContent()->getId(),$calc_papers) && count($calc_papers) > 0)
							$calc_papers[] = $calc->getPaperAddContent()->getId();
						else if (count($calc_papers) == 0)
							$calc_papers[] = $calc->getPaperAddContent()->getId();
					}
					if($calc->getPaperAddContent2()->getId() > 0) {
						if(!in_array($calc->getPaperAddContent2()->getId(),$calc_papers) && count($calc_papers) > 0)
							$calc_papers[] = $calc->getPaperAddContent2()->getId();
						else if (count($calc_papers) == 0)
							$calc_papers[] = $calc->getPaperAddContent2()->getId();
					}
					if($calc->getPaperAddContent3()->getId() > 0) {
						if(!in_array($calc->getPaperAddContent3()->getId(),$calc_papers) && count($calc_papers) > 0)
							$calc_papers[] = $calc->getPaperAddContent3()->getId();
						else if (count($calc_papers) == 0)
							$calc_papers[] = $calc->getPaperAddContent3()->getId();
					}
					if($calc->getPaperEnvelope()->getId() > 0) {
						if(!in_array($calc->getPaperEnvelope()->getId(),$calc_papers) && count($calc_papers) > 0)
							$calc_papers[] = $calc->getPaperEnvelope()->getId();
						else if (count($calc_papers) == 0)
							$calc_papers[] = $calc->getPaperEnvelope()->getId();
					}
				}
			} 
			foreach($calc_papers as $cp) {
				$tmp_paper = new Paper($cp);
				?>
				<option value="<?=$cp?>"><?=$tmp_paper->getName()?></option>
			<?
			}
			?>
			</select>
			&emsp; &emsp;
			<?=$_LANG->get('Bögen')?> <input type="text" class="text" name="paper_order_boegen" id="paper_order_boegen" style="width: 80px;"> 
			&emsp; &emsp;
			<?=$_LANG->get('Preis')?> <input type="text" class="text" name="paper_order_price" id="paper_order_price" style="width: 80px;"> <?=$_USER->getClient()->getCurrency()?>
			&emsp; &emsp;
			<?=$_LANG->get('Lieferant')?> <select id="paper_order_supplier" name="paper_order_supplier" style="width:100px" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
			</select>
		</td>
		<td class="content_row_clear">
			<ul class="postnav_text_save">
				<a href="#"	onclick="document.getElementById('createDoc').value='paper_order'; document.getElementById('form_create_document').submit();">
				<?=$_LANG->get('Generieren')?></a>
			</ul>
		</td>
		
	</tr>
<?

if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}
//---------------------------------------------------------------------------
// Rechnung
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_INVOICE, "requestId" => $order->getId(), "module" => Document::REQ_MODULE_ORDER)); ?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Rechnung')?></b></td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){ ?>
		<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&ensp;</td>
		<td class="content_row_clear">
			<span class="ok"><?=$doc->getName()?></span>
		</td>
		<td class="content_row_clear">
		<? 	if($doc->getSent())
	    	    echo '<img src="images/status/green_small.gif">';
	    	else
	        	echo '<img src="images/status/red_small.gif">'; ?>
	    </td>
		<td class="content_row_clear">
			<?=$doc->getCreateUser()->getNameAsLine()?>
		</td>
		<td class="content_row_clear">
			<?=date('d.m.Y H:m', $doc->getCreateDate())?>
		</td>
		<td class="content_row_clear">
			<table cellpaddin="0" cellspacing="0" width="100%">
				<tr>
					<td width="30%">
			    		<ul class="postnav_text">
			    			<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=email"><?=$_LANG->get('E-Mail')?></a>
			    		</ul>
	    		</td>
	    		<td width="30%">
	    			<ul class="postnav_text">
	    				<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$_LANG->get('Print')?></a>
	    			</ul>
				</td>
				<td width="40%">
					<ul class="postnav_text_del">
						<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
					</ul>
				</td>
				</tr>
			</table>
		</td>
		</tr>
	<?	$x++;
	}
}
?>
<tr class="<?=getRowColor(0)?>">
	<td class="content_row_clear">&emsp;</td>
	<td class="content_row_clear" colspan="4">
		<?=$_LANG->get('Menge');?>: 
		<input type="text" class="text" name="invoice_amount" id="invoice_amount" style="width: 80px;"
			value="<?if ($order->getInvoiceAmount() > 0) echo $order->getInvoiceAmount();?>">  
		&emsp; &emsp;
		<input type="checkbox" name="invoice_update_price" value="1" 
			<?if ($order->getInvoicePriceUpdate() == 1) echo 'checked="checked"';?> />
		<?=$_LANG->get('Preis an Menge anpassen');?>  
	</td>
	<td class="content_row_clear">
		<ul class="postnav_text_save">
			<a href="#"
				onclick="document.getElementById('createDoc').value='invoice'; 
						 document.getElementById('form_create_document').submit();"
				><?=$_LANG->get('Generieren')?></a>
		</ul>
	</td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}


//---------------------------------------------------------------------------
// Lieferschein
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_DELIVERY, "requestId" => $order->getId(), "module" => Document::REQ_MODULE_ORDER));?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Lieferschein')?></b></td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){ ?>
		<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&ensp;</td>
		<td class="content_row_clear">
			<span class="ok"><?=$doc->getName()?></span>
		</td>
		<td class="content_row_clear">
		<? 	if($doc->getSent())
	    	    echo '<img src="images/status/green_small.gif">';
	    	else
	        	echo '<img src="images/status/red_small.gif">'; ?>
	    </td>
		<td class="content_row_clear">
			<?=$doc->getCreateUser()->getNameAsLine()?>
		</td>
		<td class="content_row_clear">
			<?=date('d.m.Y H:m', $doc->getCreateDate())?>
		</td>
		<td class="content_row_clear">
			<table cellpaddin="0" cellspacing="0" width="100%">
				<tr>
					<td width="30%">
			    		<ul class="postnav_text">
			    			<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=email"><?=$_LANG->get('E-Mail')?></a>
			    		</ul>
	    		</td>
	    		<td width="30%">
	    			<ul class="postnav_text">
	    				<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$_LANG->get('Print')?></a>
	    			</ul>
				</td>
				<td width="40%">
					<ul class="postnav_text_del">
						<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
					</ul>
				</td>
				</tr>
			</table>
		</td>
		</tr>
	<?	$x++;
	}
} 
?>
	<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&emsp;</td>
		<td class="content_row_clear" colspan="4">
			<?=$_LANG->get('Menge');?>: 
			<input type="text" class="text" name="delivery_amount" id="delivery_amount" style="width: 80px;"
					value="<?if ($order->getDeliveryAmount() > 0) echo $order->getDeliveryAmount();?>" />  
		</td>
		<td class="content_row_clear">
			<ul class="postnav_text_save">
				<a href="#"
					onclick="document.getElementById('createDoc').value='delivery'; 
							 document.getElementById('form_create_document').submit();"
					><?=$_LANG->get('Generieren')?></a>
			</ul>
		</td>
		
	</tr>
<?

if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}

//---------------------------------------------------------------------------
// Etiketten
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_LABEL, "requestId" => $order->getId(), "module" => Document::REQ_MODULE_ORDER)); ?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Etiketten')?></b></td>
</tr>
<?
if(count($docs) > 0){
	foreach ($docs AS $doc){ ?>
		<tr class="<?=getRowColor(0)?>">
		<td class="content_row_clear">&ensp;</td>
		<td class="content_row_clear">
			<span class="ok"><?=$doc->getName()?></span>
		</td>
		<td class="content_row_clear">
		<? 	if($doc->getSent())
	    	    echo '<img src="images/status/green_small.gif">';
	    	else
	        	echo '<img src="images/status/red_small.gif">'; ?>
	    </td>
		<td class="content_row_clear">
			<?=$doc->getCreateUser()->getNameAsLine()?>
		</td>
		<td class="content_row_clear">
			<?=date('d.m.Y H:m', $doc->getCreateDate())?>
		</td>
		<td class="content_row_clear">
			<table cellpaddin="0" cellspacing="0" width="100%">
				<tr>
					<td width="30%">
				    	<!--ul class="postnav_text">
				    		<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=email"><?=$_LANG->get('E-Mail')?></a>
				    	</ul-->
		    		</td>
		    		<td width="30%">
		    			<ul class="postnav_text">
		    				<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$_LANG->get('Print')?></a>
		    			</ul>
					</td>
					<td width="40%">
						<ul class="postnav_text_del">
							<a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=6&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
						</ul>
					</td>
				</tr>
			</table>
		</td>
		</tr>
	<?	$x++;
	}
}
?>
<tr class="<?=getRowColor(0)?>">
	<td class="content_row_clear">&emsp;</td>
	<td class="content_row_clear" colspan="4">
		<?=$_LANG->get('Menge');?>: 
		<input type="text" class="text" name="label_box_amount" id="label_box_amount" style="width: 80px;"
			value="<?if ($order->getLabelBoxAmount() > 0) echo $order->getLabelBoxAmount();?>">  
		
		&emsp; &emsp;
		<input type="checkbox" name="label_print_logo" value="1" 
			<?if ($order->getLabelLogoActive() == 1) echo 'checked="checked"';?> />
		<?=$_LANG->get('Logo drucken');?>
		
		&emsp; &emsp;
		<?=$_LANG->get('Titel');?>:
		<input type="text" class="text" name="label_title" id="label_title" style="width: 280px;"
			value="<?if ($order->getLabelTitle() == ""){
						 echo $order->getTitle();
					} else { 
						 echo $order->getLabelTitle();
					}?>">
	</td>
	<td class="content_row_clear">
		<ul class="postnav_text_save">
			<a href="#"
				onclick="document.getElementById('createDoc').value='label'; 
						 document.getElementById('form_create_document').submit();"
				><?=$_LANG->get('Generieren')?></a>
		</ul>
	</td>
</tr>
<?
/***
if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}***/


$nachricht->setAttachments($senddocs);
?>

</table>
</div>
</form>
<table width="100%">
    <tr>
        <td>
        </td>
        <td align="center" width="200">
            <ul class="graphicalButton pointer" onclick="document.getElementById('sendmail').style.display=''">
                <?=$_LANG->get('Mail verschicken')?>
            </ul>
        </td>
    </tr>
</table>
<iframe style="width:1px;height:1px;display:none" id="idx_iframe_doc" src=""></iframe>

<!-- TinyMCE -->
<script
	type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
// General options
mode : "exact",
elements: "mail_body",
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
content_css : "css/main.css",

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

<!-- FancyBox -->
<script
	type="text/javascript"
	src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script
	type="text/javascript"
	src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link
	rel="stylesheet" type="text/css"
	href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
$(document).ready(function() {
/*
*   Examples - images
*/

$("a#add_to").fancybox({
'type'    : 'iframe'
})
});
</script>
<!-- /FancyBox -->

<script language="javascript">
function removeMailto(what, id)
{
    if (what == 'user')
    {
        document.getElementById('mail_touser_'+id).disabled = true;
        document.getElementById('touserfield_'+id).style.display = 'none';
    } else if (what == 'group')
    {
        document.getElementById('mail_togroup_'+id).disabled = true;
        document.getElementById('togroupfield_'+id).style.display = 'none';
    } else if (what == 'usercontact')
    {
        document.getElementById('mail_tousercontact_'+id).disabled = true;
        document.getElementById('tousercontactfield_'+id).style.display = 'none';
    } else if (what == 'businesscontact')
    {
        document.getElementById('mail_tobusinesscontact_'+id).disabled = true;
        document.getElementById('tobusinesscontactfield_'+id).style.display = 'none';
    } else if (what == 'contactperson')
    {
        document.getElementById('mail_tocontactperson_'+id).disabled = true;
        document.getElementById('tocontactpersonfield_'+id).style.display = 'none';
    }
}

function removeAttach(id)
{
    document.getElementById('mail_attach_'+id).disabled = true;
    document.getElementById('attachfield_'+id).style.display = 'none';
}
</script>
<link rel="stylesheet" type="text/css" href="./css/mail.css" />
<br>
<div class="box2" id="sendmail" style="display:none">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
	<input type="hidden" name="exec" value="edit">
    <input type="hidden" name="step" value="6">
	<input type="hidden" name="subexec" value="send">
	<input type="hidden" name="id" value="<?=$order->getId()?>">
	<div class="newmailHeader">
		<table width="100%">
			<colgroup>
				<col width="150">
				<col>
			</colgroup>
			<tr>
				<td><b><?=$_LANG->get('Empf&auml;nger')?> </b></td>
				<td id="td_mail_to" name="td_mail_to"><?
				foreach($nachricht->getTo() as $to)
				{
				    // Falls Empfaenger Benutzer -> anzeigen
				    if (is_a($to, "User"))
				    {
				        $addStr = '<span class="newmailToField" id="touserfield_'.$to->getId().'"><span class="glyphicons glyphicons-user"></span>&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<span class="glyphicons glyphicons-remove pointer"  onclick="removeMailto(\'user\', '.$to->getId().')"></span>';
				        $addStr .= '<input type="hidden" name="mail_touser_'.$to->getId().'" id="mail_touser_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfaenger Gruppe -> anzeigen
				    if (is_a($to, "Group"))
				    {
				        $addStr = '<span class="newmailToField" id="togroupfield_'.$to->getId().'"><span class="glyphicons glyphicons-user"></span>&nbsp;'.$to->getName().'&nbsp;&nbsp;';
				        $addStr .= '<span class="glyphicons glyphicons-remove pointer"  onclick="removeMailto(\'group\', '.$to->getId().')"></span>';
				        $addStr .= '<input type="hidden" name="mail_togroup_'.$to->getId().'" id="mail_togroup_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfaenger UserContact -> anzeigen
				    if (is_a($to, "UserContact"))
				    {
				        $addStr = '<span class="newmailToField" id="tousercontactfield_'.$to->getId().'"><span class="glyphicons glyphicons-vcard"></span>&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<span class="glyphicons glyphicons-remove pointer"  onclick="removeMailto(\'usercontact\', '.$to->getId().')"></span>';
				        $addStr .= '<input type="hidden" name="mail_tousercontact_'.$to->getId().'" id="mail_tousercontact_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfaenger BusinessContact -> anzeigen
				    if (is_a($to, "BusinessContact"))
				    {
				        $addStr = '<span class="newmailToField" id="tobusinesscontactfield_'.$to->getId().'"><span class="glyphicons glyphicons-vcard"></span>&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<span class="glyphicons glyphicons-remove pointer"  onclick="removeMailto(\'businesscontact\', '.$to->getId().')"></span>';
				        $addStr .= '<input type="hidden" name="mail_tobusinesscontact_'.$to->getId().'" id="mail_tobusinesscontact_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfaenger BusinessContact -> anzeigen
				    if (is_a($to, "ContactPerson"))
				    {
				        $addStr = '<span class="newmailToField" id="tocontactpersonfield_'.$to->getId().'"><span class="glyphicons glyphicons-user"></span>&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<span class="glyphicons glyphicons-remove pointer"  onclick="removeMailto(\'contactperson\', '.$to->getId().')"></span>';
				        $addStr .= '<input type="hidden" name="mail_tocontactperson_'.$to->getId().'" id="mail_tocontactperson_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				}
				?> <a href="libs/modules/organizer/nachrichten.addrcpt.php"  class="icon-link"
					id="add_to"><span class="glyphicons glyphicons-plus" title="<?=$_LANG->get('Hinzuf&uuml;gen')?>"></span></a>
				</td>
			</tr>
			<tr>
				<td><b><?=$_LANG->get('Betreff')?> </b></td>
				<td id="td_mail_subject" name="td_mail_subject"><input
					name="mail_subject" style="width: 635px"
					value="<?=$nachricht->getSubject()?>"></td>
			</tr>
			<tr>
			    <td><b><?=$_LANG->get('Anh&auml;nge')?> </b></td>
			    <td id="td_mail_attach" name="td_mail_attach">
			        <? 
			            foreach($nachricht->getAttachments() as $sd)
			            {
			                echo '<span class="newmailAttachmentField" id="attachfield_'.$sd->getId().'"><span class="glyphicons glyphicons-plus"></span>';
			                echo $sd->getName().'<span class="glyphicons glyphicons-remove pointer"  onclick="removeAttach('.$sd->getId().')"></span>';
			                echo '<input type="hidden" name="mail_attach_'.$sd->getId().'" id="mail_attach_'.$sd->getId().'" value="1"></span>';
			            }
			        ?>
			    </td>
			<tr>
			    
			</tr>
		</table>
	</div>
	<div class="newmailBody">
		<textarea name="mail_body" id="mail_body" style="height: 200px; width: 790px">
			<?=$nachricht->getText() ?>
		</textarea>
	</div>
	<input type="submit" value="<?=$_LANG->get('Abschicken')?>">
</form>
</div>
