<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

// $orderpos = $order->getPositions();
$orderpos = $order->getPositions(false,true);

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/colinvoice.tmpl';
$datei = ckeditor_to_smarty($tmp);

// pricetable from colinv
$pricetable = $order->getPriceTable();
$gesnetto = $pricetable['total_net'];
$sum = $pricetable['total_gross'];
$taxes = $pricetable['taxvalues'];

// Table
$smarty->assign('OrderPos',$orderpos);
$smarty->assign('DeliveryCosts',$order->getDeliveryterm()->getCharges());
$smarty->assign('SumNetto',$gesnetto);

// Steuern
$smarty->assign('Taxes',$taxes);

// Gesamtsumme
$smarty->assign('SumBrutto',$sum);

// Zahlungsbedingungen
$smarty->assign('PaymentTerm',$order->getPaymentTerm());
$smarty->assign('PayComment',$order->getPaymentTerm()->getComment());
$smarty->assign('PayNetTodays',$order->getPaymentTerm()->getNettodays());

// Werte setzen
if ($order->getPaymentTerm())
    $tmp_nettodays = 0;
else 
    $tmp_nettodays = $order->getPaymentTerm()->getNettodays();

$this->priceNetto = $gesnetto;
$this->priceBrutto = $sum;
$this->payable = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + ($tmp_nettodays * 86400);

// Footer
$smarty->assign('UserClient',$_USER->getClient()->getName());
$htmldump = $smarty->fetch('string:'.$datei);
$pdf->writeHTML($htmldump);