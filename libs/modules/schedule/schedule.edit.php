<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/timekeeping/timekeeper.class.php';
require_once('libs/modules/documents/document.class.php');

$sched = new Schedule((int)$_REQUEST["id"]);

$customers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);
$delivery = DeliveryTerms::getAllDeliveryConditions();

//gln
$order = new Order($sched->getDruckplanId());
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
    
    if($_REQUEST["createDoc"] == "delivery"){
    	$doc->setType(Document::TYPE_DELIVERY);
    	$order->setDeliveryAmount((int)$_REQUEST["delivery_amount"]);
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
    if($_REQUEST["createDoc"] == "label"){
        $doc->createDoc(Document::VERSION_PRINT, false, false);
    } else {
        $hash = $doc->createDoc(Document::VERSION_EMAIL);
        $doc->createDoc(Document::VERSION_PRINT, $hash);
    }
    $doc->save();
}

if($_REQUEST["subexec"] == "save")
{
    $_REQUEST["job_delivery_date"]       = explode(".", $_REQUEST["job_delivery_date"]);
    $_REQUEST["job_delivery_date"]       = (int)mktime(0, 0, 0, $_REQUEST["job_delivery_date"][1], $_REQUEST["job_delivery_date"][0], $_REQUEST["job_delivery_date"][2]);
    
    
    $sched->setNumber(trim(addslashes($_REQUEST["job_number"])));
    $sched->setCustomer(new BusinessContact((int)$_REQUEST["job_customer_id"]));
    $sched->setObject(trim(addslashes($_REQUEST["job_object"])));
    $sched->setDeliveryDate($_REQUEST["job_delivery_date"]);
    $sched->setDeliveryLocation(trim(addslashes($_REQUEST["job_delivery_location"])));
    $sched->setDeliveryterms(new DeliveryTerms((int)$_REQUEST["job_deliveryterms_id"]));
    $sched->setNotes(trim(addslashes($_REQUEST["job_notes"])));
    $savemsg = getSaveMessage($sched->save()).$DB->getLastError();
    
}

?>

<script language="javascript">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['de']);
	$('#job_delivery_date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
});
</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?if ($_REQUEST["exec"] == "edit") echo ($_LANG->get('Auftrag &auml;ndern')); else echo ($_LANG->get('Auftrag hinzuf&uuml;gen'));?></td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>


<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="sched_form" 
 onsubmit="return checkform(new Array(this.job_number, this.job_customer_id, this.job_object,
     this.job_delivery_date, this.job_delivery_location,
 this.job_deliveryterms_id))">
<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=(int)$_REQUEST["id"]?>">

<table>
<tr>
	<td width="750px;">
		<div class="box1" style="min-height:290px;">
		<table border="0" class="content_table" cellpadding="3" cellspacing="0" width="100%">
		<colgroup>
		   <col width="130">
		   <col>
		</colgroup>
		<tr>
		   <td class="content_row_header" colspan="2"><?=$_LANG->get('Auftragsdaten')?></td>
		</tr>
		<tr>
		   <td class="content_row_clear"><?=$_LANG->get('Auftragsnummer')?> *</td>
		   <td class="content_row_clear">
		      <input id="job_number"  name="job_number" type="text" class="text" style="width:130px" value="<?=$sched->getNumber()?>"
		      onfocus="markfield(this,0)" onblur="markfield(this,1)">
		   </td>
		</tr>
		<tr>
		   <td class="content_row_clear" valign="top"><?=$_LANG->get('Kunde')?> *</td>
		   <td class="content_row_clear">
		      <select class="text" id="job_customer_id" name="job_customer_id" style="width:400px"
		      onfocus="markfield(this,0)" onblur="markfield(this,1)">
		         <option value="">&lt; <?=$_LANG->get('Bitte ausw&auml;hlen')?> &gt;</option>
		         <?
		         foreach($customers as $c)
		         {  ?>
		            <option value="<?=$c->getId()?>" <? if($c->getId() == $sched->getCustomer()->getId()) echo "selected"?>><?=$c->getNameAsLine()?></option>
		            <?
		         }
		         ?>
		      </select>
		   </td>
		</tr>
		<tr>
		   <td class="content_row_clear"><?=$_LANG->get('Titel')?> *</td>
		   <td class="content_row_clear">
		      <input id="job_object" name="job_object" type="text" class="text" style="width:400px" value="<?=$sched->getObject()?>"
		      onfocus="markfield(this,0)" onblur="markfield(this,1)">
		   </td>
		</tr>
		<tr>
		   <td class="content_row_clear"><?=$_LANG->get('Liefertermin')?> *</td>
		   <td class="content_row_clear">
		      <input id="job_delivery_date" name="job_delivery_date" id="job_delivery_date" type="text" style="width:112px" 
		      value="<? if($sched->getDeliveryDate() > 0) echo date('d.m.Y', $sched->getDeliveryDate()); else echo date('d.m.Y');?>" class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
		   </td>
		</tr>
		<tr>
		   <td class="content_row_clear"><?=$_LANG->get('Lieferort')?> *</td>
		   <td class="content_row_clear">
		      <input id="job_delivery_location" name="job_delivery_location" type="text" class="text" style="width:400px" value="<?=$sched->getDeliveryLocation()?>"
		      onfocus="markfield(this,0)" onblur="markfield(this,1)">
		   </td>
		</tr>
		<tr>
		   <td class="content_row_clear" valign="top"><?=$_LANG->get('Versandart')?> *</td>
		   <td class="content_row_clear">
		      <select class="text" id="job_deliveryterms_id" name="job_deliveryterms_id" style="width:130px"
		      onfocus="markfield(this,0)" onblur="markfield(this,1)">
		         <option value="">&lt; <?=$_LANG->get('Bitte ausw&auml;hlen')?> &gt;</option>
		         <?
		            foreach($delivery as $d){
		                echo '<option value="'.$d->getId().'" ';
		                if($d->getId() == $sched->getDeliveryterms()->getId()) echo "selected";
		                echo '>'.$d->getName1().'</option>';
		            }
		         ?>
		      </select>
		   </td>
		<tr>
		<tr>
		   <td class="content_row_clear" valign="top"><?=$_LANG->get('Bemerkungen')?></td>
		   <td class="content_row_clear">
		      <textarea id="job_notes" name="job_notes" type="text" class="text" style="width:400px;height:100px"
		      onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$sched->getNotes()?></textarea>
		   </td>
		</tr>
		</table>
		</div>
	</td>
	<td width="5px;">&emsp;</td>
	<td>
		<?if($sched->getId() != 0){ ?>
			<div class="box2" style="min-height:290px;padding-left:16px;">
				<div style="overflow: auto; height:275px;">
				<?	$timer_moduleID = Timekeeper::MODULE_PLANNING;
					$timer_objectID = $sched->getId();
					// $div_height = "260px";
					require_once 'libs/modules/timekeeping/timekeeper.import.php';?>
				</div>
			</div>
		<?}?>
	</td>
</tr>


<tr>
	<td>
		<br>
		<div>
			<table width="100%">
				<tr>
					<td align="left">
    					<input type="button" class="button" onclick="document.location='index.php'" value="<?=$_LANG->get('Zur&uuml;ck')?>">
    				</td>
    				<td align="right">
    					<input type="submit" value="<?=$_LANG->get('Speichern')?>">
    				</td>
    			</tr>
    		</table>
		</div>
	</td>
	<td colspan="2">&ensp;</td>
</table>
</form>

<?/*gln       D O K U M E N T E           */?>
<link rel="stylesheet" href="css/documents.css" type="text/css">
<h1 style="display:<? if($sched->getDruckplanId()==0) echo 'none'?>"><?=$_LANG->get('Dokumente')?></h1>

<? // Form fuer die Eingabefelder der Dokumente?>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="form_create_document" id="form_create_document" style="display:<? if($sched->getDruckplanId()==0) echo 'none'?>">
<input type="hidden" name="id"value="<?=$sched->getId()?>">
<?/*<input type="hidden" name="step" value="6">*/?>
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


//$nachricht->setAttachments($senddocs);
?>

</table>
</div>
</form>
