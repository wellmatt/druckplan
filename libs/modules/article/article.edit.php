<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tradegroup/tradegroup.class.php';
require_once 'libs/modules/warehouse/warehouse.class.php';

$_REQUEST["aid"] = (int)$_REQUEST["aid"];

$article = new Article($_REQUEST["aid"]);

$all_tradegroups = Tradegroup::getAllTradegroups(0);



if($_REQUEST["exec"] == "copy"){
	$old_article = new Article($_REQUEST["aid"]);
	$allprices = $old_article->getPrices(); 		//Damit Preise korrekt kopiert werden
	$article->setNumber("");
	$article->clearId();
}

if($_REQUEST["subexec"] == "deletepic"){
    $picid = (int)$_REQUEST["picid"];
    $tmp_pic = $article->getPictureUrl($picid);
    $filename = "./images/products/".$tmp_pic["url"];
    
    if(unlink($filename)){
        $savemsg = getSaveMessage($article->deletePicture($picid));
    }
}

if($_REQUEST["subexec"] == "save"){
	
	$tax = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["article_tax"])));
	$orderunitweight = (float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["article_orderunitweight"])));
	
	$article->setTitle(trim(addslashes($_REQUEST["article_title"])));
	$article->setDesc(trim(addslashes($_REQUEST["article_desc"])));
	$article->setNumber(trim(addslashes($_REQUEST["article_number"])));
	$article->setPicture($_REQUEST["new_picture"]);
	$article->setTax($tax);
	$article->setShoprel((int)$_REQUEST["article_shoprel"]);
	$article->setMinorder((int)$_REQUEST["article_minorder"]);
	$article->setMaxorder((int)$_REQUEST["article_maxorder"]);
	$article->setOrderunit((int)$_REQUEST["article_orderunit"]);
	$article->setOrderunitweight($orderunitweight);
	$article->setShopCustomerID((int)$_REQUEST["article_shop_cust_id"]);
	$article->setShopCustomerRel((int)$_REQUEST["article_shop_cust_rel"]);
	$article->setIsWorkHourArt((int)$_REQUEST["article_isworkhourart"]);
	$article->setShowShopPrice((int)$_REQUEST["article_show_shop_price"]);
	$article->setShop_needs_upload((int)$_REQUEST["article_shop_needs_upload"]);
	$article->setMatchcode($_REQUEST["article_matchcode"]);
	
	$tmp_shop_bc_arr = Array();
	if ($_REQUEST["shop_appr_bc"])
	{
	    foreach ($_REQUEST["shop_appr_bc"] as $tmp_shop_appr_bc)
	        $tmp_shop_bc_arr[] = $tmp_shop_appr_bc;
	}
	$tmp_shop_cp_arr = Array();
	if ($_REQUEST["shop_appr_cp"])
	{
	    foreach ($_REQUEST["shop_appr_cp"] as $tmp_shop_appr_cp)
	        $tmp_shop_cp_arr[] = $tmp_shop_appr_cp;
	}
	$tmp_shop_arr = Array("BCs"=>$tmp_shop_bc_arr,"CPs"=>$tmp_shop_cp_arr);
	$article->setShop_approval($tmp_shop_arr);
// 	var_dump($article->getShop_approval());
	
	$quser_list = Array();
	foreach ($_REQUEST["qusr"] as $qusr)
	{
	    $quser_list[] = new User((int)$qusr);
	}
	$article->setQualified_users($quser_list);
	
	$tmp_orderamounts = Array();
	if ($_REQUEST["article_orderamounts"])
	{
    	foreach ($_REQUEST["article_orderamounts"] as $tmp_orderamount)
    	{
    	    $tmp_orderamounts[] = $tmp_orderamount;
    	}
	}
	$article->setOrderamounts($tmp_orderamounts);
		
	$article->setTradegroup(new Tradegroup((int)$_REQUEST["article_tradegroup"]));
	$savemsg = getSaveMessage($article->save()).$DB->getLastError();
	
	// Alle VK-Preisstaffeln loeschen 
	$article->deltePriceSeperations();
	// Dann neue VK-Preis-Staffeln einfuegen
	$allprice_seperations = (int)$_REQUEST["count_quantity"];
	for ($i=0 ; $i <= $allprice_seperations ; $i++){
		$min = (int)$_REQUEST["article_price_min_".$i];
		$max = (int)$_REQUEST["article_price_max_".$i];
		$price = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["article_price_price_".$i])));
		if ($price > 0){
			$article->savePrice($min, $max, $price);
		}
	}
	
	// Alle EK-Preisstaffeln loeschen
	$article->delteCostSeperations();
	// Dann neue EK-Preis-Staffeln einfuegen
	$allprice_seperations = (int)$_REQUEST["count_quantity_cost"];
	for ($i=0 ; $i <= $allprice_seperations ; $i++){
		$min = (int)$_REQUEST["article_costprice_min_".$i];
		$max = (int)$_REQUEST["article_costprice_max_".$i];
		$price = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["article_costprice_price_".$i])));
		if ($price > 0){
			$article->saveCost($min, $max, $price);
		}
	}
	
	if ($_REQUEST["new_picture"] != 0 && $_REQUEST["new_picture"] != NULL){
		$article->addPicture($_REQUEST["new_picture"]);
	}
	
	// Damit die gespeicherten Werte auch angezeigt werden
	$article = new Article($article->getId());
}

$all_pictures = $article->getAllPictures();
if($article->getId() > 0){
	$warehouses = Warehouse::getAllStocksByArticle($article->getId());
}

$allprices = $article->getPrices();
$allcostprices = $article->getCosts();
$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME);

/****************************** PHP-Funktionen ***********************************************************************/

function printSubTradegroupsForSelect($parentId, $depth){
	global $article;
	$all_subgroups = Tradegroup::getAllTradegroups($parentId);
	foreach ($all_subgroups AS $subgroup){
		global $x;
		$x++; ?>
<option value="<?=$subgroup->getId()?>"
	<?if ($article->getTradegroup()->getId() == $subgroup->getId()) echo "selected" ;?>>
				<?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
				<?= $subgroup->getTitle()?>
			</option>
<? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
	}
}


/****************************** PHP-Funktionen ***********************************************************************/

//var_dump($article);
?>
<script language="javascript">
$(document).ready(function(){
	$('#article_isworkhourart').change(function() {
	        $('#qusers').toggle();  
	    });
	});
</script>
<script language="javascript">
function addPriceRow()
{
	var obj = document.getElementById('table-prices');
	var count = parseInt(document.getElementById('count_quantity').value) + 1;
	var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="article_price_min_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="article_price_max_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="article_price_price_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> <?=$_USER->getClient()->getCurrency()?>';
	insert += '</td></tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_quantity').value = count;
}

function addCostRow()
{
	var obj = document.getElementById('table_prices_cost');
	var count = parseInt(document.getElementById('count_quantity_cost').value) + 1;
	var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="article_costprice_min_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="article_costprice_max_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="article_costprice_price_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> <?=$_USER->getClient()->getCurrency()?>';
	insert += '</td></tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_quantity_cost').value = count;
}

function checkArticleNumber(obj){
	var thisnumber = '<?=$article->getNumber()?>';
	var newnumber  = document.getElementById('article_number').value;

	<?//Erst ueberpruefen ob Art-Nr leer ist, dann ob vorhanden?>
	if (newnumber == ""){
		return checkform(obj);
	}

	if (thisnumber != newnumber){
		$.post("libs/modules/article/article.ajax.php", 
				{exec: 'checkArticleNumber', newnumber : newnumber}, 
				 function(data) {
					 data = data.substring(0,2);
					if(data == "DA"){
						alert('<?=$_LANG->get('Artikelnummer bereits vergeben!') ?>');
						document.getElementById('article_number').focus();
						return false;
					} else {
						if (checkform(obj)==true){
							document.getElementById('article_edit').submit();
						}
					}
				});
	} else {
		return checkform(obj);
	}
	return false;
}

function addOrderAmount()
{
    var amount = prompt("Bitte Bestellmenge angeben", "");
    
    if (amount != null) {
    	$("#article_orderamounts").append('<option selected="selected" value="'+amount+'">'+amount+'</option>');
    }
}
</script>

<!-- FancyBox -->
<script type="text/javascript"
	src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript"
	src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css"
	href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript">
	$(document).ready(function() {
		$("a#picture_select").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Artikel hinzuf�gen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Artikel bearbeiten')?>
			<?//if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Artikel kopieren')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>
</br>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#article_edit').submit();">Speichern</a>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post"
	name="article_edit" id="article_edit"
	onSubmit="return checkArticleNumber(new Array(this.article_title, this.article_number))">
	<div style="overflow: hidden;">
		<div style="width: 50%; float: left;">
    	<? // -------------------- Atikeldetails --------------------------------------------------- ?>
    	<div class="box1" style="min-height: 380px;">
				<b>Kopfdaten</b> <input type="hidden" name="exec" value="edit"> <input
					type="hidden" name="subexec" value="save"> <input type="hidden"
					name="aid" value="<?=$article->getId()?>">
    		
    		<? // Fuer die ein neues Bild ?>
    		<input type="hidden" name="new_picture" id="new_picture" value="">
				<input type="hidden" name="new_picture_origname"
					id="new_picture_origname" value="">

				<table width="100%">
					<colgroup>
						<col width="180">
						<col>
					</colgroup>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
						<td class="content_row_clear"><input id="article_title"
							name="article_title" type="text" class="text"
							value="<?=$article->getTitle()?>" style="width: 370px"></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Artikelnummer')?> *</td>
						<td class="content_row_clear"><input id="article_number"
							name="article_number" type="text" class="text"
							value="<?=$article->getNumber()?>" style="width: 180px"></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Matchcode')?></td>
						<td class="content_row_clear"><input id="article_matchcode"
							name="article_matchcode" type="text" class="text"
							value="<?=$article->getMatchcode()?>" style="width: 180px"></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
						<td class="content_row_clear"><textarea id="article_desc"
								name="article_desc" rows="4" cols="50" class="text"><?=stripslashes($article->getDesc())?></textarea>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Warengruppe')?></td>
						<td class="content_row_clear"><select id="article_tradegroup"
							name="article_tradegroup" style="width: 170px">
								<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
    					<?if ($article->getTradegroup() == NULL){
    						foreach ($all_tradegroups as $tg){
    							echo '<option value="'.$tg->getId().'">'.$tg->getTitle().'</option>';
    						} 
    					} else {
    						foreach ($all_tradegroups as $tg){?>
    							<option value="<?=$tg->getId()?>"
									<?if ($article->getTradegroup()->getId() == $tg->getId()) echo "selected" ?>><?= $tg->getTitle()?></option>
    						<?	printSubTradegroupsForSelect($tg->getId(), 0);
    						} //Ende foreach($all_tradegroups)
    					}//Ende else?>
    				</select></td>
					</tr>
					<tr>
						<td colspan="2">&emsp;</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Arbeitszeit Artikel')?></td>
						<td class="content_row_clear"><input id="article_isworkhourart"
							name="article_isworkhourart" class="text" type="checkbox"
							value="1"
							<?if ($article->getIsWorkHourArt() == 1) echo "checked"; ?>></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Bestellmengen')?> (Min/Max)</td>
						<td class="content_row_clear"><input id="article_minorder"
							name="article_minorder" type="text" class="text"
							value="<?=$article->getMinorder()?>" style="width: 80px">
    					<?=$_LANG->get('Stk.');?> 
    					<input id="article_maxorder" name="article_maxorder"
							type="text" class="text" value="<?=$article->getMaxorder()?>"
							style="width: 80px">
    					<?=$_LANG->get('Stk.');?> 
    				</td>
					</tr>
					<tr valign="top">
						<td class="content_row_header"><?=$_LANG->get('Mögl. Bestellmengen (Shop)')?></td>
						<td class="content_row_clear"><select
							name="article_orderamounts[]" id="article_orderamounts"
							multiple="" size="6" style="width: 184px">
    					   <?php 
    					   foreach ($article->getOrderamounts() as $orderamount)
    					   {
    					       echo '<option selected value="'.$orderamount.'">'.$orderamount.'</option>';
    					   }
    					   ?>
    					</select><img src="images/icons/plus.png"
							class="pointer icon-link" title="Bestellmenge hinzufügen"
							onclick="addOrderAmount();"></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Verpackungseinheit/-gewicht')?></td>
						<td class="content_row_clear"><input id="article_orderunit"
							name="article_orderunit" type="text" class="text"
							value="<?=$article->getOrderunit()?>" style="width: 80px">
    					<?=$_LANG->get('Stk.');?>  
    					<input id="article_orderunitweight"
							name="article_orderunitweight" type="text" class="text"
							value="<?=printPrice($article->getOrderunitweight(), 4)?>"
							style="text-align: right; width: 80px">
    					<?=$_LANG->get('Kg.');?> 
    				</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Umsatzsteuer')?> *</td>
						<td class="content_row_clear"><input id="article_tax"
							name="article_tax" type="text" class="text"
							value="<?=printPrice($article->getTax())?>" style="width: 184px">
							%</td>
					</tr>
					<tr>
						<td colspan="2">&emsp;</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Lagerplatz')?></td>
						<td class="content_row_clear">
    				<? 	$output = "";
    					foreach ($warehouses as $stock){
    						$output .= $stock->getName()."(".$stock->getAmount()." Stk.)".", ";
    					} 
    					$output = substr( $output , 0, -2);
    					echo $output;
    					?>
    				</td>
					</tr>
					<tr>
						<td colspan="2">&emsp;</td>
					</tr>
    			<?if ($article->getId() != 0 && $article->getCrt_user() != 0){// Ersteller nur beim Bearbeiten ausgeben?>
    				<tr>
						<td class="content_row_header"><?=$_LANG->get('Angelegt')?></td>
						<td class="content_row_clear">
    						<?=date('d.m.Y - H:i', $article->getCrt_date())?> <?=$_LANG->get('Uhr')?>
    						<?=$_LANG->get('von')?>
    						<?// var_dump($article->getCrt_user()); ?>
    						<?=$article->getCrt_user()->getFirstname()?> <?=$article->getCrt_user()->getLastname()?>
    					</td>

					</tr>
    				<?if ($article->getUpt_user() != 0 && $article->getUpt_date() != 0){
    						// Ge�ndert von/am nur bei bearbeiteten Artikeln ausgeben?>
    				<tr>
						<td class="content_row_header"><?=$_LANG->get('Ge&auml;ndert von')?></td>
						<td class="content_row_clear">
    						<?=date('d.m.Y - H:i', $article->getUpt_date())?> <?=$_LANG->get('Uhr')?>
    						<?=$_LANG->get('von')?>
    						<?=$article->getUpt_user()->getFirstname()?> <?=$article->getUpt_user()->getLastname()?>
    					</td>
					</tr>
    				<?} // Ende if(geaendert gesetzt) ?>
    			<?} // Ende if(neuer Artikel) ?>
    		</table>
			</div>
			</br>
        <? // -------------------- Artikelbilder --------------------------------------------------- ?>
    	<div class="box1" style="min-height: 380px;">
				<b>Artikelbilder</b>
				<table width="100%">
					<colgroup>
						<col width="200">
						<col width="200">
					</colgroup>
					<tr>
						<td class="content_row_header" colspan="2" id="td_picture_show">
    				<?=$_LANG->get('Artikelbilder')?>  &emsp; &emsp;
    				<a href="libs/modules/article/picture.iframe.php"
							id="picture_select" class="products"><input type="button"
								width="80px" class="button"
								value="<?=$_LANG->get('Hinzuf&uuml;gen')?>"></a>
						</td>
					</tr>
					<tr>
						<td id="td_newpicture" colspan="2">&ensp;</td>
					</tr>
    		<?/****?>
    		<tr>
    			<td align="left">
    				<?if ($article->getPicture()!= NULL && $article->getPicture() !=""){?>
    					<img src="images/products/<?=$article->getPicture()?>" width="130px">
    					
    					<a onclick="askDel('index.php?exec=edit&subexec=deletepic&aid=<?=$article->getId()?>&picid=<?$picture[$x]["id"]?>')"
    						href="#"><img src="images/icons/cross-script.png" title="<?=$_LANG->get(' Bild l&ouml;schen')?>"></a>
            		<?} else {?>
            			&nbsp; ...
            		<? } ?>	
    			</td>
    			<td align="right">&ensp;</td>
    		</tr><?****/?>
    	</table>
				<br />
    	
    	<? $x=0;
    	if ($all_pictures>0){
    	foreach ($all_pictures AS $pic){
    		?>
    		<img src="images/products/<?=$pic["url"]?>" width="130px"> <a
					onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&subexec=deletepic&aid=<?=$article->getId()?>&picid=<?=$pic["id"]?>')"
					class="icon-link" href="#"><img src="images/icons/cross-script.png"
					title="<?=$_LANG->get(' Bild l&ouml;schen')?>"></a>
    		&ensp; 
    	<?	$x++;
    	}}?>
    	</div>
			</br>
		</div>
		<div style="width: 50%; float: right;">
    	<?// Ab hier Preisstaffeln ein geben ?>
    	<input type="hidden" name="count_quantity" id="count_quantity"
				value="<? if(count($allprices) > 0) echo count($allprices); else echo "1";?>">
			<div class="box1" style="min-height: 180px;">
				<b>VK-Preisstaffeln</b>
				<table id="table-prices">
					<colgroup>
						<col width="40">
						<col width="80">
						<col width="120">
						<col width="120">
					</colgroup>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
						<td class="content_row_header"><?=$_LANG->get('Von')?></td>
						<td class="content_row_header"><?=$_LANG->get('Bis')?></td>
						<td class="content_row_header"><?=$_LANG->get('Preis')?>*</td>
					</tr>
    			<?
    			$x = count($allprices);
    			if ($x < 1){
    				//$allprices[] = new Array
    				$x++;
    			}
    			for ($y=0; $y < $x ; $y++){ ?>
    				<tr>
						<td class="content_row_clear">
    					<?=$y+1?>
    					</td>
						<td class="content_row_clear"><input
							name="article_price_min_<?=$y?>" class="text" type="text"
							value="<?=$allprices[$y][sep_min]?>" style="width: 50px">
    						<?=$_LANG->get('Stk.')?>
    					</td>
						<td class="content_row_clear"><input
							name="article_price_max_<?=$y?>" class="text" type="text"
							value="<?=$allprices[$y][sep_max]?>" style="width: 50px">
    						<?=$_LANG->get('Stk.')?>
    					</td>
						<td class="content_row_clear"><input
							name="article_price_price_<?=$y?>" class="text" type="text"
							value="<?=printPrice($allprices[$y][sep_price])?>"
							style="width: 50px">
    						<?=$_USER->getClient()->getCurrency()?>
    						&nbsp;&nbsp;&nbsp;
    						<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
    							echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addPriceRow()">';
    						}?> 
    					</td>
					</tr>
    			<? } //Ende alle Preis-Staffeln?>
    		</table>
				<br />* <?=$_LANG->get('VK-Staffelpreis wird gel&ouml;scht, falls Preis = 0')?> 
    	</div>
			</br>
    	<?// Ab hier Preisstaffeln (EK) ein geben ?>
    	<input type="hidden" name="count_quantity_cost"
				id="count_quantity_cost"
				value="<? if(count($allcostprices) > 0) echo count($allcostprices); else echo "1";?>">
			<div class="box1" style="min-height: 180px;">
				<b>EK-Preisstaffeln</b>
				<table id="table_prices_cost">
					<colgroup>
						<col width="40">
						<col width="80">
						<col width="120">
						<col width="120">
					</colgroup>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
						<td class="content_row_header"><?=$_LANG->get('Von')?></td>
						<td class="content_row_header"><?=$_LANG->get('Bis')?></td>
						<td class="content_row_header"><?=$_LANG->get('Preis')?>*</td>
					</tr>
    			<?
    			$x = count($allcostprices);
    			if ($x < 1){
    				//$allprices[] = new Array
    				$x++;
    			}
    			for ($y=0; $y < $x ; $y++){ ?>
    				<tr>
						<td class="content_row_clear">
    					<?=$y+1?>
    					</td>
						<td class="content_row_clear"><input
							name="article_costprice_min_<?=$y?>" class="text" type="text"
							value="<?=$allcostprices[$y][sep_min]?>" style="width: 50px">
    						<?=$_LANG->get('Stk.')?>
    					</td>
						<td class="content_row_clear"><input
							name="article_costprice_max_<?=$y?>" class="text" type="text"
							value="<?=$allcostprices[$y][sep_max]?>" style="width: 50px">
    						<?=$_LANG->get('Stk.')?>
    					</td>
						<td class="content_row_clear"><input
							name="article_costprice_price_<?=$y?>" class="text" type="text"
							value="<?=printPrice($allcostprices[$y][sep_price])?>"
							style="width: 50px">
    						<?=$_USER->getClient()->getCurrency()?>
    						&nbsp;&nbsp;&nbsp;
    						<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
    							echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addCostRow()">';
    						}?> 
    					</td>
					</tr>
    			<? } //Ende alle Preis-Staffeln?>
    		</table>
				<br />* <?=$_LANG->get('EK-Staffelpreis wird gel&ouml;scht, falls Preis = 0')?> 
    	</div>
			</br>
			<div id="qusers" class="box1" style="min-height: 180px; <?php if($article->getId()>0&&$article->getIsWorkHourArt()) echo ' display: block; '; else echo ' display: none; ';?>">
				<b>Arbeiter</b>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
        	    <?php 
        	    $all_users = User::getAllUser();
        	    $qid_arr = Array();
        	    foreach ($article->getQualified_users() as $qid)
        	    {
        	        $qid_arr[] = $qid->getId();
        	    }
        	    $qi = 0;
        	    foreach ($all_users as $qusr){
        	       if ($qi==0) echo '<tr>';
        	       ?>
        		   <td class="content_row_header" valign="top" width="20%"><input
						type="checkbox" name="qusr[]"
						<?php if(in_array($qusr->getId(), $qid_arr)) echo ' checked ';?>
						value="<?php echo $qusr->getId();?>" /> 
        		   <?php echo $qusr->getNameAsLine();?></td>
        		   <?php if ($qi==4) { echo '</tr>'; $qi = -1; }?>
        		<?php $qi++;}?>
        	</table>
			</div>
			</br>
			<div id="qusers" class="box1" style="min-height: 180px;">

				<script language="JavaScript">
            $(function() {
            	$( "#shop_add_customer" ).autocomplete({
              		 source: "libs/modules/article/article.ajax.php?ajax_action=search_customer",
              		 minLength: 2,
              		 focus: function( event, ui ) {
              		 $( "#shop_add_customer" ).val( ui.item.label );
              		 return false;
              		 },
              		 select: function( event, ui ) {
                  		 var newRow = '<tr><td class="content_row_clear">'+ui.item.label+' <img src="images/icons/cross.png" class="pointer" onclick="$(this).parent().remove();;"/>';
                   		 newRow += '<input type="hidden" name="shop_appr_bc[]" value="'+ui.item.value+'"/></td></tr>';
                    	 $("#shop_appr_bcs tr:last").after(newRow);
                  		 return false;
                  	 }
          		});
            	$( "#shop_add_customer_cp" ).autocomplete({
            		 source: "libs/modules/article/article.ajax.php?ajax_action=search_customer_cp",
            		 minLength: 2,
            		 focus: function( event, ui ) {
            		 $( "#shop_add_customer_cp" ).val( ui.item.label );
            		 return false;
            		 },
            		 select: function( event, ui ) {
                  		 var newRow = '<tr><td class="content_row_clear">'+ui.item.label+' <img src="images/icons/cross.png" class="pointer" onclick="$(this).parent().remove();;"/>';
                   		 newRow += '<input type="hidden" name="shop_appr_cp[]" value="'+ui.item.value+'"/></td></tr>';
                    	 $("#shop_appr_cps tr:last").after(newRow);
                		 return false;
                	 }
        		});
            });
            </script>

				<b>Shop</b>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Shop: Preis anzeigen')?></td>
						<td class="content_row_clear"><input id="article_show_shop_price"
							name="article_show_shop_price" class="text" type="checkbox"
							value="1"
							<?if ($article->getShowShopPrice() == 1) echo "checked"; ?>></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Shop: Datei Upload')?></td>
						<td class="content_row_clear"><input
							id="article_shop_needs_upload" name="article_shop_needs_upload"
							class="text" type="checkbox" value="1"
							<?if ($article->getShop_needs_upload() == 1) echo "checked"; ?>>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Freigegeben für alle')?></td>
						<td class="content_row_clear"><input id="article_shoprel"
							name="article_shoprel" class="text" type="checkbox" value="1"
							<?if ($article->getShoprel() == 1) echo "checked"; ?>></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Benutzerdefinierte-Freigabe')?></td>
						<td class="content_row_clear"></td>
					</tr>
					<tr>
						<td class="content_row_header" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0"
								id="shop_appr_bcs">
								<thead>
									<tr>
										<td class="content_row_header"><?=$_LANG->get('Geschäfskontakte')?></td>
									</tr>
								</thead>
                			<?php 
                			$shop_appr = $article->getShop_approval(); 
                			if (count($shop_appr["BCs"])>0){
                			    foreach ($shop_appr["BCs"] as $shop_appr_bc)
                			    {
                        			$tmp_bc = new BusinessContact($shop_appr_bc)?>
                        			<tr>
									<td class="content_row_clear">
                        				    <?php echo $tmp_bc->getNameAsLine();?> <img
										src="images/icons/cross.png" class="pointer"
										onclick="$(this).parent().remove();;" /> <input type="hidden"
										name="shop_appr_bc[]" value="<?php echo $tmp_bc->getId()?>" />
									</td>
								</tr>
                			<?php }?>
                			<?php }?>
    				    </table> Hinzufügen: <input type="text"
							id="shop_add_customer" value="" style="width: 300px" />
							<table width="100%" cellpadding="0" cellspacing="0" border="0"
								id="shop_appr_cps">
								<thead>
									<tr>
										<td class="content_row_header"><?=$_LANG->get('Ansprechpartner')?></td>
									</tr>
								</thead>
                			<?php 
                			$shop_appr = $article->getShop_approval(); 
                			if (count($shop_appr["CPs"])>0){
                			    foreach ($shop_appr["CPs"] as $shop_appr_cp)
                			    {
                        			$tmp_cp = new ContactPerson($shop_appr_cp)?>
                        			<tr>
									<td class="content_row_clear">
                        				    <?php echo $tmp_cp->getNameAsLine();?> <img
										src="images/icons/cross.png" class="pointer"
										onclick="$(this).parent().remove();;" /> <input type="hidden"
										name="shop_appr_cp[]" value="<?php echo $tmp_cp->getId()?>" />
									</td>
								</tr>
                			<?php }?>
                			<?php }?>
    				    </table> Hinzufügen: <input type="text"
							id="shop_add_customer_cp" value="" style="width: 300px" />
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<br />
</form>