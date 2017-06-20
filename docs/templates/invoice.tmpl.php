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

// Vorbearbeitung
$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
$sum_price = 0;
foreach ($calcs as $calc) {
    if ($calc->getState()) {
        // damit der Wert fuer tmp_amount initialisiert ist (falls DEtails nicht angezeigt werden sollen)
        $tmp_amount = $calc->getAmount();
        
        // Nachschauen, ob die Produktdetails ausgegeben werden sollen
        if ($order->getShowProduct()) {
            // Auflage
            if ((int) $_REQUEST["invoice_update_price"] == 1 && (int) $_REQUEST["invoice_amount"] > 0) {
                $calc->setAmount((int) $_REQUEST["invoice_amount"]);
            }
        }
        // Produktnamen holen oder ggf. ueberschreiben
        $tmp_productname = $order->getProduct()->getName();
        if ($order->getProductName() != "" && $order->getProductName() != NULL) {
            $tmp_productname = $order->getProductName();
        }
        $order->setProductName($tmp_productname);
        
        if ((int) $_REQUEST["invoice_update_price"] == 1) {
            // Wenn Haekchen gesetzt, dann Preis auf neue Menge umrechnen
            $tmp_price = $calc->getSummaryPrice() * ((int) $_REQUEST["invoice_amount"] / $calc->getAmount());
        } else {
            $tmp_price = $calc->getSummaryPrice();
        }
        $sum_price += $tmp_price;
    }
}

// Skonto & Zahlungsbedingungen
$nettodays = $order->getPaymentterm()->getNettodays();

$now = time();
$aday = 86400;
$duedate = date('d.m.y',($now + ($nettodays * $aday)));

$sk1days = $order->getPaymentterm()->getSkontodays1();
$sk1daysduedate = date('d.m.y',($now + ($sk1days * $aday)));

$sk2days = $order->getPaymentterm()->getSkontodays2();
$sk2daysduedate = date('d.m.y',($now + ($sk2days * $aday)));

$sk1_percent = $order->getPaymentterm()->getSkonto1();
$sk2_percent = $order->getPaymentterm()->getSkonto2();

// Einbindung der generellen Variablen im Templatesystem

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/invoice.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('Calcs', $calcs);

$smarty->assign('DueDate', $duedate);
$smarty->assign('Sk1duedate', $sk1daysduedate);
$smarty->assign('Sk2duedate', $sk2daysduedate);
$smarty->assign('Sk1percent', $sk1_percent);
$smarty->assign('Sk2percent', $sk2_percent);

$taxes = $order->getProduct()->getTaxes() * $sum_price / 100;
$doc_sum = $sum_price + $taxes;

$smarty->assign('ProductTaxRate', $order->getProduct()
    ->getTaxes());

$smarty->assign('ProductTaxValue', $taxes);

$smarty->assign('DeliveryTaxRate', $order->getDeliveryTerms()
    ->getTax());

$smarty->assign('DeliveryTaxValue', $order->getDeliveryTerms()
    ->getTax() * $order->getDeliveryCost() / 100);

$smarty->assign('SumBrutto', $doc_sum);

$htmldump = $smarty->fetch('string:' . $datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);
?>