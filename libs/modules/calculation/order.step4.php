<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       02.12.2013
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$calculations = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);

$adj_tickets = Ticket::getTicketsForObject(get_class($order),$order->getId());

$all_user = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());

$machines = Array();
foreach ($calculations as $c){
    $me = Machineentry::getAllMachineentries($c->getId());
    
    foreach($me as $m){
        $machines[$m->getMachine()->getId()]["name"] = $m->getMachine()->getName();
        $machines[$m->getMachine()->getId()][$c->getId()]["price"] += $m->getPrice();
        $machines[$m->getMachine()->getId()][$c->getId()]["object"] = $m;
    }
}

?>
<script language="javascript">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#idx_delivery_date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    checkDate(selectedDate);
                }
			});

});

function updateCosts(val)
{
	$.post("libs/modules/calculation/order.ajax.php", {id: val, exec: 'getDeliveryCost'}, function(data) {
		// Work on returned data
		document.getElementById('delivery_cost').value = data;
	});
}

function showDetailedOverview()
{
	newwindow = window.open('libs/modules/calculation/showDetailed.php?order=<?=$order->getId()?>', "_blank", "width=1000,height=800,left=0,top=0,scrollbars=yes");
	newwindow = focus();
}

function showGoogleMaps()
{
	newwindow = window.open('libs/modules/businesscontact/businesscontact.maps.php?bcid=<?=$order->getCustomer()->getId()?>', "_blank", "width=1000,height=800,left=0,top=0,scrollbars=yes");
	newwindow = focus();
}

function ManualOverride(obj)
{
	calc_ele = obj.name;
	var id;
	id = calc_ele.match(/^man_override_(\d+)/)[1];
	var charge = document.getElementById('add_charge_'+id).value;
	charge = charge.replace(".","");
	charge = parseFloat(charge.replace(",","."));
	var endprice = document.getElementById('hidden_endprice_'+id).value;
	endprice = endprice.replace(".","");
	endprice = parseFloat(endprice.replace(",","."));
	var override = parseFloat(obj.value.replace(",","."));
	var add = "";
	var new_charge = 0;
	if (endprice > override) {
		add = "-";
		new_charge = endprice - override;
	} else {
		new_charge = override - endprice;
	}
	new_charge = new_charge.toString().replace(".",",");
	var output = add+new_charge;
	document.getElementById('add_charge_'+id).value = output;
}

$(document).ready(function() {
	$("a#idx_change_customer").fancybox({
	    'type'    : 'iframe'
	})
});
</script>

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
    	<td class="content_row_header"><?=$_LANG->get('Vorgang')?>:</td>
        <td class="content_row_clear"><?=$order->getNumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Kundennummer')?>:</td>
        <td class="content_row_clear" id="td_customer_number"><?=$order->getCustomer()->getCustomernumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Telefon')?></td>
        <td class="content_row_clear" id="td_customer_phone"><?=$order->getCustomer()->getPhone()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top">
        	<?=$_LANG->get('Name')?>: <br>
        	<a href="libs/modules/calculation/order.change_customer.fancy.php?order_id=<?=$order->getId()?>&exec=changecustomer" id="idx_change_customer" class="icon-link">
        		<img src="images/icons/arrow-circle-double-135.png"
        		 	 alt="<?=$_LANG->get('Kunde &auml;ndern');?>" title="<?=$_LANG->get('Kunde &auml;ndern');?>">
        	</a>
       	</td>
        <td class="content_row_clear" valign="top" id="td_customer_name"><?=nl2br($order->getCustomer()->getNameAsLine())?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Adresse')?>:</td>
        <td class="content_row_clear"  valign="top" id="td_customer_adress"><?=nl2br($order->getCustomer()->getAddressAsLine())?></td>
        <td class="content_row_header"  valign="top"><?=$_LANG->get('E-Mail')?></td>
        <td class="content_row_clear" valign="top"id="td_customer_mail"><?=$order->getCustomer()->getEmail()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Produkt')?>:</td>
        <td class="content_row_clear" valign="top"><?=$order->getProduct()->getName()?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?>:</td>
        <td class="content_row_clear" valign="top"><?=$order->getProduct()->getDescription()?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Google Maps')?>:</td>
        <td class="content_row_clear" valign="top">
            <a href="#" onclick="showGoogleMaps()"><b>Karte</b></a>
        </td>
    </tr>
</table>
</div>
<br>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="step4_form">
<input name="step" value="4" type="hidden">
<input name="exec" value="edit" type="hidden">
<input name="subexec" value="save" type="hidden">
<input name="id" value="<?=$order->getId()?>" type="hidden">

<div class="box1">
<table border="0" cellspacing="0" cellpadding="0" >
<colgroup>
    <col width="210">
    <col>
    <col width="210">
    <col>        
</colgroup>
<tr>
    <td class="content_row_header" valign="top"><?=$_LANG->get('Titel')?> </td>
    <td class="content_row_clear" valign="top" colspan="3">
        <input name="order_title" value="<?=$order->getTitle()?>" style="width:870px" class="text">
    </td>
</tr>
<tr>
    <td class="content_row" valign="top"><b><?=$_LANG->get('Lieferadresse')?></b></td>
    <td class="content_row" valign="top">
       <select name="delivery_address" style="width:320px" class="text">
           <option value=""><?=$_LANG->get('an Standardadresse')?></option>
           <? 
               foreach($order->getCustomer()->getDeliveryAddresses() as $deliv)
               {
                   echo '<option value="'.$deliv->getId().'" ';
                   if($order->getDeliveryAddress()->getId() == $deliv->getId()) echo "selected";
                   echo '>'.$deliv->getNameAsLine().', '.$deliv->getAddressAsLine().'</option>';
               }
           ?>
       </select>
    </td>
    <td class="content_row" valign="top"><b><?=$_LANG->get('Rechnungsadresse')?></b></td>
    <td class="content_row" valign="top">
       <select name="invoice_address" style="width:330px" class="text">
           <option value=""><?=$_LANG->get('keine gesonderte Rechnungsadresse')?></option>
          <? 
               foreach($order->getCustomer()->getInvoiceAddresses() as $invc)
               {
                   echo '<option value="'.$invc->getId().'" ';
                   if($order->getInvoiceAddress()->getId() == $invc->getId()) echo "selected";
                   echo '>'.$invc->getNameAsLine().', '.$invc->getAddressAsLine().'</option>';
               }
           ?>
       </select>
    </td>
</tr>
<tr>
    <td class="content_row"><b><?=$_LANG->get('Lieferbedingung')?></b></td>
    <td class="content_row">
        <select name="delivery_terms" style="width:260px" class="text" onchange="updateCosts(this.value)">
            <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
            <? 
            foreach(DeliveryTerms::getAllDeliveryConditions(DeliveryTerms::ORDER_NAME) as $dt)
            {
                echo '<option value="'.$dt->getId().'" ';
                if($order->getDeliveryTerms()->getId() == $dt->getId()) echo "selected";
                echo '>'.$dt->getName1().'</option>';
            }
            ?>
        </select>    
        <input name="delivery_cost" id="delivery_cost" value="<?=printPrice($order->getDeliveryCost())?>" style="width:40px">
        <?=$_USER->getClient()->getCurrency()?>
    </td>
    <td class="content_row"><b><?=$_LANG->get('Zahlungsbedingung')?></b></td>
    <td class="content_row">
        <select name="payment_terms" style="width:330px" class="text">
            <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
            <? 
            foreach(PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $dt)
            {
                echo '<option value="'.$dt->getId().'" ';
                if($order->getPaymentTerms()->getId() == $dt->getId()) echo "selected";
                echo '>'.$dt->getName1().'</option>';
            }
            ?>
        </select>    
    </td>
</tr>
<tr>
    <td class="content_row" valign="top"><b><?=$_LANG->get('Voraussichtliches Lieferdatum')?></b></td>
    <td class="content_row" valign="top">
        <input name="delivery_date" id="idx_delivery_date" style="width:100px"
            value="<? if($order->getDeliveryDate() > 0) echo date('d.m.Y', $order->getDeliveryDate())?>">
    </td>
    <td class="content_row" valign="top" rowspan="3"><b><?=$_LANG->get('Bemerkungen (intern)')?></b></td>
    <td class="content_row" rowspan="3">
        <textarea name="order_notes" style="width:330px;height:60px" class="text"><?=$order->getNotes()?></textarea>
    </td>
</tr>
<tr>
    <td class="content_row" valign="top"><b><?=$_LANG->get('Ansprechpartner')?></b></td>
    <td class="content_row" valign="top">
        <select name="intern_contactperson" style="width:320px" class="text">
            <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
            <? 
            foreach($all_user as $us)
            {
                echo '<option value="'.$us->getId().'" ';
                if($order->getInternContact()->getId() == $us->getId()) echo "selected";
                echo '>'.$us->getNameAsLine().'</option>';
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td class="content_row" valign="top"><b><?=$_LANG->get('Ansprechpartner des Kunden')?></b></td>
    <td class="content_row" valign="top">
        <select name="cust_contactperson" id="cust_contactperson" style="width:320px" class="text">
            <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
            <? 
            foreach($order->getCustomer()->getContactpersons() as $cp)
            {
                echo '<option value="'.$cp->getId().'" ';
                if($order->getCustContactperson()->getId() == $cp->getId()) echo "selected";
                echo '>'.$cp->getNameAsLine().'</option>';
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td class="content_row"><b><?=$_LANG->get('Status')?></b></td>
    <td class="content_row">
        <table border="0" cellpadding="1" cellspacing="0">
        <tr>
            <td width="25">
                <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=4&setStatus=1">
                    <? 
                    echo '<img class="select" src="./images/status/';
                    if($order->getStatus() == 1)
                        echo 'red.gif';
                    else
                        echo 'black.gif';
                    echo '">';
                    ?>
                </a>
            </td>
            <td width="25">
                <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=4&setStatus=2">
                    <? 
                    echo '<img class="select" src="./images/status/';
                    if($order->getStatus() == 2)
                        echo 'orange.gif';
                    else
                        echo 'black.gif';
                    echo '">';
                    ?>
                </a>
            </td>
            <td width="25">
                <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=4&setStatus=3">
                    <? 
                    echo '<img class="select" src="./images/status/';
                    if($order->getStatus() == 3)
                        echo 'yellow.gif';
                    else
                        echo 'black.gif';
                    echo '">';
                    ?>
                </a>
            </td>
            <td width="25">
                <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=4&setStatus=4">
                    <? 
                    echo '<img class="select" src="./images/status/';
                    if($order->getStatus() == 4)
                        echo 'lila.gif';
                    else
                        echo 'black.gif';
                    echo '">';
                    ?>
                </a>
            </td>
            <td width="25">
                <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=4&setStatus=5">
                    <? 
                    echo '<img class="select" src="./images/status/';
                    if($order->getStatus() == 5)
                        echo 'green.gif';
                    else
                        echo 'black.gif';
                    echo '">';
                    ?>
                </a>
            </td>
            <td>
                &nbsp;<?=getOrderStatus($order->getStatus(), true)?>
            </td>
        </tr>
        </table> 
    </td>
    <td class="content_row" valign="top"><b><?=$_LANG->get('Dokumente')?> - <?=$_LANG->get('Ihre Nachricht')?></b></td>
    <td class="content_row" valign="top">
        <input name="cust_message" id="cust_message" style="width:330px"
            value="<?=$order->getCustMessage()?>">
    </td>
</tr>
<tr>
	<td class="content_row" valign="top"><b><?=$_LANG->get('Verkn. Tickets')?></b></td>
	<td class="content_row" valign="top">
	<? if(count($adj_tickets) > 0){ ?>
		<?	foreach ($adj_tickets AS $tkt){ ?>
			<a href="index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$tkt->getId()?>"><?=$tkt->getNumber()?></a> &ensp; 
		<?	} ?>
	<? } ?>&nbsp;
	</td>
	<td class="content_row" valign="top"><b><?=$_LANG->get('Dokumente')?> - <?=$_LANG->get('Ihr Zeichen')?></b></td>
    <td class="content_row" valign="top">
        <input name="cust_sign" id="cust_sign" style="width:330px"
            value="<?=$order->getCustSign()?>">
    </td>
</tr>
<?php 
// Beilagen Export
if (strlen($order->getBeilagen()) > 0){
    if (strpos($order->getBeilagen(),"\r\n") === false){
        $filename1 = './docs/'.$_USER->getId().'-Beilagen.csv';
        $csv_file = fopen($filename1, "w");
        $csv_string = $order->getBeilagen();
        $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
        fwrite($csv_file, $csv_string);
        fclose($csv_file);
    } else {
        $filename1 = './docs/'.$_USER->getId().'-Beilagen.csv';
        $csv_file = fopen($filename1, "w");
        $csv_string = "";
        foreach(explode("\r\n", $order->getBeilagen()) as $line) {
            $csv_string .= $line . ";\n";
        }
        $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
        fwrite($csv_file, $csv_string);
        fclose($csv_file);
    }
}




?>
<tr>
    <td class="content_row" valign="top" colspan="2" rowspawn="3"></td>
    <td class="content_row" valign="top" rowspan="3"><b><?=$_LANG->get('Beilagen-Hinweis')?> (<a href="./docs/<?=$_USER->getId()?>-Beilagen.csv">export</a>)</b></td>
    <td class="content_row" rowspan="3">
        <textarea name="order_beilagen" style="width:330px;height:60px" class="text"><?=$order->getBeilagen()?></textarea>
    </td>
</tr>

<?if ($order->getCollectiveinvoiceId() > 0){
$collective = new CollectiveInvoice($order->getCollectiveinvoiceId());?>
<tr>
	<td class="content_row" valign="top"><b><?=$_LANG->get('Teil der Sammelrechnung')?></b></td>
	<td class="content_row" valign="top"><?=$collective->getNumber();?></td>
</tr>
<?}// Ende if(Teil einer Sammelrechnung)?>
</table>
</div>
<br>

<table width="100%">
<tr>
    <td class="content_row_header" align="left">
        <? if($_USER->hasRightsByGroup(Group::RIGHT_DETAILED_CALCULATION)) { ?>
            <img src="images/icons/clipboard-list.png">
            <a href="#" onclick="showDetailedOverview()"><?=$_LANG->get('Detailierte &Uuml;bersicht anzeigen')?></a>
        <? } ?>        
    </td>
    <td class="content_row_header" align="right">

        <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=export&id=<?=$order->getId()?>" target="_blank"><img src="images/icons/application-export.png"> <?=$_LANG->get('Exportieren')?></a>
    </td>
    <td class="content_row_header" align="right">

        <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$order->getId()?>&step=2"><img src="images/icons/plus.png"> <?=$_LANG->get('Neue Kalkulation anlegen')?></a>
    </td>
</tr>
</table>

<!-- Kalkulationskopf -->
<div class="box2">
<table cellpadding="0" cellspacing="0">
    <colgroup>
        <col width="180">
        <? for ($i = 0; $i < count($calculations); $i++)
            echo '<col width="200">'; ?>
    </colgroup>
    <tr>
    <td class="content_row_clear">&nbsp;</td>
    <? $x = 1; foreach($calculations as $calc) { ?>
    <td class="content_row_clear" align="center">
        <b><?=$_LANG->get('Kalkulation')?> # <?=$x?></b>
    </td>
    <?  $x++;} ?>
</tr>
    <tr>
    <td class="content_row_clear">&nbsp;</td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value" align="center">
        <img src="images/icons/pencil.png" class="pointer icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?>&exec=edit&step=2'">
        <img src="images/icons/scripts.png" class="pointer icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?>&exec=edit&subexec=copy&step=2'">
        
    	<? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_ORDER) || $_USER->isAdmin()){ ?>
        		<a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$order->getId()?>&subexec=delete&calc_id=<?=$calc->getId()?>&step=4')"><img src="images/icons/cross-script.png"></a>
        <?}?>
    </td>
    <? } ?>
</tr>
<!-- added by ascherer -->
<tr class="color1">
    <td class="content_row_header"><?=$_LANG->get('Titel')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
		<input type="text" name="calc_title_<?=$calc->getId()?>" id="calc_title_<?=$calc->getId()?>" value="<?=$calc->getTitle()?>">
    </td>
    <? } ?>
</tr>
<!-- end added by ascherer -->
<tr class="color1">
    <td class="content_row_header"><?=$_LANG->get('Format')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <?=$calc->getProductFormatWidth()?> x <?=$calc->getProductFormatHeight()?> <?=$_LANG->get('mm')?>
        <?  if($calc->getProductFormat()) echo "(".$calc->getProductFormat()->getName().")";?>
    </td>
    <? } ?>
</tr>
<tr class="color1">
    <td class="content_row_header"><?=$_LANG->get('Auflage')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value"><?=printBigInt($calc->getAmount()/$calc->getSorts())?></td>
    <? } ?>
</tr>
<tr class="color1">
    <td class="content_row_header"><?=$_LANG->get('Sorten')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value"><?=printBigInt($calc->getSorts())?></td>
    <? } ?>
</tr>
<!-- / Kalkulationskopf -->
<tr>
    <td class="content_row_clear">&nbsp;</td>
</tr>

<!-- ---------------------------------------------------------------------- -->
<!-- Materialkosten                                                         -->

<? foreach($calculations as $calc) {
    if ($calc->getPagesContent())
        $hasContent = true;
    if ($calc->getPagesAddContent())
        $hasAddContent = true;
    if ($calc->getPagesEnvelope())
        $hasEnvelope = true;
    if ($calc->getPagesAddContent2())
    	$hasAddContent2 = true;
    if ($calc->getPagesAddContent3())
    	$hasAddContent3 = true;
}?>

<tr>
    <td class="content_row_header"><?=$_LANG->get('Materialkosten')?></td>

</tr>

<? if ($hasContent) {?>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('Papier Inhalt')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <? if($calc->getPaperContent()->getId() > 0) 
         echo $calc->getPaperContent()->getName().', '.$calc->getPaperContent()->getSelectedWeight().' '.$_LANG->get('g').'';?>
         &nbsp;
    </td>
    <? }?>
</tr>

<tr class="color1">
    <td class="content_row_clear">&nbsp;</td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <?=printBigInt($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant())?> <?=$_LANG->get('B&ouml;gen')?>
        <?=printPrice($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()))?>
        <?=$_USER->getClient()->getCurrency()?>
    </td>
    <? } ?>
</tr>

<tr class="color1">
    <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <input name="grant_content_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperContentGrant())?>"> <?=$_LANG->get('B&ouml;gen')?>
    </td>
    <? } ?>
</tr>

<? } ?>

<? if ($hasAddContent) { ?>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('Papier zus. Inhalt')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <?  if($calc->getPaperAddContent()->getId() > 0) { ?>
        <?=$calc->getPaperAddContent()->getName()?>, <?=$calc->getPaperAddContent()->getSelectedWeight()?> <?=$_LANG->get('g')?>
        <?  } ?>&nbsp;
    </td>
    <? }?>
</tr>

<tr class="color1">
    <td class="content_row_clear">&nbsp;</td>
    
        <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value">
            <?=printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant())?> <?=$_LANG->get('B&ouml;gen')?>
            <?=printPrice($calc->getPaperAddContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant()))?>
            <?=$_USER->getClient()->getCurrency()?>
        </td>
        <?  } ?>
    </td>
</tr>

<tr class="color1">
    <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <input name="grant_addcontent_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperAddContentGrant())?>"> <?=$_LANG->get('B&ouml;gen')?>
    </td>
    <? } ?>
</tr>
<? } ?>

<? if ($hasAddContent2) { ?>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('Papier zus. Inhalt 2')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <?  if($calc->getPaperAddContent2()->getId() > 0) { ?>
        <?=$calc->getPaperAddContent2()->getName()?>, <?=$calc->getPaperAddContent2()->getSelectedWeight()?> <?=$_LANG->get('g')?>
        <?  } ?>&nbsp;
    </td>
    <? }?>
</tr>

<tr class="color1">
    <td class="content_row_clear">&nbsp;</td>
    <? foreach($calculations as $calc) { ?>
    	<td class="content_row_clear value">
    		<?=printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant())?> <?=$_LANG->get('B&ouml;gen')?>
            <?=printPrice($calc->getPaperAddContent2()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant()))?>
            <?=$_USER->getClient()->getCurrency()?>
        </td>
	<? } ?>
</tr>

<tr class="color1">
    <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
    <? foreach($calculations as $calc) { ?>
	    <td class="content_row_clear value">
	        <input name="grant_addcontent2_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperAddContent2Grant())?>"> 
	        <?=$_LANG->get('B&ouml;gen')?>
	    </td>
    <? } ?>
</tr>
<? } ?>

<? if ($hasAddContent3) { ?>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('Papier zus. Inhalt 3')?></td>
    <? foreach($calculations as $calc) { ?>
	    <td class="content_row_clear value">
	        <?  if($calc->getPaperAddContent3()->getId() > 0) { ?>
	        <?=$calc->getPaperAddContent3()->getName()?>, <?=$calc->getPaperAddContent3()->getSelectedWeight()?> <?=$_LANG->get('g')?>
	        <?  } ?>&nbsp;
	    </td>
    <? } ?>
</tr>

<tr class="color1">
    <td class="content_row_clear">&nbsp;</td>
    <? foreach($calculations as $calc) { ?>
	    <td class="content_row_clear value">
	    	<?=printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant())?> <?=$_LANG->get('B&ouml;gen')?>
	        <?=printPrice($calc->getPaperAddContent3()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant()))?>
	        <?=$_USER->getClient()->getCurrency()?>
		</td>
    <?  } ?>
</tr>

<tr class="color1">
    <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
    <? foreach($calculations as $calc) { ?>
	    <td class="content_row_clear value">
	        <input name="grant_addcontent3_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperAddContent3Grant())?>"> 
	        <?=$_LANG->get('B&ouml;gen')?>
	    </td>
    <? } ?>
</tr>
<? } ?>

<? if ($hasEnvelope) { ?>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('Papier Umschlag')?></td>
        <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <?  if($calc->getPaperEnvelope()->getId()) { ?>
        <?=$calc->getPaperEnvelope()->getName()?>, <?=$calc->getPaperEnvelope()->getSelectedWeight()?> <?=$_LANG->get('g')?>
        <?  } ?>&nbsp;
    </td>
    <? }?>
</tr>

<tr class="color1">
    <td class="content_row_clear">&nbsp;</td>
    
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <?=printBigInt($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant())?> <?=$_LANG->get('B&ouml;gen')?>
        <?=printPrice($calc->getPaperEnvelope()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant()))?>
        <?=$_USER->getClient()->getCurrency()?>
    </td>
    <? } ?>
</tr>

<tr class="color1">
    <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_clear value">
        <input name="grant_envelope_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperEnvelopeGrant())?>"> <?=$_LANG->get('B&ouml;gen')?>
    </td>
    <? } ?>
</tr>
<? } ?>
<!-- / Materialkosten                                                       -->
<!-- ---------------------------------------------------------------------- -->

<tr>
    <td class="content_row_clear">&nbsp;</td>
</tr>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Fertigungsprozess')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value" align="center">
            <img src="images/icons/pencil.png" class="pointer icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?>&exec=edit&step=3'">
        </td>
    <?  } ?>
</tr>


<!-- ---------------------------------------------------------------------- -->
<!-- Fertigungsprozess                                                      -->
<? foreach ($machines as $m) { ?>
<tr class="color1">
    <td class="content_row_clear"><?=$m["name"]?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value" align="center">
        	<?=printPrice($m[$calc->getId()]["price"])?> <?=$_USER->getClient()->getCurrency()?></b>
        <?	// Weitere Details bei Fremdleistungen ausweisen
        
        	$mach = $m[$calc->getId()]["object"];
        	      	 
        	if($mach != NULL && $mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
				$tmp_supp = new BusinessContact($mach->getSupplierID());
				if($mach->getSupplierStatus() == 0){ $status = Machineentry::SUPPLIER_STATUS_0; $img="red.gif";};
				if($mach->getSupplierStatus() == 1){ $status = Machineentry::SUPPLIER_STATUS_1; $img="orange.gif";};
				if($mach->getSupplierStatus() == 2){ $status = Machineentry::SUPPLIER_STATUS_2; $img="lila.gif";};
				if($mach->getSupplierStatus() == 3){ $status = Machineentry::SUPPLIER_STATUS_3; $img="green.gif";};
				
				$title = $status." \n";
				if ($mach->getSupplierID() > 0 ) $title .= $tmp_supp->getNameAsLine()."\n";
				if ($mach->getSupplierSendDate() > 0 ) $title .= "Liefer-/Bestelldatum: ".date("d.m.Y", $mach->getSupplierSendDate())." \n";
				if ($mach->getSupplierReceiveDate() > 0 ) $title .= "Retour: ".date("d.m.Y", $mach->getSupplierReceiveDate())." \n";
				$title .= $mach->getSupplierInfo()." \n";
				?>
				<br>
				<img src="images/status/<?=$img?>" alt="<?=$status?>" title="<?=$title?>" >
        <?	} ?>
        </td>
    <?  } ?>
</tr>
<? } ?>
<!-- / Fertigungsprozess                                                    -->
<!-- ---------------------------------------------------------------------- -->

<tr>
    <td class="content_row_clear">&nbsp;</td>
</tr>

<?/*** /---------START----------------Artikelkosten----------------------------?>
<tr>
	<td class="content_row_header"><?=$_LANG->get('Zus. Artikel')?></td>
</tr> 
<tr class="color1">
<td class="content_row_clear">&ensp;</td>
<? foreach($calculations as $calc) {
	$all_calc_article = $calc->getArticles();?>
	<td class="content_row_clear value" align="center">
	<?if (count($all_calc_article = $calc->getArticles()) > 0){
		foreach ($all_calc_article as $calc_art){
			$tmpart_amount = $calc->getArticleamount($calc_art->getId());
			$tmpart_scale = $calc->getArticlescale($calc_art->getId());
			echo $calc_art->getTitle() ." : ";
			if ($tmpart_scale == 0){
				echo printPrice($tmpart_amount * $calc_art->getPrice($tmpart_amount));
			} elseif ($tmpart_scale == 1){
				echo printPrice($tmpart_amount * $calc_art->getPrice($tmpart_amount * $calc->getAmount()) * $calc->getAmount());
			}
			echo " ".$_USER->getClient()->getCurrency()."<br/>";
		}?>
	<?} else {
		echo printPrice(0)." ".$_USER->getClient()->getCurrency();
	}?>
	</td>
<?}?>
</tr>
<?//----------ENDE----------------Artikelkosten---------------------------- ***/?>

<? // -------- START --------------- Positionskosten ---------------------------- ?>
<tr>
	<td class="content_row_header"><?=$_LANG->get('Zus. Positionen')?></td>
</tr> 
<tr class="color1">
<td class="content_row_clear">&ensp;</td>
<? foreach($calculations as $calc) {
	$all_calc_positions = $calc->getPositions()?>
	<td class="content_row_clear value" align="center">
	<?if (count($all_calc_positions) > 0 && $all_calc_positions != false){
		foreach ($all_calc_positions AS $calc_pos){
			$tmpart_amount = $calc_pos->getQuantity();	
			$tmpart_scale = $calc_pos->getScale();		
			
			echo $calc_pos->getComment() ." : ";
			
			if($calc_pos->getType() == CalculationPosition::TYPE_ARTICLE){
				// falls Position ein Artikel ist
				$tmp_art =  new Article($calc_pos->getObjectID()); 
				if ($tmpart_scale == CalculationPosition::SCALE_PER_KALKULATION){
					echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount));
				} elseif ($tmpart_scale == CalculationPosition::SCALE_PER_PIECE){
					echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount * $calc->getAmount()) * $calc->getAmount());
				}
			} else {
				// falls Position manuell ist
				if ($tmpart_scale == CalculationPosition::SCALE_PER_KALKULATION){
					echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount));
				} elseif ($tmpart_scale == CalculationPosition::SCALE_PER_PIECE){
					echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount) * $calc->getAmount());
				}
			}
			echo " ".$_USER->getClient()->getCurrency()."<br/>";
		}?>
	<?} else {
		echo printPrice(0)." ".$_USER->getClient()->getCurrency();
	}?>
	</td>
<?}?>
</tr>
<? // -------- ENDE ---------------- Positionskosten ---------------------------- ?>

<tr>
    <td class="content_row_clear">&nbsp;</td>
</tr>

<? if($_USER->hasRightsByGroup(Group::RIGHT_DETAILED_CALCULATION)) { ?>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Zwischensumme')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_header value" align="center"><?=printPrice($calc->getSubTotal())?> <?=$_USER->getClient()->getCurrency()?></td>
    <? } ?>
</tr>
<tr>
    <td class="content_row_clear">&nbsp;</td>
</tr>

<tr>
    <td class="content_row_header"><?=$_LANG->get('Zusatzkosten')?></td>
</tr>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('Marge')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value">
            <input name="margin_<?=$calc->getId()?>" style="width:60px;text-align:center" value="<?=printPrice($calc->getMargin())?>"> %
        </td>
    <?  } ?>
</tr>
<tr>
    <td class="content_row_clear">&nbsp;</td>
</tr>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Zwischensumme')?></td>
    <? foreach($calculations as $calc) { ?>
    <td class="content_row_header value" align="center"><?=printPrice($calc->getSubTotal() + ($calc->getSubTotal() / 100 * $calc->getMargin()))?> <?=$_USER->getClient()->getCurrency()?></td>
    <? } ?>
</tr>
<tr>
    <td class="content_row_clear">&nbsp;</td>
</tr>
<? } ?>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Preiskorrekturen')?></td>
</tr>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('Rabatt')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value">
            <input name="discount_<?=$calc->getId()?>" style="width:60px;text-align:center" value="<?=printPrice($calc->getDiscount())?>"> %
        </td>
    <?  } ?>
</tr>
<tr class="color1">
    <td class="content_row_clear"><?=$_LANG->get('sonst. Auf-/Abschlag')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value">
            <input name="add_charge_<?=$calc->getId()?>" id="add_charge_<?=$calc->getId()?>" style="width:60px;text-align:center" value="<?=printPrice($calc->getAddCharge())?>">
            <?=$_USER->getClient()->getCurrency()?>
        </td>
    <?  } ?>
</tr>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Endsumme')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_header value">
            <? if($calc->getSummaryPrice() < $calc->getSubTotal()) echo '<span class="error">';?>
            <?=printPrice($calc->getSummaryPrice())?> <?=$_USER->getClient()->getCurrency()?>
            <? if($calc->getSummaryPrice() < $calc->getSubTotal()) echo '</span>';?>
			<input name="hidden_endprice_<?=$calc->getId()?>" id="hidden_endprice_<?=$calc->getId()?>" type="hidden" value="<?=printPrice($calc->getSummaryPrice())?>">
        </td>
    <? } ?>
</tr>

<tr>
    <td class="content_row_header"><?=$_LANG->get('Man. Endpreis')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_header value">
			<input name="man_override_<?=$calc->getId()?>" id="man_override_<?=$calc->getId()?>" onchange='ManualOverride(this)' style="width:60px;text-align:center" value="<?=printPrice($calc->getSummaryPrice())?>">
				<?=$_USER->getClient()->getCurrency()?>
        </td>
    <? } ?>
</tr>

<tr>
    <td class="content_row_clear"><?=$_LANG->get('St&uuml;ckpreis')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value"><?=printPrice($calc->getSummaryPrice() / $calc->getAmount())?> <?=$_USER->getClient()->getCurrency()?></td>
    <? } ?>
</tr>

<tr>
    <td class="content_row_clear"><?=$_LANG->get('Kalkulation beauftragt')?></td>
    <? foreach($calculations as $calc) { ?>
        <td class="content_row_clear value"><input type="checkbox" name="state_<?=$calc->getId()?>" value="1" <?if($calc->getState()) echo "checked"?>></td>
    <? } ?>
</tr>

</table>
</div>
<? echo $savemsg; ?>
<br>
<div>
	<table width="100%">
	    <colgroup>
	        <col width="50%">
	        <col width="50%">
	        <col>
	    </colgroup> 
	    <tr>
	        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="center">
	        		<input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	        <td class="content_row_clear" align="right">
	        	<? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_ORDER) || $_USER->isAdmin()){ ?>
		        		<input type="button" class="buttonRed" onclick="askDel('index.php?page=libs/modules/calculation/order.php&exec=delete&id=<?php echo $order->getId();?>')" 
		        				value="<?=$_LANG->get('L&ouml;schen')?>">
		        <?}?>
	        </td>
	    </tr>
	</table>
</div>
</form>