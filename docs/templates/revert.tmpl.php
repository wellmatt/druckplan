<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
error_reporting(-1);
ini_set('display_errors', 1);

require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

$revert = Revert::getNewestForColinv($order);
$positions = RevertPosition::getAllForRevert($revert);

$sum = 0;
$gesnetto = 0;
$taxeskey = Array();
$taxes = Array();

foreach ($positions as $revpos)
{
    $gesnetto += $revpos->getPrice();
    if(!in_array($revpos->getTaxkey()->getValue(), $taxeskey))
        $taxeskey[] = $revpos->getTaxkey()->getValue();

    $taxes[$revpos->getTaxkey()->getValue()] += $revpos->getPrice() / 100 * $revpos->getTaxkey()->getValue();
}


require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/revert.tmpl';
$datei = ckeditor_to_smarty($tmp);

// Table

$smarty->assign('OrderPos',$positions);

$smarty->assign('DeliveryCosts',$order->getDeliveryCosts());
if ($order->getDeliveryCosts()) {
    // Versandkosten werden auch besteuert (hier 19%)
    if(!in_array("19", $taxeskey))
        $taxeskey[] = "19";
    $taxes["19"] += $order->getDeliveryCosts() / 100 * 19;
    $gesnetto += $order->getDeliveryCosts();
}

$sum = $gesnetto;
$smarty->assign('SumNetto',$gesnetto);


// Steuern
$smarty->assign('Taxes',$taxes);
$smarty->assign('TaxesKey',$taxeskey);

// Gesamtsumme
$sum = $gesnetto + array_sum ($taxes);
$smarty->assign('SumBrutto',$sum);


// Footer

$smarty->assign('UserClient',$_USER->getClient()->getName());

$htmldump = $smarty->fetch('string:'.$datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);

// Werte setzen
if ($order->getPaymentTerm())
    $tmp_nettodays = 0;
else 
    $tmp_nettodays = $order->getPaymentTerm()->getNettodays();

$this->priceNetto = $gesnetto;
$this->priceBrutto = $sum;
$this->payable = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + ($tmp_nettodays * 86400);

?>