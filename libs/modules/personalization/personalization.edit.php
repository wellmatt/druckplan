<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/documents/document.class.php');
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/personalization/personalization.item.class.php';
require_once 'libs/modules/personalization/persofont.class.php';
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
// ini_set('display_errors', 1);

$_REQUEST["id"] = (int)$_REQUEST["id"];
$perso = new Personalization($_REQUEST["id"]);

if($_REQUEST["subexec"] == "save"){
	
	$perso->setTitle(trim(addslashes($_REQUEST["perso_title"])));
	$perso->setComment(trim(addslashes($_REQUEST["perso_comment"])));
	$perso->setPicture($_REQUEST["perso_picture1"]);
	$perso->setPicture2($_REQUEST["perso_picture2"]);
	$perso->setFormat($_REQUEST["perso_format"]);
	$perso->setDirection((int)$_REQUEST["perso_direction"]);
	$perso->setArticle(new Article($_REQUEST["perso_article"]));
	$perso->setCustomer(new BusinessContact($_REQUEST["perso_customer"]));
	$perso->setType((int)$_REQUEST["perso_type"]);
	$perso->setLineByLine((int)$_REQUEST["perso_linebyline"]);
	$perso->setHidden((int)$_REQUEST["perso_hidden"]);
	
	$perso->setFormatheight((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["perso_format_height"]))));
	$perso->setFormatwidth((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["perso_format_width"]))));
	$perso->setAnschnitt((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["perso_format_anschnitt"]))));

	if($_FILES)
	{
		$filename = $_USER->getClient()->getId().'.perpv_'.md5(time().$_FILES["preview"]["name"]).".jpg";
		if(move_uploaded_file($_FILES["preview"]["tmp_name"], "docs/personalization/".$filename))
		{
			$perso->setPreview($filename);
		}
	}
		
	$save_retval = $perso->save();
	$savemsg = getSaveMessage($save_retval);
	
	if ($save_retval){
		$all_items_counter = (int)$_REQUEST["count_quantity"];
		for ($i=0 ; $i <= $all_items_counter ; $i++){
			$item = new Personalizationitem((int)$_REQUEST["item_id_{$i}"]);
			$item->setTitle(addslashes($_REQUEST["item_title_{$i}"]));
			$item->setXpos((float)sprintf("%.3f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["item_xpos_{$i}"]))));
			$item->setYpos((float)sprintf("%.3f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["item_ypos_{$i}"]))));
			$item->setWidth((float)sprintf("%.3f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["item_width_{$i}"]))));
			$item->setHeight((float)sprintf("%.3f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["item_height_{$i}"]))));
			$item->setTextsize((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["item_textsize_{$i}"]))));
			$item->setSpacing((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["item_spacing_{$i}"]))));
			$item->setPeronalid((int)$perso->getId());
			$item->setTab((float)sprintf("%.3f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["item_tab_{$i}"]))));
			$item->setBoxtype((int)$_REQUEST["item_boxtype_{$i}"]);
			$item->setJustification((int)$_REQUEST["item_justification_{$i}"]);
			$item->setFont((int)$_REQUEST["item_font_{$i}"]);
			$item->setDependencyID((int)$_REQUEST["item_dependency_{$i}"]);
			$item->setGroup((int)$_REQUEST["item_group_{$i}"]);
			$item->setColor_c((int)$_REQUEST["item_color_c_{$i}"]);
			$item->setColor_m((int)$_REQUEST["item_color_m_{$i}"]);
			$item->setColor_y((int)$_REQUEST["item_color_y_{$i}"]);
			$item->setColor_k((int)$_REQUEST["item_color_k_{$i}"]);
			$item->setReverse((int)$_REQUEST["item_site_{$i}"]);
			$item->setPreDefined((int)$_REQUEST["item_predefined_{$i}"]);
			$item->setPosition((int)$_REQUEST["item_position_{$i}"]);
			$item->setReadOnly((int)$_REQUEST["item_readonly_{$i}"]);
			$item->setSort((int)$_REQUEST["item_sort_{$i}"]);
			if ($item->getWidth() > 0 && $item->getHeight() > 0){
				$item_save = $item->save();
				if($item_save){
					$savemsg .= " ";
				}
			} else {
				$item->delete();
			}
		}
	}
	
	if($save_retval){
		// Alle Preisstaffeln loeschen
		$perso->deltePriceSeperations();
		// Dann neue Preis-Staffeln einfuegen
		$allprice_seperations_counter = (int)$_REQUEST["count_sep_quantity"];
		for ($i=0 ; $i <= $allprice_seperations_counter ; $i++){
			$min = 0;
			$max = (int)$_REQUEST["perso_price_max_".$i];
			$price = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["perso_price_price_".$i])));
			$show = (int)$_REQUEST["perso_price_show_".$i];
			if ($price > 0){
				$perso->savePrice($min, $max, $price, $show);
			}
		}
	}
	
	if($DB->getLastError() != "" && $DB->getLastError() != NULL){
		$savemsg .= " - ".$DB->getLastError();
	}
	
	// Alte Vorschau-Dokumente entfernen
	$del_docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION,
											 "requestId" => $perso->getId(),
											 "module" => Document::REQ_MODULE_PERSONALIZATION));
	foreach ($del_docs as $del_doc){
		$del_doc->delete();
	}
	// PDF Dokument zur Vorschau erstellen (Vorderseite)
	$doc = new Document();
	$doc->setRequestId($perso->getId());
	$doc->setRequestModule(Document::REQ_MODULE_PERSONALIZATION);
	$doc->setType(Document::TYPE_PERSONALIZATION);
	$doc->setReverse(0);
	$hash = $doc->createDoc(Document::VERSION_EMAIL, false, false);
	$doc->setName("PERSO");
	$doc->save();
}

$all_article = Article::getAllArticle(Article::ORDER_TITLE);
$all_customer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_ALL);
$all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), "id", Personalizationitem::SITE_ALL);
$all_fonts = PersoFont::getAllPersoFonts(PersoFont::ORDER_TITLE);
$allprices = $perso->getPrices();
//var_dump($perso);
?>
<script language="javascript">
function addItemRow()
{
	var obj = document.getElementById('table-items');
	var count = parseInt(document.getElementById('count_quantity').value) + 1;
	var insert = '<tr>';
	insert += '<td class="content_row"><input type="number" name="item_sort_'+count+'" value="'+count+'" style="width: 30px"/></td>';
	insert += '<td class="content_row">';
	insert += '<input name="item_id_'+count+'" value="0" type="hidden">';
	insert += '<input name="item_title_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 200px">';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="item_textsize_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px">';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="item_xpos_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> x';
	// insert += '</td>';
	// insert += '<td class="content_row">';
	insert += '<input name="item_ypos_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> mm';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="item_width_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> x';
	// insert += '</td>';
	// insert += '<td class="content_row">';
	insert += '<input name="item_height_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> mm';
// 	insert += '</td>';
	insert += '</td>';
	insert += '<td class="content_row"><input name="item_tab_'+count+'" class="text" type="text" value="0" style="width: 50px">mm</td>';
	insert += '<td class="content_row">';
	insert += '<select name="item_boxtype_'+count+'" class="text" style="width: 80px">';
	insert += '<option value="1" > <?=$_LANG->get('Textfeld');?></option>';
	insert += '<option value="2" > <?=$_LANG->get('Textbox');?> </option>';
	insert += '</select>';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<select name="item_justification_'+count+'" class="text" style="width: 70px">';
	insert += '<option value="0"><?=$_LANG->get('links');?></option>';
	insert += '<option value="1"><?=$_LANG->get('zentral');?></option>';
	insert += '<option value="2"><?=$_LANG->get('rechts');?></option>';
	insert += '</select>';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<select name="item_font_'+count+'" class="text" style="width: 100px">';
	<? /* insert += '<option value="2"><?=$_LANG->get('Courier');?></option>';
	insert += '<option value="3"><?=$_LANG->get('Helvetica');?></option>';
	insert += '<option value="4"><?=$_LANG->get('Times-Roman');?></option>';
	insert += '<option value="5"> <?=$_LANG->get('Trade Gothic');?> </option>';
	insert += '<option value="6"> <?=$_LANG->get('Frutiger');?> </option>';
	insert += '<option value="99"><?=$_LANG->get('Symbol');?></option>'; ****/ ?>
	<?	foreach ($all_fonts AS $font){ ?>
		insert += '<option value="<?=$font->getId()?>"> <?=$font->getTitle();?> </option>';
	<?	} ?>
	insert += '</select>';
	insert += '</td>';
	insert += '<td class="content_row" align="center">';
	insert += '<input name="item_spacing_'+count+'" class="text" type="text" style="width: 50px"> ';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="item_color_c_'+count+'" class="text" type="text" style="width: 30px"> ';
	insert += '<input name="item_color_m_'+count+'" class="text" type="text" style="width: 30px"> ';
	insert += '<input name="item_color_y_'+count+'" class="text" type="text" style="width: 30px"> ';
	insert += '<input name="item_color_k_'+count+'" class="text" type="text" style="width: 30px"> ';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<select name="item_dependency_'+count+'" class="text" style="width: 100px">';
		insert += '<option value="0"> <?=$_LANG->get('Fix');?></option>';
	<?	foreach ($all_items AS $dep_item){?>
			insert += '<option value="<?=$dep_item->getId()?>"> <?=$dep_item->getTitle();?></option>'; 	
	<?	}?>
	insert += '</select>';
	insert += '</td>';
	
	insert += '<td class="content_row">';
	insert += '<select name="item_group_'+count+'" class="text" style="width: 100px">';
	insert += '<option value="0" selected="selected">A</option>';
	insert += '<option value="1">B</option>';
	insert += '<option value="2">C</option>';
	insert += '<option value="3">D</option>';
	insert += '<option value="4">E</option>';
	insert += '<option value="5">F</option>';
	insert += '<option value="6">G</option>';
	insert += '<option value="7">H</option>';
	insert += '<option value="8">I</option>';
	insert += '<option value="9">J</option>';
	insert += '<option value="10">K</option>';
	insert += '<option value="11">L</option>';
	insert += '<option value="12">M</option>';
	insert += '<option value="13">N</option>';
	insert += '<option value="14">O</option>';
	insert += '</select></td>';
	
	insert += '<td class="content_row">';
	insert += '<input type="checkbox" name="item_site_'+count+'" value="1" />';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input type="checkbox" name="item_predefined_'+count+'" value="1" />';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input type="checkbox" name="item_readonly_'+count+'" value="1" />';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input type="checkbox" name="item_position_'+count+'" value="1" />';
	insert += '</td>';
	insert += '</tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_quantity').value = count;
	document.getElementById('td_empty').style.display ='none';
}

function addPriceRow()
{
	var obj = document.getElementById('table-prices');
	var count = parseInt(document.getElementById('count_sep_quantity').value) + 1;
	var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '&ensp;';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="perso_price_max_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 60px"> <?=$_LANG->get('Stk.')?>';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="perso_price_price_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 70px"> <?=$_USER->getClient()->getCurrency()?>';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input type="checkbox" name="perso_price_show_'+count+'" class="text" value="1">';
	insert += '</td></tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_sep_quantity').value = count;
}
</script>

<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript">
	$(document).ready(function() {
		$("a#picture_select").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$("a#picture_select2").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Personalisierung hinzuf&uuml;gen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Personalisierung bearbeiten')?>
			<?//if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Artikel kopieren')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#perso_edit').submit();",'glyphicon-floppy-disk');
if ($perso->getId()>0){
	$quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/personalization/personalization.php&exec=delete&id=".$perso->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form 	action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="perso_edit" id="perso_edit"   
		onSubmit="return checkform(new Array(this.perso_title))" enctype="multipart/form-data">
	<? // -------------------- Pesonalisierungdetails ------------------------------------------ ?>
	<div class="box1">
		<input type="hidden" name="exec" value="edit"> 
		<input type="hidden" name="subexec" value="save"> 
		<input type="hidden" name="id" value="<?=$perso->getId()?>">
		<input type="hidden" name="perso_picture1" id="perso_picture1" value="<?=$perso->getPicture()?>">
		<input type="hidden" name="perso_picture2" id="perso_picture2" value="<?=$perso->getPicture2()?>">
		
		<table width="100%">
			<colgroup>
				<col width="180">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
				<td class="content_row_clear">
				<input id="perso_title" name="perso_title" type="text" class="text" 
					value="<?=$perso->getTitle()?>" style="width: 370px">
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Kommentar (intern)')?></td>
				<td class="content_row_clear">
					<textarea id="perso_comment" name="perso_comment" rows="4" cols="50" 
							  class="text"><?=stripslashes($perso->getComment())?></textarea>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Artikel')?></td>
				<td class="content_row_clear">
					<select id="perso_article" name="perso_article" style="width: 220px">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
					<? 	foreach ($all_article as $art){?>
							<option value="<?=$art->getId()?>"
								<?if ($perso->getArticle()->getId() == $art->getId()) echo "selected" ?>><?= $art->getTitle()?></option>
					<?	}?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Kunde')?></td>
				<td class="content_row_clear">
					<select id="perso_customer" name="perso_customer" style="width: 220px">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
					<? 	foreach ($all_customer as $cust){?>
							<option <?if ($perso->getCustomer()->getId() == $cust->getId()) echo "selected" ?>
								value="<?=$cust->getId()?>"><?= $cust->getNameAsLine()?></option>
					<?	}//Ende else?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Typ')?></td>
				<td class="content_row_clear">
					<select id="perso_type" name="perso_type" style="width: 220px">
						<option value="0" <?if($perso->getType() == 0) echo 'selected="selected"';?>><?=$_LANG->get('Vorderseite')?></option>
						<option value="1" <?if($perso->getType() == 1) echo 'selected="selected"';?>><?=$_LANG->get('Vorder- und R&uuml;ckseite')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Breite')?> &amp; <?=$_LANG->get('H&ouml;he')?> <?=$_LANG->get('(Endformat)')?></td>
				<td class="content_row_clear">
					<input id="perso_format_width" name="perso_format_width" type="text" class="text" 
							value="<?=printPrice($perso->getFormatwidth())?>" style="width: 50px"> 
					&ensp; <b> X </b>&ensp; 
					<input id="perso_format_height" name="perso_format_height" type="text" class="text" 
							value="<?=printPrice($perso->getFormatheight())?>" style="width: 50px"> mm
					&emsp;
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Anschnitt')?></td>
				<td class="content_row_clear">
					<input id="perso_format_anschnitt" name="perso_format_anschnitt" type="text" class="text"
						   value="<?=printPrice($perso->getAnschnitt())?>" style="width: 50px"> mm
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Zeile f&uuml;r Zeile')?></td>
				<td class="content_row_clear">
					<select name="perso_linebyline" id="perso_linebyline">
						<option value="0" <? if($perso->getLineByLine() == 0) echo 'selected="selected"';?>>Alle Fix</option>
					  	<option value="1" <? if($perso->getLineByLine() == 1) echo 'selected="selected"';?>>Zeile f&uuml;r Zeile (von oben)</option>
					  	<option value="2" <? if($perso->getLineByLine() == 2) echo 'selected="selected"';?>>Zeile f&uuml;r Zeile (von unten)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Im Shop versteckt?')?></td>
				<td class="content_row_clear">
					<input type="checkbox" name="perso_hidden" value="1" <?php if ($perso->getHidden() == "1"){echo " checked ";}?> />
				</td>
			</tr>
			<?if ($perso->getId() != 0 && $perso->getCrtuser() != 0){// Ersteller nur beim Bearbeiten ausgeben?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Angelegt von')?></td>
					<td class="content_row_clear">
						<?=$perso->getCrtuser()->getNameAsLine()?>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Angelegt am')?></td>
					<td class="content_row_clear">
						<?=date('d.m.Y - H:i', $perso->getCrtdate())?> <?=$_LANG->get('Uhr')?>
					</td>
				</tr>
				<?if ($perso->getUptuser() != 0 && $perso->getUptdate() != 0){
						// Geaendert von/am nur bei bearbeiteten Artikeln ausgeben?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Ge&auml;ndert von')?></td>
					<td class="content_row_clear">
						<?=$perso->getUptuser()->getNameAsLine()?>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Ge&auml;ndert am')?></td>
					<td class="content_row_clear">
						<?=date('d.m.Y - H:i', $perso->getUptdate())?> <?=$_LANG->get('Uhr')?>
					</td>
				</tr>
				<?} // Ende if(geaendert gesetzt) ?>
			<?} // Ende if(neuer Artikel) ?>
		</table>
		<table>
			<tr>
				<td class="content_row_header" >
					<?=$_LANG->get('Hintergrund (Vorderseite)')?></br>
					<a  href="libs/modules/personalization/personalization.iframe.php?picture=1" id="picture_select" class="products"
					><input type="button"  width="80px" class="button" value="<?=$_LANG->get('Ausw&auml;hlen')?>"></a>
				</td>
				<td id="td_picture1" class="content_row_clear">
					<?if ($perso->getPicture()!= NULL && $perso->getPicture() !=""){?>
						<iframe width="400" height="300" scrolling="no" src="libs/modules/personalization/personalization.preview.php?pdffile=<?=$perso->getPicture()?>" style="overflow:hidden;"></iframe>
					<?} else {?>
						&nbsp; ...
					<? } ?>
				</td>
				<td class="content_row_header" >
					<?=$_LANG->get('Hintergrund (R&uuml;ckseite)')?></br>
					<a  href="libs/modules/personalization/personalization.iframe.php?picture=2" id="picture_select2" class="products"
					><input type="button"  width="80px" class="button" value="<?=$_LANG->get('Ausw&auml;hlen')?>"></a>
				</td>
				<td id="td_picture2" class="content_row_clear">
					<?if ($perso->getPicture2()!= NULL && $perso->getPicture2() !=""){?>
						<iframe width="400" height="300" scrolling="no" src="libs/modules/personalization/personalization.preview.php?pdffile=<?=$perso->getPicture2()?>" style="overflow:hidden;"></iframe>
					<?} else {?>
						&nbsp; ...
					<? } ?>
				</td>
			</tr>
			<tr>
				<td class="content_row_header" >
					<?=$_LANG->get('Vorschaubild')?></br>
					<input type="file" id="preview" name="preview" width="80px"></a>
				</td>
				<td id="td_preview" class="content_row_clear">
					<?if ($perso->getPreview()!= NULL && $perso->getPreview() !=""){?>
						<img width="400" height="300" src="docs/personalization/<?=$perso->getPreview()?>"></img>
					<?} else {?>
						&nbsp;
					<? } ?>
				</td>
			</tr>
		</table>
	</div>

<?if ($_REQUEST["exec"] != "new"){ 
	// PDF Anzeigen lassen, damit nicht alles in HTML doppelt gemacht werden muss 
	
	// dokumente holen
	$docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION,
										"requestId" => $perso->getId(),
										"module" => Document::REQ_MODULE_PERSONALIZATION));?>
	<br>
	<h1><?=$_LANG->get('Vorschau');?></h1>
	<div class="box2" align="center">
		<table>
		<tr>
			<td align="center">
				<?
				// PDF ausgeben
				if (count($docs) && $docs != false){
					$tmp_id =$_USER->getClient()->getId();
					$hash = $docs[0]->getHash();
					
					$obj_height = ($perso->getFormatheight() / 10 * 300 / 2.54 + 20) / 2;
					$obj_width = ($perso->getFormatwidth() / 10 * 300 / 2.54 + 20) / 2;
					?>
					<iframe width="<?=$obj_width?>" height="<?=$obj_height?>" scrolling="no" src="libs/modules/personalization/personalization.preview.php?pdffile=<?=$tmp_id?>.per_<?=$hash?>_e.pdf" style="overflow:hidden;"></iframe>
				<? } ?>
			</td>
		</tr>
		</table>
	</div>
	

	
	<? // --------------- Eingabefelder -------------------------------------------------------- ?>
	<br/>
	<input	type="hidden" name="count_quantity" id="count_quantity"
			value="<? if(count($all_items) > 0) echo count($all_items); else echo "1";?>">
	
	<h1>
		<?=$_LANG->get('Eingabefelder')?> 
		&emsp; &emsp;
		<span class="glyphicons glyphicons-plus pointer" onclick="addItemRow()"
						 title="Feld hinzuf&uuml;gen"></span>
	</h1>
	<div class="box1">
		<table id="table-items">
			<colgroup>
			    <col width="32">
	        	<col width="220">
	        	<col width="60">
	        	<col width="150">		<? // x/y-Position ?>
	        	<col width="150">
	        	<col width="90">
	        	<col width="60">		<? // Text-Ausrichtung ?>
	        	<col width="80">
	        	<col width="100">
	        	<col width="150">
	        	<col width="100">
	        	<col>
	        	<col>
	        	<col>
	    	</colgroup>
			<tr>
			    <td class="content_row_header">&nbsp;</td>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> / <?=$_LANG->get('Platzhalter')?></td>
				<td class="content_row_header"><?=$_LANG->get('Schrift- gr&ouml;&szlig;e')?></td>
				<td class="content_row_header"><?=$_LANG->get('x-Position')?>** / <?=$_LANG->get('y-Position')?>**</td>
				<td class="content_row_header"><?=$_LANG->get('Breite')?>* / <?=$_LANG->get('H&ouml;he')?>*</td>
				<td class="content_row_header"><?=$_LANG->get('Tab')?></td>
				<td class="content_row_header"><?=$_LANG->get('Typ')?></td>
				<td class="content_row_header"><?=$_LANG->get('Text- Ausrichtung')?></td>
				<td class="content_row_header"><?=$_LANG->get('Schriftart')?></td>
				<td class="content_row_header"><?=$_LANG->get('Zeilenabstand')?></td>
				<td class="content_row_header"><?=$_LANG->get('Schriftfarbe CMYK')?></td>
				<td class="content_row_header"><?=$_LANG->get('Abh&auml;ngigkeit')?> &emsp; <?=$_LANG->get('(Y-Pos)')?></td>
				<td class="content_row_header"><?=$_LANG->get('Gruppe')?></td>
				<td class="content_row_header"><?=$_LANG->get('R&uuml;ck-S.')?></td>
				<td class="content_row_header"><?=$_LANG->get('Vordef.')?></td>
				<td class="content_row_header"><?=$_LANG->get('R.O.')?></td>
				<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
			</tr>
		<? 	
		if (count($all_items) > 0 && $all_items != FALSE){
			$y=1;
			foreach ($all_items as $item){ $item->getXposAbsolute();?>
				<tr>
					<td class="content_row">
					    <input type="number" name="item_sort_<?=$y?>" value="<?=$item->getSort()?>" style="width: 30px"/>
					</td>
					<td class="content_row">
						<input type="hidden" name="item_id_<?=$y?>" value="<?=$item->getId()?>">
						<input 	name="item_title_<?=$y?>" class="text" type="text"
								value ="<?=$item->getTitle()?>" style="width: 200px">
					</td>
					<td class="content_row">
						<input 	name="item_textsize_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($item->getTextsize(), 1)?>" style="width: 50px">
					</td>
					<td class="content_row">
						<input 	name="item_xpos_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($item->getXpos(), 3)?>" style="width: 50px">
						x
						<input 	name="item_ypos_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($item->getYpos(), 3)?>" style="width: 50px"> mm
					</td>
					<td class="content_row">
						<input 	name="item_width_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($item->getWidth(), 3)?>" style="width: 50px">
						x
						<input 	name="item_height_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($item->getHeight(), 3)?>" style="width: 50px"> mm
					</td>
					<td class="content_row">
						<input 	name="item_tab_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($item->getTab(), 2)?>" style="width: 50px">mm
					</td>
					<td class="content_row">
						<select name="item_boxtype_<?=$y?>" class="text" style="width: 80px">
							<option value="1" <?if($item->getBoxtype()==1) echo "selected";?>> <?=$_LANG->get('Textfeld');?></option>
							<option value="2" <?if($item->getBoxtype()==2) echo "selected";?>> <?=$_LANG->get('Textbox');?> </option>
						</select>
					</td>
					<td class="content_row">
						<select name="item_justification_<?=$y?>" class="text" style="width: 70px">
							<option value="0" <?if($item->getJustification()==0) echo "selected";?>> <?=$_LANG->get('links');?></option>
							<option value="1" <?if($item->getJustification()==1) echo "selected";?>> <?=$_LANG->get('zentral');?></option>
							<option value="2" <?if($item->getJustification()==2) echo "selected";?>> <?=$_LANG->get('rechts');?> </option>
						</select>
					</td>
					<td class="content_row">
							<? /**************** Anpassungen immer auch in der JavaScript-Funktion machen !  ****************?>
						<select name="item_font_<?=$y?>" class="text" style="width: 100px">
							<option value="2" <?if($item->getFont()==2) echo "selected";?>> <?=$_LANG->get('Courier');?></option>
							<option value="3" <?if($item->getFont()==3) echo "selected";?>> <?=$_LANG->get('Helvetica');?></option>
							<option value="4" <?if($item->getFont()==4) echo "selected";?>> <?=$_LANG->get('Times-Roman');?> </option>
							<option value="5" <?if($item->getFont()==5) echo "selected";?>> <?=$_LANG->get('Trade Gothic');?> </option>
							<option value="6" <?if($item->getFont()==6) echo "selected";?>> <?=$_LANG->get('Frutiger');?> </option>
							<option value="99" <?if($item->getFont()==99) echo "selected";?>> <?=$_LANG->get('Symbol');?> </option>
						</select>
						***/?>
						<select name="item_font_<?=$y?>" class="text" style="width: 100px">
						<?	foreach ($all_fonts AS $font){ ?>
								<option value="<?=$font->getId()?>" <?if($item->getFont() == $font->getId()) echo 'selected="selected"';?>
										> <?=$font->getTitle();?> </option>
						<?	} ?>
						</select>
					</td>
					<td class="content_row" align="center">
						<input 	name="item_spacing_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($item->getSpacing(),1)?>" style="width: 50px">
					</td>
					<td class="content_row">
						<input 	name="item_color_c_<?=$y?>" class="text" type="text"
								value ="<?=$item->getColor_c()?>" style="width: 30px">
						<input 	name="item_color_m_<?=$y?>" class="text" type="text"
								value ="<?=$item->getColor_m()?>" style="width: 30px">
						<input 	name="item_color_y_<?=$y?>" class="text" type="text"
								value ="<?=$item->getColor_y()?>" style="width: 30px">
						<input 	name="item_color_k_<?=$y?>" class="text" type="text"
								value ="<?=$item->getColor_k()?>" style="width: 30px">
					</td>
					<td class="content_row">
						<select name="item_dependency_<?=$y?>" class="text" style="width: 100px">
							<option value="0" <?if($item->getDependencyID() == 0) echo 'selected="selected"';?>
									> <?=$_LANG->get('Fix');?></option>
						<?	foreach ($all_items AS $dep_item){
								if($dep_item->getId() != $item->getId()){?>
									<option value="<?=$dep_item->getId()?>" <?if($item->getDependencyID() == $dep_item->getId()) echo 'selected="selected"';?>
									> <?=$dep_item->getTitle();?></option> 	
						<?		}
							}?>
						</select>
					</td>
					<td class="content_row">
						<select name="item_group_<?=$y?>" class="text" style="width: 100px">
							<option value="0" <?if($item->getGroup() == 0) echo 'selected="selected"';?>>A</option>
							<option value="1" <?if($item->getGroup() == 1) echo 'selected="selected"';?>>B</option>
							<option value="2" <?if($item->getGroup() == 2) echo 'selected="selected"';?>>C</option>
							<option value="3" <?if($item->getGroup() == 3) echo 'selected="selected"';?>>D</option>
							<option value="4" <?if($item->getGroup() == 4) echo 'selected="selected"';?>>E</option>
							<option value="5" <?if($item->getGroup() == 5) echo 'selected="selected"';?>>F</option>
							<option value="6" <?if($item->getGroup() == 6) echo 'selected="selected"';?>>G</option>
							<option value="7" <?if($item->getGroup() == 7) echo 'selected="selected"';?>>H</option>
							<option value="8" <?if($item->getGroup() == 8) echo 'selected="selected"';?>>I</option>
							<option value="9" <?if($item->getGroup() == 9) echo 'selected="selected"';?>>J</option>
							<option value="10" <?if($item->getGroup() == 10) echo 'selected="selected"';?>>K</option>
							<option value="11" <?if($item->getGroup() == 11) echo 'selected="selected"';?>>L</option>
							<option value="12" <?if($item->getGroup() == 12) echo 'selected="selected"';?>>M</option>
							<option value="13" <?if($item->getGroup() == 13) echo 'selected="selected"';?>>N</option>
							<option value="14" <?if($item->getGroup() == 14) echo 'selected="selected"';?>>O</option>
						</select>
					</td>
					<td class="content_row">
						<input type="checkbox" name="item_site_<?=$y?>" value="1" 
								<?if($item->getReverse() == 1) echo 'checked="checked"';?>/>
					</td>
					<td class="content_row">
						<input type="checkbox" name="item_predefined_<?=$y?>" value="1" 
								<?if($item->getPreDefined() == 1) echo 'checked="checked"';?>/>
					</td>
					<td class="content_row">
						<input type="checkbox" name="item_readonly_<?=$y?>" value="1" 
								<?if($item->getReadOnly() == 1) echo 'checked="checked"';?>/>
					</td>
					<td class="content_row">
						<input type="checkbox" name="item_position_<?=$y?>" value="1" 
								<?if($item->getPosition() == 1) echo 'checked="checked"';?>/>
					</td>
				</tr>
		<? 	$y++;
			} 	
		} else { ?>
			<tr class="<?=getRowColor(0)?>"><td class="content_row" colspan="8" id="td_empty" align="center"><?=$_LANG->get('Keine Felder angelegt');?></td></tr>
	<?	} ?>
		</table>
		<br/>
		* <?=$_LANG->get('Eingabefeld wird gel&ouml;scht, falls Breite u. H&ouml;he = 0')?> <br/>
		** <?=$_LANG->get('x- und y-Position ausgehend von der LINKEN OBEREN Ecke (PDF) + Anschnitt')?><br/>
		*** <?=$_LANG->get('Textausrichtung in den Eingabefeldern ist immer UNTEN LINKS')?>
	</div>
	<br/>
	<? // ---------------------------------------- Preisstaffeln ----------------------------------------------------------------------- ?>
	<input 	type="hidden" name="count_sep_quantity" id="count_sep_quantity" 
			value="<? if(count($allprices) > 0) echo count($allprices); else echo "1";?>">
	<h1><?=$_LANG->get('Preisstaffeln')?></h1>
	<div class="box2">
		<table id="table-prices">
			<colgroup>
	        	<col width="40">
	        	<col width="20">
	        	<col width="120">
	        	<col width="120">
	        	<col width="120">
	    	</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
				<td class="content_row_header">&ensp;</td>
				<td class="content_row_header"><?=$_LANG->get('Menge')?></td>
				<td class="content_row_header"><?=$_LANG->get('Preis')?>*</td>
				<td class="content_row_header"><?=$_LANG->get('Preis anzeigen')?></td>
				<td class="content_row_header"><span class="glyphicons glyphicons-plus pointer" onclick="addPriceRow()"></span></td>
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
					<td class="content_row_clear">
						&ensp;
					</td>
					<td class="content_row_clear">
						<input 	name="perso_price_max_<?=$y?>" class="text" type="text"
								value ="<?=$allprices[$y][sep_max]?>" style="width: 60px">
						<?=$_LANG->get('Stk.')?>
					</td>
					<td class="content_row_clear">
						<input 	name="perso_price_price_<?=$y?>" class="text" type="text"
								value ="<?=printPrice($allprices[$y][sep_price])?>" style="width: 70px">
						<?=$_USER->getClient()->getCurrency()?>
					</td>
					<td class="content_row_clear">
						<input type="checkbox" name="perso_price_show_<?=$y?>" class="text" value="1"
								<?if ($allprices[$y]["sep_show"] == 1 ) echo "checked";?>> 
					</td>
				</tr>
			<? } //Ende alle Preis-Staffeln?>
		</table>
		<br/>* <?=$_LANG->get('Staffelpreis wird gel&ouml;scht, falls Preis = 0')?> 
	</div>
	<br/>
<? } ?>
</form>