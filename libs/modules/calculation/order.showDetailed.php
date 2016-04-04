<?php

$order = new Order((int)$_REQUEST["id"]);
?>
    <!-- <link rel="stylesheet" type="text/css" href="../../../css/main.css" /> -->
    <!-- <link rel="stylesheet" type="text/css" href="../../../css/calcoverview.css" /> -->

    <!--<h2><?= $_LANG->get('Detaillierte Kalkulationsbersicht') ?></h2>-->
    <div class="box1">
        <table cellpadding="0" cellspacing="0" border="0" width="100%" >
            <h2 align="center" ><b><?= $_LANG->get('Auftragsdetails') ?>: <?= $order->getTitle() ?> </b><br></h2>
            <colgroup>
                <col width="20%">
                <col width="15%">
                <col width="20%">
                <col width="15%">

                <col>
            </colgroup>
            <tr>
                <!--<td><b><?= $_LANG->get('Kundennummer') ?>:</b></td>
        <td><?= $order->getCustomer()->getCustomernumber() ?></td>-->
                <td><b><?= $_LANG->get('Vorgang') ?>:</b></td>
                <td><?= $order->getNumber() ?></td>
                <td><b><?= $_LANG->get('Beschreibung') ?>:</b></td>

                <td><b><?= $_LANG->get('Bemerkungen') ?>:</b></td>

            </tr>
            <tr>
                <td><b><?= $_LANG->get('Produkt') ?>:</b></td>
                <td><?= $order->getProduct()->getName() ?></td>
                <td><?= $order->getProduct()->getDescription() ?></td>
                <td><?= nl2br($order->getNotes()) ?></td>
                <!--<td><b><?= $_LANG->get('Telefon') ?>:</b></td>
        <td><?= $order->getCustomer()->getPhone() ?></td>-->
            </tr>
            <!--<tr>
        <td valign="top"><b><?= $_LANG->get('Name') ?>:</b></td>
        <td valign="top"><?= nl2br($order->getCustomer()->getNameAsLine()) ?></td>
        <td valign="top"><b><?= $_LANG->get('Adresse') ?>:</b></td>
        <td valign="top"><?= nl2br($order->getCustomer()->getAddressAsLine()) ?></td>
        <td valign="top"><b><?= $_LANG->get('E-Mail') ?>:</b></td>
        <td valign="top"><?= $order->getCustomer()->getEmail() ?></td>
    </tr>-->
        </table>
    </div>
    <br>
    <!--<div class="color2">
<table cellpadding="0" cellspacing="0" border="0"  width="100%">
    <colgroup>
      <col width="20%">
        <col width="15%">
        <col width="20%">
        <col width="15%">
        <col width="20%">
        <col>
    </colgroup>
    <tr>
        <td valign="top"><b><?= $_LANG->get('Produkt') ?>:</b></td>
        <td valign="top"><?= $order->getProduct()->getName() ?></td>
        <td valign="top"><b><?= $_LANG->get('Beschreibung') ?>:</b></td>
        <td valign="top"><?= $order->getProduct()->getDescription() ?></td>
        <td valign="top"><b><?= $_LANG->get('Bemerkungen') ?>:</b></td>
        <td valign="top"><?= nl2br($order->getNotes()) ?></td>
    </tr>
    <tr>
        <td><b><?= $_LANG->get('Lieferadresse') ?>:</b></td>
        <td><?= nl2br($order->getDeliveryAddress()->getAddressAsLine()) ?></td>
        <td><b><?= $_LANG->get('Lieferbedingungen') ?>:</b></td>
        <td><?= $order->getDeliveryTerms()->getComment() ?></td>
        <td><b><?= $_LANG->get('Lieferdatum') ?>:</b></td>
        <td><? if ($order->getDeliveryDate() > 0) echo date('d.m.Y', $order->getDeliveryDate()) ?></td>
    </tr>
    <tr>
        <td><b><?= $_LANG->get('Zahlungsadresse') ?>:</b></td>
        <td><?= nl2br($order->getInvoiceAddress()->getAddressAsLine()) ?></td>
        <td><b><?= $_LANG->get('Zahlungsbedingungen') ?>:</b></td>
        <td><?= $order->getPaymentTerms()->getComment() ?></td>
        <td><b>&nbsp;</b></td>
        <td>&nbsp;</td>
    </tr>
</table>
</div>-->
    <br>

<? $i = 1;
foreach (Calculation::getAllCalculations($order) as $calc) {
    $calc_sorts = $calc->getSorts();
    if ($calc_sorts == 0)
        $calc_sorts = 1;
    ?>

    <div class="box3">
        <tr>
            <td>

                <h3 align="center" ><b><?= $i ?>. <?= $_LANG->get('Auflage') ?>: <?= printBigInt($calc->getAmount()) ?></b>
                    (<?= $calc_sorts ?> Sorte(n) x <?= $calc->getAmount() / $calc_sorts ?> Auflage)</h3>
            </td>


            <div class="box1">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#D3D3D3 height:12px">
                    <h3><?= $_LANG->get('Fertigungsprozess') ?></h3>
                    <br>
                    <colgroup>
                        <col width="15%">
                        <col width="85%">


                    </colgroup>
                    <? foreach (MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION) as $mg) {
                    $machentries = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID, $mg->getId());
                    if (count($machentries) > 0)
                    {
                    ?>
                    <tr>
                        <td valign="top"><b><?= $mg->getName() ?></b></td>
                        <td valign="top">
                            <? foreach($machentries as $me) {
                                ?>
                                <b><?=$_LANG->get('Maschine:')?> <?=$me->getMachine()->getName()?></b>
                                <? if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                                    $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET ||
                                    $me->getMachine()->getType() == Machine::TYPE_FOLDER) {
                                    switch($me->getPart())
                                    {
                                        case Calculation::PAPER_CONTENT:
                                            echo "(".$_LANG->get('Inhalt 1').")";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT:
                                            echo "(".$_LANG->get('Inhalt 2').")";
                                            break;
                                        case Calculation::PAPER_ENVELOPE:
                                            echo "(".$_LANG->get('Umschlag').")";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT2:
                                            echo "(".$_LANG->get('Inhalt 3').")";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT3:
                                            echo "(".$_LANG->get('Inhalt 4').")";
                                            break;
                                    }
                                }?>

                                <br>

                                <? if($me->getMachine()->getType() == Machine::TYPE_CTP) {
                                    $machentries2 = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID);
                                    foreach($machentries2 as $me2) {
                                        if($me2->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                                            switch($me2->getPart())
                                            {
                                                case Calculation::PAPER_CONTENT:
                                                    echo $_LANG->get('Anzahl Druckplatten Inhalt 1').": ".$calc->getPlateCount($me2)."</br>";
                                                    break;
                                                case Calculation::PAPER_ADDCONTENT:
                                                    echo $_LANG->get('Anzahl Druckplatten Inhalt 2').": ".$calc->getPlateCount($me2)."</br>";
                                                    break;
                                                case Calculation::PAPER_ENVELOPE:
                                                    echo $_LANG->get('Anzahl Druckplatten Umschlag').": ".$calc->getPlateCount($me2)."</br>";
                                                    break;
                                                case Calculation::PAPER_ADDCONTENT2:
                                                    echo $_LANG->get('Anzahl Druckplatten Inhalt 3').": ".$calc->getPlateCount($me2)."</br>";
                                                    break;
                                                case Calculation::PAPER_ADDCONTENT3:
                                                    echo $_LANG->get('Anzahl Druckplatten Inhalt 4').": ".$calc->getPlateCount($me2)."</br>";
                                                    break;
                                            }
                                        }
                                    }
                                    echo $_LANG->get('Anzahl Druckplatten gesamt').": ".$calc->getPlateCount();
                                    echo "<br>";
                                }
                                ?>

                                <? if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                                    switch($me->getPart())
                                    {
                                        case Calculation::PAPER_CONTENT:
                                            echo "<b>Farbigkeit:</b> ".$calc->getChromaticitiesContent()->getName()."</br>";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT:
                                            echo "<b>Farbigkeit:</b> ".$calc->getChromaticitiesAddContent()->getName()."</br>";
                                            break;
                                        case Calculation::PAPER_ENVELOPE:
                                            echo "<b>Farbigkeit:</b> ".$calc->getChromaticitiesEnvelope()->getName()."</br>";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT2:
                                            echo "<b>Farbigkeit:</b> ".$calc->getChromaticitiesAddContent2()->getName()."</br>";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT3:
                                            echo "<b>Farbigkeit:</b> ".$calc->getChromaticitiesAddContent3()->getName()."</br>";
                                            break;
                                    }?>





                                    <b><?=$_LANG->get('Druckart')?>: </b>
                                    <?php
                                    if ((int)$me->getUmschl() == 1)
                                        echo 'Umschlagen';
                                    elseif ((int)$me->getUmst() == 1)
                                        echo 'Umscht&uuml;lpen';
                                    else
                                        echo 'Sch&ouml;n & Wider';
                                    echo '</br>';
                                    ?>


                                <? } ?>


                                <b><?=$_LANG->get('Grundzeit')?>: </b><?=$me->getMachine()->getTimeBase()?> min., </br>
                                <? if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) { ?>

                                    <b><?=$_LANG->get('Einrichtzeit Druckplatten')?>:</b>
                                    RÃ¼sten <?=$calc->getPlateCount($me)?> Platten Ã¡ <?=$me->getMachine()->getTimePlatechange();?> min. =
                                    <? echo $calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange();?> min.<br>
                                    <b><?=$_LANG->get('Produktionslaufzeit')?>: </b>
                                    <?=$me->getTime() - ($calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange()) - $me->getMachine()->getTimeBase()?> min.
                                    <?=$me->getInfo();?>


                                <? }

                                else { ?>
                                    <b><?=$_LANG->get('Produktionslaufzeit')?>:</b>
                                    <?=$me->getTime() + $me->getMachine()->getTimeBase()?> min.
                                    <b><?=$_LANG->get('RÃ¼stzeiten')?></b>
                                    <?=$me->getMachine()->getTimeBase()?> min.
                                <? } ?>
                                <br>



                                <b><?=$_LANG->get('Gesamtzeit')?>: </b><?=$me->getTime()?> min.,
                                <b><?=$_LANG->get('Preis')?>: </b><?=printPrice($me->getPrice())?> <?=$_USER->getClient()->getCurrency()?><br>
                                <br>


                            <? } ?>


                        </td>


                        <? }    // ENDE foreach(Alle Maschinen einer Gruppe)
                        } // ENDE foreach(Alle MaschinenGruppen)?>
                        <? /*** if (count($calc->getArticles())>0) {?>
                         * <tr>
                         * <td valign="top"><b><?=$_LANG->get('Zus. Artikel') ?></b></td>
                         * <td>
                         * <?foreach($calc->getArticles() as $article){
                         * $tmpart_amount = $calc->getArticleamount($article->getId());
                         * $tmpart_scale = $calc->getArticlescale($article->getId());
                         * echo $article->getTitle() ." : ";
                         * if ($tmpart_scale == 0){
                         * echo printPrice($tmpart_amount * $article->getPrice($tmpart_amount));
                         * } elseif ($tmpart_scale == 1){
                         * echo printPrice($tmpart_amount * $article->getPrice($tmpart_amount * $calc->getAmount()) * $calc->getAmount());
                         * }
                         * echo " ".$_USER->getClient()->getCurrency()."<br/>";
                         * ?>
                         * <br/>
                         * <?}?>
                         * </td>
                         * </tr>
                         * <?} // ENDE Auflistung der Artikel***/ ?>
                        <? if (count($calc->getPositions()) > 0 && $calc->getPositions() != FALSE) { ?>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Zus. Positionen') ?></b></td>
                        <td>
                            <? foreach ($calc->getPositions() as $pos) {
                                echo $pos->getComment() . " : ";
                                echo printPrice($pos->getCalculatedPrice()) . " " . $_USER->getClient()->getCurrency() . "<br/>";
                                ?>
                                <br/>
                            <? } ?>
                        </td>
                    </tr>

                <? } // ENDE Auflistung der Artikel?>

                </table>
            </div>


            <h3><?= $_LANG->get('Rohbogen') ?></h3>
            <div class="box1">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <colgroup>
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">

                    </colgroup>
                    <tr>
                        <?php
                        foreach (Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID) as $me) {
                            if ($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                                $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET
                            ) {
                                switch ($me->getPart()) {
                                    case Calculation::PAPER_CONTENT:
                                        if ($calc->getFormat_in_content() != "") {
                                            $format_in = explode("x", $calc->getFormat_in_content());
                                            $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                                            $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth()));
                                            $roh2 = ceil($sheets_content / $roh);
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 1') . ':</b></br>';
                                            echo 'Format: ' . $calc->getFormat_in_content() . ' mm</br>';
                                            echo 'Anzahl: ' . $roh2 . ' Bogen</br>';
                                            // echo 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperContentHeight().' * '.$calc->getPaperContentWidth().')) / Bogen</br>';
                                            echo 'Nutzen: ' . (int)$roh_schnitte . '</br>';
                                            echo '</td>';
                                        } else {
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 1') . ':</b></td>';
                                        }
                                        break;
                                    case Calculation::PAPER_ADDCONTENT:
                                        if ($calc->getFormat_in_addcontent() != "") {
                                            $format_in = explode("x", $calc->getFormat_in_addcontent());
                                            $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                                            $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth()));
                                            $roh2 = ceil($sheets_addcontent / $roh);
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 2') . ':</b></br>';
                                            echo 'Format: ' . $calc->getFormat_in_addcontent() . ' mm</br>';
                                            echo 'Anzahl: ' . $roh2 . ' Bogen</br>';
                                            // echo 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContentHeight().' * '.$calc->getPaperAddContentWidth().')) / Bogen</br>';
                                            echo 'Nutzen: ' . (int)$roh_schnitte . '</br>';
                                            echo '</td>';
                                        } else {
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 2') . ':</b></td>';
                                        }
                                        break;
                                    case Calculation::PAPER_ADDCONTENT2:
                                        if ($calc->getFormat_in_addcontent2() != "") {
                                            $format_in = explode("x", $calc->getFormat_in_addcontent2());
                                            $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                                            $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width()));
                                            $roh2 = ceil($sheets_addcontent2 / $roh);
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 3') . ':</b></br>';
                                            echo 'Format: ' . $calc->getFormat_in_addcontent2() . ' mm</br>';
                                            echo 'Anzahl: ' . $roh2 . ' Bogen</br>';
                                            // echo 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContent2Height().' * '.$calc->getPaperAddContent2Width().')) / Bogen</br>';
                                            echo 'Nutzen: ' . (int)$roh_schnitte . '</br>';
                                            echo '</td>';
                                        } else {
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 3') . ':</b></td>';
                                        }
                                        break;
                                    case Calculation::PAPER_ADDCONTENT3:
                                        if ($calc->getFormat_in_addcontent3() != "") {
                                            $format_in = explode("x", $calc->getFormat_in_addcontent3());
                                            $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                                            $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width()));
                                            $roh2 = ceil($sheets_addcontent3 / $roh);
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 4') . ':</b></br>';
                                            echo 'Format: ' . $calc->getFormat_in_addcontent3() . ' mm</br>';
                                            echo 'Anzahl: ' . $roh2 . ' Bogen</br>';
                                            // echo 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContent3Height().' * '.$calc->getPaperAddContent3Width().')) / Bogen</br>';
                                            echo 'Nutzen: ' . (int)$roh_schnitte . '</br>';
                                            echo '</td>';
                                        } else {
                                            echo '<td valign="top"><b>' . $_LANG->get('Inhalt 4') . ':</b></td>';
                                        }
                                        break;
                                    case Calculation::PAPER_ENVELOPE:
                                        if ($calc->getFormat_in_envelope() != "") {
                                            $format_in = explode("x", $calc->getFormat_in_envelope());
                                            $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                                            $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth()));
                                            $roh2 = ceil($sheets_envelope / $roh);
                                            echo '<td valign="top"><b>' . $_LANG->get('Umschlag') . ':</b></br>';
                                            echo 'Format: ' . $calc->getFormat_in_envelope() . ' mm</br>';
                                            echo 'Anzahl: ' . $roh2 . ' Bogen</br>';
                                            // echo 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperEnvelopeHeight().' * '.$calc->getPaperEnvelopeWidth().')) / Bogen</br>';
                                            echo 'Nutzen: ' . (int)$roh_schnitte . '</br>';
                                            echo '</td>';
                                        } else {
                                            echo '<td valign="top"><b>' . $_LANG->get('Umschlag') . ':</b></td>';
                                        }
                                        break;
                                }
                            }
                        }
                        ?>
                    </tr>
                </table>
            </div>
            <br>
            <h3><?= $_LANG->get('Druckbogen / Detaillierte Informationen') ?></h3>
            <div class="box1">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <colgroup>
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                    </colgroup>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Inhalt 1') ?>:</b> <br>
                            <b><?= $calc->getPaperContent()->getName() ?>, <?= $calc->getPaperContentWeight() ?> g</b>
                            <br>
                            <b> Farbigkeit </b><?= $calc->getChromaticitiesContent()->getName() ?> <br>
                            <b><?= $_LANG->get('Bogenformat') ?>: </b><?= $calc->getPaperContentWidth() ?> mm
                            x <?= $calc->getPaperContentHeight() ?> mm <br>
                            <b><?= $_LANG->get('Produktformat') ?>:</b> <?= $calc->getProductFormatWidth() ?> mm
                            x <?= $calc->getProductFormatHeight() ?> mm, <br>
                            <?= $calc->getProductFormatWidthOpen() ?> mm x
                            <?= $calc->getProductFormatHeightOpen() ?> mm (offen)<br>
                            <b><?= $_LANG->get('Seiten pro Bogen') ?>
                                : </b><?= $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) ?>,<br>
                            <?php
                            // if ($calc->getChromaticitiesContent()->getReversePrinting() == 0 AND $calc->getChromaticitiesContent()->getColorsBack() == 0 )
                            // {
                            ?><b><?= $_LANG->get('Nutzen pro Bogen') ?>
                                : </b><?= $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) ?>,<br>
                            <? /*

					} else {
						?><b><?=$_LANG->get('Nutzen pro Bogen')?>: </b><?=$calc->getProductsPerPaper(Calculation::PAPER_CONTENT) / 2?>,<br>
						<?
					}*/ ?>

                            <!--<?= $_LANG->get('verschiedene Druckformen pro Auflage (Bogendeckung)') ?>: <? echo printPrice($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT)); ?><br>-->
                            <?php
                            if ($calc->getChromaticitiesContent()->getReversePrinting() == 0 AND $calc->getChromaticitiesContent()->getColorsBack() == 1) {
                                ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                <? $sheets = ceil($calc->getPagesContent() / ($calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * 2) * $calc->getAmount());
                                echo printBigInt($sheets);


                            } else {
                                ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                <? $sheets = ceil($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * $calc->getAmount());
                                echo printBigInt($sheets);
                            } ?>

                            + <?= $_LANG->get('Zuschuss') ?>
                            <? echo printBigInt($calc->getPaperContentGrant());
                            $sheets += $calc->getPaperContentGrant() ?><br>
                            <?php $sheets_content = $sheets; ?>

                            <b><?= $_LANG->get('Druckbogen insgesamt') ?>: </b><br>
                            <? $sheets = ceil($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * $calc->getAmount());
                            echo printBigInt($sheets);
                            ?> Druckbogen + <?= $_LANG->get('Zuschuss') ?>
                            <? echo printBigInt($calc->getPaperContentGrant());
                            $sheets += $calc->getPaperContentGrant() ?>
                            <?php $sheets_content = $sheets; ?>
                            = <?= printBigInt($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()) ?> <?= $_LANG->get('Bogen') ?>
                            <br>
                            <b><?= $_LANG->get('Papiergewicht') ?>: </b>
                            <? $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();
                            echo printPrice(($calc->getPaperContentWidth() * $calc->getPaperContentHeight() * $calc->getPaperContentWeight() / 10000 / 100) * $calc->getAmount() / 10000);
                            ?> kg,<br>
                            <b><?= $_LANG->get('Papiergewicht pro StÃ¼ck') ?>: </b>
                            <? $area = $calc->getProductFormatWidth() * $calc->getProductFormatHeight();
                            echo printPrice(($area * $calc->getPaperContentWeight() / 100000) / 1000);
                            ?> kg,<br>

                            <b><?= $_LANG->get('Farbe') ?>: </b>
                            <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000));
                            echo printPrice($sheets);
                            ?> kg pro Farbton<br>
                            <b><?= $_LANG->get('Farbe gesamt') ?>: </b>
                            <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesContent()->getColorsBack() + $calc->getChromaticitiesContent()->getColorsFront()));
                            echo printPrice($sheets);
                            ?> kg<br>
                            <b><?= $_LANG->get('Farbe Kosten ') ?>: </b>
                            <? $sheets = ($calc->getChromaticitiesContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000)));
                            echo printPrice($sheets);
                            ?> â‚¬ pro Farbton<br>
                            <b><?= $_LANG->get('Farbe Kosten gesamt ') ?>: </b>
                            <? $sheets_color1 = ($calc->getChromaticitiesContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesContent()->getColorsBack() + $calc->getChromaticitiesContent()->getColorsFront())));
                            echo printPrice($sheets_color1);
                            ?> â‚¬ <br>


                        </td>
                        <td valign="top"><b>

                                <? if ($calc->getPaperAddContent()->getId() > 0) { ?>
                                <?= $_LANG->get('Inhalt 2') ?>:</b><br>

                            <? } ?>

                            <? if ($calc->getPaperAddContent()->getId()) { ?>
                                <b><?= $calc->getPaperAddContent()->getName() ?>, <?= $calc->getPaperAddContentWeight() ?> g</b>
                                <br>
                                <b> Farbigkeit </b><?= $calc->getChromaticitiesAddContent()->getName() ?> <br>
                                <b><?= $_LANG->get('Bogenformat') ?>
                                : </b><?= $calc->getPaperAddContentWidth() ?> mm x <?= $calc->getPaperAddContentHeight() ?> mm
                                <br>
                                <b><?= $_LANG->get('Produktformat') ?>
                                : </b><?= $calc->getProductFormatWidth() ?> mm x <?= $calc->getProductFormatHeight() ?> mm, <br>
                                <?= $calc->getProductFormatWidthOpen() ?> mm x <?= $calc->getProductFormatHeightOpen() ?> mm (offen)
                                <br>
                                <b> <?= $_LANG->get('Seiten pro Bogen') ?>
                                : </b><?= $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) ?>,<br>
                                <b><?= $_LANG->get('Nutzen pro Bogen') ?>
                                : </b><?= $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) / 2 ?>,<br>
                                <!-- <?= $_LANG->get('verschiedene Druckformen pro Auflage (Bogendeckung)') ?>: <? echo printPrice($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT)); ?><br>-->
                                <?php
                                if ($calc->getChromaticitiesAddContent()->getReversePrinting() == 0 AND $calc->getChromaticitiesContent()->getColorsBack() == 1) {
                                    ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                    <? $sheets = ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount());
                                    echo printBigInt($sheets);


                                } else {
                                    ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                    <? $sheets = ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount() * 2);
                                    echo printBigInt($sheets);
                                } ?>

                                + <?= $_LANG->get('Zuschuss') ?>
                                <? echo printBigInt($calc->getPaperAddContentGrant());
                                $sheets += $calc->getPaperAddContentGrant() ?><br>
                                <?php $sheets_content = $sheets; ?>

                                <b><?= $_LANG->get('Druckbogen') ?>: </b><br>
                                <? $sheets = ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount());
                                echo printBigInt($sheets);
                                ?> Druckbogen + <?= $_LANG->get('Zuschuss') ?>
                                <? echo printBigInt($calc->getPaperAddContentGrant());
                                $sheets += $calc->getPaperAddContentGrant() ?>
                                <?php $sheets_addcontent = $sheets; ?>
                                <?php $sheets_content = $sheets; ?> = <?= printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant()) ?> <?= $_LANG->get('Bogen') ?>
                                <br>
                                <b><?= $_LANG->get('Papiergewicht') ?>: </b>
                                <? $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();
                                echo printPrice(($calc->getProductFormatWidth() * $calc->getProductFormatHeight() * $calc->getPaperContentWeight() / 10000 / 100) * $calc->getAmount() / 1000);
                                ?> kg,<br>
                                <b><?= $_LANG->get('Papiergewicht pro StÃ¼ck') ?>: </b>
                                <? $area = $calc->getProductFormatWidth() * $calc->getProductFormatHeight();
                                echo printPrice(($area * $calc->getPaperAddContentWeight() / 100000) / 1000);
                                ?> kg,<br>
                                <b><?= $_LANG->get('Farbe') ?>: </b>
                                <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000));
                                echo printPrice($sheets);
                                ?> kg pro Farbton<br>
                                <b><?= $_LANG->get('Farbe gesamt') ?>: </b>
                                <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent()->getColorsBack() + $calc->getChromaticitiesAddContent()->getColorsFront()));
                                echo printPrice($sheets);
                                ?> kg<br>
                                <b><?= $_LANG->get('Farbe Kosten ') ?>: </b>
                                <? $sheets = ($calc->getChromaticitiesAddContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000)));
                                echo printPrice($sheets);
                                ?> â‚¬ pro Farbton<br>
                                <b><?= $_LANG->get('Farbe Kosten gesamt ') ?>: </b>
                                <? $sheets_color2 = ($calc->getChromaticitiesAddContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent()->getColorsBack() + $calc->getChromaticitiesAddContent()->getColorsFront())));
                                echo printPrice(sheets_color2);
                                ?> â‚¬ <br>

                            <? } ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><b>
                                <? if ($calc->getPaperAddContent2()->getId() > 0) { ?>
                                <?= $_LANG->get('Inhalt 3') ?>:</b><br>

                            <? } ?>

                            <? if ($calc->getPaperAddContent2()->getId()) { ?>
                                <b><?= $calc->getPaperAddContent2()->getName() ?>, <?= $calc->getPaperAddContent2Weight() ?>
                                    g</b>
                                <br>
                                <b> Farbigkeit </b><?= $calc->getChromaticitiesAddContent2()->getName() ?> <br>
                                <b><?= $_LANG->get('Bogenformat') ?>
                                : </b><?= $calc->getPaperAddContent2Width() ?> mm x <?= $calc->getPaperAddContent2Height() ?> mm
                                <br>
                                <b><?= $_LANG->get('Produktformat') ?>
                                : </b><?= $calc->getProductFormatWidth() ?> mm x <?= $calc->getProductFormatHeight() ?> mm, <br>
                                <?= $calc->getProductFormatWidthOpen() ?> mm x <?= $calc->getProductFormatHeightOpen() ?> mm (offen)
                                <br>
                                <b><?= $_LANG->get('Seiten pro Bogen') ?>
                                    :</b> <?= $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) ?>,
                                <b><?= $_LANG->get('Nutzen pro Bogen') ?>
                                : </b><?= $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) / 2 ?>,<br>
                                <!--<?= $_LANG->get('verschiedene Druckformen pro Auflage (Bogendeckung)') ?>:
                    <? echo printPrice($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2)); ?><br>-->
                                <?php
                                if ($calc->getChromaticitiesAddContent2()->getReversePrinting() == 0 AND $calc->getChromaticitiesContent2()->getColorsBack() == 1) {
                                    ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                    <? $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount());
                                    echo printBigInt($sheets);


                                } else {
                                    ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                    <? $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount() * 2);
                                    echo printBigInt($sheets);
                                } ?>

                                + <?= $_LANG->get('Zuschuss') ?>
                                <? echo printBigInt($calc->getPaperAddContent2Grant());
                                $sheets += $calc->getPaperAddContent2Grant() ?><br>
                                <?php $sheets_content = $sheets; ?>
                                <b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                <? $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount() * 2);
                                echo printBigInt($sheets);
                                ?> + <?= $_LANG->get('Zuschuss') ?>
                                <? echo printBigInt($calc->getPaperAddContent2Grant() * 2);
                                $sheets += $calc->getPaperAddContent2Grant() ?><br>
                                <?php $sheets_addcontent2 = $sheets; ?>
                                <b><?= $_LANG->get('Druckbogen insgesamt') ?>: </b><br>
                                <? $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount());
                                echo printBigInt($sheets);
                                ?> Druckbogen + <?= $_LANG->get('Zuschuss') ?>
                                <? echo printBigInt($calc->getPaperAddContent2Grant());
                                $sheets += $calc->getPaperAddContent2Grant() ?>
                                <?php $sheets_content = $sheets; ?> = <?= printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant()) ?> <?= $_LANG->get('Bogen') ?>
                                <br>
                                <b><?= $_LANG->get('Papiergewicht') ?>: </b>
                                <? $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();
                                echo printPrice(($calc->getProductFormatWidth() * $calc->getProductFormatHeight() * $calc->getPaperContentWeight() / 10000 / 100) * $calc->getAmount() / 1000);
                                ?> kg,<br>
                                <b><?= $_LANG->get('Papiergewicht pro StÃ¼ck') ?>: </b>
                                <? $area = $calc->getProductFormatWidth() * $calc->getProductFormatHeight();
                                echo printPrice(($area * $calc->getPaperAddContent2Weight() / 100000) / 1000);
                                ?> kg,<br>
                                <b><?= $_LANG->get('Farbe') ?>: </b>
                                <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000));
                                echo printPrice($sheets);
                                ?> kg pro Farbton<br>
                                <b><?= $_LANG->get('Farbe gesamt') ?>: </b>
                                <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent2()->getColorsBack() + $calc->getChromaticitiesAddContent2()->getColorsFront()));
                                echo printPrice($sheets);
                                ?> kg<br>
                                <b><?= $_LANG->get('Farbe Kosten ') ?>: </b>
                                <? $sheets = ($calc->getChromaticitiesAddContent2()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000)));
                                echo printPrice($sheets);
                                ?> â‚¬ pro Farbton<br>
                                <b><?= $_LANG->get('Farbe Kosten gesamt ') ?>: </b>
                                <? $sheets_color3 = ($calc->getChromaticitiesAddContent2()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent2()->getColorsBack() + $calc->getChromaticitiesAdd2Content()->getColorsFront())));
                                echo printPrice($sheets_color3);
                                ?> â‚¬ <br>
                            <? } ?>
                        </td>
                        <td valign="top"><b>
                                <? if ($calc->getPaperAddContent3()->getId() > 0) { ?>
                                    <b><?= $_LANG->get('Inhalt 4') ?>:</b><br>

                                <? } ?>
                                <? if ($calc->getPaperAddContent3()->getId()) { ?>
                                <b><?= $calc->getPaperAddContent3()->getName() ?>, <?= $calc->getPaperAddContent3Weight() ?>
                                    g</b>
                                <br>
                                <b> Farbigkeit </b><?= $calc->getChromaticitiesAddContent3()->getName() ?> <br>
                                <? if ($calc->getPaperAddContent3()->getId()) { ?>
                                    <b><?= $_LANG->get('Bogenformat') ?>
                                    : </b><?= $calc->getPaperAddContent3Width() ?> mm x <?= $calc->getPaperAddContent3Height() ?> mm
                                    <br>
                                    <b><?= $_LANG->get('Produktformat') ?>
                                    : </b><?= $calc->getProductFormatWidth() ?> mm x <?= $calc->getProductFormatHeight() ?> mm,
                                    <br>
                                    <?= $calc->getProductFormatWidthOpen() ?> mm x <?= $calc->getProductFormatHeightOpen() ?> mm (offen)
                                    <br>
                                    <b><?= $_LANG->get('Seiten pro Bogen') ?>
                                        :</b> <?= $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) ?>,
                                    <b><?= $_LANG->get('Nutzen pro Bogen') ?>
                                    : </b><?= $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) / 2 ?>,<br>
                                    <!--<?= $_LANG->get('verschiedene Druckformen pro Auflage (Bogendeckung)') ?>:
                    <? echo printPrice($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3)); ?><br>-->
                                    <?php
                                    if ($calc->getChromaticitiesAddContent3()->getReversePrinting() == 0 AND $calc->getChromaticitiesContent3()->getColorsBack() == 1) {
                                        ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                        <? $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount());
                                        echo printBigInt($sheets);


                                    } else {
                                        ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                        <? $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount() * 2);
                                        echo printBigInt($sheets);
                                    } ?>

                                    + <?= $_LANG->get('Zuschuss') ?>
                                    <? echo printBigInt($calc->getPaperAddContent3Grant());
                                    $sheets += $calc->getPaperAddContent3Grant() ?><br>
                                    <b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                    <? $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount() * 2);
                                    echo printBigInt($sheets);
                                    ?> + <?= $_LANG->get('Zuschuss') ?>
                                    <? echo printBigInt($calc->getPaperAddContent3Grant() * 2);
                                    $sheets += $calc->getPaperAddContent3Grant() ?><br>
                                    <b><?= $_LANG->get('Druckbogen insgesamt') ?>: </b><br>
                                    <? $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount());
                                    echo printBigInt($sheets);
                                    ?>Druckbogen + <?= $_LANG->get('Zuschuss') ?>
                                    <? echo printBigInt($calc->getPaperAddContent3Grant());
                                    $sheets += $calc->getPaperAddContent3Grant() ?>
                                    <?php $sheets_addcontent3 = $sheets; ?>
                                    <?php $sheets_content = $sheets; ?> = <?= printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant()) ?> <?= $_LANG->get('Bogen') ?>
                                    <br>
                                    <b><?= $_LANG->get('Papiergewicht') ?>: </b>
                                    <? $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();
                                    echo printPrice(($calc->getProductFormatWidth() * $calc->getProductFormatHeight() * $calc->getPaperContentWeight() / 10000 / 100) * $calc->getAmount() / 1000);
                                    ?> kg,<br>
                                    <b><?= $_LANG->get('Papiergewicht pro StÃ¼ck') ?>: </b>
                                    <? $area = $calc->getProductFormatWidth() * $calc->getProductFormatHeight();
                                    echo printPrice(($area * $calc->getPaperAddContent3Weight() / 100000) / 1000);
                                    ?> kg,<br>
                                    <b><?= $_LANG->get('Farbe') ?>: </b>
                                    <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000));
                                    echo printPrice($sheets);
                                    ?> kg pro Farbton<br>
                                    <b><?= $_LANG->get('Farbe gesamt') ?>: </b>
                                    <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent3()->getColorsBack() + $calc->getChromaticitiesAddContent3()->getColorsFront()));
                                    echo printPrice($sheets);
                                    ?> kg<br>
                                    <b><?= $_LANG->get('Farbe Kosten ') ?>: </b>
                                    <? $sheets = ($calc->getChromaticitiesAddContent3()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000)));
                                    echo printPrice($sheets);
                                    ?> â‚¬ pro Farbton<br>
                                    <b><?= $_LANG->get('Farbe Kosten gesamt ') ?>: </b>
                                    <? $sheets_color4 = ($calc->getChromaticitiesAddContent3()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent3()->getColorsBack() + $calc->getChromaticitiesAdd3Content()->getColorsFront())));
                                    echo printPrice($sheets_color4);
                                    ?> â‚¬ <br>

                                <? } ?>


                        </td>
                        <? } ?>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>


                    <tr>
                        <td valign="top"><b>
                                <? if ($calc->getPaperEnvelope()->getId()) { ?>

                                <?= $_LANG->get('Umschlag') ?>:</b><br>

                            <? } ?>
                            <b><?= $calc->getPaperEnvelope()->getName() ?>, <?= $calc->getPaperEnvelopeWeight() ?> g</b>
                            <br>
                            <b> Farbigkeit </b><?= $calc->getChromaticitiesEnvelope()->getName() ?> <br>
                            <? if ($calc->getPaperEnvelope()->getId()) { ?>
                            <b><?= $_LANG->get('Bogenformat') ?>:</b> <?= $calc->getPaperEnvelopeWidth() ?> mm
                            x <?= $calc->getPaperEnvelopeHeight() ?> mm <br>
                            <b><?= $_LANG->get('Produktformat') ?>:</b> <?= $calc->getProductFormatWidth() ?> mm
                            x <?= $calc->getProductFormatHeight() ?> mm, <br>
                            <?= $calc->getEnvelopeWidthOpen() ?> mm x <?= $calc->getEnvelopeHeightOpen() ?> mm (offen)<br>
                            <b><?= $_LANG->get('Seiten pro Bogen') ?>
                                :</b> <?= $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) ?>, </br>
                            <b><?= $_LANG->get('Nutzen pro Bogen') ?>
                                : </b><?= $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) / 2 ?>,<br>
                            <!-- <?= $_LANG->get('verschiedene Druckformen pro Auflage (Bogendeckung)') ?>: <? echo printPrice($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE)); ?><br>-->
                            <?php
                            if ($calc->getChromaticitiesEnvelope()->getReversePrinting() == 0 AND $calc->getChromaticitiesEnvelope()->getColorsBack() == 1) {
                                ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                <? $sheets = ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount());
                                echo printBigInt($sheets);


                            } else {
                                ?><b><?= $_LANG->get('Druckleistung insgesamt') ?>: </b>
                                <? $sheets = ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount() * 2);
                                echo printBigInt($sheets);
                            } ?>

                            + <?= $_LANG->get('Zuschuss') ?>
                            <? echo printBigInt($calc->getPaperEnvelopeGrant());
                            $sheets += $calc->getPaperEnvelopeGrant() ?><br>
                            <b><?= $_LANG->get('Druckbogen insgesamt') ?>: </b><br>
                            <? $sheets = ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount());
                            echo printBigInt($sheets);
                            ?> Druckbogen + <?= $_LANG->get('Zuschuss') ?>
                            <? echo printBigInt($calc->getPaperEnvelopeGrant());
                            $sheets += $calc->getPaperEnvelopeGrant() ?>
                            <?php $sheets_envelope = $sheets; ?>
                            <?php $sheets_content = $sheets; ?>
                            = <?= printBigInt($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant()) ?> <?= $_LANG->get('Bogen') ?>
                            <br>
                            <b><?= $_LANG->get('Papiergewicht') ?>: </b>
                            <? $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();
                            echo printPrice(($calc->getProductFormatWidth() * $calc->getProductFormatHeight() * $calc->getPaperContentWeight() / 10000 / 100) * $calc->getAmount() / 1000);
                            ?> kg,<br>
                            <b><?= $_LANG->get('Papiergewicht pro StÃ¼ck') ?>: </b>
                            <? $area = $calc->getProductFormatWidth() * $calc->getProductFormatHeight();
                            echo printPrice(($area * $calc->getPaperEnvelopeWeight() / 100000) / 1000);
                            ?> kg,<br>
                            <b><?= $_LANG->get('Farbe') ?>: </b>
                            <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000));
                            echo printPrice($sheets);
                            ?> kg pro Farbton<br>
                            <b><?= $_LANG->get('Farbe gesamt') ?>: </b>
                            <? $sheets = (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesEnvelope()->getColorsBack() + $calc->getChromaticitiesEnvelope()->getColorsFront()));
                            echo printPrice($sheets);
                            ?> kg<br>
                            <b><?= $_LANG->get('Farbe Kosten ') ?>: </b>
                            <? $sheets = ($calc->getChromaticitiesEnvelope()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000)));
                            echo printPrice($sheets);
                            ?> â‚¬ pro Farbton<br>
                            <b><?= $_LANG->get('Farbe Kosten gesamt ') ?>: </b>
                            <? $sheets_envelope = ($calc->getChromaticitiesEnvelope()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesEnvelope()->getColorsBack() + $calc->getChromaticitiesEnvelope()->getColorsFront())));
                            echo printPrice($sheets_envelope);
                            ?> â‚¬ <br>
                        </td>
                    </tr>
                    <? } ?>
                </table>
            </div>
            <br>


            <h2><?= $_LANG->get('Papierpreise') ?></h2>
            <div class="box1">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <colgroup>
                        <col width="15%">
                        <col width="50%">
                        <col width="35%">

                    </colgroup>
                    <? if ($calc->getPaperContent()->getId() > 0) { ?>
                        <tr>
                            <td valign="top"><b><?= $_LANG->get('Inhalt 1') ?>:</b></td>
                            <td valign="top">
                                <?= $calc->getPaperContent()->getName() ?>, <?= $calc->getPaperContentWeight() ?> g
                            </td>
                            <td valign="top"><b>
                                    <?= $_LANG->get('Papierpreis') ?>: <?= printPrice($calc->getPaperContent()->getSumPrice(
                                        ceil($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * $calc->getAmount()))) ?> <?= $_USER->getClient()->getCurrency() ?>
                                    <br>
                                </b></td>
                        </tr>
                        <tr>
                            <td valign="top"></td>
                            <td valign="top">
                                <?= $calc->getPagesContent() ?> Seiten, <?= $calc->getProductFormat()->getName() ?>
                                , <?= $calc->getChromaticitiesContent()->getName() ?>
                            </td>
                            <td>
                                <?= $_LANG->get('Preisbasis') ?>: <?
                                if ($calc->getPaperContent()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg');
                                else echo $_LANG->get('Preis pro 1000 Bogen');
                                ?>
                            </td>
                        </tr>
                    <? } ?>

                    <? if ($calc->getPaperAddContent()->getId()) { ?>
                        <tr>
                            <td valign="top">
                                <b><?= $_LANG->get('Inhalt 2') ?>:</b>
                            </td>
                            <td valign="top">
                                <? if ($calc->getPaperAddContent()->getId()) { ?>
                                    <?= $calc->getPaperAddContent()->getName() ?>, <?= $calc->getPaperAddContentWeight() ?> g
                                <? } ?>
                            </td>
                            <td valign="top"><b>
                                    <?= $_LANG->get('Papierpreis') ?>: <?= printPrice($calc->getPaperAddContent()->getSumPrice(
                                        ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount()))) ?> <?= $_USER->getClient()->getCurrency() ?>
                                    <br>
                                </b></td>
                        </tr>
                        <tr>
                            <td valign="top"></td>
                            <td valign="top">
                                <?= $calc->getPagesAddContent() ?> Seiten, <?= $calc->getProductFormat()->getName() ?>
                                , <?= $calc->getChromaticitiesAddContent()->getName() ?>

                            </td>
                            <td>
                                <?= $_LANG->get('Preisbasis') ?>: <?
                                if ($calc->getPaperAddContent()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg');
                                else echo $_LANG->get('Preis pro 1000 Bogen');
                                ?>
                            </td>
                        </tr>
                    <? } ?>
                    <? if ($calc->getPaperAddContent2()->getId() > 0) { ?>
                        <tr>

                            <td valign="top">
                                <? if ($calc->getPaperAddContent2()->getId() > 0) { ?>
                                    <b><?= $_LANG->get('Inhalt 3') ?>:</b>
                                <? } ?>
                            </td>
                            <td valign="top">
                                <? if ($calc->getPaperAddContent2()->getId() > 0) { ?>
                                    <?= $calc->getPaperAddContent2()->getName() ?>, <?= $calc->getPaperAddContentWeight() ?> g
                                <? } ?>
                            </td>
                            <td valign="top"><b>
                                    <? if ($calc->getPaperAddContent2()->getId() > 0) { ?>
                                        <?= $_LANG->get('Papierpreis') ?>: <?= printPrice($calc->getPaperAddContent2()->getSumPrice(
                                            ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount()))) ?> <?= $_USER->getClient()->getCurrency() ?>
                                        <br>
                                    <? } ?>
                                </b></td>
                        </tr>
                        <tr>
                            <td valign="top"></td>
                            <td valign="top">
                                <? if ($calc->getPaperAddContent2()->getId() > 0) { ?>
                                    <?= $calc->getPagesAddContent2() ?> Seiten, <?= $calc->getProductFormat()->getName() ?>, <?= $calc->getChromaticitiesAddContent2()->getName() ?>
                                <? } ?>
                            </td>
                            <td valign="top">
                                <? if ($calc->getPaperAddContent2()->getId() > 0) { ?>
                                    <?= $_LANG->get('Preisbasis') ?>:


                                    <?
                                    if ($calc->getPaperAddContent2()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg');
                                    else echo $_LANG->get('Preis pro 1000 Bogen');
                                    ?>
                                <? } ?>


                            </td>
                        </tr>
                    <? } ?>
                    <? if ($calc->getPaperAddContent3()->getId() > 0) { ?>
                        <tr>

                            <td valign="top">
                                <? if ($calc->getPaperAddContent3()->getId() > 0) { ?>
                                    <b><?= $_LANG->get('Inhalt 4') ?>:</b>
                                <? } ?>
                            </td>
                            <td valign="top">
                                <? if ($calc->getPaperAddContent3()->getId() > 0) { ?>
                                    <?= $calc->getPaperAddContent3()->getName() ?>, <?= $calc->getPaperAddContentWeight() ?> g
                                <? } ?>
                            </td>
                            <td valign="top"><b>
                                    <? if ($calc->getPaperAddContent3()->getId() > 0) { ?>
                                        <?= $_LANG->get('Papierpreis') ?>: <?= printPrice($calc->getPaperAddContent3()->getSumPrice(
                                            ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount()))) ?> <?= $_USER->getClient()->getCurrency() ?>
                                        <br>
                                    <? } ?>
                                </b></td>
                        </tr>
                        <tr>
                            <td valign="top"></td>
                            <td valign="top">
                                <? if ($calc->getPaperAddContent3()->getId() > 0) { ?>
                                    <?= $calc->getPagesAddContent3() ?> Seiten, <?= $calc->getProductFormat()->getName() ?>, <?= $calc->getChromaticitiesAddContent2()->getName() ?>
                                <? } ?>
                            </td>
                            <td valign="top">
                                <? if ($calc->getPaperAddContent3()->getId() > 0) { ?>
                                    <?= $_LANG->get('Preisbasis') ?>:
                                    <?
                                    if ($calc->getPaperAddContent3()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg');
                                    else echo $_LANG->get('Preis pro 1000 Bogen');
                                    ?>
                                <? } ?>

                            </td>
                        </tr>
                    <? } ?>
                    <? if ($calc->getPaperEnvelope()->getId() > 0) { ?>
                        <tr>
                            <td valign="top">
                                <? if ($calc->getPaperEnvelope()->getId()) { ?>
                                    <b><?= $_LANG->get('Umschlag') ?>:</b>
                                <? } ?>
                            </td>
                            <td valign="top">
                                <? if ($calc->getPaperEnvelope()->getId() > 0) { ?>
                                    <?= $calc->getPaperEnvelope()->getName() ?>, <?= $calc->getPaperEnvelopeWeight() ?> g
                                <? } ?>
                            </td>
                            <td valign="top"><b>
                                    <? if ($calc->getPaperEnvelope()->getId() > 0) { ?>
                                        <?= $_LANG->get('Papierpreis') ?>: <?= printPrice($calc->getPaperEnvelope()->getSumPrice(
                                            ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount()))) ?> <?= $_USER->getClient()->getCurrency() ?>
                                        <br>
                                    <? } ?>
                                </b></td>
                        </tr>
                        <tr>
                            <td valign="top"></td>
                            <td valign="top">
                                <? if ($calc->getPaperEnvelope()->getId() > 0) { ?>
                                    <?= $calc->getPagesEnvelope() ?> Seiten, <?= $calc->getProductFormat()->getName() ?>, <?= $calc->getChromaticitiesEnvelope()->getName() ?>
                                <? } ?>
                            </td>
                            <td valign="top">
                                <? if ($calc->getPaperEnvelope()->getId() > 0) { ?>
                                    <?= $_LANG->get('Preisbasis') ?>:
                                    <?
                                    if ($calc->getPaperEnvelope()->getPriceBase() == Paper::PRICE_PER_100KG) echo $_LANG->get('Preis pro 100 kg');
                                    else echo $_LANG->get('Preis pro 1000 Bogen');
                                    ?>
                                <? } ?>

                            </td>
                        </tr>
                    <? } ?>
                </table>
            </div>
            <br>
            <h2><?= $_LANG->get('Kosten / Ertragsaufstellung') ?></h2>
            <div class="box1">
                <table cellpadding="0" cellspacing="0" border="0" width="50%">
                    <colgroup>
                        <col width="60%">
                        <col width="40%">
                    </colgroup>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Fertigungskosten') ?>:</b></td>
                        <td valign="top"><b>
                                <?= printPrice($calc->getSubTotal() - ($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()) + $calc->getPaperAddContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant()) + $calc->getPaperAddContent2()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant()) + $calc->getPaperAddContent3()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant()) + $calc->getPaperEnvelope()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant()))) ?> <?= $_USER->getClient()->getCurrency() ?></b>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Materialkosten') ?>:</b></td>
                        <td valign="top"><b>

                                <?= printPrice($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()) + $calc->getPaperAddContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant()) + $calc->getPaperAddContent2()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant()) + $calc->getPaperAddContent3()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant()) + $calc->getPaperEnvelope()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant()) + ($sheets_color1) + ($sheets_color2) + ($sheets_color3) + ($sheets_color4) + ($sheets_envelope)) ?> <?= $_USER->getClient()->getCurrency() ?></b>
                        </td>

                    </tr>

                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Produktionskosten insgesamt') ?>:</b></td>
                        <td valign="top"><b>
                                <?= printPrice($calc->getSubTotal() + ($sheets_color1) + ($sheets_color2) + ($sheets_color3) + ($sheets_color4) + ($sheets_envelope)) ?> <?= $_USER->getClient()->getCurrency() ?>


                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Vertriebskonditionen') ?>:</b></td>
                        <td valign="top"><b></td>
                    </tr>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Marge') ?>:</b></td>
                        <td><?= printPrice($calc->getMargin()) ?> % </b></td>
                    </tr>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Rabatt') ?>:</b></td>
                        <td><?= printPrice($calc->getDiscount()) ?><?= $_USER->getClient()->getCurrency() ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Manueller Auf & Abschlag auf Endpreis') ?>:</b></td>
                        <td><?= printPrice($calc->getAddCharge()) ?><?= $_USER->getClient()->getCurrency() ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><b><?= $_LANG->get('Verkaufspreispreis') ?>:</b></td>
                        <b>
                            <td><?= printPrice($calc->getSummaryPrice() + ($sheets_color1) + ($sheets_color2) + ($sheets_color3) + ($sheets_color4) + ($sheets_envelope)) ?> <?= $_USER->getClient()->getCurrency() ?>
                        </b>
                        </td>
                        </td>
                    </tr>

                </table>
            </div>
    </div>
    <br>
    <? $i++;
} ?>