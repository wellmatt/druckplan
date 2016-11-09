<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			24.01.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
// require_once 'thirdparty/ezpdf/class.ezpdf.php';
require_once 'thirdparty/ezpdf/new/src/Cezpdf.php';
require_once 'warehouse.class.php';

if($_REQUEST["exec"] == "edit"){
	require_once 'warehouse.edit.php';
}else{
	if($_REQUEST["exec"] == "delete"){
		$del_stock = new Warehouse((int)$_REQUEST["stockid"]);
		$savemsg = getSaveMessage($del_stock->delete());
	}
	
	//  Sortierung verwalten
	$orderby = Warehouse::ORDER_NAME;
	if($_REQUEST["orderby"] == "name") $orderby = Warehouse::ORDER_NAME; 
	if($_REQUEST["orderby"] == "ordernum") $orderby = Warehouse::ORDER_ORDERNUMBER;
	if($_REQUEST["orderby"] == "recall") $orderby = Warehouse::ORDER_RECALL;
	//if($_REQUEST["orderby"] == "cust") $orderby = Warehouse::ORDER_CUSTOMER;
	
	$orderhow = " ASC";
	
	if($_SESSION["warehouse"]["order"] == $_REQUEST["orderby"] && $_REQUEST["orderby"] != NULL){
		if($_SESSION["warehouse"]["orderhow"] == " ASC"){
			$orderhow = " DESC";
		} else {
			$orderhow = " ASC";
		}
	}
	 
	$_SESSION["warehouse"]["order"] = $_REQUEST["orderby"];
	$_SESSION["warehouse"]["orderhow"] = $orderhow;
	
	//Such-Optionen verwalten
	$search["name"]=trim($_REQUEST["sql_name"]);
	$search["cust"]=(int)$_REQUEST["sql_cust"];
	$search["input"]=trim($_REQUEST["sql_input"]);
	$search["ordernumber"]=trim($_REQUEST["sql_ordernum"]);
	//gln 27.01.2014, zusaetzliche Auswahl Artikelnr.
	$search["artid"]=trim($_REQUEST["sql_artnr"]);
	
	// Alle Lagerplaetze holen
	$all_stocks = Warehouse::getAllStocks($orderby.$orderhow , $search);
	$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME);
	//gln 24.01.2014 27.01.2014, zusaetzliche Auswahl Artikelnr.
	$allarticle = Article::getAllArticle(Article::ORDER_NUMBER);
	
	
	// Start der PDF-Datei
	$pdf = new Cezpdf("A4", "landscape"); // Fuer den Export der aktuell angezeigten Liste
// 	$pdf->selectFont("./libs/thirdparty/pdfClassesAndFonts/fonts/Helvetica.afm");
	$pdf->ezSetMargins(20, 20, 40, 30);
	$filename = $_CONFIG->docsBaseDir."warehouse/".$_USER->getId()."-Lager.pdf";
	$pdf->ezText("Export des Lagers (Stand: ".date('d.m.Y').")", 12);
	$pdf->ezText("", 12);
	unset($data); 
	$data = Array();
	$attr = Array  (  "showHeadings" => 0, "shaded" => 1, "width" => "735", "xPos" => "left", "showLines" => 0,
			"rowGap" => 0, "colGap" => 4, "protectRows" => 18, "xOrientation"=>"right",  
			"cols" =>   Array (
					"col1"   => Array("width" => "70", "align"=>"center"),
					"col2"   => Array("width" => "150"),
					"col3"   => Array("width" => "300"),
					"col4"   => Array("width" => "80"),
					"col5"   => Array("width" => "150")
			)
	);
?>

<table border="0" cellpadding="0" cellspacing="0" width="1000">
	<tr>
		<td height="30" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<span style="font-size: 13px"><?=$_LANG->get('Lager-&Uuml;bersicht')?></span>
		</td>
		<td width="240" class="content_header" align="right">
			<span style="font-size: 13px">&ensp;
			<?
			if(Warehouse::hasGroupRight($_SESSION[user_id])){?>
				<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&subexec=new"><img src="images/icons/wooden-box--plus.png"><?=$_LANG->get('Lagerplatz erstellen')?></a>
			<?} ?>
			</span>
		</td>
		<td align="right" width="580" style="padding-right:10px"><?=$savemsg?></td>
	</tr>
</table>

<?//--------------------- Suchfeld ----------------------------------------------------------------------------------
?>
<?/*gln*/?>
<table border="0" cellpadding="0" cellspacing="0" >
	<tr>
		<td>
			<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_stocksearch">
				<input type="hidden" name="subexec" value="search">
				<input type="hidden" name="mid" value="<?=$_REQUEST["mid"]?>">
				<div class="box2" style="min-height:210px;width:400px">
					<table border="0" class="content_table" cellpadding="3" cellspacing="0" >
						<colgroup>
							<col width="150">
							<col>
						</colgroup>
						<tr>
							<td class="content_row_header" colspan="2">Suchoptionen</td>
						</tr>
						<tr>
							<td class="content_row">Material / Ware / Inhalt</td>
							<td class="content_row">
								<input 	name="sql_input" type="text" class="text" style="width:250px" value="<?=$_REQUEST["sql_input"]?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</td>
						</tr>
						<tr>
							<td class="content_row">Kunde</td>
							<td class="content_row">
								<select type="text" id="sql_cust" name="sql_cust" style="width:250px"
									onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
									<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
									<? 	foreach ($allcustomer as $cust){?>
										<option value="<?=$cust->getId()?>"
										<?if ($search["cust"] == $cust->getId()) echo 'selected="selected"' ?>><?= $cust->getNameAsLine()?></option>
									<?	} ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="content_row">Lagerplatz</td>
							<td class="content_row">
								<input 	name="sql_name" type="text" class="text" style="width:250px" value="<?=$_REQUEST["sql_name"]?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</td>
						</tr>
						<tr>
							<td class="content_row">Auftragsnummer</td>
							<td class="content_row">
								<input 	name="sql_ordernum" type="text" class="text" style="width:250px" value="<?=$_REQUEST["sql_ordernum"]?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</td>
						</tr>
						<? /*gln 24.01.2014, zusaetzliche Auswahl Artikelnr.*/ ?>
						<tr>
							<td class="content_row">Artikel</td>
							<td class="content_row">
								<select type="text" id="sql_artnr" name="sql_artnr" style="width:250px"
										onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
									<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
								<? 	foreach ($allarticle as $art){?>
										<option value="<?=$art->getId()?>"
										<?if ($search["artid"] == $art->getId()) echo 'selected="selected"' ?>><?= $art->getNumber()?> <?= $art->getTitle()?></option>
								<?	} ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="content_row" align="center">
								<a href="<?=$filename?>"> 
									<img src="images/icons/wooden-box--arrow.png" title="<?=$_LANG->get('Export der aktuellen Liste')?>"/><?=$_LANG->get('Export');?></a>
							</td>
							<td class="content_row" align="right">
								<input type="submit" value="<?=$_LANG->get('Suche starten')?>">
							</td>
						</tr>
					</table>
				</div>
			</form>
		</td>
		<td>&emsp;</td>
		<td>
			<? /* ---------------------------- Lager -------------------------------------------------------------*/?>
			<?/*28.01.2014,gln: Anzeige unterschrittene Mindestmengen jetzt hier anstatt auf home */?>
			<div class="box1" style="min-height:210px;padding-left:16px;">
				<div style="overflow: auto; height:195px;">
					<?require_once("./libs/modules/warehouse/warehouse.lowamount.php");?>
				</div>
			</div>
		</td>
	</tr>
</table>
<br/>
<?//------------------ Tabelle mit den Lagereinheiten ---------------------------------------------------------------?>
<div class="box1">
<table border="0" class="content_table" cellpadding="3" cellspacing="0" width="100%">
	<colgroup>
		<col width="80">
		<col width="220">
		<col >
		<col width="150">
		<col width="120">
		<col width="100">
		<col width="80">
		<col width="25">
		<col width="100">
	</colgroup>
	<!-- tr>
		<td class="content_tbl_header" colspan="7">Lager</td>
	</tr-->
	<tr>
		<td class="content_row_header" align="center"><a class="link" href="index.php?page=<?=$_REQUEST['page']?>&orderby=name"><?=$_LANG->get('Lagerplatz');?></a></td>
		<td class="content_row_header"><?=$_LANG->get('Kunde / Lieferant');?></td>
		<td class="content_row_header"><?=$_LANG->get('Material / Ware / Inhalt');?></td>
		<td class="content_row_header"><?=$_LANG->get('Artikel');?></td>
		<td class="content_row_header"><a class="link" href="index.php?page=<?=$_REQUEST['page']?>&orderby=ordernum"><?=$_LANG->get('Auftragsnummer');?></a></td>
		<td class="content_row_header"><?=$_LANG->get('Lagermenge (Reserviert)');?></td>
		<td class="content_row_header"><a class="link" href="index.php?page=<?=$_REQUEST['page']?>&orderby=recall"><?=$_LANG->get('Abruf-Datum');?></a></td>
		<td class="content_row_header">&ensp;<? // Kommentar?></td>
		<td class="content_row_header" align="center"><?=$_LANG->get('Optionen');?></td>
	</tr>
	<?
	$data[0]["col1"] = "<b>".$_LANG->get('Lagerplatz')."</b>";
	$data[0]["col2"] = "<b>".$_LANG->get('Kunde')."</b>";
	$data[0]["col3"] = "<b>".$_LANG->get('Material / Inhalt / Ware')."</b>";
	$data[0]["col4"] = "<b>".$_LANG->get('Menge (Min.)')."</b>";
	$data[0]["col5"] = "<b>".$_LANG->get('Artikel')."</b>";
	
	if(count($all_stocks) > 0 && $all_stocks != FALSE){
		$x=0;
		foreach ($all_stocks AS $stock){ 
				$data[$x+1]["col1"] = "<b>".$stock->getName()."</b>";
				$data[$x+1]["col2"] = $stock->getCustomer()->getNameAsLine();
				$data[$x+1]["col3"] = $stock->getInput();
				if ($stock->getAmount() >0){
					$data[$x+1]["col4"] = $stock->getAmount(). "(".$stock->getMinimum().")";
				} else {
					$data[$x+1]["col4"] = " ";
				}
				if ($stock->getArticle()->getID() > 0){
					$data[$x+1]["col5"] = $stock->getArticle()->getTitle(). " \n "."(Art-Nr.: ".$stock->getArticle()->getNumber().")";
				} else {
					$data[$x+1]["col5"] = "";
				}
			
			?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row" align="center">&ensp;<?=$stock->getName()?>&ensp;</td>
				<td class="content_row"><?=$stock->getCustomer()->getNameAsLine()?>&ensp;</td>
				<td class="content_row">
					<?echo nl2br($stock->getInput());?> &ensp;
				</td>
				<td class="content_row">
					<?if ($stock->getArticle()->getID() > 0){
						echo $stock->getArticle()->getTitle(). "<br>";
					 	echo "(Art-Nr.: ".$stock->getArticle()->getNumber().")";
					 } ?> &emsp;
				</td>
				<td class="content_row"><?=$stock->getOrdernumber()?>&ensp;</td>
				<td class="content_row"><?=$stock->getAmount()?>&ensp;</td>
				<td class="content_row">
					<?if($stock->getRecall() != 0){ echo date('d.m.Y', $stock->getRecall());}?>&ensp;
				</td>
				<td class="content_row" align="center">
				<?	if($stock->getComment() != NULL && $stock->getComment() != ""){?>
					<img src="./images/icons/balloon-ellipsis.png" alt="Kommentar" title="<?=$stock->getComment()?>" />
					<!-- img src="./images/icons/exclamation-octagon.png" alt="Kommentar" title="<?=$stock->getComment()?>" /-->	 
				<?	} else {
						echo "&ensp;";
					} ?> &ensp;
				</td>
				<td class="content_row" align="center">
					<?
					if(Warehouse::hasGroupRight($_SESSION[user_id])){?>
						<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&stockid=<?=$stock->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
						&ensp;
						<a href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&stockid=<?=$stock->getId()?>')"><span style="color:red" class="glyphicons glyphicons-remove"></span></a>
					<?} else {
						echo "&ensp;<br/> &ensp;";
					}
					?>
				</td>
			</tr>
		<?	$x++;
		} //ENDE foreach($all_stocks)	
	} else {
		echo '<tr class="'.getRowColor($x) .'"> <td colspan="8" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Keine Lagerpl&auml;tze gefunden').'</span>';
		echo '</td></tr>';
	}?>
</table>
<br/>
</div>
<br/>
	
<?	// Tabelle mit den Inhalten einfuegen
	$pdf->ezTable($data,$type,$dummy,$attr);
	// PDF-Datei schreiben 
	$fp = fopen($filename, "w");
	$pdfdata    = $pdf->output();
	fwrite($fp, $pdfdata);
	fclose($fp);
	
} // Ende else (exec...)?>