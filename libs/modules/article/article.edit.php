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

if ($_REQUEST["exec"] != "fromorder")
{
    $article = new Article($_REQUEST["aid"]);
}

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

if((int)$_REQUEST["remove_apiobj"]>0)
{
    $tmp_del_api_obj = new APIObject((int)$_REQUEST["remove_apiobj"]);
    $tmp_del_api_obj->delete();
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
	if ($_REQUEST['usesstorage'])
		$article->setUsesstorage($_REQUEST["usesstorage"]);
	else
		$article->setUsesstorage(0);
	
	if ($_REQUEST["article_tags"])
	{
	   $tags = explode(";", $_REQUEST["article_tags"]);
	   $article->setTags($tags);
	}
	
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
	if ($_REQUEST["qusr"])
	{
    	foreach ($_REQUEST["qusr"] as $qusr)
    	{
    	    $quser_list[] = new User((int)$qusr);
    	}
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
		$supplier = (int)$_REQUEST["article_costprice_supplier_".$i];
		$artnum = $_REQUEST["article_costprice_artnum_".$i];
		$price = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["article_costprice_price_".$i])));
		if ($price > 0){
			$article->saveCost($min, $max, $price, $supplier, $artnum);
		}
	}

	if ($_FILES){
		if (isset($_FILES['file']['name'])) {
			if (isset($_FILES['file']['name'][0]) && $_FILES['file']['name'][0] != "" && $_FILES['file']['name'][0] != null){
				$j = 0;     // Variable for indexing uploaded image.
				for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
					$target_path = "./images/products/";     // Declaring Path for uploaded images.
					// Loop to get individual element from the array
					$validextensions = array("jpeg", "jpg", "png");      // Extensions which are allowed.
					$ext = explode('.', basename($_FILES['file']['name'][$i]));   // Explode file name from dot(.)
					$file_extension = end($ext); // Store extensions in the variable.
					$filename = md5(time().$_FILES["file"]["name"][$i]) . "." . $ext[count($ext) - 1];
					$target_path = $target_path . $filename;     // Set the target path with a new name of image.
					$j = $j + 1;      // Increment the number of uploaded images according to the files in array.
					if (in_array($file_extension, $validextensions)) {
						if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path)) {
							// If file moved to uploads folder.
							$savemsg .= '<br>' .$j. ').<span id="noerror">Bild erfolgreich hochgeladen!.</span><br/>';
							$article->addPicture($filename);
						} else {     //  If File Was Not Moved.
							$savemsg .= '<br>' .$j. ').<span id="error">Bitte erneut versuchen!.</span><br/>';
						}
					} else {     //   If File Size And File Type Was Incorrect.
						$savemsg .= '<br>' .$j. ').<span id="error">***Ungültige Dateierweiterung***</span><br/>';
					}
				}
			}
		}
	}
	
	// API Zuordnung
	
	if ((int)$_REQUEST["api_new"]>0)
	{
	   $tmp_api_obj = new APIObject();
	   $tmp_api_obj->setApi((int)$_REQUEST["api_new"]);
	   $tmp_api_obj->setType(API::TYPE_ARTICLE);
	   $tmp_api_obj->setObject($article->getId());
	   $tmp_api_obj->save();   
	}

	// Damit die gespeicherten Werte auch angezeigt werden
	$article = new Article($article->getId());
}

$all_pictures = $article->getAllPictures();

$allprices = $article->getPrices();
$allcostprices = $article->getCosts();
$allsupplier = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,' supplier = 1 ');

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

<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css"	href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" charset="utf8" src="jscripts/tinymce/tinymce.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/tagit/tag-it.min.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/tagit/jquery.tagit.css" media="screen" />
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<!-- <link rel="stylesheet" type="text/css" href="jscripts/jquery-ui-1.11.4.custom/jquery-ui.min.css" media="screen" /> -->


<table width="100%">
	<tr>
		<td width="350" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Artikel hinzufügen')?>
			<?if ($_REQUEST["exec"] == "edit") 
			{
			    echo $_LANG->get('Artikel bearbeiten');
			    if ($article->getOrderid()>0)
			        echo " - aus <a href='index.php?page=libs/modules/calculation/order.php&exec=edit&id={$article->getOrderid()}&step=4'><u>Kalkulation</u></a> generiert";
			}?>
			<?if ($_REQUEST["exec"] == "fromorder" || $_REQUEST["exec"] == "uptfromorder")  echo $_LANG->get('Artikel aus Kalkulation generieren')?>
			<?//if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Artikel kopieren')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>
</br>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#article_edit').submit();",'glyphicon-floppy-disk');
if ($article->getId()>0){
	$quickmove->addItem('Neuer Vorgang','index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=select_user&startart='.$article->getId(),null,'glyphicon-book');
}
if ($article->getOrderid()>0){
	$quickmove->addItem('Zur Kalkulation','index.php?page=libs/modules/calculation/order.php&exec=edit&id='.$article->getOrderid().'&step=4',null,'glyphicon-book');
}
if ($_USER->isAdmin() && $article->getId()>0){
	$quickmove->addItem('Löschen', '#', "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&did=".$article->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="article_edit" id="article_edit" onSubmit="return checkArticleNumber(new Array(this.article_title, this.article_number))" enctype="multipart/form-data">
	<input type="hidden" name="exec" value="edit"> 
	<input type="hidden" name="subexec" value="save"> 
	<input type="hidden" name="fromorder" value="<?php if ($_REQUEST["fromorder"]) echo "1"; else echo "0";?>"> 
	<input type="hidden" name="aid" value="<?=$article->getId()?>">

	<input type="hidden" name="new_picture" id="new_picture" value="">
	<input type="hidden" name="new_picture_origname" id="new_picture_origname" value="">

	<div class="row">
		<? // -------------------- Atikel Kopfdaten --------------------------------------------------- ?>
		<div class="col-md-6">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">Kopfdaten</h3>
				  </div>
				  <div class="panel-body">
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
						  <?php if ($article->getId()>0){?>
							  <tr>
								  <td class="content_row_header"><?=$_LANG->get('Artikel-ID')?> *</td>
								  <td class="content_row_clear"><?=$article->getId()?></td>
							  </tr>
						  <?php }?>
						  <tr>
							  <?php
							  $tags = $article->getTags();
							  if (count($tags)>0)
								  $tags = implode(";", $tags);
							  ?>
							  <td class="content_row_header"><?=$_LANG->get('Tags')?></td>
							  <td class="content_row_clear"><input id="article_tags"
																   name="article_tags" type="text" class="text"
																   value="<?php echo $tags; ?>" style="width: 180px"></td>
						  </tr>
						  <tr>
							  <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?></td>
							  <td class="content_row_clear"><textarea id="article_desc" name="article_desc" rows="4" cols="50" class="text artdesc"><?=stripslashes($article->getDesc())?></textarea>
							  </td>
						  </tr>
						  <tr>
							  <td class="content_row_header"><?=$_LANG->get('Warengruppe')?></td>
							  <td class="content_row_clear"><select id="article_tradegroup"
																	name="article_tradegroup" style="width: 170px" required>
									  <!-- <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option> -->
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
						  <tr valign="top">
							  <td class="content_row_header"><?=$_LANG->get('Mögl. Bestellmengen (Shop)')?></td>
							  <td class="content_row_clear">
								  <div id="orderamounts">
									  <?php
									  foreach ($article->getOrderamounts() as $orderamount)
									  {
										  echo '<span><input name="article_orderamounts[]" type="hidden" value="'.$orderamount.'">'.$orderamount.'
                                        <span class="glyphicons glyphicons-remove pointer" title="entfernen" onclick="$(this).parent().remove();"></span> &nbsp;';
									  }
									  ?>
								  </div>
								  <span class="glyphicons glyphicons-plus pointer" title="Bestellmenge hinzufügen" onclick="addOrderAmount();"></span>
							  </td>
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
							  <td colspan="2">&emsp;</td>
						  </tr>
						  <?if ($article->getId() != 0 && $article->getCrt_user() != 0){?>
							  <tr>
								  <td class="content_row_header"><?=$_LANG->get('Angelegt')?></td>
								  <td class="content_row_clear">
									  <?=date('d.m.Y - H:i', $article->getCrt_date())?> <?=$_LANG->get('Uhr')?>
									  <?=$_LANG->get('von')?>
									  <?// var_dump($article->getCrt_user()); ?>
									  <?=$article->getCrt_user()->getFirstname()?> <?=$article->getCrt_user()->getLastname()?>
								  </td>

							  </tr>
							  <?if ($article->getUpt_user() != 0 && $article->getUpt_date() != 0){?>
								  <tr>
									  <td class="content_row_header"><?=$_LANG->get('Ge&auml;ndert von')?></td>
									  <td class="content_row_clear">
										  <?=date('d.m.Y - H:i', $article->getUpt_date())?> <?=$_LANG->get('Uhr')?>
										  <?=$_LANG->get('von')?>
										  <?=$article->getUpt_user()->getFirstname()?> <?=$article->getUpt_user()->getLastname()?>
									  </td>
								  </tr>
							  <?}?>
						  <?}?>
					  </table>
				  </div>
			</div>
		</div>
		<? // -------------------- Artikel Bilder --------------------------------------------------- ?>
		<div class="col-md-6">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">Bilder</h3>
				  </div>
				  <div class="panel-body">
                      <?php
                      if ($all_pictures)
                          $picarray = break_array($all_pictures,4);
                      else
                          $picarray = [];
                      ?>
                      <table>
                          <?php
                          if ($all_pictures>0) {
                              foreach ($picarray as $item) {
                                  echo '<tr>';
                                  foreach ($item as $picture) {
                                      echo '<td>';
                                      echo '<img src="images/products/' . $picture["url"] . '" width="130px" height="82px">' .
                                          '<a onclick="askDel(\'index.php?page=' . $_REQUEST['page'] . '&exec=edit&subexec=deletepic&aid=' . $article->getId() . '&picid=' . $picture["id"] . '\')"' .
                                          'class="icon-link" href="#"><span class="glyphicons glyphicons-remove" title = "Bild löschen"></span>';

                                      echo '</td>';
                                  }
                                  echo '</tr>';
                              }
                          }
                          ?>
                      </table>
                      <br>
                      <div id="filediv">
                          <input name="file[]" type="file" id="file"/>
                      </div>
                      <br>
                      <input type="button" id="add_more" class="btn-sm" value="mehr Bilder hinzufügen"/>
                      <input type="button" value="Hochladen" name="upload" id="upload" class="btn-sm btn-success" onclick="$('#article_edit').submit();"/>
				  </div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">Shop Einstellungen</h3>
				  </div>
				  <div class="panel-body">
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
													  <?php echo $tmp_bc->getNameAsLine();?> <span class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().remove();;" ></span>
													  <input type="hidden" name="shop_appr_bc[]" value="<?php echo $tmp_bc->getId()?>" />
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
													  <?php echo $tmp_cp->getNameAsLine();?> <span class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().remove();" ></span>
													  <input type="hidden" name="shop_appr_cp[]" value="<?php echo $tmp_cp->getId()?>" />
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
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">Preisstaffeln VK</h3>
				  </div>
				  <div class="panel-body">
					  <input type="hidden" name="count_quantity" id="count_quantity" value="<? if(count($allprices) > 0) echo count($allprices); else echo "1";?>">
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
										  echo '<span class="glyphicons glyphicons-plus pointer icon-link" onclick="addPriceRow()"></span>';
									  }?>
								  </td>
							  </tr>
						  <? } //Ende alle Preis-Staffeln?>
					  </table>
					  <br />* <?=$_LANG->get('VK-Staffelpreis wird gel&ouml;scht, falls Preis = 0')?>
					  <br />* <?=$_LANG->get('Preis entspricht dem Einzelstückpreis / Bei Kalkulationsartikeln entspricht Preis dem Endpreis')?>
				  </div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">Preisstaffeln EK</h3>
				  </div>
				<input type="hidden" name="count_quantity_cost"	id="count_quantity_cost" value="<? if(count($allcostprices) > 0) echo count($allcostprices); else echo "1";?>">
				<table id="table_prices_cost" class="table table-condensed table-hover">
					<thead>
						<tr>
							<th><?=$_LANG->get('Nr.')?></th>
							<th><?=$_LANG->get('Von')?></th>
							<th><?=$_LANG->get('Bis')?></th>
							<th><?=$_LANG->get('Lieferant')?></th>
							<th><?=$_LANG->get('Lief-Art.Nr.')?></th>
							<th><?=$_LANG->get('Preis')?>*</th>
						</tr>
					</thead>
					<tbody>
						<?
						$x = count($allcostprices);
						if ($x < 1){
							//$allprices[] = new Array
							$x++;
						}
						for ($y=0; $y < $x ; $y++){ ?>
							<tr>
								<td><?=$y+1?></td>
								<td>
									<input name="article_costprice_min_<?=$y?>" class="text" type="text"
										value="<?=$allcostprices[$y][sep_min]?>" style="width: 50px">
									<?=$_LANG->get('Stk.')?>
								</td>
								<td>
									<input name="article_costprice_max_<?=$y?>" class="text" type="text"
										value="<?=$allcostprices[$y][sep_max]?>" style="width: 50px">
									<?=$_LANG->get('Stk.')?>
								</td>
								<td>
									<select name="article_costprice_supplier_<?=$y?>" style="width:160px">
										<option value="0">-> bitte wählen <-</option>
										<?php
										foreach ($allsupplier as $supplier){
											if ($article->getId()>0){
												if ($allcostprices[$y]['supplier'] == $supplier->getId()){
													echo '<option value="'.$supplier->getId().'" selected>'.$supplier->getNameAsLine().'</option>';
												} else {
													echo '<option value="'.$supplier->getId().'">'.$supplier->getNameAsLine().'</option>';
												}
											} else {
												echo '<option value="'.$supplier->getId().'">'.$supplier->getNameAsLine().'</option>';
											}
										}
										?>
									</select>
								</td>
								<td>
									<input name="article_costprice_artnum_<?=$y?>" class="text" type="text" value="<?=$allcostprices[$y][supplier_artnum]?>" style="width: 100px">
								</td>
								<td><input name="article_costprice_price_<?=$y?>" class="text" type="text"
										value="<?=printPrice($allcostprices[$y][sep_price])?>"
										style="width: 50px">
									<?=$_USER->getClient()->getCurrency()?>
									&nbsp;&nbsp;&nbsp;
									<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
										echo '<span class="glyphicons glyphicons-plus pointer icon-link" onclick="addCostRow()"></span>';
									}?>
								</td>
							</tr>
						<? } //Ende alle Preis-Staffeln?>
					</tbody>
				</table>
				<br />* <?=$_LANG->get('EK-Staffelpreis wird gel&ouml;scht, falls Preis = 0')?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6" id="qusers" style="<?php if($article->getId()>0&&$article->getIsWorkHourArt()) echo ' display: block; '; else echo ' display: none; ';?>">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">Qualifizierte Benutzer</h3>
				  </div>
				  <div class="panel-body">
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
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">API</h3>
				  </div>
				  <div class="panel-body">
					  <table width="100%" cellpadding="0" cellspacing="0" border="0">
						  <?php
						  $api_objects = APIObject::getAllForObject($article->getId(), API::TYPE_ARTICLE);
						  if (count($api_objects)>0){
							  foreach ($api_objects as $api_object)
							  {
								  $api = new API($api_object->getApi());
								  ?>
								  <tr>
									  <td class="content_row_header"><?php echo $api->getTitle();?>
										  <a href="index.php?page=libs/modules/article/article.php&exec=edit&aid=<?php echo $article->getId()?>&remove_apiobj=<?php echo $api_object->getId()?>"><span class="glyphicons glyphicons-remove pointer"></span></a></td>
								  </tr>
								  <?php
							  }
						  } else {
							  echo '<tr><td>keine Zuordnungen vorhanden!</td></tr>';
						  }
						  ?>
						  <tr><td><hr></td></tr>
						  <?php
						  $all_apis = API::getAllApisByType(API::TYPE_ARTICLE);
						  if (count($all_apis)>0){
							  ?>
							  <tr>
								  <td class="content_row_header">
									  Neue Zuordnung:
									  <select name="api_new" id="api_new">
										  <option value="0">Api wählen</option>
										  <?php
										  foreach ($all_apis as $api)
										  {
											  ?>
											  <option value="<?php echo $api->getId()?>"><?php echo $api->getTitle(). " (" . $api->getId() . ")";?></option>
											  <?php
										  }
										  ?>
									  </select>
								  </td>
							  </tr>
						  <?php }?>
					  </table>
				  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Lager</h3>
				</div>
				<div class="panel-body">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<colgroup>
							<col width="80">
							<col>
						</colgroup>
						<tr>
							<td class="content_row_header"><?=$_LANG->get('Lagerartikel')?></td>
							<td class="content_row_clear">
								<input id="usesstorage" name="usesstorage" class="text" type="checkbox" value="1"
									<?if ($article->getUsesstorage() == 1) echo "checked"; ?>>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>



<script type="text/javascript">
    jQuery(document).ready(function() {
        tinymce.init(
            {
                selector:'#article_desc',
                menubar: false,
                statusbar: false,
                toolbar: false
            }
        );
    });
</script>

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
        insert += '<td>';
        insert += '<input name="article_price_min_'+count+'" class="text" type="text"';
        insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
        insert += '</td>';
        insert += '<td>';
        insert += '<input name="article_price_max_'+count+'" class="text" type="text"';
        insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
        insert += '</td>';
        insert += '<td>';
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
        insert += '<td>';
        insert += '<input name="article_costprice_min_'+count+'" class="text" type="text"';
        insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
        insert += '</td>';
        insert += '<td>';
        insert += '<input name="article_costprice_max_'+count+'" class="text" type="text"';
        insert += 'value ="" style="width: 50px"> <?=$_LANG->get('Stk.')?>';
        insert += '</td>';
        insert += '<td>';
        insert += '<select name="article_costprice_supplier_'+count+'" style="width:160px">';
        <?php foreach ($allsupplier as $supplier){?>
        insert += '<option value="<?=$supplier->getId()?>"><?=$supplier->getNameAsLine()?></option>';
        <?}?>
        insert += '</select>';
        insert += '</td>';
		insert += '<td>';
		insert += '<input name="article_costprice_artnum_'+count+'" class="text" type="text"';
		insert += 'value ="" style="width: 100px">';
		insert += '</td>';
        insert += '<td>';
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
            $("#orderamounts").append('<span><input name="article_orderamounts[]" type="hidden" value="'+amount+'">'+amount+'<span class="glyphicons glyphicons-remove pointer icon-link" title="entfernen" onclick="$(this).parent().remove();"></span></br></span>');
        }
    }
</script>

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
                var newRow = '<tr><td class="content_row_clear">'+ui.item.label+' <span class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().remove();;"></span>';
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
                var newRow = '<tr><td class="content_row_clear">'+ui.item.label+' <span class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().remove();;"></span>';
                newRow += '<input type="hidden" name="shop_appr_cp[]" value="'+ui.item.value+'"/></td></tr>';
                $("#shop_appr_cps tr:last").after(newRow);
                return false;
            }
        });
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#article_tags").tagit({
            singleField: true,
            singleFieldNode: $('#article_tags'),
            singleFieldDelimiter: ";",
            allowSpaces: true,
            minLength: 2,
            removeConfirmation: true,
            tagSource: function( request, response ) {
                $.ajax({
                    url: "libs/modules/article/article.ajax.php?ajax_action=search_tags",
                    data: { term:request.term },
                    dataType: "json",
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                label: item.label,
                                value: item.value
                            }
                        }));
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $('#article_edit').validate();
    });
</script>

<script type="text/javascript">
    var abc = 0;      // Declaring and defining global increment variable.
    $(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
        $('#add_more').click(function() {
            $(this).before($("<div/>", {
                id: 'filediv'
            }).fadeIn('slow').append($("<input/>", {
                name: 'file[]',
                type: 'file',
                id: 'file'
            }), $("<br/>")));
        });
// Following function will executes on change event of file input to select different file.
        $('body').on('change', '#file', function() {
            if (this.files && this.files[0]) {
                abc += 1; // Incrementing global variable by 1.
                var z = abc - 1;
                var x = $(this).parent().find('#previewimg' + z).remove();
                $(this).before("<div id='abcd" + abc + "' class='abcd'><img id='previewimg" + abc + "' src='' width='130px' height='82px'/></div>");
                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
                $(this).hide();
                $("#abcd" + abc).append($("<img/>", {
                    id: 'img',
                    src: 'images/icons/cross-script.png',
                    alt: 'delete',
                    style: 'margin-left: -15px; margin-bottom: 66px;'
                }).click(function() {
                    $(this).parent().parent().remove();
                }));
            }
        });
// To Preview Image
        function imageIsLoaded(e) {
            $('#previewimg' + abc).attr('src', e.target.result);
        };
        $('#upload').click(function(e) {
            var name = $(":file").val();
            if (!name) {
                alert("First Image Must Be Selected");
                e.preventDefault();
            }
        });
    });
</script>