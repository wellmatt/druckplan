<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			17.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
//		Diese Datei behandelt die globale Suchfunktion auf der Startseite
//
// ----------------------------------------------------------------------------------
require_once 'warehouse.class.php';

if ($main_searchstring != "" && $main_searchstring!=NULL){
	// $main_searchstring siehe /libs/basic/home.php
	$all_stocks= Warehouse::getAllStocksForHome(Warehouse::ORDER_NAME,$main_searchstring);
} else {
	$all_stocks=FALSE;	
} 
?>

<h1><?=$_LANG->get('Suchergebnisse Lager');?></h1>
<table border="0" class="content_table" cellpadding="3" cellspacing="0" width="100%">
	<colgroup>
		<col width="70">
		<col width="160">
		<col >
		<col width="120">
		<col width="100">
		<col width="80">
		<col width="25">
	</colgroup>
	<? if(count($all_stocks) > 0 && $all_stocks != FALSE){?>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Lagerplatz');?></td>
			<td class="content_row_header"><?=$_LANG->get('Kunde / Lieferant');?></td>
			<td class="content_row_header"><?=$_LANG->get('Material / Ware / Inhalt');?></td>
			<td class="content_row_header"><?=$_LANG->get('Auftragsnummer');?></td>
			<td class="content_row_header"><?=$_LANG->get('Lagermenge');?></td>
			<td class="content_row_header"><?=$_LANG->get('Abruf-Datum');?></td>
			<td class="content_row_header">&ensp;<? // Kommentar?></td>
		</tr>
	<?	$x=0;
		foreach ($all_stocks AS $stock){ ?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row" align="center">
					<a href="index.php?page=libs/warehouse/warehouse.php&exec=edit&stockid=<?=$stock->getId();?>" title="Lagerplatzdetails"><?=$stock->getName();?></a>
				</td>
				<td class="content_row"><?=$stock->getCustomer()->getNameAsLine()?>&ensp;</td>
				<td class="content_row">
					<?echo nl2br($stock->getInput());?> &ensp;
				</td>
				<td class="content_row"><?=$stock->getOrdernumber()?>&ensp;</td>
				<td class="content_row"><?=$stock->getAmount()?>&ensp;</td>
				<td class="content_row">
					<?if($stock->getRecall() != 0){ echo date('d.m.Y', $stock->getRecall());}?>&ensp;
				</td>
				<td class="content_row" align="center">
				<?	if($stock->getComment() != NULL && $stock->getComment() != ""){?>
					<span class="glyphicons glyphicons-ipad" title="<?=$stock->getComment()?>"></span>
					<!-- img src="./images/icons/exclamation-octagon.png" alt="Kommentar" title="<?=$stock->getComment()?>" /-->	 
				<?	} else {
						echo "&ensp;";
					} ?> &ensp;
				</td>
			</tr>
		<?	$x++;
		} //ENDE foreach($all_stocks)	
	} else {
		echo '<tr class="'.getRowColor(0) .'"> <td colspan="7" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Keine Lagerpl&auml;tze gefunden').'</span>';
		echo '</td></tr>';
	}?>
</table>

