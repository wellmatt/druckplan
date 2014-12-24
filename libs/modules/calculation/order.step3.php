<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.01.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$filter = BusinessContact::FILTER_SUPP;
$all_supplier = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, $filter);
$formats = Paper::getAllPapers();
$paper_sizes_unique = Paper::getAllUniquePaperSizes();
$format_sizes_unique = Paper::getAllUniquePaperFormats();
?>

<script language="javascript">
function addRow(group)
{
	var counter = parseInt(document.getElementById('counter_machs').value);
	var lastcount = counter - 1;
	$.post("libs/modules/calculation/order.ajax.php", 
		    {exec: 'addMachineRow', group: group, idx: counter, orderId: <?=$order->getId()?>}, 
		    function(data) {
        		// Work on returned data
        		document.getElementById('table_group_'+group).insertAdjacentHTML("BeforeEnd", data);
        		document.getElementById('counter_machs').value = counter + 1;
        	});	
}

function addArticleRow(){
	var counter = parseInt(document.getElementById('number_of_article').value);
	var lastcount = counter - 1;
	$.post("libs/modules/calculation/order.ajax.php", 
		    {exec: 'addArticleRow', idy: counter, orderId: <?=$order->getId()?>}, 
		    function(data) {
        		// Work on returned data
        		document.getElementById('table_article').insertAdjacentHTML("BeforeEnd", data);
        		document.getElementById('number_of_article').value = counter + 1;
        	});	
}

function deleteRow(idx)
{
	document.getElementById('mach_id_'+idx).disabled = 'true';
	document.getElementById('tr_mach_'+idx).style.display = 'none';
	document.getElementById('tr_mach_'+idx+'_1').style.display = 'none';
}

function deleteArticleRow(idy){
	document.getElementById('calcart_id_'+idy).disabled = 'true';
	document.getElementById('tr_calcart_'+idy).style.display = 'none';
	var num_art = parseInt(document.getElementById('number_of_article').value);
	document.getElementById('number_of_article').value = (num_art-1);
}

function updateMachineProps(idx, machId){	
	partId = document.getElementsByName('mach_part_'+idx)[0].value;
	$.post("libs/modules/calculation/order.ajax.php", 
		    {exec: 'updateMachineProps', idx: idx, machId: machId, calcId: <?=$calc->getId()?> , partId: partId}, 
		    function(data) {
        		// Work on returned data
        		document.getElementById('td-machopts-'+idx).innerHTML = data;
        	    updateAvailPapers(idx);
        	});
}

function updateAvailPapers(idx){
	machId = document.getElementsByName('mach_id_'+idx)[0].value;
	partId = document.getElementsByName('mach_part_'+idx)[0].value;
	$.post("libs/modules/calculation/order.ajax.php", 
		    {exec: 'updateAvailPapers', idx: idx, machId: machId, calcId: <?=$calc->getId()?>, partId: partId}, 
		    function(data) {
        		// Work on returned data
        		document.getElementById('td-papersize-'+idx).innerHTML = data;
        		checkMashineContentCombination(idx);
        	});
}

function updateArticle(idy){

	amount = document.getElementById('art_amount_'+idy).value;
	amount = amount.replace("." , "");
	amount = amount.replace("," , ".");
	amount = parseFloat(amount);
	scale = parseInt(document.getElementById('art_scale_'+idy).value);
	artId = parseInt(document.getElementById('calcart_id_'+idy).value);
	

	$.post("libs/modules/calculation/order.ajax.php", 
		    {exec: 'calculateArticlePrice', idy: idy, artId: artId, calcId: <?=$calc->getId()?>, amount: amount, scale: scale}, 
			function(data) {
				// Work on returned data
				document.getElementById('art_cost_'+idy).innerHTML = data;
			});
}

function checkMashineContentCombination(idx){
	var alerttext = "";
	machId = document.getElementsByName('mach_id_'+idx)[0].value;
	partId = document.getElementsByName('mach_part_'+idx)[0].value;
	$.post("libs/modules/calculation/order.ajax.php", 
		    {exec: 'checkMashineContentCombination', idx: idx, machId: machId, calcId: <?=$calc->getId()?>, partId: partId}, 
		    function(data) {
        		// Work on returned data
        		if (!data){
            		if (partId == <?=Calculation::PAPER_CONTENT?>){
                		<? echo "alerttext = '".$_LANG->get('Inhalt').": ".$_LANG->get('Kombination aus Maschine und Inhalt pr&uuml;fen')."';";?>
            		}
            		if (partId == <?=Calculation::PAPER_ADDCONTENT?>){
                		<? echo "alerttext = '".$_LANG->get('zus. Inhalt').": ".$_LANG->get('Kombination aus Maschine und zus. Inhalt pr&uuml;fen')."';";?>
            		}
            		if (partId == <?=Calculation::PAPER_ENVELOPE?>){
                		<? echo "alerttext = '".$_LANG->get('Umschlag').": ".$_LANG->get('Kombination aus Maschine und Umschlag pr&uuml;fen')."';";?>
            		}
            		if (partId == <?=Calculation::PAPER_ADDCONTENT2?>){
                		<? echo "alerttext = '".$_LANG->get('zus. Inhalt 2').": ".$_LANG->get('Kombination aus Maschine und zus. Inhalt 2 pr&uuml;fen')."';";?>
            		}
            		if (partId == <?=Calculation::PAPER_ADDCONTENT3?>){
                		<? echo "alerttext = '".$_LANG->get('zus. Inhalt 3').": ".$_LANG->get('Kombination aus Maschine und zus. Inhalt 3 pr&uuml;fen')."';";?>
            		}
            		alert(alerttext);
        		}
        	});
}

var errortext = "";
<? 
// Auf Zuordnungsfehler pruefen
// Papiere
if($calc->getPaperContentHeight() == 0 && $calc->getPaperContentWidth() == 0 && $calc->getPaperContent()->getId())
    echo "errortext += '".$_LANG->get('Inhalt').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
if($calc->getPaperAddContentHeight() == 0 && $calc->getPaperAddContentWidth() == 0 && $calc->getPaperAddContent()->getId())
    echo "errortext += '".$_LANG->get('zus. Inhalt').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
if($calc->getPaperEnvelopeHeight() == 0 && $calc->getPaperEnvelopeWidth() == 0 && $calc->getPaperEnvelope()->getId())
    echo "errortext += '".$_LANG->get('Umschlag').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
if($calc->getPaperAddContent2Height() == 0 && $calc->getPaperAddContent2Width() == 0 && $calc->getPaperAddContent2()->getId())
	echo "errortext += '".$_LANG->get('zus. Inhalt 2').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
if($calc->getPaperAddContent3Height() == 0 && $calc->getPaperAddContent3Width() == 0 && $calc->getPaperAddContent3()->getId())
	echo "errortext += '".$_LANG->get('zus. Inhalt 3').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";

// Offenes Produktformat groesser als maximales Format der ausgewaehlten Maschinen?
// Nur in Gruppen bis einschliesslich Druck

// Gucken, ob eine Druck-Maschine ausgewaehlt wurde, sonst koennte eine Farbeeinstellung gewaehlt werden, die nicht druckbar ist
if (!$printer_part1_exists && $calc->getPagesContent()>0)
	echo "errortext += '".$_LANG->get('Inhalt').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
if (!$printer_part2_exists && $calc->getPagesAddContent()>0)
	echo "errortext += '".$_LANG->get('zus. Inhalt').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
if (!$printer_part3_exists && $calc->getPagesEnvelope()>0)
	echo "errortext += '".$_LANG->get('Umschlag').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
if (!$printer_part4_exists && $calc->getPagesAddContent2()>0)
	echo "errortext += '".$_LANG->get('zus. Inhalt 2').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
if (!$printer_part5_exists && $calc->getPagesAddContent3()>0)
	echo "errortext += '".$_LANG->get('zus. Inhalt 3').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
?>

// if(errortext.length > 0){
//	alert(errortext);
// }


<? // ------------------------------- JavaScript fuer Zus. Positionen ---------------------------------------------- ?>

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
		document.getElementById('orderpos_cost_'+id_i).value = "";
	}
}

function clickSearch(id_i){
	var tmp_type= document.getElementById('orderpos_type_'+id_i).value;
	var str = document.getElementById('orderpos_search_'+id_i).value;
	
	$.post("libs/modules/calculation/order.ajax.php", 
		{exec: 'searchPositions', type : tmp_type, str : str},  //cust_id : <?//=$selected_customer->getId()?> 
		 function(data) {
			document.getElementById('orderpos_searchlist_'+id_i).innerHTML = data;
			document.getElementById('orderpos_searchlist_'+id_i).style.display = "";
		});
}

function updatePosDetails(id_i){
	var tmp_type = document.getElementById('orderpos_type_'+id_i).value;
	var tmp_objid= document.getElementById('orderpos_searchlist_'+id_i).value;
	
	if(tmp_type == 2){
		$.post("libs/modules/calculation/order.ajax.php", 
			{exec: 'getArticleDetails', articleid: tmp_objid}, 
			 function(data) {
				var teile = data.split("-+-+-");
				document.getElementById('orderpos_objid_'+id_i).value = teile[0];
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
				document.getElementById('orderpos_comment_'+id_i).value = teile[4];
				document.getElementById('orderpos_comment_'+id_i).style.height = 100;
				document.getElementById('orderpos_quantity_'+id_i).value = "1";
				document.getElementById('td_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
				document.getElementById('orderpos_cost_'+id_i).value = printPriceJs(parseFloat(teile[3]));
		}); 
	}
}

function updateArticlePrice(id_i){
	var type = document.getElementById('orderpos_type_'+id_i).value;
	var tmp_objid = document.getElementById('orderpos_searchlist_'+id_i).value;
	var amount = document.getElementById('orderpos_quantity_'+id_i).value;
	var scale = document.getElementById('orderpos_scale_'+id_i).value;
	var input_price = document.getElementById('orderpos_price_'+id_i).value;
	var price = 0;
	var article_price = 0;

	input_price = input_price.replace(",",".")


	if(type == <?=CalculationPosition::TYPE_ARTICLE?>){
		// Artikel Preis holen und Betrag berechnen
		$.post("libs/modules/calculation/order.ajax.php", 
			{exec: 'getArticlePrice', articleid: tmp_objid, amount: amount}, 
			 function(data) {
				var teile = data.split("-+-+-");
				// parseFloat(data) ist der Artikel Einzel-Preis
				if(scale == <?=CalculationPosition::SCALE_PER_KALKULATION?>){
					// Betrag berechnen bei Menge pro Kalkulation
					price = parseFloat(teile[0]) * amount;
				} else { 
					// Betrag berechnen bei Menge pro Stueck
					if(tmp_objid == 0){
						// Kein Artikel ausgewaehlt 
						article_price = parseFloat(input_price);
					} else {
						article_price = parseFloat(teile[0]);
					}
					price = article_price * amount * parseFloat(<?=$calc->getAmount()?>); 
				}
				document.getElementById('td_totalprice_'+id_i).innerHTML = printPriceJs(price)+" <?=$_USER->getClient()->getCurrency()?>";
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[0]));
				document.getElementById('orderpos_cost_'+id_i).value = printPriceJs(parseFloat(teile[1]));
		});  
	} else {
		// Manuelle Position
		if(scale == <?=CalculationPosition::SCALE_PER_KALKULATION?>){
			price = amount * parseFloat(input_price);
		} else {
			price = amount * parseFloat(input_price) * parseFloat(<?=$calc->getAmount()?>);
		}
		// document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(data));
		document.getElementById('td_totalprice_'+id_i).innerHTML = printPriceJs(price)+" <?=$_USER->getClient()->getCurrency()?>";
	}
}

function updateArticleCosts(id_i){
	
}

<? // ---------------------------- Funktionen fuer die Erweiterung der Fremdleistungen ----------------------------- ?>

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('.mach_senddate').datepicker(
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

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('.mach_receivedate').datepicker(
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
</script>

<style type="text/css">
<!--
/* body{margin:0px; padding:0px;} */
#fl_menu{
    position:absolute; 
    top:100px; 
    left:0px; 
    z-index:9999; 
    width:150px; 
    height:50px;
	margin-left:100px;
	margin-top:100px;	
}
#fl_menu .label{
    padding-left:20px; 
/*     line-height:50px;  */
/*     font-family:"Arial Black", Arial, Helvetica, sans-serif;  */
    font-size:14px; 
    font-weight:bold;
    background:#000; 
    color:#fff; 
/*     letter-spacing:7px; */
}
#fl_menu .menu{
/*     display:none; */
}
#fl_menu .menu .menu_item{
    display:block; 
    background:#000; 
    color:#bbb; 
    border-top:1px solid #333; 
    padding:10px 20px; 
/*     font-family:Arial, Helvetica, sans-serif;  */
    font-size:12px; 
    text-decoration:none;
}
#fl_menu .menu a.menu_item:hover{
    background:#333; 
    color:#fff;
}
-->
</style>
<script type="text/javascript" src="./jscripts/jquery.easing.1.3.js"></script>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
	<?
	$tmp_all_calcs = Calculation::getAllCalculations($order);
	foreach ($tmp_all_calcs as $tmp_calc){
	    echo '<a href="index.php?page=libs/modules/calculation/order.php&id='.$order->getId().'&calc_id='.$tmp_calc->getId().'&exec=edit&step=3" class="menu_item">Auflage '.$tmp_calc->getAmount().'</a>';
	}
	?>
        <a href="#" onclick="document.getElementsByName('nextstep')[0].value='4';document.step3_form.submit();" class="menu_item">Weiter</a>
        <a href="#" onclick="document.step3_form.submit();" class="menu_item">Speichern</a>
    </div>
</div>

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

<div class="box1">
<table width="100%">
    <colgroup>
        <col width="16%">
        <col width="17%">
        <col width="16%">
        <col width="16%">
        <col width="18%">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Endformat')?>:</td>
        <td class="content_row_clear"><?=$calc->getProductFormat()->getName()?></td>
        <td class="content_row_header"><?=$_LANG->get('Abmessungen offen')?>:</td>
        <td class="content_row_clear"><?=$calc->getProductFormatWidthOpen()?> x <?=$calc->getProductFormatHeightOpen()?> mm</td>
        <td class="content_row_header"><?=$_LANG->get('Abmessungen geschlossen')?>:</td>
        <td class="content_row_clear"><?=$calc->getProductFormatWidth()?> x <?=$calc->getProductFormatHeight()?> mm</td>
    </tr>
    <tr>
        <td class="content_row"><b><?=$_LANG->get('Material')?> <?=$_LANG->get('Inhalt')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperContent()->getName()?></td>
        <td class="content_row"><b><?=$_LANG->get('Gewicht')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperContentWeight()?> g / qm</td>
        <td class="content_row"><b><?=$_LANG->get('Umfang')?>:</b></td>
        <td class="content_row"><?=$calc->getPagesContent()?> <?=$_LANG->get('Seiten')?> </td>
    </tr>
    <tr>
        <td class="content_row_header">&nbsp;</td>
        <td class="content_row_clear">&nbsp;</td>
        <td class="content_row_header"><?=$_LANG->get('Farbigkeit')?> <?=$_LANG->get('Inhalt')?></td>
        <td class="content_row_clear"><?=$calc->getChromaticitiesContent()->getName()?></td> <!--  Muss noch von den Maschineneintr�gen in die Kalkulation gezogen werden -->
        <td class="content_row_header"><?=$_LANG->get('Auflage')?></td>
        <td class="content_row_clear"><?=printBigInt($calc->getAmount())?></td>
    </tr>
    <?  if($calc->getPaperAddContent()->getId() != 0) { ?>
    <tr>
        <td class="content_row"><b><?=$_LANG->get('Material')?> <?=$_LANG->get('zus. Inhalt')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperAddContent()->getName()?></td>
        <td class="content_row"><b><?=$_LANG->get('Gewicht')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperAddContentWeight()?> g / qm</td>
        <td class="content_row"><b><?=$_LANG->get('Umfang')?>:</b></td>
        <td class="content_row"><?=$calc->getPagesAddContent()?> <?=$_LANG->get('Seiten')?> </td>
    </tr>
    <tr>
        <td class="content_row_header">&nbsp;</td>
        <td class="content_row_clear">&nbsp;</td>
        <td class="content_row_header"><?=$_LANG->get('Farbigkeit')?> <?=$_LANG->get('zus. Inhalt')?></td>
        <td class="content_row_clear"><?=$calc->getChromaticitiesAddContent()->getName()?></td> <!--  Muss noch von den Maschineneintr�gen in die Kalkulation gezogen werden -->
        <td class="content_row_header"></td>
        <td class="content_row_clear"></td>
    </tr>
    <?  } if($calc->getPaperEnvelope()->getId() != 0){ ?>
    <tr>
        <td class="content_row"><b><?=$_LANG->get('Material')?> <?=$_LANG->get('Umschlag')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperEnvelope()->getName()?></td>
        <td class="content_row"><b><?=$_LANG->get('Gewicht')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperEnvelopeWeight()?> g / qm</td>
        <td class="content_row"><b><?=$_LANG->get('Umfang')?>:</b></td>
        <td class="content_row"><?=$calc->getPagesEnvelope()?> <?=$_LANG->get('Seiten')?> </td>
    </tr>
    <tr>
        <td class="content_row_header">&nbsp;</td>
        <td class="content_row_clear">&nbsp;</td>
        <td class="content_row_header"><?=$_LANG->get('Farbigkeit')?> <?=$_LANG->get('Umschlag')?></td>
        <td class="content_row_clear"><?=$calc->getChromaticitiesEnvelope()->getName()?></td> <!--  Muss noch von den Maschineneintr�gen in die Kalkulation gezogen werden -->
        <td class="content_row_header"><?=$_LANG->get('Offenes Format')?>:</td>
        <td class="content_row_clear"><?=$calc->getEnvelopeWidthOpen()?> x <?=$calc->getEnvelopeHeightOpen()?> mm</td>
    </tr>
    <?  }
	if($calc->getPaperAddContent2()->getId() != 0) { ?>
    <tr>
        <td class="content_row"><b><?=$_LANG->get('Material')?> <?=$_LANG->get('zus. Inhalt 2')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperAddContent2()->getName()?></td>
        <td class="content_row"><b><?=$_LANG->get('Gewicht')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperAddContent2Weight()?> g / qm</td>
        <td class="content_row"><b><?=$_LANG->get('Umfang')?>:</b></td>
        <td class="content_row"><?=$calc->getPagesAddContent2()?> <?=$_LANG->get('Seiten')?> </td>
    </tr>
    <tr>
        <td class="content_row_header">&nbsp;</td>
        <td class="content_row_clear">&nbsp;</td>
        <td class="content_row_header"><?=$_LANG->get('Farbigkeit')?> <?=$_LANG->get('zus. Inhalt')?></td>
        <td class="content_row_clear"><?=$calc->getChromaticitiesAddContent2()->getName()?></td> 
        							 <!--  Muss noch von den Maschineneintraegen in die Kalkulation gezogen werden -->
        <td class="content_row_header"></td>
        <td class="content_row_clear"></td>
    </tr>
    <?  }
	if($calc->getPaperAddContent3()->getId() != 0) { ?>
    <tr>
        <td class="content_row"><b><?=$_LANG->get('Material')?> <?=$_LANG->get('zus. Inhalt 3')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperAddContent3()->getName()?></td>
        <td class="content_row"><b><?=$_LANG->get('Gewicht')?>:</b></td>
        <td class="content_row"><?=$calc->getPaperAddContent3Weight()?> g / qm</td>
        <td class="content_row"><b><?=$_LANG->get('Umfang')?>:</b></td>
        <td class="content_row"><?=$calc->getPagesAddContent3()?> <?=$_LANG->get('Seiten')?> </td>
    </tr>
    <tr>
        <td class="content_row_header">&nbsp;</td>
        <td class="content_row_clear">&nbsp;</td>
        <td class="content_row_header"><?=$_LANG->get('Farbigkeit')?> <?=$_LANG->get('zus. Inhalt 3')?></td>
        <td class="content_row_clear"><?=$calc->getChromaticitiesAddContent3()->getName()?></td> 
        							 <!--  Muss noch von den Maschineneintraegen in die Kalkulation gezogen werden -->
        <td class="content_row_header"></td>
        <td class="content_row_clear"></td>
    </tr>
    <?  } ?>
</table>
</div>
<br>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="step3_form">
<input name="step" value="3" type="hidden">
<input name="exec" value="edit" type="hidden">
<input name="subexec" value="save" type="hidden">
<input name="id" value="<?=$order->getId()?>" type="hidden">
<input name="calc_id" value="<?=$calc->getId()?>" type="hidden">
<input name="nextstep" value="" type="hidden"> 
<? if($_REQUEST["addorder_amount"]){
	foreach ($_REQUEST["addorder_amount"] as $amount){
		echo '<input name ="addorder_amount[]" value="'.$amount.'" type="hidden">';
		echo '<input name="origcalc" value="calc->getId()" type="hidden">';
	}
	foreach ($_REQUEST["addorder_sorts"] as $sorts){
		echo '<input name ="addorder_sorts[]" value="'.$sorts.'" type="hidden">';
	}
}?>


<? 
/*if($_REQUEST["addorder_amount"])
    foreach($_REQUEST["addorder_amount"] as $addamount)
    {
        if((int)$addamount > 0)
            echo '<input name="addorder_amount[]" class="text" value="'.$addamount.'" type="hidden"> ';
    }*/
?>

<? 
$x = 0;
$machines = $order->getProduct()->getMachines();
$groups = MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION);

foreach($groups as $group)
{
    // Alle Maschinen der Gruppe ermitteln?
    $groupmachs = Array();
    foreach($machines as $m){
        if($m->getGroup()->getId() == $group->getId()){
			$groupmachs[] = $m;
    	}
	}
    if(count($groupmachs)){
        ?>
        <h1><?=$group->getName()?></h1>
        <div class="box2">
            <table width="100%" id="table_group_<?=$group->getId()?>">
                <colgroup>
                    <col width="290">
                    <col width="70">
                    <col>
                    <col width="370">
                    <col width="90">
                    <col width="80">
                    <col width="80">
                </colgroup>
                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Maschine')?></td>
                    <td class="content_row_header"><?=$_LANG->get('Zeit')?> *</td>
                    <td class="content_row_header"><?=$_LANG->get('Einstellungen')?></td>
                    <td class="content_row_header"><?=$_LANG->get('Sonstiges')?></td>
                    <td class="content_row_header"><?=$_LANG->get('Bogengr&ouml;&szlig;e')?></td>
                    <td class="content_row_header"><?=$_LANG->get('Kosten')?> *</td>
                    <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
                </tr>
                <?
                $groupHasDefaultMachs = false;
                //getAllMachineentriesByColor
                foreach (Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID, $group->getId()) as $mach)
                {
                    $groupHasDefaultMachs = true;
                    echo '<tr id="tr_mach_'.$x.'">';
                        echo '<td class="content_row">';
                            echo '<input type="hidden" name="mach_group_'.$x.'" value="'.$group->getId().'">';
                            echo '<select name="mach_id_'.$x.'" style="width:280px" class="text" id="mach_id_'.$x.'" onchange="updateMachineProps('.$x.', this.value);">';
                            foreach ($groupmachs as $gm)
                            {
                            	echo '<option value="'.$gm->getId().'" ';
                            	if($mach->getMachine()->getId() == $gm->getId()) echo "selected";
                            	echo '>'.$gm->getName().'</option>';
                            }
                            echo '</select>';
                        echo '</td>';
                        echo '<td class="content_row"><input class="text" style="width:40px" name="mach_time_'.$x.'" value="'.$mach->getTime().'"> min.</td>';
                        echo '<td class="content_row" id="td-machopts-'.$x.'" rowspan="2" valign="top">&nbsp;';
                       
                        
                        // Inhalt, zus. Inhalt oder Umschlag?
                        if($mach->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $mach->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET
                                || $mach->getMachine()->getType() == Machine::TYPE_FOLDER || $mach->getMachine()->getType() == Machine::TYPE_CUTTER)
                        {
                            echo '<select name="mach_part_'.$x.'" style="width:120px" class="text" onchange="updateAvailPapers('.$x.')">';
                                if($calc->getPaperContent()->getId())
                                {
                                    echo '<option value="'.Calculation::PAPER_CONTENT.'" ';
                                    if($mach->getPart() == Calculation::PAPER_CONTENT) echo 'selected';
                                    echo '>'.$_LANG->get('Inhalt').'</option>';
                                }
                                if($calc->getPaperAddContent()->getId())
                                {                                
                                    echo '<option value="'.Calculation::PAPER_ADDCONTENT.'" ';
                                    if($mach->getPart() == Calculation::PAPER_ADDCONTENT) echo 'selected';
                                    echo '>'.$_LANG->get('zus. Inhalt').'</option>';
                                }
                                if($calc->getPaperEnvelope()->getId())
                                {
                                    echo '<option value="'.Calculation::PAPER_ENVELOPE.'" ';
                                    if($mach->getPart() == Calculation::PAPER_ENVELOPE) echo 'selected';
                                    echo '>'.$_LANG->get('Umschlag').'</option>';
                                }
                                if($calc->getPaperAddContent2()->getId())
                                {
                                	echo '<option value="'.Calculation::PAPER_ADDCONTENT2.'" ';
                                	if($mach->getPart() == Calculation::PAPER_ADDCONTENT2) echo 'selected';
                                	echo '>'.$_LANG->get('zus. Inhalt 2').'</option>';
                                }
                                if($calc->getPaperAddContent3()->getId())
                                {
                                	echo '<option value="'.Calculation::PAPER_ADDCONTENT3.'" ';
                                	if($mach->getPart() == Calculation::PAPER_ADDCONTENT3) echo 'selected';
                                	echo '>'.$_LANG->get('zus. Inhalt 3').'</option>';
                                }
                            echo '</select> ';
                        }
                        
                        // Falls Maschine manuell berechnet wird, Feld anzeigen
                        if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                        {
                            echo $_LANG->get('Preis').': <input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" 
                                    style="width:60px;"> '.$_USER->getClient()->getCurrency();
                            if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                            	echo '<br> &nbsp; ';
                            	echo $_LANG->get('EK').':  &ensp; <input class="text" name="mach_supplierprice_'.$x.'" ';
                            	echo 'style="width:60px;margin-top:3px;" title="'.$_LANG->get('Einkaufspreis').'"';
                            	echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" > '.$_USER->getClient()->getCurrency();
                            }
                        }
                        
                        // Option Lack
                        if($mach->getMachine()->getFinish())
                        {
                            $finishings = Finishing::getAllFinishings();
                            
                            echo '<select name="mach_finishing_'.$x.'" style="width:120px" class="text">';
                            echo '<option value="0">'.$_LANG->get('kein Lack').'</option>';
                            foreach($finishings as $f)
                            {
                                echo '<option value="'.$f->getId().'" ';
                                if($mach->getFinishing()->getId() == $f->getId()) echo "selected";
                                echo '>'.$f->getName().'</option>';
                            }
                            echo '</select>';
                        }
                        
                        //gln hier!!!!!!!!!!!!!!!!!! umschlagen/umstuelpen anzeigen
                        //if($mach->getMachine()->getUmschlUmst())
                        //{
                        //    echo $_LANG->get('umschlagen/umst&uuml;lpen');
                        //}
                        if( $mach->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET && $mach->getMachine()->getUmschlUmst() > 0)
                        {
							echo $_LANG->get('umschlagen/umst&uuml;lpen');
							echo '<input type="checkbox" name="umschl_umst_'.$x. '" value="1"';
							if($mach->getUmschlagenUmstuelpen()) echo ' checked="checked"';
							echo '>';
                      	}
                      	
                      	
//                       	if($mach->getMachine()->getType() == Machine::TYPE_CUTTER) // added by ascherer 22.07.14 // temp remove  && $group->getName() == "Verarbeitung"
//                       	{
//                       	    echo $_LANG->get('Schnitte');
//                       	    echo '<input type="text" name="mach_cutter_cuts" value="'.$mach->getCutter_cuts().'" style="width:80px">';
//                       	}

                        // Falls Sammelhefter -> Falzbogenschema auswaehlen
                        if($mach->getMachine()->getType() == Machine::TYPE_SAMMELHEFTER)
                        {
                            $schemes = $calc->getAvailableFoldschemes();
                            echo $_LANG->get('Falzb&ouml;gen')." ".$_LANG->get('Inhalt')." ";
                            echo '<select name="foldscheme_content" style="width:120px" class="text">';
                            foreach($schemes[1] as $scheme)
                            {
                                $str = '';
                                if($scheme[16])
                                    $str .= $scheme[16]." x 16, ";
                                if($scheme[8])
                                    $str .= $scheme[8]." x 8, ";
                                if($scheme[4])
                                    $str .= $scheme[4]." x 4, ";
                                
                                $str = substr($str, 0, -2);
                                $val = preg_replace("/ /", '', $str);

                                echo '<option value="'.$val.'" ';
                                if($calc->getFoldschemeContent() == $val) echo "selected";
                                echo '>'.$str.'</option>';
                            }
                            echo '</select><br>';
                            
                            if($calc->getPagesAddContent()){
                                echo $_LANG->get('zus. Inhalt')." &ensp; ";
                                echo '<select name="foldscheme_addcontent" style="width:120px" class="text">';
                                foreach($schemes[2] as $scheme)
                                {
                                    $str = '';
                                    if($scheme[16])
                                        $str .= $scheme[16]." x 16, ";
                                    if($scheme[8])
                                        $str .= $scheme[8]." x 8, ";
                                    if($scheme[4])
                                        $str .= $scheme[4]." x 4, ";
                                
                                    $str = substr($str, 0, -2);
                                    $val = preg_replace("/ /", '', $str);
                                
                                    echo '<option value="'.$val.'" ';
                                    if($calc->getFoldschemeAddContent() == $val) echo "selected";
                                    echo '>'.$str.'</option>';
                                }
                                echo '</select><br>';   
                            }
                            if($calc->getPagesAddContent2()){
                            	echo $_LANG->get('zus. Inhalt 2')." ";
                            	echo '<select name="foldscheme_addcontent2" style="width:120px" class="text">';
                            	foreach($schemes[4] as $scheme)
                            	{
                            		$str = '';
                            		if($scheme[16])
                            			$str .= $scheme[16]." x 16, ";
                            		if($scheme[8])
                            			$str .= $scheme[8]." x 8, ";
                            		if($scheme[4])
                            			$str .= $scheme[4]." x 4, ";
                            
                            		$str = substr($str, 0, -2);
                            		$val = preg_replace("/ /", '', $str);
                            
                            		echo '<option value="'.$val.'" ';
                            		if($calc->getFoldschemeAddContent2() == $val) echo "selected";
                            		echo '>'.$str.'</option>';
                            	}
                            	echo '</select><br>';
                            }
                            if($calc->getPagesAddContent3()){
                            	echo $_LANG->get('zus. Inhalt 3')." ";
                            	echo '<select name="foldscheme_addcontent3" style="width:120px" class="text">';
                            	foreach($schemes[5] as $scheme)
                            	{
                            		$str = '';
                            		if($scheme[16])
                            			$str .= $scheme[16]." x 16, ";
                            		if($scheme[8])
                            			$str .= $scheme[8]." x 8, ";
                            		if($scheme[4])
                            			$str .= $scheme[4]." x 4, ";
                            
                            		$str = substr($str, 0, -2);
                            		$val = preg_replace("/ /", '', $str);
                            
                            		echo '<option value="'.$val.'" ';
                            		if($calc->getFoldschemeAddContent3() == $val) echo "selected";
                            		echo '>'.$str.'</option>';
                            	}
                            	echo '</select><br>';
                            }
                            
                            
                        }
                        // Sonder-Felder fuer Fremdleistungen
                        echo '<td class="content_row text" rowspan="2">';
                        echo '<table cellpadding="0" cellspacing="2">';
                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){ ?>
                        	<tr>
                        		<td><?=$_LANG->get('Lieferung')?> </td>
                        		<td width="110px;">
                        			<input class="text mach_senddate" name="mach_senddate_<?=$x?>" style="width:70px;" type="text"
                        		 		<? if ($mach->getSupplierSendDate() > 0 ) echo 'value="'.date("d.m.Y", $mach->getSupplierSendDate()).'"';?> 
                        		 			title="<?=$_LANG->get('Liefer-/Bestelldatum');?>">
								</td>
								<td>&ensp;</td>
								<td><?=$_LANG->get('Lieferant')?> </td>
								<td>
									<select name="mach_supplierid_<?=$x?>" class="text" style="width:120px;">
										<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen');?> &gt;</option>
									<? 	foreach ($all_supplier AS $supplier){ ?>
										<option value="<?=$supplier->getId()?>"
											<?if ($mach->getSupplierID() == $supplier->getId()) echo 'selected="selected"';?>
											><?=$supplier->getNameAsLine()?></option>
									<?	} ?>
									</select>
								</td>
							</tr>
							<tr>
								<td><?=$_LANG->get('Retour')?> </td>
								<td>
									<input class="text mach_receivedate" name="mach_receivedate_<?=$x?>" style="width:70px;" type="text"
										<? if ($mach->getSupplierReceiveDate() > 0 ) echo 'value="'.date("d.m.Y", $mach->getSupplierReceiveDate()).'"';?> >
								</td>
								<td>&ensp;</td>
								<td><?=$_LANG->get('Lief.-Status')?> </td>
								<td>
									<select name="mach_supplierstatus_<?=$x?>" class="text" style="width:120px;">
										<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen');?> &gt;</option>
										<option value="1" <?if($mach->getSupplierStatus() == 1) echo 'selected="selected"';?> 
												><?=Machineentry::SUPPLIER_STATUS_1?></option>
										<option value="2" <?if($mach->getSupplierStatus() == 2) echo 'selected="selected"';?> 
												><?=Machineentry::SUPPLIER_STATUS_2?></option>
										<option value="3" <?if($mach->getSupplierStatus() == 3) echo 'selected="selected"';?> 
												><?=Machineentry::SUPPLIER_STATUS_3?></option>
									</select>
								</td>
							</tr>
							<tr>
								<td><?=$_LANG->get('Lief.-Info')?> </td>
								<td colspan="4">
									<input class="text" name="mach_supplierinfo_<?=$x?>" style="width:300px;" type="text"
										value="<?=$mach->getSupplierInfo()?>" title="<?=$_LANG->get('');?>">
								</td>
							</tr>

                    <?	} else if ($mach->getMachine()->getType() == Machine::TYPE_CUTTER) { // added by ascherer 22.07.14  // temp remove  && $group->getName() == "Verarbeitung"
                            echo $_LANG->get('Schnitte: ');
                            echo '<input type="text" name="mach_cutter_cuts_'.$x.'" value="'.$mach->getCutter_cuts().'" style="width:80px">';
							echo "<tr><td align='top'>";
							echo $_LANG->get('Laufrichtung: ');
							echo '<select name="mach_roll_dir_'.$x.'" style="width:80px">';
							echo '<option value=""></option>';
							echo '<option value="1" ';
							if ($mach->getRoll_dir() == 1) echo 'selected';
							echo '>';
							echo 'breite Bahn</option>';
							echo '<option value="2" ';
							if ($mach->getRoll_dir() == 2) echo 'selected';
							echo '>';
							echo 'schmale Bahn</option>';
							echo '</select>';
							echo "</td></tr>";
							echo "<tr><td>&nbsp;</td></tr>";
							echo "<tr><td>&nbsp;</td></tr>";
						} else {
                    		echo "<tr><td>&nbsp;</td></tr>";
                    	}
                        echo "</table>";
                        echo "</td>";
                        
                        //** ENDE:  Sonderfelder fuer Fremdleistungen
                        
                        echo '<td class="content_row" id="td-papersize-'.$x.'">&nbsp;';
                        // Zu verwendene Papiergroesse auswaehlen
                        if($mach->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||$mach->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                        {
                            if ($mach->getPart() == Calculation::PAPER_CONTENT)
                                $sizes = $calc->getPaperContent()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen());
                            else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT)
                                $sizes = $calc->getPaperAddContent()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen());
                            else if ($mach->getPart() == Calculation::PAPER_ENVELOPE)
                                $sizes = $calc->getPaperEnvelope()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getEnvelopeWidthOpen(), $calc->getEnvelopeHeightOpen());
                            else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT2)
                            	$sizes = $calc->getPaperAddContent2()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen());
                            else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT3)
                            	$sizes = $calc->getPaperAddContent3()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen());
                            
                            echo '<select name="mach_papersize_'.$x.'" style="width:80px">';
                            foreach($sizes as $s)
                            {
                                echo '<option value="'.$s["width"].'x'.$s["height"].'" ';
                                if($mach->getPart() == Calculation::PAPER_CONTENT)
                                    if ($s["width"].'x'.$s["height"] == $calc->getPaperContentWidth().'x'.$calc->getPaperContentHeight()) echo 'selected';
                                if($mach->getPart() == Calculation::PAPER_ADDCONTENT)
                                    if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContentWidth().'x'.$calc->getPaperAddContentHeight()) echo 'selected';
                                if($mach->getPart() == Calculation::PAPER_ENVELOPE)
                                    if ($s["width"].'x'.$s["height"] == $calc->getPaperEnvelopeWidth().'x'.$calc->getPaperEnvelopeHeight()) echo 'selected';
                                if($mach->getPart() == Calculation::PAPER_ADDCONTENT2)
                                	if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContent2Width().'x'.$calc->getPaperAddContent2Height()) echo 'selected';
                                if($mach->getPart() == Calculation::PAPER_ADDCONTENT3)
                                	if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContent3Width().'x'.$calc->getPaperAddContent3Height()) echo 'selected';
                                echo '>';
                                echo $s["width"].' x '.$s["height"].'</option>';
                            }
                            echo '</select>';
                        }
						else if($mach->getMachine()->getType() == Machine::TYPE_CUTTER) // added by ascherer 22.07.14 // temp remove  && $group->getName() == "Verarbeitung"
                        {
// 							echo 'IN: <select name="mach_format_in_'.$x.'" style="width:80px">';
// 							foreach($formats as $paper)
// 							{
// 								foreach ($paper->getSizes() as $size)
// 								{
// 									echo '<option value="'.$size["width"].'x'.$size["height"].'" ';
// 									if ($size["width"].'x'.$size["height"] == $mach->getFormat_in_width().'x'.$mach->getFormat_in_height()) echo 'selected';
// 									echo '>';
// 									echo $size["width"].' x '.$size["height"].'</option>';
// 								}
// 							}
// 							echo '</select>';
// 							echo 'OUT: <select name="mach_format_out_'.$x.'" style="width:80px">';
// 							foreach($formats as $paper)
// 							{
// 							    foreach ($paper->getSizes() as $size)
// 							    {
// 							        echo '<option value="'.$size["width"].'x'.$size["height"].'" ';
// 							        if ($size["width"].'x'.$size["height"] == $mach->getFormat_out_width().'x'.$mach->getFormat_out_height()) echo 'selected';
// 							        echo '>';
// 							        echo $size["width"].' x '.$size["height"].'</option>';
// 							    }
// 							}
// 							echo '</select>';
                            // NEU NUR NOCH UNIQUE PAPIER FORMATE
                            echo 'IN: <select name="mach_format_in_'.$x.'" style="width:80px">';
                            foreach ($format_sizes_unique as $size)
                            {
                                echo '<option value="'.$size['width'].'x'.$size['height'].'" ';
                                if ($size['width'].'x'.$size['height'] == $mach->getFormat_in_width().'x'.$mach->getFormat_in_height()) echo 'selected';
                                echo '>';
                                echo $size['width'].'x'.$size['height'].' ( '.$size['name'].' )</option>';
                            }
                            echo '</select>';
                            echo 'OUT: <select name="mach_format_out_'.$x.'" style="width:80px">';
                            foreach ($format_sizes_unique as $size)
                            {
                                echo '<option value="'.$size['width'].'x'.$size['height'].'" ';
                                if ($size['width'].'x'.$size['height'] == $mach->getFormat_out_width().'x'.$mach->getFormat_out_height()) echo 'selected';
                                echo '>';
                                echo $size['width'].'x'.$size['height'].' ( '.$size['name'].' )</option>';
                            }
                            echo '</select>';
						}
                        echo '</td>';
                        echo '</td>';
                        echo '<td class="content_row" id="td-cost-'.$x.'">';
                            echo printPrice($mach->getPrice())." ".$_USER->getClient()->getCurrency();
                        echo '</td>';
                        echo '<td class="content_row">';
                            echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addRow('.$group->getId().')"> ';
                            echo '<img src="images/icons/cross-script.png" class="pointer icon-link" onclick="deleteRow('.$x.')"> ';
                        echo '</td>';
                        
                    echo '</tr>';
                    
                    echo '<tr id="tr_mach_'.$x.'_1">';
                        echo '<td colspan="2" class="content_row_clear">Hinweise: ';
                        echo '<input name="mach_info_'.$x.'" id="mach_info_'.$x.'" style="width:300px" class="text"';
                        echo ' value="'.$mach->getInfo().'">';
                        echo '</td>';
                        echo '<td colspan="3" class="content_row_clear">&nbsp;</td>';
                    echo '</tr>';
                    
                    if ($mach->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $mach->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
                        echo '<tr id="tr_mach_'.$x.'_2">';
                        echo '<td colspan="2" class="content_row_clear">Farbton: ';
                        echo '<input name="mach_color_detail_'.$x.'" id="mach_color_detail_'.$x.'" style="width:300px" class="text"';
                        echo ' value="'.$mach->getColor_detail().'">';
                        echo '</td>';
                        echo '<td colspan="3" class="content_row_clear">&nbsp;</td>';
                        echo '</tr>';
                    }
                    
                    $x++;
                }
                
                // Es sind keine Standardmaschinen eingetragen worden
                if(!$groupHasDefaultMachs)
                {
                    echo '<tr id="tr_mach_'.$x.'">';
                        echo '<td class="content_row_clear">';
                            echo '<input type="hidden" name="mach_group_'.$x.'" value="'.$group->getId().'">';
                            echo '<select name="mach_id_'.$x.'" style="width:280px" class="text" id="mach_id_'.$x.'" onchange="updateMachineProps('.$x.', this.value)">';
                                echo '<option value=""></option>';
                            foreach ($groupmachs as $gm)
                            {
                                echo '<option value="'.$gm->getId().'" ';
                                echo '>'.$gm->getName().'</option>';
                            
                            }
                            echo '</select>';
                        echo '</td>';
                        echo '<td class="content_row_clear"><input class="text" style="width:40px" name="mach_time_'.$x.'" value=""> min.</td>';
                        echo '<td class="content_row_clear" id="td-machopts-'.$x.'">';
                        echo '<select name="mach_part_'.$x.'" style="display:none"></select>';
                        echo '</td>';
                        echo '<td class="content_row_clear" id="td-papersize-'.$x.'">';
                        echo '</td>';
                        echo '<td class="content_row_clear" id="td-cost-'.$x.'">';
                        echo '</td>';
                        echo '<td class="content_row_clear">';
                            echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addRow('.$group->getId().')"> ';
                            echo '<img src="images/icons/cross-script.png" class="pointer icon-link" onclick="deleteRow('.$x.')"> ';
                        echo '</td>';
                    echo '</tr>';
                    $x++;
                }

                ?> 
            </table>
        </div>
        <br>
    <? } ?>
<? } ?>

<h1><?=$_LANG->get('Zus. Optionen')?></h1>
<div class="box2">
<table width="100%">
	<colgroup>
		<col width="270" >
		<col>
	</colgroup>
	<tr>
		<td class="content_row_header">
			<?=$_LANG->get('Farbkontrollstreifen aktiv')?>
		</td>
		<td class="content_row_clear">
			<input type="checkbox" name="color_control" value="1" <?if($calc->getColorControl() == 1) echo 'checked="checked"';?>>
		</td>
	</tr>
	<tr>
		<td class="content_row_header">
			<?=$_LANG->get('Anschnitt')?>
		</td>
		<td class="content_row_clear">
		<? 	if ($calc->getPagesContent() > 0 && $calc->getPaperContent()->getId() > 0) { ?>
				<?=$_LANG->get('Inhalt');?>: 
				<input name="cut_content" id="cut_content" style="width:50px" class="text"';
                   	value="<?=printPrice($calc->getCutContent())?>"> <?=$_LANG->get('mm');?>
		<?	} ?>
		<? 	if ($calc->getPagesAddContent() > 0 && $calc->getPaperAddContent()->getId() > 0) { ?>
				&ensp;&ensp;<?=$_LANG->get('Zus. Inhalt');?>: 
				<input name="cut_addcontent" id="cut_addcontent" style="width:50px" class="text"';
                	   value="<?=printPrice($calc->getCutAddContent())?>"> <?=$_LANG->get('mm');?>
		<?	} ?>
		<? 	if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent3()->getId() > 0) { ?>
				&ensp;&ensp;<?=$_LANG->get('Zus. Inhalt 2');?>: 
				<input name="cut_addcontent2" id="cut_addcontent2" style="width:50px" class="text"';
                	   value="<?=printPrice($calc->getCutAddContent3())?>"> <?=$_LANG->get('mm');?>
		<?	} ?>
		<? 	if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent3()->getId() > 0) { ?>
				&ensp;&ensp;<?=$_LANG->get('Zus. Inhalt 3');?>: 
				<input name="cut_addcontent3" id="cut_addcontent3" style="width:50px" class="text"';
                	   value="<?=printPrice($calc->getCutAddContent3())?>"> <?=$_LANG->get('mm');?>
		<?	} ?>
		<? 	if ($calc->getPagesEnvelope() > 0 && $calc->getPaperEnvelope()->getId() > 0) { ?>
				&ensp;&ensp;<?=$_LANG->get('Umschlag');?>: 
				<input name="cut_envelope" id="cut_envelope" style="width:50px" class="text"';
                	   value="<?=printPrice($calc->getCutEnvelope())?>"> <?=$_LANG->get('mm');?>
		<?	} ?>	
		</td>
	</tr>
    <tr>
        <td class="content_row_header" width="290" valign="top">
            <?=$_LANG->get('Verarbeitung')?>
        </td>
        <td class="content_row_clear">
            <textarea name="text_processing" class="text" style="width:660px;height:50px;"><?=$calc->getTextProcessing()?></textarea>
        </td>
    </tr>
</table>
</div>
<br/>
<? /**** /----------------------------ARTIKEL------------------------------------------- 
$y=0;
$all_article = Article::getAllArticle();
$all_calc_article = $calc->getArticles();
?>
<h1><?=$_LANG->get('Zus. Artikel')?></h1>
<input 	type="hidden" name="number_of_article" id="number_of_article" 
		value="<? if (count($all_calc_article)>0) echo count($all_calc_article); else echo "1";?>">
<div class="box2">
<table width="100%" id="table_article">
	<colgroup>
		<col width="65">
		<col width="80">
		<col width="290">
		<col width="70">
		<col>
		<col width="90">
		<col width="80">
		<col width="80">
	</colgroup>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('Typ')?></td>
		<td class="content_row_header">&ensp;<?// Suchfeld ?>	</td>
		<td class="content_row_header"><?=$_LANG->get('Artikel')?></td>
		<td class="content_row_header"><?=$_LANG->get('Menge')?></td>
		<td class="content_row_header"><?=$_LANG->get('Einstellungen')?></td>
		<td class="content_row_header">&ensp;</td>
		<td class="content_row_header"><?=$_LANG->get('Kosten')?> *</td>
		<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
	</tr>
	<?if (count($all_calc_article)) {
		foreach ($all_calc_article as $calc_art){
			$tmp_art_amount	= $calc->getArticleamount($calc_art->getId());
			$tmp_art_scale	= $calc->getArticlescale($calc_art->getId());	 // Staffelung: pro Stk oder Auftrag
		?>
		<tr id="tr_calcart_<?=$y?>" >
			<td class="content_row_header" valign="top">
				<select name="calcart_type_<?=$y?>" style="width:60px" class="text" 
						id="calcart_type_<?=$y?>" onchange="updatePosition(<?=$y?>)">
					<option value="1"><?=$_LANG->get('Manuell');?></option>
					<option value="2"><?=$_LANG->get('Artikel');?></option>
				</select>
			</td>
			<td class="content_row_header" width="290" valign="top">
				<select name="calcart_id_<?=$y?>" style="width:280px" class="text" 
						id="calcart_id_<?=$y?>" onchange="updateArticle(<?=$y?>)">
					<option value=""></option>
					<?foreach ($all_article as $art){
						echo '<option value="'.$art->getId().'" ';
						echo '>'.$art->getTitle().'</option>';
					}?>
				</select>
			</td>
			<td class="content_row_clear">
				<input 	id="art_amount_<?=$y?>" name="art_amount_<?=$y?>" class="text" 
						value="<?= printPrice($tmp_art_amount)?>" style="width:40px;"
						onchange="updateArticle(<?=$y?>)">
				<?=$_LANG->get('Stk.')?>
			</td>
			<td class="content_row_clear">
				<select name="art_scale_<?=$y?>" style="width:120px" class="text" 
						id="art_scale_<?=$y?>" onchange="updateArticle(<?=$y?>)">
					<option value="0" <?if($tmp_art_scale==0) echo "selected";?>> <?=$_LANG->get('pro Kalkulation')?></option>
					<option value="1" <?if($tmp_art_scale==1) echo "selected";?>> <?=$_LANG->get('pro St&uuml;ck')?></option>
				</select>
			</td>
			<td class="content_row_clear">&ensp;</td>
			<td class="content_row_clear" id="art_cost_<?=$y?>">
				<? 	if ($tmp_art_scale==0){
						echo printPrice($tmp_art_amount * $calc_art->getPrice($tmp_art_amount));
					} elseif($tmp_art_scale==1){
						echo printPrice($tmp_art_amount * $calc_art->getPrice($tmp_art_amount*$calc->getAmount())*$calc->getAmount());
					}
					echo " ".$_USER->getClient()->getCurrency();?> 
			</td>
			<td class="content_row_clear">
				<img src="images/icons/plus.png" class="pointer" onclick="addArticleRow()">
				<img src="images/icons/cross-script.png" class="pointer" onclick="deleteArticleRow(<?=$y?>)">
			</td>
		</tr>		
		<? $y++; 
		}?>
	<? } else { //Wenn bislang keine Artikel hinzugefuegt wurden?>
		<tr>
			<td class="content_row_header" valign="top">
				<select name="calcart_type_<?=$y?>" style="width:60px" class="text" 
						id="calcart_type_<?=$y?>" onchange="updateArticleDropDown(<?=$y?>)">
						<option value="1"><?=$_LANG->get('Manuell');?></option>
						<option value="2"><?=$_LANG->get('Artikel');?></option>
				</select>
			</td>
			<td class="content_row_header" width="290" valign="top">
				<select name="calcart_id_<?=$y?>" style="width:280px" class="text" 
						id="calcart_id_<?=$y?>" onchange="updateArticle(<?=$y?>)">
					<option value=""></option>
					<?foreach ($all_article as $art){
						echo '<option value="'.$art->getId().'" ';
						echo '>'.$art->getTitle().'</option>';
					}?>
				</select>
			</td>
			<td class="content_row_clear">
				<input 	name="art_amount_<?=$y?>" id="art_amount_<?=$y?>" class="text" style="width:40px;" 
						onchange="updateArticle(<?=$y?>)">
				<?=$_LANG->get('Stk.')?>
			</td>
			<td class="content_row_clear">
				<select name="art_scale_<?=$y?>" id="art_scale_<?=$y?>" style="width:120px" class="text" 
						onchange="updateArticle(<?=$y?>)" >
					<option value="0"><?=$_LANG->get('pro Kalkulation')?></option>
					<option value="1"><?=$_LANG->get('pro St&uuml;ck')?></option>
				</select>
			</td>
			<td class="content_row_clear">&ensp;</td>
			<td class="content_row_clear" id="art_cost_<?=$y?>">
			</td>
			<td class="content_row_clear">
				<img src="images/icons/plus.png" class="pointer" onclick="addArticleRow()">
				<img src="images/icons/cross-script.png" class="pointer" onclick="deleteArticleRow(<?=$y?>)">
			</td>
		</tr>
	<?}?>
</table>
</div>

<br/><br/><br/><br/>

****/?>

<? // ---------------------------- Zus. Positionen --------------------------------------------------------------------
$all_article	= Article::getAllArticle();
$all_pos		= $calc->getPositions();
$sum_prices		= 0;
$sum_costs		= 0;
?>
<h1><?=$_LANG->get('Zus. Positionen')?></h1>
<div class="box2">
	<table width="100%" cellspacing="0" cellpadding="0">
		<colgroup>
			<col width="50px"> 
			<col width="100px">
            <col> 
			<col width="150px">
			<col width="150px">
			<col width="150px">
			<col width="150px">
			<col width="80px">
			<col width="40px">
			<col width="40px">
			<col width="40px">
			<col width="10px">
		<colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Position')?></td>
			<td class="content_row_header"><?=$_LANG->get('Suche')?></td>
			<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
			<td class="content_row_header"><?=$_LANG->get('Menge')?></td>
			<td class="content_row_header">&emsp;</td>
			<td class="content_row_header"><?=$_LANG->get('Preis')?></td>
			<td class="content_row_header"><?=$_LANG->get('Steuer')?></td>
			<td class="content_row_header"><?=$_LANG->get('Betrag')?></td>
			<td class="content_row_header">
				<span title="<?=$_LANG->get('Posiiton auf Dokumenten ausweisen');?>">
					<?=$_LANG->get('Dok. Rel.')?>
				</span>
			</td>
			<td class="content_row_header">
				<span title="<?=$_LANG->get('Preis der Position auf Dokumenten ausweisen');?>">
					<?=$_LANG->get('Preis anz.')?>
				</span>
			</td>
			<td class="content_row_header">
				<span title="<?=$_LANG->get('Auf Dokumenten ausweisen');?>">
					<?=$_LANG->get('ME anz.')?>
				</span>
			</td>
			<td class="content_row_header">&ensp;</td>
		</tr>
	<? 	$i = 0;
		if(count($all_pos) > 0){
			foreach($all_pos as $position){?>
				<tr class="<?= getRowColor($i)?>">
					<td valign="top" class="content_row"> 
						<select name="orderpos[<?=$i?>][type]" class="text" id="orderpos_type_<?=$i?>" 
								onchange="updatePos(<?=$i?>)">
							<option value="1" <?if($position->getType() == 1) echo "selected";?>><?=$_LANG->get('Manuell')?></option>
							<option value="2" <?if($position->getType() == 2) echo "selected";?>><?=$_LANG->get('Artikel')?></option>
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
						<img src="images/icons/magnifier-left.png" class="pointer icon-link" id="orderpos_searchbutton_<?=$i?>"
							 onclick="clickSearch(<?=$i?>)" <?if($position->getType() == 0) echo 'style="display:none"';?>>
					</td>
					<td valign="top" class="content_row">
						<select style="width: 440px; <?if($position->getType() == 0) echo 'display:none;';?>" 
								class="text" id="orderpos_searchlist_<?=$i?>" 
								onchange="updatePosDetails(<?=$i?>)"> 
							<?
							if($position->getType() == 2){ 
								echo '<option value=""> &lt; '.$_LANG->get('Artikel w&auml;hlen...').'&gt;</option>';
								foreach ($all_article as $article) {
									echo '<option value="'. $article->getId() .'"';
									if ($article->getId() == $position->getObjectid()) echo 'selected="selected"';
									echo'>'. $article->getNumber()." - ".$article->getTitle() .'</option>';
								}
							}
							?>
						</select>
						<textarea name="orderpos[<?=$i?>][comment]" class="text" id="orderpos_comment_<?=$i?>"
								  style="width: 440px; height: 100px"><?=$position->getComment()?></textarea>
					</td>
					<td valign="top" class="content_row">
						<input 	name="orderpos[<?=$i?>][quantity]" id="orderpos_quantity_<?=$i?>"
								value="<?= $position->getQuantity()?>" class="text" style="width: 60px" 
								onfocus="markfield(this,0)" onblur="markfield(this,1)"">
						<?=$_LANG->get('Stk.')?> <br/>
						&ensp;&ensp;&ensp;
						<img src="images/icons/arrow-circle-double-135.png" class="pointer icon-link" id="orderpos_uptpricebutton_<?=$i?>"
							 onclick="updateArticlePrice(<?=$i?>)" title="<?=$_LANG->get('Staffelpreis aktualisieren')?>"
							 <?if($position->getType() != 2) echo 'style="display:none"';?>>
					</td>
					<td valign="top" class="content_row">
						<select name="orderpos[<?=$i?>][scale]" id="orderpos_scale_<?=$i?>" style="width:100px" class="text" 
								onchange="updateArticlePrice(<?=$i?>)" >
							<option value="<?=CalculationPosition::SCALE_PER_KALKULATION?>" 
									<?if($position->getScale() == CalculationPosition::SCALE_PER_KALKULATION) echo 'selected="selected"'?>
									><?=$_LANG->get('pro Kalkulation')?></option>
							<option value="<?=CalculationPosition::SCALE_PER_PIECE?>" 
									<?if($position->getScale() == CalculationPosition::SCALE_PER_PIECE) echo 'selected="selected"'?>
									><?=$_LANG->get('pro St&uuml;ck')?></option>
						</select>
					</td>
					<td class="content_row" align="right" valign="top">
						<? /*<input type="hidden" name="orderpos[<?=$i?>][scale]" id="orderpos_scale_<?=$i?>" 
								value="<?=CalculationPosition::SCALE_PER_PIECE?>">*/?>
						<?=$_LANG->get('VK:');?> <br> &emsp; <br>
						<?=$_LANG->get('EK:');?>
					</td>
					<td valign="top" class="content_row">
						<input 	name="orderpos[<?=$i?>][price]" id="orderpos_price_<?=$i?>" class="text"
								value="<?= printPrice($position->getPrice())?>" style="width: 60px" 
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<?=$_USER->getClient()->getCurrency()?> <br>
						<input 	name="orderpos[<?=$i?>][cost]" id="orderpos_cost_<?=$i?>" class="text"
								value="<?= printPrice($position->getCost())?>" style="width:60px;margin-top:10px; 
								onfocus="markfield(this,0)" onblur="markfield(this,1)"> 
						<?=$_USER->getClient()->getCurrency()?>
					</td>
					<td valign="top" class="content_row">
						<input 	name="orderpos[<?=$i?>][tax]" class="text" id="orderpos_tax_<?=$i?>"
								value="<?= printPrice($position->getTax())?>" style="width: 60px;" > %
					</td>
					<td valign="top" class="content_row" id="td_totalprice_<?=$i?>">
						<?= printPrice($position->getCalculatedPrice())." ". $_USER->getClient()->getCurrency()?>
					</td>
					<td valign="top" class="content_row">
						<input type="checkbox" value="1" name="orderpos[<?=$i?>][inv_rel]" title="<?=$_LANG->get('Position auf den Dokumenten ausweisen');?>"
								<?if ($position->getInvrel() == 1) echo 'checked="checked"';?>>
					</td>
					<td valign="top" class="content_row">
						<input type="checkbox" value="1" name="orderpos[<?=$i?>][show_doc_price]" 
								title="<?=$_LANG->get('Preis der Position auf den Dokumenten ausweisen');?>"
								<?if ($position->getShowPrice() == 1) echo 'checked="checked"';?>>
					</td>
					<td valign="top" class="content_row">
						<input type="checkbox" value="1" name="orderpos[<?=$i?>][show_doc_quantity]" 
								title="<?=$_LANG->get('Menge der Position auf den Dokumenten ausweisen');?>"
								<?if ($position->getShowQuantity() == 1) echo 'checked="checked"';?>>
					</td>
					<td valign="top" class="content_row">
						<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$order->getId()?>&step=3&subexec=deletepos&calc_id=<?=$calc->getId()?>&delpos=<?=$position->getId()?>">
							<img src="images/icons/cross-script.png" title="<?= $_LANG->get('Position l&ouml;schen')?>"></a>
					</td>
				</tr>
			<?
			$i++;
			$sum_costs += $position->getCalculatedCosts();
			$sum_prices += $position->getCalculatedPrice();
			} //ende FOREACH
		}?>
			<tr class="<?= getRowColor($i)?>">
				<td valign="top" class="content_row">
					<select name="orderpos[<?=$i?>][type]" class="text" id="orderpos_type_<?=$i?>" 
							onchange="updatePos(<?=$i?>)">
						<option value="1" ><?=$_LANG->get('Manuell')?></option>
						<option value="2" ><?=$_LANG->get('Artikel')?></option>
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
					<img src="images/icons/arrow-circle-double-135.png" class="pointer icon-link" id="orderpos_uptpricebutton_<?=$i?>"
						 onclick="updateArticlePrice(<?=$i?>)" style="display:none"
						 title="<?=$_LANG->get('Staffelpreis aktualisieren')?>">
				</td>
				<td valign="top" class="content_row">
						<select name="orderpos[<?=$i?>][scale]" id="orderpos_scale_<?=$i?>" style="width:100px" class="text" 
								onchange="updateArticlePrice(<?=$i?>)" >
							<option value="<?=CalculationPosition::SCALE_PER_KALKULATION?>"><?=$_LANG->get('pro Kalkulation')?></option>
							<option value="<?=CalculationPosition::SCALE_PER_PIECE?>"><?=$_LANG->get('pro St&uuml;ck')?></option>
						</select>
					</td>
				<td class="content_row" align="right" valign="top">
					<?=$_LANG->get('VK:');?> <br> &emsp; <br>
					<?=$_LANG->get('EK:');?>
				</td>
				<td valign="top" class="content_row">
					<input 	name="orderpos[<?=$i?>][price]" id="orderpos_price_<?=$i?>" value="" 
							style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<?= $_USER->getClient()->getCurrency()?><br>
					
					<input 	name="orderpos[<?=$i?>][cost]" id="orderpos_cost_<?=$i?>" value="" 
							style="width:60px;margin-top:10px;" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<?= $_USER->getClient()->getCurrency()?>
				</td>
				<td valign="top" class="content_row">
					<input 	name="orderpos[<?=$i?>][tax]" id="orderpos_tax_<?=$i?>" value="<?= printPrice('19.0')?>" 
							title="<?=$_LANG->get('Auf Dokumenten ausweisen');?>" style="width: 60px" 
							onfocus="markfield(this,0)" onblur="markfield(this,1)"> %
				</td>
				<td valign="top" class="content_row" id="td_totalprice_<?=$i?>">
					&ensp;
				</td>
				<td valign="top" class="content_row">
					<input type="checkbox" checked value="1" name="orderpos[<?=$i?>][inv_rel]"
						title="<?=$_LANG->get('Position auf den Dokumenten ausweisen');?>">				
				</td>
				<td valign="top" class="content_row">
					<input type="checkbox" value="1" name="orderpos[<?=$i?>][show_doc_price]" 
							title="<?=$_LANG->get('Preis der Position auf den Dokumenten ausweisen');?>">
				</td>
				<td valign="top" class="content_row">
					<input type="checkbox" value="1" name="orderpos[<?=$i?>][show_doc_quantity]" 
							title="<?=$_LANG->get('Menge der Position auf den Dokumenten ausweisen');?>">
				</td>
				<td class="content_row">
					&ensp;
				</td>
			</tr>
			<tr>
				<td colspan="3"> &ensp;	</td>
				<td colspan="2" align="right">
					<b><?=$_LANG->get('Erl&ouml;s')?>:</b> <?=printPrice(($sum_prices-$sum_costs), 2);?> 
					<?= $_USER->getClient()->getCurrency()?>
					&emsp; &emsp;
				</td>
				<td align="right">
					<b><?=$_LANG->get('Summe EK')?>:</b> <?=printPrice($sum_costs, 2);?> 
					<?= $_USER->getClient()->getCurrency()?>
				</td>
				<td align="right" >
				<b><?=$_LANG->get('Summe VK')?>:</b>
				</td>
				<td colspan="3">
					&ensp;
					 <?=printPrice($sum_prices, 2);?> 
					<?= $_USER->getClient()->getCurrency()?>
				</td>
			</tr>
	</table>
</div>	
<br/><br/><br/>


<input type="hidden" name="counter_machs" id="counter_machs" value="<?=$x?>">
<table width="100%">
    <tr>
        <td class="content_row_clear">
            <input type="checkbox" name="auto_calc_values" value="1" <?if($calc->getCalcAutoValues()) echo "checked";?>> 
            * <?=$_LANG->get('Werte automatisch kalkulieren')?>
        </td>
        <td align="right">
            <input type="button" class="button" value="<?=$_LANG->get('Zur&uuml;ck')?>"
                onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&step=2&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?><? 
                if($_REQUEST["addorder_amount"])
                    foreach ($_REQUEST["addorder_amount"] as $amount)
                    echo '&addorder_amount[]='.$amount;
                ?>'">
            <input type="button" class="button" value="<?= $_LANG->get('Weiter') ?>"
                   onclick="document.getElementsByName('nextstep')[0].value='4';document.step3_form.submit();">
            <input type="submit" value="<?=$_LANG->get('Speichern')?>">
        </td>
    </tr>
</table>
</form>

<script>
//config
$float_speed=1500; //milliseconds
$float_easing="easeOutQuint";
$menu_fade_speed=500; //milliseconds
$closed_menu_opacity=0.75;

//cache vars
$fl_menu=$("#fl_menu");
$fl_menu_menu=$("#fl_menu .menu");
$fl_menu_label=$("#fl_menu .label");

$(window).load(function() {
	menuPosition=$('#fl_menu').position().top;
	FloatMenu();
	$fl_menu.hover(
		function(){ //mouse over
// 			$fl_menu_label.fadeTo($menu_fade_speed, 1);
// 			$fl_menu_menu.fadeIn($menu_fade_speed);
		},
		function(){ //mouse out
// 			$fl_menu_label.fadeTo($menu_fade_speed, $closed_menu_opacity);
// 			$fl_menu_menu.fadeOut($menu_fade_speed);
		}
	);
});

$(window).scroll(function () { 
	FloatMenu();
});

function FloatMenu(){
	var scrollAmount=$(document).scrollTop();
	var newPosition=menuPosition+scrollAmount;
	if($(window).height()<$fl_menu.height()+$fl_menu_menu.height()){
		$fl_menu.css("top",menuPosition);
	} else {
		$fl_menu.stop().animate({top: newPosition}, $float_speed, $float_easing);
	}
}
</script>
