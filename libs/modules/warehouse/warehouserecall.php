<? //--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			17.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//-----------------------------------------------------------------------------------
//
// Diese Datei steuert den den Lagerabruf fuer den jeweiligen Tag auf der Home-Seite
//
//-----------------------------------------------------------------------------------

require_once './libs/modules/warehouse/warehouse.class.php';

$all_stocks = Warehouse::getAllStocksForToday();

?>
<!-------------------------------- INNER START --------------------------->
<h1><?=$_LANG->get('Lagerabruf heute')?></h1>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed">
	<colgroup>
		<col width="70" valign="top">
		<col width="120" valign="top">
		<col>
		<col width="50" valign="top">
		<col width="50" valign="top">
	</colgroup>
	<tr>
		<td><b><?=$_LANG->get('Lagerplatz')?></b></td>
		<td><b><?=$_LANG->get('Kunde')?> - <?=$_LANG->get('Lieferant')?></b></td>
		<td><b><?=$_LANG->get('Inhalt - Artikel - Material - Ware')?></b></td>
		<td colspan="2">&ensp;</td>
	</tr>
<?
	//Lagerabruf heute
	if (count($all_stocks) >= 1 && $all_stocks != false){?>	
<? 		$x=0;
		foreach ($all_stocks as $stock){?>
			<tr class="<?= getRowColor($x) ?>">
				<td class="content_row pointer">&ensp;
					<a href="index.php?page=libs/warehouse/warehouse.php&exec=edit&stockid=<?=$stock->getId();?>" title="Lagerplatzdetails"><?=$stock->getName();?></a>
				</td>
				<td class="content_row"><?=$stock->getCustomer()->getNameAsLine();?>&ensp;</td>
				<td class="content_row"><?=$stock->getInput();?></td>
				<td class="content_row"><?=date('d.m.Y',$stock->getRecall());?></td>
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
		echo '<span class="error">'.$_LANG->get('Keine Lagerabrufe f&uuml;r heute zu finden').'</span>';
		echo '</td></tr>';
	}	?>
</table>