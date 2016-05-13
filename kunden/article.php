<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.11.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited. 
// ---------------------------------------------------------------------------------- 

$_USER = new User($busicon->getSupervisor()->getId());
Global $_USER;

$all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_ID, Address::FILTER_DELIV_SHOP);
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
			$wh_count = Warehouse::getTotalStockByArticle($article->getId());
			
			if ($_SESSION["shopping_basket"]){
				$shopping_basket = $_SESSION["shopping_basket"];
			} else {
				$shopping_basket = new Shoppingbasket();
			}
			// Verf�gbare Menge vorhanden?
			if ($_REQUEST["subexec"]=="add_item"){
// 			    $wh_count = Warehouse::getTotalStockByArticle((int)$_REQUEST["articleid"]);
// 			    if((int)$_REQUEST["shopping_amount"]<=$wh_count)
    				if ($_REQUEST["shopping_amount"]>0){
			            $deliv_adr = new Address((int)$_REQUEST["article_deliv"]);
    					$attributes["id"] 		= $article->getId();
    					$attributes["title"] 	= $article->getTitle();
    					$attributes["amount"] 	= (int)$_REQUEST["shopping_amount"];
    					$attributes["price"]	= $article->getPrice($attributes["amount"]);
    					$attributes["type"]		= Shoppingbasketitem::TYPE_ARTICLE;
    					$attributes["entryid"]	= count($shopping_basket->getEntrys())+1;
    					$item = new Shoppingbasketitem($attributes);
    					$item->setDeliveryAdressID($deliv_adr->getId());
    			
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
//     					    $tmp_def_deli_ad = Address::getDefaultAddress($busicon, Address::FILTER_DELIV);
    					    $item->setInvoiceAdressID($tmp_def_invc_ad->getId());
//     					    $item->setDeliveryAdressID($tmp_def_deli_ad->getId());
    						$shopping_basket->addItem($item);
    					}
    					// Einkaufskorb auch wieder in die Session schreiben
    					$_SESSION["shopping_basket"] = $shopping_basket;
    				}
			    // else Meldung: Meldung nicht genug Artikel auf Lager!
			}
			?>	
<form method="post" action="index.php" name="form_additems">
	<input type="hidden" name="pid" value="<?=(int)$_REQUEST["pid"]?>">
	<input type="hidden" name="articleid" value="<?=$article->getId()?>">
	<input type="hidden" name="exec" value="showArticleDetails">
	<input type="hidden" name="subexec" value="add_item">
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					<b>Artikeldetails:</b> <?=$article->getTitle()?>
				</h3>
		  </div>
		  <div class="panel-body">
			  <div class="form-horizontal">
				   <div class="row">

					   <div class="col-md-8">
						   <div class="row">
							   <div class="col-sm-3"><b>Artikelnummer:</b></div>
							   <div class="col-sm-2">
								   <?=$article->getNumber()?>
							   </div>
						   </div>
						   <div class="row">
							   <div class="col-sm-3"><b>Beschreibung:</b></div>
							   <div class="col-sm-9">
								   <?=$article->getDesc()?>
							   </div>
						   </div>



					   </div>

					   <div class="col-md-4" style="text-align: right">
						   <div class="row">
							   <?if ($all_pictures[0]["url"] == NULL || $all_pictures[0]["url"] ==""){?>
								   <img src="../images/icons/image.png" title="<?=$_LANG->get('Kein Bild hinterlegt');?>">
								   &ensp;
							   <?} else {?>
								   <?	foreach ($all_pictures AS $pic){ ?>
									   <a href="../images/products/<?=$pic["url"]?>" target="_blank"><img src="../images/products/<?=$pic["url"]?>"></a>
									   &ensp;
								   <? }?>
							   <? }?>
						   </div>


					   </div>
				   </div>
				  <div class="row">
					  <div class="col-sm-2"><b>Bestellmenge:</b></div>
					  <div class="col-sm-5">
						  <div class="input-group">
							  <?php
							  if (count($article->getOrderamounts())>0){?>
							  <select class="form-control" name="shopping_amount" id="shopping_amount">
								  <option value=""></option>
								  <?php
								  foreach ($article->getOrderamounts() as $orderamount)
								  {
									  echo '<option value="'.$orderamount.'">'.$orderamount.'</option>';
								  }
								  ?>
							  </select>
							  <span class="input-group-addon">Stk</span>
						  </div>
					  </div>
					  <?php } else {?>
						  <div class="col-sm-6">
							  <input name="shopping_amount"  value="0"> Stk.
						  </div>
					  <?php }?>
				  </div>

						  <div class="row">
							  <div class="col-sm-2"><b>Auf Lager:</b></div>
							  <div class="col-sm-5">
								 <div class="form-control"><?=$wh_count?></div>
							  </div>
						  </div>

						  <div class="row">
							  <div class="col-sm-2"><b>Lieferadresse:</b></div>
							  <div class="col-sm-5">
								  <select class=form-control name="article_deliv">
									  <?	foreach($all_deliveryAddresses AS $deliv){ ?>
										  <option value="<?=$deliv->getId()?>"
											  <?if($deliv->getDefault()){echo 'selected="selected"';}?>>
											  <?=$deliv->getNameAsLine()?> (<?=$deliv->getAddressAsLine()?>)
										  </option>
									  <?	} ?>
								  </select>
							  </div>
						  </div>
				  <br/>
				          <div class="row">
							  <div class="col-md-1">
									<button class="btn btn-success">
										<?=$_LANG->get('Zum Warenkorb hinzuf&uuml;gen') ?>
									</button>
							  </div>
						  </div>

				   </div>
		       </div>
					<div class="panel panel-default">
						  <div class="panel-heading">
								<h3 class="panel-title">
									<b>Preisliste</b>
								</h3>
						  </div>
						<? if ($article->getShowShopPrice() == 1) { ?>
						<div class="table-responsive">
							<table class="table table-hover">
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
						</div>
					</div>
	      </div>
	</div>
</form>
<br/>
				

<?		} else { // ---------- Auflistung der freigegeben Artikel fuer dieses Kunden ------------------------
			if ($_REQUEST["search"])
				$all_article = Article::getAllShopArticleByCustomerAndCp((int)$_SESSION["cust_id"],(int)$_SESSION["contactperson_id"]," WHERE title LIKE '%{$_REQUEST["search"]}%'");
			else
				$all_article = Article::getAllShopArticleByCustomerAndCp((int)$_SESSION["cust_id"],(int)$_SESSION["contactperson_id"]);
//			prettyPrint($_REQUEST["search"]);
// 			var_dump($all_article);
			?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<b>Artikel</b>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?pid=60" method="get">
			  <input type="hidden" name="pid" value="60">
			  Suche: <input type="text" name="search" id="search" style="width: 200px;"><button type="submit">Suchen</button>
		  </form>
		  <div class="table-responsive">
			  <table class="table table-hover">
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
	  </div>
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
	