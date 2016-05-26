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
    $doc = new Document();
    $doc->setRequestId($collectinv->getId());
    $doc->setRequestModule(Document::REQ_MODULE_COLLECTIVEORDER);
    
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
/*
 * Datei per Mail verschicken
*/
$_REQUEST["mail_subject"] = trim($_REQUEST["mail_subject"]);
$_REQUEST["mail_body"] = trim($_REQUEST["mail_body"]);
$nachricht = new Nachricht();

if ($_REQUEST["subexec"] == "send")
{

    // Nachricht mit werten F�llen
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
    $nachricht->setTo(Array($collectinv->getBusinessContact()))


?>

<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>

<link rel="stylesheet" href="css/documents.css" type="text/css">
<table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tbody>
    	<tr>
            <td width="100%" align="left">
                <div class="btn-group" role="group">
                  <button type="button" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$collectinv->getId()?>';" class="btn btn-sm btn-default">Zurück</button>
                </div>
            </td>
			<td width="100%" align="right">
				<h1><?=$_LANG->get('Dokumentenverwaltung')?></h1>
			</td>
    	</tr>
    </tbody>
</table>
<br>
<?
//---------------------------------------------------------------------------
// Dokumenten Header + Footer
//---------------------------------------------------------------------------
?>
<div id="tabs">
    <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="form_collectiveinvoices" name="form_collectiveinvoices">
	<input 	type="hidden" name="exec" value="docs">
	<input 	type="hidden" name="subexec" value="doc_texts">
	<input 	type="hidden" name="ciid" value="<?php echo $collectinv->getId();?>">
	<ul>
		<li><a href="#tabs-0"><? echo $_LANG->get('Angebot');?></a></li>
		<li><a href="#tabs-1"><? echo $_LANG->get('Auftragsbestätigung');?></a></li>
		<li><a href="#tabs-2"><? echo $_LANG->get('Auftragstasche');?></a></li> 
		<li><a href="#tabs-3"><? echo $_LANG->get('Lieferschein');?></a></li> 
		<li><a href="#tabs-4"><? echo $_LANG->get('Rechnung');?></a></li>
		<li><a href="#tabs-5"><? echo $_LANG->get('Gutschrift');?></a></li>
	</ul>
 	<div id="tabs-0"> <!-- Angebot -->
	<table width="100%">
		<tr>
			<td>Header:</br><textarea name="offer_header" rows="4" cols="1"><?php echo $collectinv->getOffer_header()?></textarea></td>
			<td>Footer:</br><textarea name="offer_footer" rows="4" cols="1"><?php echo $collectinv->getOffer_footer()?></textarea></td>
		</tr>
	</table>
	</div>
	
 	<div id="tabs-1"> <!-- Auftragsbestätigung -->
	<table width="100%">
		<tr>
			<td>Header:</br><textarea name="offerconfirm_header" rows="4" cols="1"><?php echo $collectinv->getOfferconfirm_header()?></textarea></td>
			<td>Footer:<textarea name="offerconfirm_footer" rows="4" cols="1"><?php echo $collectinv->getOfferconfirm_footer()?></textarea></td>
		</tr>
	</table>
	</div>
	
 	<div id="tabs-2"> <!-- Auftragstasche -->
	<table width="100%">
		<tr>
			<td>Header:</br><textarea name="factory_header" rows="4" cols="1"><?php echo $collectinv->getFactory_header()?></textarea></td>
			<td>Footer:<textarea name="factory_footer" rows="4" cols="1"><?php echo $collectinv->getFactory_footer()?></textarea></td>
		</tr>
	</table>
	</div>
	
 	<div id="tabs-3"> <!-- Lieferschein -->
	<table width="100%">
		<tr>
			<td>Header:</br><textarea name="delivery_header" rows="4" cols="1"><?php echo $collectinv->getDelivery_header()?></textarea></td>
			<td>Footer:<textarea name="delivery_footer" rows="4" cols="1"><?php echo $collectinv->getDelivery_footer()?></textarea></td>
		</tr>
	</table>
	</div>
	
 	<div id="tabs-4"> <!-- Rechnung -->
	<table width="100%">
		<tr>
			<td>Header:</br><textarea name="invoice_header" rows="4" cols="1"><?php echo $collectinv->getInvoice_header()?></textarea></td>
			<td>Footer:<textarea name="invoice_footer" rows="4" cols="1"><?php echo $collectinv->getInvoice_footer()?></textarea></td>
		</tr>
	</table>
	</div>
	
 	<div id="tabs-5"> <!-- Gutschrift -->
	<table width="100%">
		<tr>
			<td>Header:</br><textarea name="revert_header" rows="4" cols="1"><?php echo $collectinv->getRevert_header()?></textarea></td>
			<td>Footer:<textarea name="revert_footer" rows="4" cols="1"><?php echo $collectinv->getRevert_footer()?></textarea></td>
		</tr>
	</table>
	</div>
	
    <input type="submit" value="<?=$_LANG->get('Speichern')?>" class="text">
	</form>
</div>
<h1><?=$_LANG->get('Dokumente')?></h1>
<div class="box2">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<colgroup>
	<col width="10%">
	<col width="25%">
	<col width="5%">
	<col width="10%">
	<col width="10%">
	<col width="15%">
</colgroup>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
    <td class="content_row_header"><?=$_LANG->get('Dokumentenname')?></td>
    <td class="content_row_header"><?=$_LANG->get('Versch.')?></td>
    <td class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
    <td class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
	<td class="content_row_header"><?=$_LANG->get('Dokumente')?></td>
</tr>

<? 
//---------------------------------------------------------------------------
// Angebot
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_OFFER, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>
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
				echo '<img src="images/status/green_small.svg">';
			else
				echo '<img src="images/status/red_small.svg">'; ?>
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
				<td width="30%">
					<ul class="postnav_text_del">
						<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
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
			<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&createDoc=offer"><?=$_LANG->get('Generieren')?></a>
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
// Angebotsbets�tigung
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_OFFERCONFIRM, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>

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
				echo '<img src="images/status/green_small.svg">';
			else
				echo '<img src="images/status/red_small.svg">'; ?>
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
						<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
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
			<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&createDoc=offerconfirm"><?=$_LANG->get('Generieren')?></a>
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
?>

<?php 
//---------------------------------------------------------------------------
// Auftragstasche
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_FACTORY, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Auftragstasche')?></b></td>
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
				echo '<img src="images/status/green_small.svg">';
			else
				echo '<img src="images/status/red_small.svg">'; ?>
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
						<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
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
			    <a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&createDoc=factory"><?=$_LANG->get('Generieren')?></a>
			</ul>
		</td>
		
	</tr>
<?php 

//---------------------------------------------------------------------------
// Lieferschein
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_DELIVERY, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
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
				echo '<img src="images/status/green_small.svg">';
			else
				echo '<img src="images/status/red_small.svg">'; ?>
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
						<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
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
			<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&createDoc=delivery"><?=$_LANG->get('Generieren')?></a>
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
$docs = Document::getDocuments(Array("type" => Document::TYPE_LABEL, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
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
				echo '<img src="images/status/green_small.svg">';
			else
				echo '<img src="images/status/red_small.svg">'; ?>
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
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
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
		<input type="text" class="text" name="label_box_amount" id="label_box_amount" style="width: 80px;" value="1">  
		
		&emsp; &emsp;
		<input type="checkbox" name="label_print_logo" id="label_print_logo" value="1"/>
		<?=$_LANG->get('Logo drucken');?>
		
		&emsp; &emsp;
		<?=$_LANG->get('Titel');?>:
		<input type="text" class="text" name="label_title" id="label_title" style="width: 280px;"
			value="<? echo $collectinv->getTitle();?>">
	</td>
	<td class="content_row_clear">
		<ul class="postnav_text_save">
			<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&createDoc=label" onclick="$(this).attr('href',$(this).attr('href')+'&label_box_amount='+$('#label_box_amount').val()+'&label_print_logo='+$('#label_print_logo').val()+'&label_title='+$('#label_title').val())"><?=$_LANG->get('Generieren')?></a>
		</ul>
	</td>
</tr>
<?php 
//---------------------------------------------------------------------------
// Rechnung
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_INVOICE, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
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
				echo '<img src="images/status/green_small.svg">';
			else
				echo '<img src="images/status/red_small.svg">'; ?>
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
						<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
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
			<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&createDoc=invoice"><?=$_LANG->get('Generieren')?></a>
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
// Gutschriften
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_REVERT, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER)); ?>
<tr class="<?=getRowColor(1)?>">
	<td class="content_row" colspan="6"><b><?=$_LANG->get('Gutschrift')?></b></td>
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
				echo '<img src="images/status/green_small.svg">';
			else
				echo '<img src="images/status/red_small.svg">'; ?>
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
						<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&deleteDoc=<?=$doc->getId()?>"><?=$_LANG->get('L&ouml;schen')?></a>
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
			<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=docs&createDoc=revert"><?=$_LANG->get('Generieren')?></a>
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
// Ende Dokumente
//---------------------------------------------------------------------------?>
<?

if(count($docs) > 0){
	foreach ($docs AS $doc){
    	if($doc->getSent() == 0){
        	$senddocs[] = $doc;
        }
	}
}
//---------------------------------------------------------------------------
// Ende Dokumente
//---------------------------------------------------------------------------
$nachricht->setAttachments($senddocs);?>

</table>
</div>
<br>
<table width="100%">
    <tr>
        <td>
        </td>
        <td align="center" width="200">
			<div class="btn-group" role="group">
				<button type="button" onclick="document.getElementById('sendmail').style.display='';" class="btn btn-sm btn-default">Mail verschicken</button>
			</div>
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
	<input type="hidden" name="exec" value="docs">
	<input type="hidden" name="subexec" value="send">
	<input type="hidden" name="id" value="<?=$collectinv->getId()?>">
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
				    // Falls Empf�nger Benutzer -> anzeigen
				    if (is_a($to, "User"))
				    {
				        $addStr = '<span class="newmailToField" id="touserfield_'.$to->getId().'"><img src="images/icons/user.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'user\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_touser_'.$to->getId().'" id="mail_touser_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empf�nger Gruppe -> anzeigen
				    if (is_a($to, "Group"))
				    {
				        $addStr = '<span class="newmailToField" id="togroupfield_'.$to->getId().'"><img src="images/icons/users.png" />&nbsp;'.$to->getName().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'group\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_togroup_'.$to->getId().'" id="mail_togroup_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empf�nger UserContact -> anzeigen
				    if (is_a($to, "UserContact"))
				    {
				        $addStr = '<span class="newmailToField" id="tousercontactfield_'.$to->getId().'"><img src="images/icons/card-address.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'usercontact\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_tousercontact_'.$to->getId().'" id="mail_tousercontact_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empf�nger BusinessContact -> anzeigen
				    if (is_a($to, "BusinessContact"))
				    {
				        $addStr = '<span class="newmailToField" id="tobusinesscontactfield_'.$to->getId().'"><img src="images/icons/building.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'businesscontact\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_tobusinesscontact_'.$to->getId().'" id="mail_tobusinesscontact_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empf�nger BusinessContact -> anzeigen
				    if (is_a($to, "ContactPerson"))
				    {
				        $addStr = '<span class="newmailToField" id="tocontactpersonfield_'.$to->getId().'"><img src="images/icons/user-business.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'contactperson\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_tocontactperson_'.$to->getId().'" id="mail_tocontactperson_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				}
				?> <a href="libs/modules/organizer/nachrichten.addrcpt.php"
					id="add_to"><img src="images/icons/plus-white.png"
						title="<?=$_LANG->get('Hinzuf&uuml;gen')?>"> </a>
				</td>
			</tr>
			<tr>
				<td><b><?=$_LANG->get('Betreff')?> </b></td>
				<td id="td_mail_subject" name="td_mail_subject"><input
					name="mail_subject" style="width: 635px"
					value="<? if ($nachricht->getSubject() == "" || $nachricht->getSubject() == null) echo 'Dokumente zum Auftrag '.$collectinv->getNumber();?>"></td>
			</tr>
			<tr>
			    <td><b><?=$_LANG->get('Anh&auml;nge')?> </b></td>
			    <td id="td_mail_attach" name="td_mail_attach">
			        <? 
			            foreach($nachricht->getAttachments() as $sd)
			            {
			                echo '<span class="newmailAttachmentField" id="attachfield_'.$sd->getId().'"><img src="images/icons/paper-clip.png">';
			                echo $sd->getName().' <img src="images/icons/cross-white.png" class="pointer" onclick="removeAttach('.$sd->getId().')">';
			                echo '<input type="hidden" name="mail_attach_'.$sd->getId().'" id="mail_attach_'.$sd->getId().'" value="1"></span>';
			            }
			        ?>
			    </td>
			<tr>
			    
			</tr>
		</table>
	</div>
	<div class="newmailBody">
		<textarea name="mail_body" style="height: 200px; width: 790px">
			<?=$nachricht->getText() ?>
		</textarea>
	</div>
	<input type="submit" value="<?=$_LANG->get('Abschicken')?>">
</form>
</div>