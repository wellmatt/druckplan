<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			21.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
// Diese Datei steuert den den Lagerabruf fuer den jeweiligen Tag auf der Home-Seite
//
// ----------------------------------------------------------------------------------

require_once './libs/modules/warehouse/warehouse.class.php';

$all_stocks = Warehouse::getAllStocksWithLowAmount();

?>
<!-------------------------------- INNER START --------------------------->
<h1><?=$_LANG->get('Unterschrittene Mindestmengen')?></h1>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed">
	<colgroup>
		<col width="70" valign="top">
		<col>
		<col width="120" valign="top">
		<col width="60" valign="top">
		<col width="40" valign="top">
	</colgroup>
	<tr>
		<td><b><?=$_LANG->get('Lagerplatz')?></b></td>
		<td><b><?=$_LANG->get('Inhalt - Artikel - Material - Ware')?></b></td>
		<td><b><?=$_LANG->get('Anprechpartner')?></b></td>
		<td colspan="2"><b><?=$_LANG->get('IST / MIN')?></b></td>
	</tr>
<?
	//Lagerabruf heute
	if (count($all_stocks) >= 1 && $all_stocks != false){
		$x=0;
		foreach ($all_stocks as $stock){?>
			<tr class="<?= getRowColor($x) ?>">
				<td class="content_row pointer">&ensp;
					<a href="index.php?page=libs/warehouse/warehouse.php&exec=edit&stockid=<?=$stock->getId();?>" title="Lagerplatzdetails"><?=$stock->getName();?></a>
				</td>
				<td class="content_row"><?=$stock->getInput();?></td>
				<td class="content_row"><?=$stock->getContactperson()->getNameAsLine();?>&ensp;</td>
				<td class="content_row"><?=$stock->getamount()?> / <?=$stock->getMinimum()?></td>
				<td class="content_row"> &ensp;
<?					if($stock->getComment() != NULL && $stock->getComment() != ""){?>
						<img src="./images/icons/balloon-ellipsis.png" alt="Kommentar" title="<?=$stock->getComment()?>" />	 
<?					} ?>
				</td>
			</tr>
<?			$x++;	
		}
	} else {
		echo '<tr class="'.getRowColor($x) .'"> <td colspan="5" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Alle Mengen &uuml;ber den Mindestmengen').'</span>';
		echo '</td></tr>';
	}	?>
</table>