<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.11.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited. 
// ---------------------------------------------------------------------------------- 

// Tabelle um den Warenkorb zu plazieren ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<colgroup>
	<col>
	<col width="2">
	<col width="200">
	</colgroup>
	<tr>
		<td valign="top">
<?		if($_REQUEST["exec"] == "showArticleDetails"){
			$article = new Article((int)$_REQUEST["articleid"]);
			$all_pictures = $article->getAllPictures();
			$art_prices = $article->getPrices();
			$warehouses = Warehouse::getAllStocksByArticle((int)$_REQUEST["articleid"]);
			
			$wh_count = 0;
			foreach ($warehouses as $wh) {
				$wh_count += $wh->getAmount();
			}
			
			if ($_SESSION["shopping_basket"]){
				$shopping_basket = $_SESSION["shopping_basket"];
			} else {
				$shopping_basket = new Shoppingbasket();
			}
			
			if ($_REQUEST["subexec"]=="add_item"){
				if ($_REQUEST["shopping_amount"]>0){
					$attributes["id"] 		= $article->getId();
					$attributes["title"] 	= $article->getTitle();
					$attributes["amount"] 	= (int)$_REQUEST["shopping_amount"];
					$attributes["price"]	= $article->getPrice($attributes["amount"]);
					$attributes["type"]		= Shoppingbasketitem::TYPE_ARTICLE;
					$item = new Shoppingbasketitem($attributes);
			
					//schauen, ob Artikel schon im Warenkorb ist
					if($shopping_basket->itemExists($item)){
						// Altes loeschen, aber temporaer zwischenspeichern
						$del_item = $shopping_basket->deleteItem($item->getId(), $item->getType());
						if ($del_item != NULL){
							// Neue Menge berechnen
							$newamount = $del_item->getAmount() + $item->getAmount();
							$item->setAmount($newamount);
							// ggf Preis anpassen (an die neue Menge)
							$newprice = $article->getPrice($newamount); // $item->getAmount());
							$item->setPrice($newprice);
								
							$shopping_basket->addItem($item);
						}
					}else{
					    $tmp_def_invc_ad = Address::getDefaultAddress($busicon, Address::FILTER_INVC);
					    $tmp_def_deli_ad = Address::getDefaultAddress($busicon, Address::FILTER_DELIV);
					    $item->setInvoiceAdressID($tmp_def_invc_ad->getId());
					    $item->setDeliveryAdressID($tmp_def_deli_ad->getId());
						$shopping_basket->addItem($item);
					}
					// Einkaufskorb auch wieder in die Session schreiben
					$_SESSION["shopping_basket"] = $shopping_basket;
				}
			}
			?>	
		<form method="post" action="index.php" name="form_additems">
			<input type="hidden" name="pid" value="<?=(int)$_REQUEST["pid"]?>">
			<input type="hidden" name="articleid" value="<?=$article->getId()?>">
			<input type="hidden" name="exec" value="showArticleDetails">
			<input type="hidden" name="subexec" value="add_item">
			<div class="box2">
				<h1><?=$_LANG->get('Artikeldetails')?>: <?=$article->getTitle()?></h1>
				<table width="100%">
					<colgroup>
							<col>
							<col width="450">
					</colgroup>
					<tr>
						<td  valign="top">
							<b><?=$_LANG->get('Artikelnummer')?>: </b><br/><?=$article->getNumber()?>
						</td>
						<td align="right" valign="top" rowspan="3">
							<?	foreach ($all_pictures AS $pic){ ?>
									<img src="../images/products/<?=$pic["url"]?>" width="130px">
									&ensp; 
							<?	}?>
						</td>
					</tr>
					<tr>
						<td  valign="top">
							<b><?=$_LANG->get('Beschreibung')?>: </b><br/><?=$article->getDesc()?>
						</td>
					</tr>
					<tr>
						<td>&ensp;</td>
					</tr>
					<tr>
						<td>&ensp;</td>
						<td class="content_row_header" align="right">
								<input name="shopping_amount" style="width: 25px;" value="0"> Stk.
								<input	type="image" style="border:none;" title="<?=$_LANG->get('Zum Warenkorb hinzuf&uuml;gen') ?>"
										src="../images/icons/shopping-basket--plus.png" />
								&ensp; &ensp; &ensp; 
						</td>
					</tr>
					<tr>
						<td>&ensp;</td>
						<td class="content_row_header" align="right">
								Auf Lager: <?=$wh_count?>
								&ensp; &ensp; &ensp; 
						</td>
					</tr>
				</table>
				<br/>
				
				<? if ($article->getShowShopPrice() == 1) { ?>
				
				<b><?=$_LANG->get('Preisliste')?></b>
				<br/>
				<table width="100%" cellpadding="2" cellspacing="0" border="0" >
					<colgroup>
							<col width="60">
							<col width="100">
							<col width="100">
							<col width="80">
							<col>
					</colgroup>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
						<td class="content_row_header"><?=$_LANG->get('Von')?></td>
						<td class="content_row_header"><?=$_LANG->get('Bis')?></td>
						<td class="content_row_header"><?=$_LANG->get('Preis')?></td>
						<td class="content_row_header">&ensp;</td>
					</tr>
					<? $x=1;
					foreach($art_prices as $price){ ?>
					<tr class="color<?=$x % 2?>">
						<td class="filerow">
							<?=$x?>
						</td>
						<td class="filerow">
							<?=$price["sep_min"]?> <?=$_LANG->get('Stk.')?>
						</td>
						<td class="filerow">
							<?=$price["sep_max"]?> <?=$_LANG->get('Stk.')?>
						</td>
						<td class="filerow">
							<?=$price["sep_price"]?> &euro;
						</td >
						<td class="filerow">&ensp;</td>
					</tr>
					<?$x++;
					} ?>
				</table>
				
				<? } ?>
				
				<table>
					<colgroup>
							<col>
							<col width="120">
							<col width="50">
					</colgroup>
					<tr>
						<td>&ensp;</td>
						
						<td align="left">&ensp;</td>
					</tr>
				</table>
			</div>
		</form>
<?		} else { // ---------- Auflistung der freigegeben Artikel fuer dieses Kunden ------------------------
			$all_article = Article::getAllShopArticleByCustomer($busicon->getId());
			?>
			<div class="box2" style="min-height:180px;">
			<b>Artikel</b>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<colgroup>
			<col width="100">
			<col>
			<col width="160">
			</colgroup>
			<tr>
			<td class="filerow_header">Bild</td>
			<td class="filerow_header">Titel</td>
			<td class="filerow_header">Optionen</td>
			</tr>
				<?	foreach ($all_article AS $article){ 
				    	$all_pictures = $article->getAllPictures(); ?>
					    <tr class="filerow">
							<td class="filerow">
								<?if ($all_pictures[0]["url"] != NULL && $all_pictures[0]["url"] !=""){?>
									<img src="../images/products/<?=$all_pictures[0]["url"]?>" width="100px">&nbsp;
				        		<?} else {?>
				        			<img src="../images/icons/image.png" title="<?=$_LANG->get('Kein Bild hinterlegt');?>">&nbsp;
				        		<? } ?>
							</td>
					        <td class="filerow"><?=$article->getTitle()?></td>
					        <td class="filerow">
					        	<a href="index.php?pid=<?=$_REQUEST["pid"]?>&articleid=<?=$article->getId()?>&exec=showArticleDetails" class="button">Ansehen</a>
					        </td>
					    </tr>
				<? 	} ?>
				</table>
			</div>
		<?
		}
		?>
		</td>
		<td>&ensp;</td>
		<td>
			<div class="box1"  style="min-height:600px;">
			<? // Warenkorb laden
				require_once 'kunden/modules/shoppingbasket/shopping_sidebar.php';?>
			</div>
		</td>
	</tr>
</table>
	