<?php

$order = new Order((int)$_REQUEST["id"]);
?>
<!-- <link rel="stylesheet" type="text/css" href="../../../css/main.css" /> -->
<!-- <link rel="stylesheet" type="text/css" href="../../../css/calcoverview.css" /> -->

<h1><?=$_LANG->get('Kalkulationsbersicht')?></h1>
<div class="outer">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col>
    </colgroup>
    <tr>
        <td><b><?=$_LANG->get('Kundennummer')?>:</b></td>
        <td><?=$order->getCustomer()->getCustomernumber()?></td>
        <td><b><?=$_LANG->get('Vorgang')?>:</b></td>
        <td><?=$order->getNumber()?></td>
        <td><b><?=$_LANG->get('Telefon')?>:</b></td>
        <td><?=$order->getCustomer()->getPhone()?></td>
    </tr>
    <tr>
        <td valign="top"><b><?=$_LANG->get('Name')?>:</b></td>
        <td valign="top"><?=nl2br($order->getCustomer()->getNameAsLine())?></td>
        <td valign="top"><b><?=$_LANG->get('Adresse')?>:</b></td>
        <td valign="top"><?=nl2br($order->getCustomer()->getAddressAsLine())?></td>
        <td valign="top"><b><?=$_LANG->get('E-Mail')?>:</b></td>
        <td valign="top"><?=$order->getCustomer()->getEmail()?></td>
    </tr>
</table>
</div>
<br>
<div class="outer">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col>
    </colgroup>
    <tr>
        <td valign="top"><b><?=$_LANG->get('Produkt')?>:</b></td>
        <td valign="top"><?=$order->getProduct()->getName()?></td>
        <td valign="top"><b><?=$_LANG->get('Beschreibung')?>:</b></td>
        <td valign="top"><?=$order->getProduct()->getDescription()?></td>
        <td valign="top"><b><?=$_LANG->get('Bemerkungen')?>:</b></td>
        <td valign="top"><?=nl2br($order->getNotes())?></td>
    </tr>    
    <tr>
        <td><b><?=$_LANG->get('Lieferadresse')?>:</b></td>
        <td><?=nl2br($order->getDeliveryAddress()->getAddressAsLine())?></td>
        <td><b><?=$_LANG->get('Lieferbedingungen')?>:</b></td>
        <td><?=$order->getDeliveryTerms()->getComment()?></td>
        <td><b><?=$_LANG->get('Lieferdatum')?>:</b></td>
        <td><? if($order->getDeliveryDate() > 0) echo date('d.m.Y', $order->getDeliveryDate())?></td>
    </tr>
    <tr>
        <td><b><?=$_LANG->get('Zahlungsadresse')?>:</b></td>
        <td><?=nl2br($order->getInvoiceAddress()->getAddressAsLine())?></td>
        <td><b><?=$_LANG->get('Zahlungsbedingungen')?>:</b></td>
        <td><?=$order->getPaymentTerms()->getComment()?></td>
        <td><b>&nbsp;</b></td>
        <td>&nbsp;</td>
    </tr>
</table>
</div>
<br>
<? $i = 1; foreach(Calculation::getAllCalculations($order) as $calc) { 
    $calc_sorts = $calc->getSorts();
    if ($calc_sorts == 0)
        $calc_sorts = 1;
    ?>
<h2><?=$_LANG->get('Teilauftag')?> # <?=$i?> - <?=$_LANG->get('Auflage')?> <?=printBigInt($calc->getAmount())?> (<?=$calc_sorts?> Sorte(n)* <?=$calc->getAmount()/$calc_sorts?> Auflage)</h2>
<div class="outer">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col width="15%">
        <col width="35%">
        <col width="15%">
        <col width="35%">
    </colgroup>
    <tr>
        <td valign="top"><b><?=$_LANG->get('Inhalt')?>:</b></td>
        <td valign="top">
            <?=$calc->getPaperContent()->getName()?>, <?=$calc->getPaperContentWeight()?> g
        </td>
        <td valign="top"><b><?=$_LANG->get('zus. Inhalt')?>:</b></td>
        <td valign="top">
            <? if($calc->getPaperAddContent()->getId()) { ?>
                <?=$calc->getPaperAddContent()->getName()?>, <?=$calc->getPaperAddContentWeight()?> g
            <? } ?> 
        </td>
    </tr>
    <tr>
        <td valign="top"></td>
        <td valign="top">
            <?=$calc->getPagesContent()?> Seiten, <?=$calc->getProductFormat()->getName()?>, <?=$calc->getChromaticitiesContent()->getName()?>
        </td>
        <td valign="top"></td>
        <td valign="top">
            <? if($calc->getPaperAddContent()->getId()) { ?>
                <?=$calc->getPagesAddContent()?> Seiten, <?=$calc->getProductFormat()->getName()?>, <?=$calc->getChromaticitiesAddContent()->getName()?>
            <? } ?> 
        </td>
    </tr>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td valign="top">
        	<? if($calc->getPaperAddContent2()->getId() > 0) { ?>
        		<b><?=$_LANG->get('zus. Inhalt 2')?>:</b>
        	<? } ?>
        </td>
        <td valign="top">
        	<? if($calc->getPaperAddContent2()->getId() > 0) { ?>
            	<?=$calc->getPaperAddContent2()->getName()?>, <?=$calc->getPaperAddContent2Weight()?> g
            <? } ?>
        </td>
        <td valign="top">
        	<? if($calc->getPaperAddContent3()->getId() > 0) { ?>
        		<b><?=$_LANG->get('zus. Inhalt 3')?>:</b>
        	<? } ?>
        </td>
        <td valign="top">
            <? if($calc->getPaperAddContent3()->getId()) { ?>
                <?=$calc->getPaperAddContent3()->getName()?>, <?=$calc->getPaperAddContent3Weight()?> g
            <? } ?> 
        </td>
    </tr>
    <tr>
        <td valign="top"></td>
        <td valign="top">
            <? if($calc->getPaperAddContent2()->getId()) { ?>
                <?=$calc->getPagesAddContent2()?> Seiten, <?=$calc->getProductFormat()->getName()?>, <?=$calc->getChromaticitiesAddContent2()->getName()?>
            <? } ?> 
        </td>
        <td valign="top"></td>
        <td valign="top">
            <? if($calc->getPaperAddContent3()->getId()) { ?>
                <?=$calc->getPagesAddContent3()?> Seiten, <?=$calc->getProductFormat()->getName()?>, <?=$calc->getChromaticitiesAddContent3()->getName()?>
            <? } ?> 
        </td>
    </tr>
    <? if($calc->getPaperEnvelope()->getId()) { ?>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td valign="top"><b><?=$_LANG->get('Umschlag')?>:</b></td>
        <td valign="top">
            <?=$calc->getPaperEnvelope()->getName()?>, <?=$calc->getPaperEnvelopeWeight()?> g
        </td>
    </tr>
    <tr>
        <td valign="top"></td>
        <td valign="top">
            <?=$calc->getPagesEnvelope()?> Seiten, <?=$calc->getProductFormat()->getName()?>, <?=$calc->getChromaticitiesEnvelope()->getName()?>
        </td>
    </tr>
    <? } ?>
</table>
</div>
<br>
<h3><?=$_LANG->get('Papierpreise')?></h3>
<div class="outer">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col width="15%">
        <col width="35%">
        <col width="15%">
        <col width="35%">
    </colgroup>
    <tr>
        <td valign="top"><b><?=$_LANG->get('Inhalt')?>:</b></td>
        <td valign="top">
            <?=$_LANG->get('Bogenformat')?>: <?=$calc->getPaperContentWidth()?> mm x <?=$calc->getPaperContentHeight()?> mm <br>
            <?=$_LANG->get('Produktformat')?>: <?=$calc->getProductFormatWidth()?> mm x <?=$calc->getProductFormatHeight()?> mm, 
                <?=$calc->getProductFormatWidthOpen()?> mm x <?=$calc->getProductFormatHeightOpen()?> mm (offen)<br>
            <?=$_LANG->get('Nutzen pro Bogen')?>: <?=$calc->getProductsPerPaper(Calculation::PAPER_CONTENT)?>,
                <?=$_LANG->get('Anzahl B&ouml;gen pro Auflage')?>: <? echo printPrice($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT));?><br>
            <?=$_LANG->get('B&ouml;gen insgesamt')?>: 
                <?  $sheets = ceil($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * $calc->getAmount());
                    echo printBigInt($sheets);
                ?> + <?=$_LANG->get('Zuschuss')?> 
                    <? echo printBigInt($calc->getPaperContentGrant());
                    $sheets += $calc->getPaperContentGrant()?><br>
            <?=$_LANG->get('Papiergewicht')?>: 
                <? $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();
                   echo printPrice((($area * $calc->getPaperContentWeight() / 10000 / 100) * $sheets) / 1000);
                ?> kg,
            <?=$_LANG->get('Papierpreis')?>: <?=printPrice($calc->getPaperContent()->getSumPrice($sheets))?> <?=$_USER->getClient()->getCurrency()?><br>
            <?=$_LANG->get('Preisbasis')?>: <? 
                if ($calc->getPaperContent()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg'); 
                else echo $_LANG->get('Preis pro 1000 B&ouml;gen');
            ?>
        </td>
        <td valign="top"><b><?=$_LANG->get('zus. Inhalt')?>:</b></td>
        <td valign="top">
            <? if($calc->getPaperAddContent()->getId()) { ?>
                <?=$_LANG->get('Bogenformat')?>: <?=$calc->getPaperAddContentWidth()?> mm x <?=$calc->getPaperAddContentHeight()?> mm <br>
                <?=$_LANG->get('Produktformat')?>: <?=$calc->getProductFormatWidth()?> mm x <?=$calc->getProductFormatHeight()?> mm, 
                    <?=$calc->getProductFormatWidthOpen()?> mm x <?=$calc->getProductFormatHeightOpen()?> mm (offen)<br>
                <?=$_LANG->get('Nutzen pro Bogen')?>: <?=$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT)?>,
                    <?=$_LANG->get('Anzahl B&ouml;gen pro Auflage')?>: <? echo printPrice($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT));?><br>
                <?=$_LANG->get('B&ouml;gen insgesamt')?>: 
                    <?  $sheets = ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount());
                        echo printBigInt($sheets);
                    ?> + <?=$_LANG->get('Zuschuss')?>
                    <? echo printBigInt($calc->getPaperAddContentGrant());
                    $sheets += $calc->getPaperAddContentGrant()?><br>
                <?=$_LANG->get('Papiergewicht')?>: 
                    <? $area = $calc->getPaperAddContentWidth() * $calc->getPaperAddContentHeight();
                       echo printPrice((($area * $calc->getPaperAddContentWeight() / 10000 / 100) * $sheets) / 1000);
                    ?> kg,
                <?=$_LANG->get('Papierpreis')?>: <?=printPrice($calc->getPaperAddContent()->getSumPrice($sheets))?> <?=$_USER->getClient()->getCurrency()?><br>
                <?=$_LANG->get('Preisbasis')?>: <? 
                    if ($calc->getPaperAddContent()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg'); 
                    else echo $_LANG->get('Preis pro 1000 B&ouml;gen');
                ?>
            <? } ?>
        </td>
    </tr>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td valign="top"><b><?=$_LANG->get('zus. Inhalt 2')?>:</b></td>
        <td valign="top">
            <? if($calc->getPaperAddContent2()->getId()) { ?>
                <?=$_LANG->get('Bogenformat')?>: <?=$calc->getPaperAddContent2Width()?> mm x <?=$calc->getPaperAddContent2Height()?> mm <br>
                <?=$_LANG->get('Produktformat')?>: <?=$calc->getProductFormatWidth()?> mm x <?=$calc->getProductFormatHeight()?> mm, 
                    <?=$calc->getProductFormatWidthOpen()?> mm x <?=$calc->getProductFormatHeightOpen()?> mm (offen)<br>
                <?=$_LANG->get('Nutzen pro Bogen')?>: <?=$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2)?>,
                    <?=$_LANG->get('Anzahl B&ouml;gen pro Auflage')?>: 
                    <? echo printPrice($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2));?><br>
                <?=$_LANG->get('B&ouml;gen insgesamt')?>: 
                    <?  $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount());
                        echo printBigInt($sheets);
                    ?> + <?=$_LANG->get('Zuschuss')?>
                    <? echo printBigInt($calc->getPaperAddContent2Grant());
                    $sheets += $calc->getPaperAddContent2Grant()?><br>
                <?=$_LANG->get('Papiergewicht')?>: 
                    <? $area = $calc->getPaperAddContent2Width() * $calc->getPaperAddContent2Height();
                       echo printPrice((($area * $calc->getPaperAddContent2Weight() / 10000 / 100) * $sheets) / 1000);
                    ?> kg,
                <?=$_LANG->get('Papierpreis')?>: <?=printPrice($calc->getPaperAddContent2()->getSumPrice($sheets))?> <?=$_USER->getClient()->getCurrency()?><br>
                <?=$_LANG->get('Preisbasis')?>: <? 
                    if ($calc->getPaperAddContent2()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg'); 
                    else echo $_LANG->get('Preis pro 1000 B&ouml;gen');
                ?>
            <? } ?>
        </td>
        <td valign="top"><b><?=$_LANG->get('zus. Inhalt 3')?>:</b></td>
        <td valign="top">
            <? if($calc->getPaperAddContent3()->getId()) { ?>
                <?=$_LANG->get('Bogenformat')?>: <?=$calc->getPaperAddContent3Width()?> mm x <?=$calc->getPaperAddContent3Height()?> mm <br>
                <?=$_LANG->get('Produktformat')?>: <?=$calc->getProductFormatWidth()?> mm x <?=$calc->getProductFormatHeight()?> mm, 
                    <?=$calc->getProductFormatWidthOpen()?> mm x <?=$calc->getProductFormatHeightOpen()?> mm (offen)<br>
                <?=$_LANG->get('Nutzen pro Bogen')?>: <?=$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3)?>,
                    <?=$_LANG->get('Anzahl B&ouml;gen pro Auflage')?>: 
                    <? echo printPrice($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3));?><br>
                <?=$_LANG->get('B&ouml;gen insgesamt')?>: 
                    <?  $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount());
                        echo printBigInt($sheets);
                    ?> + <?=$_LANG->get('Zuschuss')?>
                    <? echo printBigInt($calc->getPaperAddContent3Grant());
                    $sheets += $calc->getPaperAddContent3Grant()?><br>
                <?=$_LANG->get('Papiergewicht')?>: 
                    <? $area = $calc->getPaperAddContent3Width() * $calc->getPaperAddContent3Height();
                       echo printPrice((($area * $calc->getPaperAddContent3Weight() / 10000 / 100) * $sheets) / 1000);
                    ?> kg,
                <?=$_LANG->get('Papierpreis')?>: <?=printPrice($calc->getPaperAddContent3()->getSumPrice($sheets))?> <?=$_USER->getClient()->getCurrency()?><br>
                <?=$_LANG->get('Preisbasis')?>: <? 
                    if ($calc->getPaperAddContent3()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg'); 
                    else echo $_LANG->get('Preis pro 1000 B&ouml;gen');
                ?>
            <? } ?>
        </td>
    </tr>
    <? if($calc->getPaperEnvelope()->getId()) { ?>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td valign="top"><b><?=$_LANG->get('Umschlag')?>:</b></td>
        <td valign="top">
            <?=$_LANG->get('Bogenformat')?>: <?=$calc->getPaperEnvelopeWidth()?> mm x <?=$calc->getPaperEnvelopeHeight()?> mm <br>
            <?=$_LANG->get('Produktformat')?>: <?=$calc->getProductFormatWidth()?> mm x <?=$calc->getProductFormatHeight()?> mm, 
                <?=$calc->getEnvelopeWidthOpen()?> mm x <?=$calc->getEnvelopeHeightOpen()?> mm (offen)<br>
            <?=$_LANG->get('Nutzen pro Bogen')?>: <?=$calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE)?>,
                <?=$_LANG->get('Anzahl B&ouml;gen pro Auflage')?>: <? echo printPrice($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE));?><br>
            <?=$_LANG->get('B&ouml;gen insgesamt')?>: 
                <?  $sheets = ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount());
                    echo printBigInt($sheets);
                ?> + <?=$_LANG->get('Zuschuss')?>
                    <? echo printBigInt($calc->getPaperEnvelopeGrant());
                    $sheets += $calc->getPaperEnvelopeGrant()?><br>
            <?=$_LANG->get('Papiergewicht')?>: 
                <? $area = $calc->getPaperEnvelopeWidth() * $calc->getPaperEnvelopeHeight();
                   echo printPrice((($area * $calc->getPaperEnvelopeWeight() / 10000 / 100) * $sheets) / 1000);
                ?> kg,
            <?=$_LANG->get('Papierpreis')?>: <?=printPrice($calc->getPaperEnvelope()->getSumPrice($sheets))?> <?=$_USER->getClient()->getCurrency()?><br>
            <?=$_LANG->get('Preisbasis')?>: <? 
                if ($calc->getPaperEnvelope()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg'); 
                else echo $_LANG->get('Preis pro 1000 B&ouml;gen');
            ?>
        </td>
    </tr>
    <? } ?>
</table>
</div>
<br>
<h3><?=$_LANG->get('Fertigungsprozess')?></h3>
<div class="outer">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col width="15%">
        <col width="35%">
        <col width="15%">
        <col width="35%">
    </colgroup>
    <? foreach(MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION) as $mg) {
        $machentries = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID, $mg->getId()); 
        if(count($machentries) > 0)
        {
    ?>
    <tr>
        <td valign="top"><b><?=$mg->getName()?></b></td>
        <td valign="top">
            <? foreach($machentries as $me) { 
                ?>
                <?=$_LANG->get('Maschine')?> <?=$me->getMachine()->getName()?>
                <? if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                       $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET ||
                       $me->getMachine()->getType() == Machine::TYPE_FOLDER) {
                        switch($me->getPart())
                        {
                            case Calculation::PAPER_CONTENT:
                                echo "(".$_LANG->get('Inhalt').")";
                                break;
                            case Calculation::PAPER_ADDCONTENT:
                                echo "(".$_LANG->get('zus. Inhalt').")";
                                break;
                            case Calculation::PAPER_ENVELOPE:
                                echo "(".$_LANG->get('Umschlag').")";
                                break;
                            case Calculation::PAPER_ADDCONTENT2:
                            	echo "(".$_LANG->get('zus. Inhalt 2').")";
                            	break;
							case Calculation::PAPER_ADDCONTENT3:
                                echo "(".$_LANG->get('zus. Inhalt 3').")";
                              	break;
                        }
                }?><br>
                <? if($me->getMachine()->getType() == Machine::TYPE_CTP) { 
                    echo $_LANG->get('Anzahl Druckplatten').": ".$calc->getPlateCount();
                    echo "<br>";
                }
                ?>
                
                <?=$_LANG->get('Grundzeit')?>: <?=$me->getMachine()->getTimeBase()?> min.,
                <? if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) { ?>
                    <?=$_LANG->get('Einrichtzeit Druckplatten')?>: 
                    <? echo $calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange();?> min.
                    <?=$_LANG->get('Laufzeit')?>: 
                    <?=$me->getTime() - ($calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange()) - $me->getMachine()->getTimeBase()?> min.
                <? } else { ?>
                    <?=$_LANG->get('Laufzeit')?> <?=$_LANG->get('inkl. maschinenspez. R&uuml;stzeiten')?>: 
                    <?=$me->getTime() - $me->getMachine()->getTimeBase()?> min.
                <? } ?>
                <br>
                
                <?=$_LANG->get('Zeit')?>: <?=$me->getTime()?> min., 
                    <?=$_LANG->get('Preis')?>: <?=printPrice($me->getPrice())?> <?=$_USER->getClient()->getCurrency()?><br>
                <br>
            <? } ?>
        </td>
    </tr>
    <? }    // ENDE foreach(Alle Maschinen einer Gruppe)
    } // ENDE foreach(Alle MaschinenGruppen)?>
	<?/*** if (count($calc->getArticles())>0) {?>
		<tr>
			<td valign="top"><b><?=$_LANG->get('Zus. Artikel') ?></b></td>
			<td>        
		    <?foreach($calc->getArticles() as $article){ 
				$tmpart_amount = $calc->getArticleamount($article->getId());
				$tmpart_scale = $calc->getArticlescale($article->getId());
				echo $article->getTitle() ." : ";
				if ($tmpart_scale == 0){
					echo printPrice($tmpart_amount * $article->getPrice($tmpart_amount));
				} elseif ($tmpart_scale == 1){
					echo printPrice($tmpart_amount * $article->getPrice($tmpart_amount * $calc->getAmount()) * $calc->getAmount());
				}
				echo " ".$_USER->getClient()->getCurrency()."<br/>";
				?>
				<br/>
			<?}?>
			</td>
		</tr>
	<?} // ENDE Auflistung der Artikel***/?>
	<? if (count($calc->getPositions())>0 && $calc->getPositions() != FALSE) {?>
		<tr>
			<td valign="top"><b><?=$_LANG->get('Zus. Positionen') ?></b></td>
			<td>        
		    <?foreach($calc->getPositions() as $pos){ 
				echo $pos->getComment() ." : ";
				echo printPrice($pos->getCalculatedPrice())." ".$_USER->getClient()->getCurrency()."<br/>";
				?>
				<br/>
			<?}?>
			</td>
		</tr>
	<?} // ENDE Auflistung der Artikel?>
</table>
</div>
<br>
<div class="outer">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col width="15%">
        <col width="35%">
        <col width="15%">
        <col width="35%">
    </colgroup>
    <tr>
        <td valign="top"><b><?=$_LANG->get('Produktionskosten')?>:</b></td>
        <td valign="top"><b>
            <?=printPrice($calc->getSubTotal())?> <?=$_USER->getClient()->getCurrency()?></b>
        </td>
    </tr>
</table>
</div>
<br>
<? $i++;} ?>