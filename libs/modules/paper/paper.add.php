<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
$paper = new Paper($_REQUEST["id"]);
$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_SUPP);

if(isset($_GET['returnTo']) && $_GET['cloneProductId']) {
   /*
  	* ds / 08.05.2014
	* This GET param 'returnTo' is set via order.step2.php. It contains the URL for the redirection
	* if a new paper type was created completely (Paper::hasPriceBase() )
	* If this param is set, the new paper type will be assigned to a cloned version of
	* the product. (@todo: store product id in session?`)
	*
	* Keep in mind that this get param will be given only for te first call, so we have
	* to store this information in a session.
	*
	*
	* 'cloneProductId' contains the product id which has to be cloned later. We have to store this
	* id in a session, too.
	*
	*/
	$_SESSION['_alternativePaperMode'] = array(
			'alternativePaperModeReturnUrl' => $_GET['returnTo'],
			'alternativePaperModeProductId' => $_GET['cloneProductId'],
	);
}
$alternativePaperMode = (isset($_SESSION['_alternativePaperMode']) && is_array($_SESSION['_alternativePaperMode']));


if($_REQUEST["exec"] == "copy")
    $paper->clearId();

if($_REQUEST["subexec"] == "save")
{
//	echo "Papierpreis in €: " . $_REQUEST['paper_100kg'] . "</br>";
    $sizes = Array();
    $weights = Array();
    $prices = Array();
	$supplier = Array();
    foreach(array_keys($_REQUEST) as $key)
    {
        if(preg_match("/paper_size_width_(?P<id>\d+)/", $key, $match))
        {
            if(trim($_REQUEST["paper_size_width_{$match["id"]}"]) != "" && trim($_REQUEST["paper_size_height_{$match["id"]}"]) != "")
            {
                $t["width"] = trim($_REQUEST["paper_size_width_{$match["id"]}"]);
                $t["height"] = trim($_REQUEST["paper_size_height_{$match["id"]}"]);
                $sizes[] = $t;
            }
        }
        
        if(preg_match("/paper_weight_(?P<id>\d+)/", $key, $match))
        {
            if(trim($_REQUEST["paper_weight_{$match["id"]}"]) != "")
                $weights[] = trim($_REQUEST["paper_weight_{$match["id"]}"]);
        }

        if(preg_match("/price_quantity_from_(?P<id>\d+)/", $key, $match))
        {
            $t["size_width"] = (int)$_REQUEST["price_size_width_{$match["id"]}"]; 
            $t["size_height"] = (int)$_REQUEST["price_size_height_{$match["id"]}"];
            $t["weight_from"] = (int)$_REQUEST["price_weight_from_{$match["id"]}"];
            $t["weight_to"] = (int)$_REQUEST["price_weight_to_{$match["id"]}"];
            $t["quantity_from"] = (int)$_REQUEST["price_quantity_from_{$match["id"]}"];
            $t["price"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["price_{$match["id"]}"])));
            if ($t["weight_from"] && $t["weight_to"] && $t["price"])
                $prices[] = $t;
        }
        
        if(preg_match("/priceperthousand_(?P<width>\d+)x(?P<height>\d+)_(?P<weight>\d+)_(?P<id>\d+)/", $key, $match))
        {
            $t["size_width"] = (int)$match["width"];
            $t["size_height"] = (int)$match["height"];
            $t["weight_from"] = (int)$match["weight"];
            $t["weight_to"] = (int)$match["weight"];
            $t["quantity_from"] = (int)$_REQUEST["quantity_{$t["size_width"]}x{$t["size_height"]}_{$t["weight_from"]}_{$match["id"]}"];
            $t["weight"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["kgperthousand_{$t["size_width"]}x{$t["size_height"]}_{$t["weight_from"]}_{$match["id"]}"])));
            $t["price"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST[$key])));
			if($_REQUEST['paper_100kg']){
				$t["price"] = ((($t["size_width"] * $t["size_height"] / 1000) * ($match["weight"]))*(($_REQUEST['paper_100kg'] / 100) * ($t["quantity_from"])) / 1000);
			}
			if ($_REQUEST['paper_1qm'])
			{
				$t["price"] = ((($t["size_width"] * $t["size_height"] / 1000) * ($match["weight"]))*(($_REQUEST['paper_1qm'] / 100) * ($t["quantity_from"])) / 1000);
			}
			//             var_dump($t);
            if ($t["weight_from"] && $t["weight_to"] && $t["price"])
                $prices[] = $t;
        }
    }
	
	for ($i = 0; $i < $_REQUEST["supplier_counter"]; $i++) {
		if ($_REQUEST["supplier_".$i] != "") {
			if (count($supplier) > 0 && !in_array($_REQUEST["supplier_".$i],$supplier)) {
				$supplier[] = Array('id'=>$_REQUEST["supplier_".$i],'descr'=>$_REQUEST["supplier_descr_".$i]);
			}
			else if (count($supplier) == 0) {
                $supplier[] = Array('id'=>$_REQUEST["supplier_".$i],'descr'=>$_REQUEST["supplier_descr_".$i]);
			}
		}
	}
    $paper->setPrices($prices);
	$paper->setSupplier($supplier);
    
    if($paper->getPriceBase() != (int)$_REQUEST["paper_pricebase"])
        $paper->setPrices(Array());
    
    $paper->setName(trim(addslashes($_REQUEST["paper_name"])));
    $paper->setComment(trim(addslashes($_REQUEST["paper_comment"])));
    $paper->setPriceBase((int)$_REQUEST["paper_pricebase"]);
    $paper->setDilivermat(trim(addslashes($_REQUEST["paper_dilivermat"])));
    $paper->setGlue(trim(addslashes($_REQUEST["paper_glue"])));
    $paper->setThickness(trim(addslashes($_REQUEST["paper_thickness"])));
    $paper->setTotalweight(trim(addslashes($_REQUEST["paper_totalweight"])));
    $paper->setPrice_100kg(trim(addslashes($_REQUEST["paper_100kg"])));
    $paper->setPrice_1qm(trim(addslashes($_REQUEST["paper_1qm"])));
    $paper->setVolume(trim(addslashes($_REQUEST["paper_volume"])));
    $paper->setRolle(trim(addslashes($_REQUEST["paper_rolle"])));
    
    $paper->setWeights($weights);
    $paper->setSizes($sizes);
    $savemsg = getSaveMessage($paper->save());
}
?>
<table width="100%">
   <tr>
      <td width="200" class="content_header">
          <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
          <?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Papier hinzuf&uuml;gen')?>
          <?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Papier &auml;ndern')?>
          <?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Papier kopieren')?>
      </td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<script language="javascript">
message = '<?=$_LANG->get('Achtung \n\nDurch Aendern der Preisbasis werden alle Preise geloescht!')?>';
function warnPBChange(val)
{
	alert('message');
}

function removeOption(what, id)
{
	if(what == 'size')
	{
		document.getElementById('paper_size_width_'+id).disabled = true;
	    document.getElementById('paper_size_height_'+id).disabled = true;
	    document.getElementById('paper_size_'+id).style.display = 'none';
	} else if(what == 'weight')
	{
	    document.getElementById('paper_weight_'+id).disabled = true;
	    document.getElementById('sp_paper_weight_'+id).style.display = 'none';
	} else if(what == 'supplier')
	{
		var element = document.getElementById('supplier_tr_'+id);
	    element.parentNode.removeChild(element);
		var count = parseInt(document.getElementById('supplier_counter').value);
		var count = count -1;
		document.getElementById('supplier_counter').value = count;
	}
}

function addSizeField()
{
	obj = document.getElementById('span-size');
	var count = parseInt(document.getElementById('count_size').value);

    var insert = '<span id="paper_size_'+count+'">';
	insert += '<input name="paper_size_width_'+count+'" id="paper_size_width_'+count+'" class="text" style="width:40px"> x';
	insert += ' <input name="paper_size_height_'+count+'" id="paper_size_height_'+count+'" class="text" style="width:40px">';
	insert += '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeOption(\'size\', '+count+')">&nbsp;&nbsp;&nbsp;</span>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_size').value = count + 1;
}

function addWeightField()
{
	obj = document.getElementById('span-weight');
	var count = parseInt(document.getElementById('count_weight').value);

	var insert = '<span id="sp_paper_weight_'+count+'"><input name="paper_weight_'+count+'" id="paper_weight_'+count+'" class="text" style="width:40px"> g';
	insert += '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeOption(\'weight\', '+count+')">&nbsp;&nbsp;&nbsp;</span>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_weight').value = count + 1;
}

<? if($paper->getPriceBase() == Paper::PRICE_PER_100KG) { ?>
function addPriceRow()
{
	var obj = document.getElementById('table-prices');
	var count = parseInt(document.getElementById('price_counter').value);
	var lastcount = count - 1;
	
	var weight_from = parseInt(document.getElementById('price_weight_from_'+lastcount).value);
	var weight_to = parseInt(document.getElementById('price_weight_to_'+lastcount).value);
	
    var insert = '<tr><td class="content_row_clear">';
    insert += 'Von <input name="price_weight_from_'+count+'" id="price_weight_from_'+count+'" class="text" style="width:40px" value="'+weight_from+'"> g'; 
    insert += ' bis <input name="price_weight_to_'+count+'" id="price_weight_to_'+count+'" class="text" style="width:40px" value="'+weight_to+'"> g';
    insert += '</td><td class="content_row_clear">';
    insert += 'ab <input name="price_quantity_from_'+count+'" id="price_quantity_from_'+count+'" class="text" style="width:40px" value=""> kg';
    insert += ' <input name="price_'+count+'" id="price_'+count+'" class="text" style="width:60px;text-align:right" value="">';
    insert += ' <?=$_USER->getClient()->getCurrency()?></td></tr>';
    obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('price_counter').value = count + 1;
	document.getElementById('price_quantity_from_'+count).focus();
}
<?  } else { ?>
function addPriceRow(config, qty)
{
	var obj = document.getElementById('price_'+config+'_'+qty);
	var count = parseInt(document.getElementById('count_quantity_'+config).value);
	var insert = '<tr><td class="content_row_clear">&nbsp;</td><td class="content_row_clear">&nbsp;</td>';
	insert += '<td class="content_row_clear"><?=$_LANG->get('Ab')?> <input name="quantity_'+config+'_'+count+'" ';
	insert += 'class="text" style="width:60px;text-align:right;" value="1"> <?=$_LANG->get('Bogen')?></td>';
	insert += '<td class="content_row_clear"><?=$_LANG->get('Preis pro 1000 Bogen')?> ';
	insert += ' &nbsp;&nbsp;<input name="priceperthousand_'+config+'_'+count+'" class="text"'; 
    insert += 'style="width:60px;text-align:right;" value="1"> <?=$_USER->getClient()->getCurrency()?>';
	insert += '</td>';
	// insert += '<td class="content_row_clear"><?=$_LANG->get('KG pro 1000 Bogen')?> ';
	// insert += ' &nbsp;&nbsp;<input name="kgperthousand_'+config+'_'+count+'" class="text"'; 
    // insert += 'style="width:60px;text-align:right;" value="">';
	// insert += '</td>';
	insert += '</tr>';
	obj.insertAdjacentHTML("AfterEnd", insert);
}
<?  } ?>

function prepareProductCloning() {
	var newProductName = window.prompt("Das Produkt wird für diese neue Papiersorte kopiert. Bitte geben Sie einen neuen Namen für das Produkt an.", "Neues Produkt"),
		redirectTo = "<?= $_SESSION['_alternativePaperMode']['alternativePaperModeReturnUrl'] ?>&newProductName=" + encodeURIComponent(newProductName) + "&paperId=<?= $paper->getId()?>";

	window.location.href = redirectTo;
		

}

function addSupplierRow()
{
	var obj = document.getElementById('table-supplier');
	var count = parseInt(document.getElementById('supplier_counter').value);
	
    var insert = '<tr id="supplier_tr_'+count+'"><td class="content_row_clear">';
	insert += '<select id="supplier_'+count+'" name="supplier_'+count+'" style="width:370px" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">';
	insert += '<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>';
	insert += '<? 	foreach ($allcustomer as $cust){?>';
	insert += '<option value="<?=$cust->getId()?>"><?=str_replace("'", "\'", $cust->getNameAsLine())?></option>';
	insert += '<?	} //Ende ?>';
    insert += '</select>Papierbez. b. Lief.: <input name="supplier_descr_'+count+'" id="supplier_descr_'+count+'" value="" style="width: 500px;">';
	insert += '<img src="images/icons/cross-white.png" class="pointer" onclick="removeOption("supplier", '+count+')"></td></tr>';


	
    obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('supplier_counter').value = count + 1;
	document.getElementById('supplier_'+count).focus();
}
</script>

	    	<?php if($paper->hasPriceBase() && $alternativePaperMode) : ?>
		        <td class="content_row_header">
		        	<input type="button" value="<?= $_LANG->get('&laquo; Zur&uuml;ck zur Auftragsbearbeitung') ?>" class="button" 
		        		onclick="prepareProductCloning()" >
		        </td>
	        <?php else : ?>
		    	<td class="content_row_header">
	    	    	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
			        	onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
		        </td>
	        <?php endif; ?>



<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Speichern','#',"$('#paper_form').submit();",'glyphicon-floppy-disk');
if($paper->hasPriceBase() && $alternativePaperMode) {
    $quickmove->addItem('Zurück zur Auftragsbearbeitung','#',"prepareProductCloning()",'glyphicon-step-backward');
}
else{
    $quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="paper_form" name="paper_form" onSubmit="return checkform(new Array(this.paper_name))">
<input name="exec" value="edit" type="hidden">
<input name="subexec" value="save" type="hidden">
<input name="id" value="<?=$paper->getId()?>" type="hidden">
<input name="count_weight" id="count_weight" type="hidden" value="<?=count($paper->getWeights())?>">
<input name="count_size" id="count_size" type="hidden" value="<?=count($paper->getSizes())?>">
<div class="box1">
<table width="100%">
    <colgroup>
        <col width="15%">
        <col width="30%">
		<col width="15%">
		<col width="40%">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Name')?> *</td>
        <td class="content_row_clear">
            <input name="paper_name" value="<?=$paper->getName()?>" class="text" style="width:300px">
        </td>   
    </tr>
    
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Preisbasis')?></td>
        <td class="content_row_clear">	
		
	
<!--		Auskommentierter Radio Button

		  <?=$_LANG->get('Preis')?> 
<?php /*            <input name="paper_pricebase" value="<?=Paper::PRICE_PER_100KG?>" type="radio" id="paper_pricebase"
                <?if($paper->getPriceBase() == Paper::PRICE_PER_100KG) echo "checked"?> onchange="warnPBChange(<?=Paper::PRICE_PER_100KG?>)"> 
            <?=$_LANG->get('pro')?> 100kg */ ?>
            <input name="paper_pricebase" value="<?=Paper::PRICE_PER_THOUSAND?>" type="radio" id="paper_pricebase"
                <?if($paper->getPriceBase() == Paper::PRICE_PER_THOUSAND) echo "checked"?> onchange="warnPBChange(<?=Paper::PRICE_PER_THOUSAND?>)">--> 
				
				
            <?=$_LANG->get('pro')?> 1.000 <?=$_LANG->get('Bogen')?>
        </td>   
    </tr>
    
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Trägermaterial')?> *</td>
        <td class="content_row_clear">
            <input name="paper_dilivermat" value="<?=$paper->getDilivermat()?>" class="text" style="width:300px">
        </td>   
        <td class="content_row_header"><?=$_LANG->get('Kleber')?> *</td>
        <td class="content_row_clear">
            <input name="paper_glue" value="<?=$paper->getGlue()?>" class="text" style="width:300px">
        </td>  
    </tr>
    
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Dicke gesamt in µm')?> *</td>
        <td class="content_row_clear">
            <input name="paper_thickness" value="<?=$paper->getThickness()?>" class="text" style="width:300px">
        </td>   
        <td class="content_row_header"><?=$_LANG->get('Gesamtgewicht in g/m2')?> *</td>
        <td class="content_row_clear">
            <input name="paper_totalweight" value="<?=$paper->getTotalweight()?>" class="text" style="width:300px">
        </td>  
    </tr>
	
	<tr>
		<!--<td class="content_row_header" valign="top">Beschreibung</td>
			<td><textarea name="paper_comment" style="width:300px" class="text"><?=$paper->getComment()?></textarea></td>-->
			<td class="content_row_header"><?=$_LANG->get('Verf&uuml;gbare Gr&ouml;&szlig;en')?> <?=$_LANG->get('(BxH)')?></td>
        <td class="content_row_clear" id="td-size">
            <span id="span-size">
            <? $i = 0; foreach ($paper->getSizes() as $s) {?>
                <span id="paper_size_<?=$i?>">
                    Breite <input name="paper_size_width_<?=$i?>" id="paper_size_width_<?=$i?>" class="text" style="width:40px" value="<?=$s["width"]?>"> mm 
                    Höhe <input name="paper_size_height_<?=$i?>" id="paper_size_height_<?=$i?>" class="text" style="width:40px" value="<?=$s["height"]?>"> mm 
                    <img src="images/icons/minus.png" class="pointer icon-link" onclick="removeOption('size', <?=$i?>)">&nbsp;&nbsp;&nbsp; <br>
                </span>
            <? $i++; } ?>                
            </span>
            <img src="images/icons/plus.png" class="pointer" onclick="addSizeField()">
        </td>
    
        <td class="content_row_header"><?=$_LANG->get('Verf&uuml;gbare Grammaturen')?></td>
        <td class="content_row_clear" id="td-weight">
            <span id="span-weight">
            <? $i = 0; foreach ($paper->getWeights() as $w) {?>
                <span id="sp_paper_weight_<?=$i?>"><input name="paper_weight_<?=$i?>" id="paper_weight_<?=$i?>" class="text" style="width:40px" value="<?=$w?>"> g
                <img src="images/icons/minus.png" class="pointer icon-link" onclick="removeOption('weight', <?=$i?>)">&nbsp;&nbsp;&nbsp;</span> <br>
            <? $i++; } ?>
            </span>
            <img src="images/icons/plus.png" class="pointer icon-link" onclick="addWeightField()">
        </td>
		</tr>
		<tr>		
			<td class="content_row_header"><?=$_LANG->get('100Kg-Preis')?>
			<td class="content_row_clear">
				<input name="paper_100kg" value="<?php echo $paper->getPrice_100kg();?>" class="text" style="width:300px"><br>
				<class="content_row_header"><?=$_LANG->get('* Überschreibt alle 1000 Bogenpreise "Ab 1 Bogen"  !!!')?> 
			</td>
		</tr> 
		<tr>		
			<td class="content_row_header"><?=$_LANG->get('1qm-Preis')?>
			<td class="content_row_clear">
				<input name="paper_1qm" value="<?php echo $paper->getPrice_1qm();?>" class="text" style="width:300px"><br>
				<class="content_row_header"><?=$_LANG->get('* Überschreibt alle 1000 Bogenpreise "Ab 1 Bogen"  !!!')?> 
			</td>
		</tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Volumen')?>
        <td class="content_row_clear">
            <input name="paper_volume" value="<?php echo $paper->getVolume();?>" class="text" style="width:300px">
        </td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Rolle')?>
        <td class="content_row_clear">
            <input name="paper_rolle" value="1" <?php if ($paper->getRolle()) echo ' checked ';?> type="checkbox">
        </td>
    </tr>
    <!--<tr>
        <td class="content_row_header"><?=$_LANG->get('Verf&uuml;gbare Gr&ouml;&szlig;en')?> <?=$_LANG->get('(BxH)')?></td>
        <td class="content_row_clear" id="td-size">
            <span id="span-size">
            <? $i = 0; foreach ($paper->getSizes() as $s) {?>
                <span id="paper_size_<?=$i?>">
                    Breite <input name="paper_size_width_<?=$i?>" id="paper_size_width_<?=$i?>" class="text" style="width:40px" value="<?=$s["width"]?>"> mm 
                    Höhe <input name="paper_size_height_<?=$i?>" id="paper_size_height_<?=$i?>" class="text" style="width:40px" value="<?=$s["height"]?>"> mm 
                    <img src="images/icons/minus.png" class="pointer icon-link" onclick="removeOption('size', <?=$i?>)">&nbsp;&nbsp;&nbsp; <br>
                </span>
            <? $i++; } ?>                
            </span>
            <img src="images/icons/plus.png" class="pointer" onclick="addSizeField()">
        </td>
    
        <td class="content_row_header"><?=$_LANG->get('Verf&uuml;gbare Grammaturen')?></td>
        <td class="content_row_clear" id="td-weight">
            <span id="span-weight">
            <? $i = 0; foreach ($paper->getWeights() as $w) {?>
                <span id="sp_paper_weight_<?=$i?>"><input name="paper_weight_<?=$i?>" id="paper_weight_<?=$i?>" class="text" style="width:40px" value="<?=$w?>"> g
                <img src="images/icons/minus.png" class="pointer icon-link" onclick="removeOption('weight', <?=$i?>)">&nbsp;&nbsp;&nbsp;</span> <br>
            <? $i++; } ?>
            </span>
            <img src="images/icons/plus.png" class="pointer icon-link" onclick="addWeightField()">
        </td>
       
	
		
	
          
    </tr>-->
		</td>
	</tr>
</table>
</div>
</br>
<h1><?=$_LANG->get('Lieferanten')?></h1>
<div class="box2">
<input type="hidden" name="supplier_counter" id="supplier_counter" value="<? if(count($paper->getSupplier()) > 0) echo count($paper->getSupplier()); else echo "0";?>">
<table width="100%" id="table-supplier">
    <colgroup>
        <col width="250">
        <col>
    </colgroup>
    <? $i = 0; foreach($paper->getSupplier() as $s) { 
	$tmp_supplier = new BusinessContact($s['id']);
	?>
    <tr id="supplier_tr_<?=$i?>">
        <td class="content_row_clear">
			<select id="supplier_<?=$i?>" name="supplier_<?=$i?>" style="width:370px" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
				<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
			<? 	foreach ($allcustomer as $cust){?>
					<option value="<?=$cust->getId()?>"
						<?if ($tmp_supplier->getId() == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine()?></option>
			<?	} //Ende ?>
			</select>
            Papierbez. b. Lief.: <input name="supplier_descr_<?=$i?>" id="supplier_descr_<?=$i?>" value="<?php echo $s['descr'];?>" style="width: 500px;">
			<img src="images/icons/cross-white.png" class="pointer" onclick="removeOption('supplier', <?=$i?>)">
        </td>
    </tr>
    <?  $i++; } ?>	
	<tr>
		<td><img src="images/icons/plus.png" class="pointer" onclick="addSupplierRow()"></td>
	</tr>
</table>
</div>
<br>
<h1><?=$_LANG->get('Preise')?></h1>
<div class="box2">
<? if($paper->getPriceBase() == Paper::PRICE_PER_100KG) { ?>
<input type="hidden" name="price_counter" id="price_counter" value="<? if(count($paper->getPrices()) > 0) echo count($paper->getPrices()); else echo "1";?>">
<table width="100%" id="table-prices">
    <colgroup>
        <col width="250">
        <col>
    </colgroup>
    <? $i = 0; foreach($paper->getPrices() as $p) { ?>
    <tr>
        <td class="content_row_clear">
            <?=$_LANG->get('Von')?> <input name="price_weight_from_<?=$i?>" id="price_weight_from_<?=$i?>" class="text" style="width:40px" value="<?=$p["weight_from"]?>"> g 
            <?=$_LANG->get('bis')?> <input name="price_weight_to_<?=$i?>" id="price_weight_to_<?=$i?>" class="text" style="width:40px" value="<?=$p["weight_to"]?>"> g
        </td>
        <td class="content_row_clear">
            <?=$_LANG->get('ab')?> <input name="price_quantity_from_<?=$i?>" id="price_quantity_from_<?=$i?>" class="text" style="width:40px" value="<?=$p["quantity_from"]?>"> kg
            <input name="price_<?=$i?>" id="price_<?=$i?>" class="text" style="width:60px;text-align:right" value="<?=printPrice($p["price"])?>">
            <?=$_USER->getClient()->getCurrency()?>
            <? if($i == count($paper->getPrices())-1)
                    echo '&nbsp;&nbsp;&nbsp;<img src="images/icons/plus.png" class="pointer" onclick="addPriceRow()">'; ?>
        </td>
    </tr>
    <?  $i++; }
    if(count($paper->getPrices())== 0) { ?>
    <tr>
        <td class="content_row_clear">
            Von <input name="price_weight_from_0" id="price_weight_from_0" class="text" style="width:40px"> g 
            bis <input name="price_weight_to_0" id="price_weight_to_0" class="text" style="width:40px"> g
        </td>
        <td class="content_row_clear">
            ab <input name="price_quantity_from_0" id="price_quantity_from_0" class="text" style="width:40px"> kg
            <input name="price_0" id="price_0" class="text" style="width:60px;text-align:right">
            <?=$_USER->getClient()->getCurrency()?>
            &nbsp;&nbsp;&nbsp;<img src="images/icons/plus.png" class="pointer icon-link" onclick="addPriceRow()">
        </td>
    </tr>
    
    <? } ?>
</table>
<?  } else { ?>
<table width="100%">
    <colgroup>
        <col width="80">
        <col width="80">
        <col width="230">
        <col width="230">
        <col>
    </colgroup>
    <? foreach ($paper->getSizes() as $s) { 
        $firstSize = true; 
        foreach ($paper->getWeights() as $w)
        {
            $firstWeight = true;
            $paper_prices = $paper->getPrices();
            $i = 0;
            $prices = Array();
            while($i < count($paper_prices))
            {
                if($paper_prices[$i]["size_width"] == $s["width"] && 
                        $paper_prices[$i]["size_height"] == $s["height"] && 
                        $paper_prices[$i]["weight_from"] == $w)
                {
                    $prices[] = $paper_prices[$i];
                }
                $i++;
            }
            
            // Falls noch keine Preise existieren, Preise vorgaukeln
            if(count($prices) == 0)
                $prices[] = Array("size_width" => $s["width"], "size_height" => $s["height"]);

            $x = 0;
            foreach($prices as $price)
            {
            
                echo '<tr id="price_'.$s["width"].'x'.$s["height"].'_'.$w.'_'.$price["quantity_from"].'">
                    <td class="content_row_clear">';
                
                if ($firstSize)
                    echo $s["width"]." x ".$s["height"];
                else
                    echo "&nbsp;";
                $firstSize = false;
                echo '<td class="content_row_clear">';
                
                if ($firstWeight)
                    echo $w.' g';
                else 
                    echo '&nbsp;';
                
                if ($price["quantity_from"]<=0)
                    $price["quantity_from"] = 1;
                if ($price["price"]<=0)
                    $price["price"] = 1;
                
                echo '<td class="content_row_clear">
                        <input name="count_quantity_'.$s["width"].'x'.$s["height"].'_'.$w.'" value="'.count($prices).'" type="hidden" id="count_quantity_'.$s["width"].'x'.$s["height"].'_'.$w.'">
                        '.$_LANG->get('Ab').' <input name="quantity_'.$s["width"].'x'.$s["height"].'_'.$w.'_'.$x.'" class="text"
                            style="width:60px;text-align:right;" value="'.$price["quantity_from"].'"> '.$_LANG->get('Bogen').'
                      </td>';
                
                echo '</td>
                    <td class="content_row_clear">';
                
                echo $_LANG->get('Preis pro 1000 Bogen')." ";
                echo '&nbsp;&nbsp;<input name="priceperthousand_'.$s["width"].'x'.$s["height"].'_'.$w.'_'.$x.'" class="text" 
                        style="width:60px;text-align:right;" value="'.printPrice($price["price"]).'">
                        '.$_USER->getClient()->getCurrency().'</td>';

                echo '</td>
                    <td class="content_row_clear">';
		
        
            /*<input name="paper_100kg" value="" class="text" style="width:300px"><br>
			<class="content_row_header"><?=$_LANG->get('* Überschreibt alle 1000 Bogenpreise "Ab 1 Bogen"  !!!')?> 
        </td>*/				

				/*echo $_LANG->get('100KG Preis manuell')." ";
                echo '&nbsp;&nbsp;<input name="pricepertousand_'.$s["width"].'x'.$s["height"].'_'.$w.'_'.$x.'" class="text"
                                    style="width:60px;text-align:right;" value="'.printPrice($t["price"]). '">  
									'.$_USER->getClient()->getCurrency().'
                                        &nbsp;&nbsp;&nbsp;<img src="images/icons/plus.png" class="pointer icon-link" onclick="addPriceRow(\''.$s["width"].'x'.$s["height"].'_'.$w.'\', \''.$price["quantity_from"].'\')">
                                        </td>';*/
                
               /* echo $_LANG->get('100KG Preis manuell')." ";
                echo '&nbsp;&nbsp;<input name="paper_100kg" class="text"
                                    style="width:60px;text-align:right;" value="">  
									'.$_USER->getClient()->getCurrency().'
                                        &nbsp;&nbsp;&nbsp;<img src="images/icons/plus.png" class="pointer icon-link" onclick="addPriceRow(\''.$s["width"].'x'.$s["height"].'_'.$w.'\', \''.$price["quantity_from"].'\')">
                                        </td>';*/
										echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addPriceRow(\''.$s["width"].'x'.$s["height"].'_'.$w.'\', \''.$price["quantity_from"].'\')">';
                $firstWeight = false;
                $x++;
            }
            echo '</tr>';
            
        }
    } ?>        
</table>
<?  } ?>
</div>
</form>