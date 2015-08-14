<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       18.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/attachment/attachment.class.php';



$all_user = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());


//Falls eine neue manuelle Rechnung erzeugt wird
if($collectinv->getId()==0){
	
	//ausgew?hlten Benutzer aus der DB holen und setzen
	$selected_customer = new BusinessContact((int)$_REQUEST["order_customer"]);
	$collectinv->setBusinesscontact($selected_customer);
	$tmp_presel_cp = new ContactPerson((int)$_REQUEST["order_contactperson"]);
	$collectinv->setCustContactperson($tmp_presel_cp);
	//Datum und Benutzer setzen, wer erstellt hat
	$collectinv->setCrtuser($_USER);
	$collectinv->setCrtdate(time());
	$all_bc_cp = ContactPerson::getAllContactPersons($collectinv->getBusinesscontact());
}else{//Falls eine bestehende Rechnung veraendert werden soll
	$selected_customer = new BusinessContact($collectinv->getBusinesscontact()->getId());
}

// Alle Zahlungsarten holen
$allpaymentterms = PaymentTerms::getAllPaymentTerms();

// Alle Versandoptionen holen
$alldeliverycondition = DeliveryTerms::getAllDeliveryConditions();

// Lieferaddressen des Geschaeftskontakts holen
$all_deliveryadress = Address::getAllAddresses($selected_customer, Address::ORDER_NAME, Address::FILTER_DELIV);
$all_invoiceadress = Address::getAllAddresses($selected_customer, Address::ORDER_NAME, Address::FILTER_INVC);


if (!empty($_REQUEST['subexec']) && $_REQUEST['subexec']){
    if ($_REQUEST['subexec'] == "movedown"){
        $i = 0;
        foreach($collectinv->getPositions() as $position){
            if ($position->getId() == $_REQUEST['posid']){
                $tmp_index = $i;
                break; 
            }
            $i++;
        }
        $all_positions = $collectinv->getPositions();
        
        $tmp_old_id1 = $all_positions[$tmp_index]->getId();
        $tmp_old_id2 = $all_positions[$tmp_index+1]->getId();
        
        $all_positions[$tmp_index]->setId($tmp_old_id2);
        $all_positions[$tmp_index+1]->setId($tmp_old_id1);
        
        Orderposition::saveMultipleOrderpositions($all_positions);
        
//         echo $all_positions[$tmp_index]->getId() . " wird zu " . $all_positions[$tmp_index+1]->getId() . "</br>";
//         echo $all_positions[$tmp_index+1]->getId() . " wird zu " . $all_positions[$tmp_index]->getId() . "</br>";
    } else if ($_REQUEST['subexec'] == "moveup"){
        $i = 0;
        foreach($collectinv->getPositions() as $position){
            if ($position->getId() == $_REQUEST['posid']){
                $tmp_index = $i;
                break;
            }
            $i++;
        }
        $all_positions = $collectinv->getPositions();
        
        $tmp_old_id1 = $all_positions[$tmp_index]->getId();
        $tmp_old_id2 = $all_positions[$tmp_index-1]->getId();
        
        $all_positions[$tmp_index]->setId($tmp_old_id2);
        $all_positions[$tmp_index-1]->setId($tmp_old_id1);
        
        Orderposition::saveMultipleOrderpositions($all_positions);
    }
} // &exec=edit&subexec=movedown&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">


//----------------------------------- Javascript---------------------------------------?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script type="text/javascript">
$(function() {
	$('#colinv_deliverydate').datetimepicker({
		 lang:'de',
		 i18n:{
		  de:{
		   months:[
		    'Januar','Februar','März','April',
		    'Mai','Juni','Juli','August',
		    'September','Oktober','November','Dezember',
		   ],
		   dayOfWeek:[
		    "So.", "Mo", "Di", "Mi", 
		    "Do", "Fr", "Sa.",
		   ]
		  }
		 },
		 timepicker:false,
		 format:'d.m.Y'
	});
});


function printPriceJs(zahl){
    //var ret = (Math.round(zahl * 100) / 100).toString(); //100 = 2 Nachkommastellen
    ret = zahl.toFixed(2);
    ret = ret.replace(".",",");
    return ret;
}


function updatePos(id_i){
	var tmp_type= document.getElementById('orderpos_type_'+id_i).value;

	if(tmp_type > 0){
		document.getElementById('orderpos_search_'+id_i).style.display= '';
		document.getElementById('orderpos_searchbutton_'+id_i).style.display= '';
		document.getElementById('orderpos_searchlist_'+id_i).style.display = '';
		document.getElementById('orderpos_uptpricebutton_'+id_i).style.display = 'none';
		if (tmp_type == 2){
			document.getElementById('orderpos_uptpricebutton_'+id_i).style.display = '';
		}
	} else {
		document.getElementById('orderpos_search_'+id_i).style.display= 'none';
		document.getElementById('orderpos_searchbutton_'+id_i).style.display= 'none';
		document.getElementById('orderpos_searchlist_'+id_i).style.display = 'none';
		document.getElementById('orderpos_uptpricebutton_'+id_i).style.display = 'none';
		document.getElementById('orderpos_quantity_'+id_i).value = "";
		document.getElementById('orderpos_comment_'+id_i).value = "";
		document.getElementById('orderpos_price_'+id_i).value = "";
	}
}

function clickSearch(id_i){
	var tmp_type= document.getElementById('orderpos_type_'+id_i).value;
	var str = document.getElementById('orderpos_search_'+id_i).value;
	
	$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
		{exec: 'searchPositions', type : tmp_type, str : str, cust_id : <?=$selected_customer->getId()?>}, 
		 function(data) {
			document.getElementById('orderpos_searchlist_'+id_i).innerHTML = data;
			document.getElementById('orderpos_searchlist_'+id_i).style.display = "";
		});
}

function updatePosDetails(id_i){
	var tmp_type = document.getElementById('orderpos_type_'+id_i).value;
	var tmp_objid= document.getElementById('orderpos_searchlist_'+id_i).value;

	if(tmp_type == 1){
		$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
			{exec: 'getOrderDetails', orderid: tmp_objid}, 
			 function(data) {
				var teile = data.split("-+-+-");
				document.getElementById('orderpos_objid_'+id_i).value = teile[0];
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
				document.getElementById('orderpos_comment_'+id_i).value = teile[3];
				document.getElementById('orderpos_comment_'+id_i).style.height = 250;
				document.getElementById('orderpos_quantity_'+id_i).value = "1";
				document.getElementById('td_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
		});  
	}
	if(tmp_type == 2){
		$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
			{exec: 'getArticleDetails', articleid: tmp_objid}, 
			 function(data) {
				var teile = data.split("-+-+-");
				document.getElementById('orderpos_objid_'+id_i).value = teile[0];
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
				document.getElementById('orderpos_comment_'+id_i).value = teile[3];
				document.getElementById('orderpos_comment_'+id_i).style.height = 100;
				document.getElementById('orderpos_quantity_'+id_i).value = "1";
				document.getElementById('td_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
		}); 
	}
	if(tmp_type == 3){
		$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
			{exec: 'getPersonalizationDetails', persoid: tmp_objid}, 
			 function(data) {
				var teile = data.split("-+-+-");
				document.getElementById('orderpos_objid_'+id_i).value = teile[0];
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
				document.getElementById('orderpos_comment_'+id_i).value = teile[3];
				document.getElementById('orderpos_comment_'+id_i).style.height = 100;
				document.getElementById('orderpos_quantity_'+id_i).value = "1";
				document.getElementById('td_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
		}); 
	}
}

function updateArticlePrice(id_i){
	var tmp_objid= document.getElementById('orderpos_searchlist_'+id_i).value;
	var amount = document.getElementById('orderpos_quantity_'+id_i).value;
	
	$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
		{exec: 'getArticlePrice', articleid: tmp_objid, amount: amount}, 
		 function(data) {
			document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(data));
			document.getElementById('td_totalprice_'+id_i).innerHTML = printPriceJs(parseFloat(data) * amount)+" <?=$_USER->getClient()->getCurrency()?>";
	}); 
}

function updateDeliveryPrice(){
	var del_id = document.getElementsByName('colinv_deliveryterm')[0].value;
	
	$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
		{exec: 'getDeliveryPrice', delivid: del_id}, 
		 function(data) {
			document.getElementById('colinv_deliverycosts').value = data;
	}); 
}

function addPositionRow(){
	var count = parseInt($('#poscount').val())+1;
    var newrow = "";
    newrow += '<tr class="">';
    newrow += '<td valign="top" class="content_row">';
    newrow += '<select name="orderpos['+count+'][type]" class="text" id="orderpos_type_'+count+'" onchange="updatePos('+count+')">';
    newrow += '<option value="0" >Manuell</option>';
    newrow += '<option value="1" >Auftrag</option>';
    newrow += '<option value="2" >Artikel</option>';
    newrow += '<option value="3" >Personalisierung</option></select>';
    newrow += '<input type="hidden" name="orderpos['+count+'][id]" value="0">';
	newrow += '<input type="hidden" name="orderpos['+count+'][obj_id]" id="orderpos_objid_'+count+'"value=""></td>';
	newrow += '<td valign="top" class="content_row" ><input type="text" value="" id="orderpos_search_'+count+'"';
	newrow += 'style="width: 50px; display:none;" class="text"><br/>&ensp;&ensp;';
	newrow += '<img src="images/icons/magnifier-left.png" class="pointer" style="border:0; display:none;" title="Suchen" onclick="clickSearch('+count+')"'; 
	newrow += 'id="orderpos_searchbutton_'+count+'"></td><td valign="top" class="content_row">';
	newrow += '<select style="width: 440px; display:none;" class="text" id="orderpos_searchlist_'+count+'" onchange="updatePosDetails('+count+')" name="orderpos['+count+'][obj_id]">'; 
	newrow += '<option value="0"> &lt; Suchergebnisse... &gt;</option></select>';
	newrow += '<textarea name="orderpos['+count+'][comment]" id="orderpos_comment_'+count+'" style="width: 440px; height: 100px" ></textarea>';
	newrow += '</td><td valign="top" class="content_row">';
	newrow += '<input 	name="orderpos['+count+'][quantity]" id="orderpos_quantity_'+count+'" value="" style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
    newrow += 'Stk.<br/> &ensp;&ensp;&ensp;';
    newrow += '<img src="images/icons/arrow-circle-double-135.png" class="pointer" id="orderpos_uptpricebutton_'+count+'"';
	newrow += 'onclick="updateArticlePrice('+count+')" style="display:none" title="Staffelpreis aktualisieren">';
	newrow += '</td><td valign="top" class="content_row"><input 	name="orderpos['+count+'][price]" id="orderpos_price_'+count+'" value=""'; 
	newrow += 'style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
    newrow += '<?= $_USER->getClient()->getCurrency()?></td><td valign="top" class="content_row">';
	newrow += '<input 	name="orderpos['+count+'][tax]" id="orderpos_tax_'+count+'" value="19,00" style="width: 60px"'; 
	newrow += 'onfocus="markfield(this,0)" onblur="markfield(this,1)"> %</td>';
	newrow += '<td valign="top" class="content_row" id="td_totalprice_'+count+'">&ensp;</td>';
	newrow += '<td valign="top" class="content_row">';
	newrow += '<input type="checkbox" checked value="1" name="orderpos['+count+'][inv_rel]">';				
	newrow += '</td><td class="content_row">&ensp;</td></tr>';
    $('#poscount').val(count);
    $('#sort').append(newrow);
}
</script>


<?//--------------------------------------HTML ----------------------------------------?>
<?if ($collectinv->getId() >0){

	$all_bc_cp = ContactPerson::getAllContactPersons($collectinv->getBusinesscontact());?>
<div class="menuorder">
	<span class="menu_order" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&exec=docs&ciid=<?=$collectinv->getId()?>'"><?= $_LANG->get('Dokumente')?></span>
	<?php 
    // Associations
    $association_object = $collectinv;
    include 'libs/modules/associations/association.include.php';
    //-> END Associations
    ?>
</div>
<?}?>	
<div class="box1" <?if ($collectinv->getId() >0){?>style="margin-top:50px;"<?}?>>
	<table width="100%">
		<tr>
			<td  class="content_row_header">
				<?= $_LANG->get('Kundendaten')?>
			</td>
			<td align="right">
			<? echo $savemsg; ?>
			</td>
		</tr>
	</table>
	<table width'="100%">
		<colgroup>
			<col width="180">
			<col width="350">
			<col width="180">
			<col width="300">
		<colgroup>
		<tr>
			<td class="content_row_header">
				<?= $_LANG->get('Firmenname') ?>
			</td>
			<td class="content_row_clear">
				<a href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$selected_customer->getId()?>" target="_blank"><?= $selected_customer->getNameAsLine()?></a>			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('E-Mail') ?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getEmail()?>
			</td>
		</tr>
		<tr>
			<td class="content_row_header">
				<?= $_LANG->get('Vor-/Nachname') ?>
			</td>
			<td class="content_row_clear">			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Telefon') ?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getPhone()?>
			</td>
		</tr><tr>
			<td class="content_row_header">
				<?= $_LANG->get('Strasse')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getAddress1()?>			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Fax')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getFax()?>
			</td>
		</tr><tr>
			<td class="content_row_header">
				<?= $_LANG->get('Postleitzahl/Ort')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getZip()." ".$selected_customer->getCity()?>
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Webseite')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getWeb()?>
			</td>
		</tr><tr>
			<td class="content_row_header">
				<?= $_LANG->get('Land')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getCountry()->getName()?>			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Bemerkungen')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getComment() ?>
			</td>
		</tr>
	</table>
</div>
<br/>
	
<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#form_collectiveinvoices').submit();">Speichern</a>
        <? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_COLINV) || $_USER->isAdmin()){ ?>
            <a href="#" class="menu_item_delete" onclick="askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=delete&del_id=<?php echo $collectinv->getId();?>')">Löschen</a>
        <?}?>
    </div>
</div>
	
<?//--------------------------Beginn Formular----------------------------?>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="form_collectiveinvoices" name="form_collectiveinvoices" onsubmit="return checkform(new Array(colinv_title))">
	
	<input 	type="hidden" name="exec" value="save">
	<input 	type="hidden" name="ciid" value="<?=$collectinv->getId()?>">


	<input 	type="hidden" name="colinv_businesscontact"  value="<?=$collectinv->getBusinesscontact()->getId()?>">  

<div class="box1">
	<table width="100%">
		<tr>
			<td class="content_row_header">
			<?= $_LANG->get('Auftragskopfdaten') ?>
			</td>
		</tr>
	</table>
	<table width'="100%">
		<colgroup>
			<col width="180">
			<col width="350">
			<col width="180">
			<col width="300">
		<colgroup>
		<tr>
			<td class="content_header"><?= $_LANG->get('Auftragstitel')?></td>
			<td class="content_row_clear" colspan="3">
				<input name="colinv_title" style="width: 850px" class="text" 
				value="<?= $collectinv->getTitle()?>" 
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row"><?=$_LANG->get('Auftragsnummer')?></td>
			<td class="content_row"><?=$collectinv->getNumber()?></td>
			<td class="content_row"><?=$_LANG->get('Erstellt am')?></td>
			<td class="content_row">
				<?if ($collectinv->getCrtdate() != 0) echo date("d.m.Y H:i:s",$collectinv->getCrtdate())?>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Status')?></td>
			<td class="content_row">
				<table>
					<tr>
						<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?= $collectinv->getId() ?>&exec=setState2&state=1">
	            				<img class="select" title="Vorgang angelegt" src="./images/status/<?
	            				if($collectinv->getStatus() == 1)
	            	    				echo 'red.gif';
					            	else
										echo 'black.gif'; ?>">
	                		</a>
	                	</td>
	                	<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=2">
	            				<img class="select" title="Vorgang gesendet" src="./images/status/<?
	            					if($collectinv->getStatus() == 2)
					                	echo 'orange.gif';
					                else
					                	echo 'black.gif';?>">
							</a>
						</td>
						<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=3">
								<img class="select" title="Vorgang angenommen" src="./images/status/<?
									if($collectinv->getStatus() == 3)
					                	echo 'yellow.gif';
					                else
					                	echo 'black.gif'; ?>">
							</a>
						</td>
						<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=4">
	            				<img class="select" title="Erledigt" src="./images/status/<?
	            					if($collectinv->getStatus() == 4)
	            						echo 'lila.gif';
									else
										echo 'black.gif';?>">
							</a>
					</tr>
				</table>
			</td>
			<td class="content_row"><?= $_LANG->get('Erstellt von')?></td>
			<td class="content_row">
				<?if ($collectinv->getCrtuser()->getId() != 0){
					echo $collectinv->getCrtuser()->getNameAsLine();
				}?>
			</td>
		</tr>
		<tr>
			<td class="content_row">&ensp;</td>
			<td class="content_row">&ensp;</td>
			<td class="content_row">
				<?= $_LANG->get('Ge&auml;ndert am')?>
			</td>
			<td class="content_row">
				<?if ($collectinv->getUptdate() != 0) echo date("d.m.Y H:i:s",$collectinv->getUptdate())?>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Zahlungsart')?></td>
			<td class="content_row" colspawn="7">
				<select name="colinv_paymentterm" style="width: 300px" class="text" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<option value="0"> &lt; <?=$_LANG->get('Bitte w&auml;hlen') ?> &gt;</option>
				<?	foreach($allpaymentterms as $payterm){
						echo '<option value="'. $payterm->getId() . '"';
						if($payterm->getId() == $collectinv->getPaymentTerm()->getId())
						{ 
						    echo ' selected="selected"'; 
						} else if ($collectinv->getId() <= 0 && $payterm->getId() == $collectinv->getBusinesscontact()->getPaymentTerms()->getId())
						{
						    echo ' selected="selected"'; 
						}

						echo ">".$payterm->getName1() . "</option>";
					} ?>
				</select>
			</td> 
			<td class="content_row"><?=$_LANG->get('Ge&auml;ndert von')?></td>
			<td class="content_row">
				<?if ($collectinv->getUptuser()->getId()){
					echo $collectinv->getUptuser()->getNameAsLine();
				}?>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Lieferadresse')?></td>
			<td class="content_row" colspawn="7">
				<select name="colinv_deliveryadress" style="width: 300px" class="text" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?	foreach($all_deliveryadress as $adress){
				        if ($collectinv->getId() == 0)
				        {
				            echo '<option value="'. $adress->getId() . '"';
				            if($adress->getDefault()){ echo ' selected="selected"'; }
				            echo ">".$adress->getAddressAsLine() . "</option>";
				        } else {
    						echo '<option value="'. $adress->getId() . '"';
    						if($adress->getId() == $collectinv->getDeliveryaddress()->getId()){ echo ' selected="selected"'; }
    						echo ">".$adress->getAddressAsLine() . "</option>";
				        }
					} ?>
				</select>
			</td> 
			<td class="content_row" valign="top">
				<?=$_LANG->get('Lieferdatum')?>
			</td>
			<td class="content_row"  valign="top">
				<input 	name="colinv_deliverydate" id="colinv_deliverydate" style="width: 80px" class="text" 
						value="<?php if ($collectinv->getDeliverydate()>0) echo date('d.m.Y',$collectinv->getDeliverydate()); else echo date('d.m.Y');?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
            <td class="content_row" valign="top"><b><?=$_LANG->get('Rechnungsadresse')?></b></td>
            <td class="content_row" valign="top">
               <select name="invoice_address" style="width:300px" class="text">
                  <? 
                       foreach($all_invoiceadress as $invc)
                       {
    				       if ($collectinv->getId() == 0)
    				       {
                               echo '<option value="'.$invc->getId().'" ';
                               if($invc->getDefault()) echo "selected";
                               echo '>'.$invc->getNameAsLine().', '.$invc->getAddressAsLine().'</option>';
    				       } else {
                               echo '<option value="'.$invc->getId().'" ';
                               if($collectinv->getInvoiceAddress()->getId() == $invc->getId()) echo "selected";
                               echo '>'.$invc->getNameAsLine().', '.$invc->getAddressAsLine().'</option>';
    				       }
                       }
                   ?>
               </select>
            </td>
			<td class="content_row" valign="top" rowspan="2">
				<?= $_LANG->get('Bemerkungen (intern)')?>
			</td>
			<td class="content_row"  rowspan="2">
				<textarea name="colinv_comment" style="width: 300px; height: 40px"
						  onfocus="markfield(this,0)" onblur="markfield(this,1)"><?= $collectinv->getComment()?></textarea>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?=$_LANG->get('Versandart')?></td>
			<td class="content_row" colspawn="7">
				<select name="colinv_deliveryterm" style="width: 300px" class="text" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)"
						onchange="updateDeliveryPrice()">
						<option value="0"> &lt; <?=$_LANG->get('Bitte w&auml;hlen') ?> 	&gt;</option>
					<?foreach($alldeliverycondition as $delcon){
							echo '<option value="' . $delcon->getId() . '"';
							if ($delcon->getId() == $collectinv->getDeliveryTerm()->getID()){ echo 'selected="selected" ';} 
							echo ">".$delcon->getName1() . "</option>";
						}?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Versandkosten')?></td>
			<td class="content_row">
				<input 	name="colinv_deliverycosts" id="colinv_deliverycosts" style="width: 80px" class="text" 
						value="<?= printPrice($collectinv->getDeliveryCosts())?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<?= $_USER->getClient()->getCurrency()?>
			</td>
			<td class="content_row" valign="top" rowspan="2">
				<?= $_LANG->get('Bemerkungen (extern)')?>
			</td>
			<td class="content_row"  rowspan="2">
				<textarea name="colinv_extcomment" style="width: 300px; height: 40px"
						  onfocus="markfield(this,0)" onblur="markfield(this,1)"><?= $collectinv->getExt_comment()?></textarea>
			</td>
		</tr>
		<tr>
			<td class="content_row" valign="top">
				<?= $_LANG->get('Zweck')?> / <?= $_LANG->get('Kostenstelle')?> <br/> <?= $_LANG->get('des Kunden')?>
			</td>
			<td class="content_row"  valign="top">
				<input 	name="colinv_intent" id="colinv_intent" style="width: 300px" class="text" 

						value="<?=$collectinv->getIntent()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>    
		    <td class="content_row" valign="top"><b><?=$_LANG->get('Dokumente')?> - <?=$_LANG->get('Ihre Nachricht')?></b></td>
            <td class="content_row" valign="top">
                <input name="cust_message" id="cust_message" style="width:300px"
                    value="<?=$collectinv->getCustMessage()?>">
            </td>
			<td class="content_row" valign="top">
				<?=$_LANG->get('Benötigt Planung')?>
			</td>
			<td class="content_row"  valign="top">
				<?php if ($collectinv->getNeeds_planning()) { echo 'Ja'; } else { echo 'Nein'; }?>
			</td>
        </tr>
        <tr>
        	<td class="content_row" valign="top"><b><?=$_LANG->get('Dokumente')?> - <?=$_LANG->get('Ihr Zeichen')?></b></td>
            <td class="content_row" valign="top">
                <input name="cust_sign" id="cust_sign" style="width:300px"
                    value="<?=$collectinv->getCustSign()?>">
            </td>
        </tr>
        <tr>
            <td class="content_row" valign="top"><b><?=$_LANG->get('Ansprechpartner')?></b></td>
            <td class="content_row" valign="top">
                <select name="intern_contactperson" style="width:300px" class="text">
                    <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
                    <? 
                    foreach($all_user as $us)
                    {
                        if ($collectinv->getId() == 0)
                        {
                            echo '<option value="'.$us->getId().'" ';
                            if($_USER->getId() == $us->getId()) echo "selected";
                            echo '>'.$us->getNameAsLine().'</option>';
                        } else {
                            echo '<option value="'.$us->getId().'" ';
                            if($collectinv->getInternContact()->getId() == $us->getId()) echo "selected";
                            echo '>'.$us->getNameAsLine().'</option>';
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="content_row" valign="top"><b><?=$_LANG->get('Ansprechp. d. Kunden')?></b></td>
            <td class="content_row" valign="top">
                <select name="custContactperson" style="width:300px" class="text">
                    <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
                    <? 
                    foreach($all_bc_cp as $cp)
                    {
                        echo '<option value="'.$cp->getId().'" ';
                        if($collectinv->getCustContactperson()->getId() == $cp->getId()) echo "selected";
                        echo '>'.$cp->getNameAsLine().'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
	</table>
</div>
<br/>

<div class="box2">
	<table width="100%">
		<tr>
			<td class="content_row_header">
			<?= $_LANG->get('Auftragspositionen') ?> <img class="pointer" src="images/icons/plus.png" onclick="addPositionRow();"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing="0" cellpadding="0" id="sort">
		<colgroup>
			<col width="50px"> 
			<col width="100px">
            <col>
			<col width="180px">
			<col width="150px">
			<col width="150px">
			<col width="80px">
			<col width="40px">

			<col width="70px">
		<colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Position')?></td>
			<td class="content_row_header"><?=$_LANG->get('Suche')?></td>
			<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
			<td class="content_row_header"><?=$_LANG->get('Menge')?></td>
			<td class="content_row_header"><?=$_LANG->get('Preis')?></td>
			<td class="content_row_header"><?=$_LANG->get('Steuer')?></td>
			<td class="content_row_header"><?=$_LANG->get('Betrag')?></td>
			<td class="content_row_header"><?=$_LANG->get('RE-rel.')?></td>

			<td class="content_row_header">&ensp;</td>
		</tr>
		<? $i = 0;
		$all_orders 	= Order::searchOrderByTitleNumber("", $selected_customer->getId());
		$all_article	= Article::getAllArticle();
		$all_persos 	= Personalizationorder::getAllPersonalizationorders($selected_customer->getId(), Personalizationorder::ORDER_CRTDATE, true); 
		if(count($collectinv->getPositions())>0){
			foreach($collectinv->getPositions() as $position){?>
				<tr class="<?= getRowColor($i)?>">
					<td valign="top" class="content_row"> 
						<select name="orderpos[<?=$i?>][type]" class="text" id="orderpos_type_<?=$i?>" 
								onchange="updatePos(<?=$i?>)">
							<option value="0" <?if($position->getType() == 0) echo "selected";?>><?=$_LANG->get('Manuell')?></option>
							<option value="1" <?if($position->getType() == 1) echo "selected";?>><?=$_LANG->get('Auftrag')?></option>
							<option value="2" <?if($position->getType() == 2) echo "selected";?>><?=$_LANG->get('Artikel')?></option>
							<option value="3" <?if($position->getType() == 3) echo "selected";?>><?=$_LANG->get('Personalisierung')?></option>
						</select>
						<input type="hidden" name="orderpos[<?=$i?>][id]" value="<?= $position->getId()?>">
						<input type="hidden" name="orderpos[<?=$i?>][obj_id]" id="orderpos_objid_<?=$i?>" 
								value="<?= $position->getObjectid()?>">
					</td>
					<td valign="top" class="content_row">
						<input 	type="text" name="orderpos[<?=$i?>][search]" value="" id="orderpos_search_<?=$i?>"
								 class="text" style="width: 50px;
								<?if($position->getType() == 0) echo 'display:none;';?>">
						<br/>&ensp;&ensp;
						<img src="images/icons/magnifier-left.png" class="pointer" id="orderpos_searchbutton_<?=$i?>"
							 onclick="clickSearch(<?=$i?>)" <?if($position->getType() == 0) echo 'style="display:none"';?>>
					</td>
					<td valign="top" class="content_row">
						<select style="width: 440px; <?if($position->getType() == 0) echo 'display:none;';?>" 
								class="text" id="orderpos_searchlist_<?=$i?>" 
								onchange="updatePosDetails(<?=$i?>)"> 
							<?
							if($position->getType() == 1){ //Auftraege auflisten
								echo '<option value=""> &lt; '.$_LANG->get('Auftrag w&auml;hlen...').'&gt;</option>';
								foreach ($all_orders as $order) {
									echo '<option value="'. $order->getId() .'"';
									if ($order->getId() == $position->getObjectid()) echo "selected";
									echo '>'. $order->getNumber() ." - ". $order->getTitle() .'</option>';
								}
							}
							if($position->getType() == 2){ 
								echo '<option value=""> &lt; '.$_LANG->get('Artikel w&auml;hlen...').'&gt;</option>';
								foreach ($all_article as $article) {
									echo '<option value="'. $article->getId() .'"';
									if ($article->getId() == $position->getObjectid()) echo "selected";
									echo'>'. $article->getNumber()." - ".$article->getTitle() .'</option>';
								}

							}
							if($position->getType() == 3){
								echo '<option value=""> &lt; '.$_LANG->get('Bitte w&auml;hlen...').'&gt;</option>';
								foreach ($all_persos as $perso) {
									echo '<option value="'. $perso->getId() .'"';
									if ($perso->getId() == $position->getObjectid()) echo "selected";
									echo'>'. $perso->getTitle().'</option>';
								}
							}
							?>
						</select>
						<textarea name="orderpos[<?=$i?>][comment]" class="text" id="orderpos_comment_<?=$i?>"
								  style="width: 440px; height: 100px"><?=$position->getComment()?></textarea>
					</td>
					<td valign="top" class="content_row">
						<input 	name="orderpos[<?=$i?>][quantity]" id="orderpos_quantity_<?=$i?>"
								value="<?php echo printPrice($position->getQuantity(),2);?>" class="text" style="width: 60px" 
								onfocus="markfield(this,0)" onblur="markfield(this,1)"">
						<?=$_LANG->get('Stk.')?> <br/>
						&ensp;&ensp;&ensp;
						<img src="images/icons/arrow-circle-double-135.png" class="pointer" id="orderpos_uptpricebutton_<?=$i?>"
							 onclick="updateArticlePrice(<?=$i?>)" title="<?=$_LANG->get('Staffelpreis aktualisieren')?>"
							 <?if($position->getType() != 2) echo 'style="display:none"';?>>
					</td>
					<td valign="top" class="content_row">
						<input 	name="orderpos[<?=$i?>][price]" id="orderpos_price_<?=$i?>" class="text"
								value="<?= printPrice($position->getPrice())?>" style="width: 60px" 
								onfocus="markfield(this,0)" onblur="markfield(this,1)"> 
						<?=$_USER->getClient()->getCurrency()?>
					</td>
					<td valign="top" class="content_row">
						<input 	name="orderpos[<?=$i?>][tax]" class="text" id="orderpos_tax_<?=$i?>"
								value="<?= printPrice($position->getTax())?>" style="width: 60px;" > %
					</td>
					<td valign="top" class="content_row" id="td_totalprice_<?=$i?>">
						<?= printPrice($position->getNetto())." ". $_USER->getClient()->getCurrency()?>
					</td>
					<td valign="top" class="content_row">
						<input type="checkbox" value="1" name="orderpos[<?=$i?>][inv_rel]"
						<?if ($position->getInvrel() == 1) echo "checked";?>>
					</td>
					<td valign="top" class="content_row">




						<a href="index.php?page=<?=$_REQUEST['page']?>&exec=deletepos&ciid=<?=$_REQUEST["ciid"]?>&delpos=<?=$position->getId()?>">
							<img src="images/icons/cross-script.png" title="<?= $_LANG->get('Position l�schen')?>"></a>
						<? 
						if ($i == 0){
						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=movedown&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                  <img src="images/icons/arrow-270.png" title="nach unten bewegen"></a>';
						} else if ($i+1 >= count($collectinv->getPositions())){
						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=moveup&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                  <img src="images/icons/arrow-090.png" title="nach oben bewegen"></a>';
			            } else {
						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=movedown&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                  <img src="images/icons/arrow-270.png" title="nach unten bewegen"></a>';
						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=moveup&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                  <img src="images/icons/arrow-090.png" title="nach oben bewegen"></a>';
			            }
			            if ($position->getFile_attach()>0){
			                $tmp_attach = new Attachment($position->getFile_attach());
			                echo '<a href="'.Attachment::FILE_DESTINATION.$tmp_attach->getFilename().'" download="'.$tmp_attach->getOrig_filename().'">
                                  <img src="images/icons/disk--arrow.png" title="Angehängte Datei herunterladen"></a>';
			            } elseif ($position->getPerso_order()>0){
			                echo '</br>';
			                $perso_order = new Personalizationorder($position->getPerso_order());
			                $docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER,
			                                                     "requestId" => $perso_order->getId(),
			                                                     "module" => Document::REQ_MODULE_PERSONALIZATION));
			                if (count($docs) > 0)
			                {
			                    $tmp_id = $_USER->getClient()->getId();
			                    $hash = $docs[0]->getHash();
    			                echo '<a class="icon-link" target="_blank" href="./docs/personalization/'.$tmp_id.'.per_'.$hash.'_e.pdf">
                                      <img src="images/icons/application-browser.png" title="Download mit Hintergrund" alt="Download"></a>
    			                      <a href="./docs/personalization/'.$tmp_id.'.per_'.$hash.'_p.pdf" class="icon-link" target="_blank">
    			                      <img src="images/icons/application.png" title="Download ohne Hintergrund" alt="Download"></a></br>';
			                }
			            }
						    
						?>
					</td>
				</tr>
			<?$i++;
			} //ende FOREACH
		}?>
			<tr class="<?= getRowColor($i)?>">
				<td valign="top" class="content_row">
					<select name="orderpos[<?=$i?>][type]" class="text" id="orderpos_type_<?=$i?>" 
							onchange="updatePos(<?=$i?>)">
						<option value="0" ><?=$_LANG->get('Manuell')?></option>
						<option value="1" ><?=$_LANG->get('Auftrag')?></option>
						<option value="2" ><?=$_LANG->get('Artikel')?></option>
						<option value="3" ><?=$_LANG->get('Personalisierung')?></option>
					</select>
					<input type="hidden" name="orderpos[<?=$i?>][id]" value="0">
					<input type="hidden" name="orderpos[<?=$i?>][obj_id]" id="orderpos_objid_<?=$i?>"value="">
				</td>
				<td valign="top" class="content_row" >
					<input 	type="text" value="" id="orderpos_search_<?=$i?>"
							style="width: 50px; display:none;" class="text">
					<br/>&ensp;&ensp;
					<img 	src="images/icons/magnifier-left.png" class="pointer" style="border:0; display:none;"
							title="<?=$_LANG->get('Suchen')?>" onclick="clickSearch(<?=$i?>)" 
						 	id="orderpos_searchbutton_<?=$i?>">
				</td>
				<td valign="top" class="content_row">
					<select style="width: 440px; display:none;" class="text" id="orderpos_searchlist_<?=$i?>"
							onchange="updatePosDetails(<?=$i?>)" name="orderpos[<?=$i?>][obj_id]"> 
						<option value="0"> &lt; <?=$_LANG->get('Suchergebnisse...') ?> 	&gt;</option>
					</select>
					<textarea name="orderpos[<?=$i?>][comment]" id="orderpos_comment_<?=$i?>"
								style="width: 440px; height: 100px" ></textarea>
				</td>
				<td valign="top" class="content_row">
					<input 	name="orderpos[<?=$i?>][quantity]" id="orderpos_quantity_<?=$i?>" value="" 
							style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<?=$_LANG->get('Stk.')?><br/> 
					&ensp;&ensp;&ensp;
					<img src="images/icons/arrow-circle-double-135.png" class="pointer" id="orderpos_uptpricebutton_<?=$i?>"
						 onclick="updateArticlePrice(<?=$i?>)" style="display:none"
						 title="<?=$_LANG->get('Staffelpreis aktualisieren')?>">
				</td>
				<td valign="top" class="content_row">
					<input 	name="orderpos[<?=$i?>][price]" id="orderpos_price_<?=$i?>" value="" 
							style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<?= $_USER->getClient()->getCurrency()?>
				</td>
				<td valign="top" class="content_row">
					<input 	name="orderpos[<?=$i?>][tax]" id="orderpos_tax_<?=$i?>"
							value="<?= printPrice('19.0')?>" style="width: 60px" 
							onfocus="markfield(this,0)" onblur="markfield(this,1)"> %
				</td>
				<td valign="top" class="content_row" id="td_totalprice_<?=$i?>">
					&ensp;
				</td>
				<td valign="top" class="content_row">
					<input type="checkbox" checked value="1" name="orderpos[<?=$i?>][inv_rel]">				
				</td>
				<td class="content_row">
					&ensp;
				</td>
			</tr>
	</table>
</div>	
<br/>

<input type="hidden" id="poscount" value="<?php echo $i;?>"/>
</form>