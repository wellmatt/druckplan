<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
$perf = new Perferences();
?>

<div class="box1">
<table width="100%">
    <colgroup>
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Kundennummer')?>:</td>
        <td class="content_row_clear"><?=$order->getCustomer()->getId()?></td>
        <td class="content_row_header"><?=$_LANG->get('Auftrag')?>:</td>
        <td class="content_row_clear"><?=$order->getNumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Telefon')?></td>
        <td class="content_row_clear"><?=$order->getCustomer()->getPhone()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Name')?>:</td>
        <td class="content_row_clear" valign="top"><?=nl2br($order->getCustomer()->getNameAsLine())?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Adresse')?>:</td>
        <td class="content_row_clear"  valign="top"><?=nl2br($order->getCustomer()->getAddressAsLine())?></td>
        <td class="content_row_header"  valign="top"><?=$_LANG->get('E-Mail')?></td>
        <td class="content_row_clear" valign="top"><?=$order->getCustomer()->getEmail()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Produkt')?>:</td>
        <td class="content_row_clear" valign="top"><?=$order->getProduct()->getName()?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?>:</td>
        <td class="content_row_clear" valign="top" colspan="3"><?=$order->getProduct()->getDescription()?></td>
    </tr>
</table>
</div>
<br>

<? $x = 1; 
foreach ($calcs as $c) { 
	$part_count = 0;?>
<div class="box2">
<b><?=$_LANG->get('Teilauftrag')?> # <?=$x?> - <?=$_LANG->get('Auflage')?> <?=printBigInt($c->getAmount())?></b>
<table>
<colgroup>
	<col width="30%">
	<col width="30%">
	<col width="30%">
</colgroup>
<tr>
    <? if($c->getPaperContent()->getId()) { 
	$schemes = array();
	$part = Calculation::PAPER_CONTENT;
	$calc = $c;
	$mach = Machineentry::getMachineForPapertype($part, $c->getId());
	$mach = $mach[0]->getMachine();
	$product_max = 0;
	$product_counted = false;
	
	include('scheme.php');
	?>
	<td width="300" valign="top"><table>
	<?
	for ($i = 0; $i < count($schemes); $i++)
	{ // vorschau pro anweichender Produkzahl pro Bogen
		?>
		<tr width="300">
			<? /*<u><?=$_LANG->get('Inhalt')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
			<u><?=$_LANG->get('Inhalt')?>:</u><br>
			<?=$c->getPaperContent()->getName()?>, <?=$c->getPaperContentWeight()?> g,<br>
			<?=$c->getPaperContentWidth()?> x <?=$c->getPaperContentHeight()?> <?=$_LANG->get('mm')?>, 
			<?=printPrice($c->getCutContent())?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?> <br>
			<? 	if($c->getPaperContent()->getPaperDirection($c, Calculation::PAPER_CONTENT) == Paper::PAPER_DIRECTION_SMALL){
					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
				} else {
					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
				} ?>	
			<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_CONTENT?>&max=<?=$schemes[$i]['nutzen']?>&counted=true" 
						width="300" height="300" ></object>
			<!--img src="libs/modules/calculation/order.printpreview.image.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_CONTENT?>"-->
		</tr></br>
		<? 
		if ($perf->getCalc_detailed_printpreview() == 0){
		  break;   
		}
	}
	?>
	</table></td></br>
	<?
	$part_count++;
	} ?>
    
    <? if($c->getPaperAddContent()->getId()) { 
	$schemes = array();
	$part = Calculation::PAPER_ADDCONTENT;
	$calc = $c;
	$mach = Machineentry::getMachineForPapertype($part, $c->getId());
	$mach = $mach[0]->getMachine();
	$product_max = 0;
	$product_counted = false;
	
	include('scheme.php');
	
	for ($i = 0; $i < count($schemes); $i++)
	{ // vorschau pro anweichender Produkzahl pro Bogen
		?>
		<td width="300">
			<? /*<u><?=$_LANG->get('zus. Inhalt')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
			<u><?=$_LANG->get('zus. Inhalt')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
			<?=$c->getPaperAddContent()->getName()?>, <?=$c->getPaperAddContentWeight()?> <?=$_LANG->get('g')?>,<br>
			<?=$c->getPaperAddContentWidth()?> x <?=$c->getPaperAddContentHeight()?> <?=$_LANG->get('mm')?>, 
			<?=printPrice($c->getCutAddContent())?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
			<? 	if($c->getPaperAddContent()->getPaperDirection($c, Calculation::PAPER_ADDCONTENT) == Paper::PAPER_DIRECTION_SMALL){
					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
				} else {
					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
				} ?>
			<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT?>&max=<?=$schemes[$i]['nutzen']?>&counted=true" 
					width="300" height="300" ></object>	
			<!--img src="libs/modules/calculation/order.printpreview.image.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT?>"-->
		</td>
		<? 
		if ($perf->getCalc_detailed_printpreview() == 0){
		  break;   
		}
	}
	$part_count++;
	} ?>
    
    <? if($c->getPaperEnvelope()->getId()) { 
	$schemes = array();
	$part = Calculation::PAPER_ENVELOPE;
	$calc = $c;
	$mach = Machineentry::getMachineForPapertype($part, $c->getId());
	$mach = $mach[0]->getMachine();
	$product_max = 0;
	$product_counted = false;
	
	include('scheme.php');
	?>
	<td width="300" valign="top"><table>
	<?
	
	for ($i = 0; $i < count($schemes); $i++)
	{ // vorschau pro anweichender Produkzahl pro Bogen
	?>
		<tr width="300">
			<? /*<u><?=$_LANG->get('Umschlag')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
			<u><?=$_LANG->get('Umschlag')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
			<?=$c->getPaperEnvelope()->getName()?>, <?=$c->getPaperEnvelopeWeight()?> <?=$_LANG->get('g')?>,<br>
			<?=$c->getPaperEnvelopeWidth()?> x <?=$c->getPaperEnvelopeHeight()?> <?=$_LANG->get('mm')?>, 
			<?=printPrice($c->getCutEnvelope())?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
			<? 	if($c->getPaperEnvelope()->getPaperDirection($c, Calculation::PAPER_ENVELOPE) == Paper::PAPER_DIRECTION_SMALL){
					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
				} else {
					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
				} ?>
				<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ENVELOPE?>&max=<?=$schemes[$i]['nutzen']?>&counted=true" 
					width="300" height="300" ></object>	
			<!--img src="libs/modules/calculation/order.printpreview.image.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ENVELOPE?>"-->
		</tr></br>
		<? 
		if ($perf->getCalc_detailed_printpreview() == 0){
		  break;   
		}
	}
	?>
	</table></td></br>
	<?
	$part_count++;
	} ?> 
	
	
    <? if($c->getPaperAddContent2()->getId()) { 
    	if($part_count >= 3){ echo "</tr><tr>"; $part_count = 1;}
		$schemes = array();
		$part = Calculation::PAPER_ENVELOPE;
		$calc = $c;
		$mach = Machineentry::getMachineForPapertype($part, $c->getId());
		$mach = $mach[0]->getMachine();
		$product_max = 0;
		$product_counted = false;
		
		include('scheme.php');
		?>
		<td width="300" valign="top"><table>
		<?
		
		for ($i = 0; $i < count($schemes); $i++)
		{ // vorschau pro anweichender Produkzahl pro Bogen
		?>
			<tr width="300">
				<? /*<u><?=$_LANG->get('zus. Inhalt 2')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
				<u><?=$_LANG->get('zus. Inhalt 2')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
				<?=$c->getPaperAddContent2()->getName()?>, <?=$c->getPaperAddContent2Weight()?> <?=$_LANG->get('g')?>,<br>
				<?=$c->getPaperAddContent2Width()?> x <?=$c->getPaperAddContent2Height()?> <?=$_LANG->get('mm')?>, 
				<?=$_CONFIG->anschnitt?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
				<? 	if($c->getPaperAddContent2()->getPaperDirection($c, Calculation::PAPER_ADDCONTENT2) == Paper::PAPER_DIRECTION_SMALL){
						echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
					} else {
						echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
					} ?>
					<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT2?>" 
						width="300" height="300" ></object>	
				<!-- img src="libs/modules/calculation/order.printpreview.image.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT2?>"-->
			</tr></br>
			<?	
    		if ($perf->getCalc_detailed_printpreview() == 0){
    		  break;   
    		}
		}
		?>
		</table></td></br>
		<?
	$part_count++; 
	} ?>
    
    <? if($c->getPaperAddContent3()->getId()) { 
    	if($part_count >= 3){ echo "</tr><tr>"; $part_count++;}
		$schemes = array();
		$part = Calculation::PAPER_ENVELOPE;
		$calc = $c;
		$mach = Machineentry::getMachineForPapertype($part, $c->getId());
		$mach = $mach[0]->getMachine();
		$product_max = 0;
		$product_counted = false;
		
		include('scheme.php');
		?>
		<td width="300" valign="top"><table>
		<?
		
		for ($i = 0; $i < count($schemes); $i++)
		{ // vorschau pro anweichender Produkzahl pro Bogen
		?>
			<tr width="300">
				<? /*<u><?=$_LANG->get('zus. Inhalt 3')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
				<u><?=$_LANG->get('zus. Inhalt 3')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
				<?=$c->getPaperAddContent3()->getName()?>, <?=$c->getPaperAddContent3Weight()?> <?=$_LANG->get('g')?>,<br>
				<?=$c->getPaperAddContent3Width()?> x <?=$c->getPaperAddContent3Height()?> <?=$_LANG->get('mm')?>, 
				<?=$_CONFIG->anschnitt?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
				<? 	if($c->getPaperAddContent3()->getPaperDirection($c, Calculation::PAPER_ADDCONTENT3) == Paper::PAPER_DIRECTION_SMALL){
						echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
					} else {
						echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
					} ?>
					<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT3?>" 
						width="300" height="300" ></object>	
				<!--img src="libs/modules/calculation/order.printpreview.image.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT3?>"-->
			</tr></br>
			<?	
    		if ($perf->getCalc_detailed_printpreview() == 0){
    		  break;   
    		}
		}
		?>
		</table></td></br>
		<?
	$part_count++; 
	} ?>
    
</tr>
</table>
</div>
<br>
<? $x++;
} ?>