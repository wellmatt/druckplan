<?
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

$orderpos = $order->getPositions();

//Gesamtpreis
$sum = 0;
$gesnetto = 0;
$taxeskey = Array();
$taxes = Array();
foreach ($orderpos as $op)
{
    $gesnetto += $op->getNetto();
    if(!in_array($op->getTax(), $taxeskey))
        $taxeskey[] = $op->getTax();

    $taxes[$op->getTax()] += $op->getNetto() / 100 * $op->getTax();
}

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/colinvoice.tmpl';
$datei = ckeditor_to_smarty($tmp);

// Table

$smarty->assign('OrderPos',$orderpos);

$smarty->assign('DeliveryCosts',$order->getDeliveryCosts());
if ($order->getDeliveryCosts()) {
    // Versandkosten werden auch besteuert (hier 19%)
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

// Zahlungsbedingungen
$smarty->assign('PaymentTerm',$order->getPaymentTerm());
$smarty->assign('PayComment',$order->getPaymentTerm()->getComment());
$smarty->assign('PayNetTodays',$order->getPaymentTerm()->getNettodays());

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