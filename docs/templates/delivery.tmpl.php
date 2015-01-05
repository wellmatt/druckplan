<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

// Einbindung der generellen Variablen im Templatesystem

require 'docs/templates/generel.tmpl.php';
$tmp = 'C:/xampp/htdocs/dev/docs/tmpl_files/delivery.tmpl';
$datei = ckeditor_to_smarty($tmp);

// Table
$smarty->assign('Order',$order);
$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);

// Vorbearbeitung
foreach ($calcs as $calc)
{
    // Produktnamen holen oder ggf. ueberschreiben
    $tmp_productname = $order->getProduct()->getName();
    if ($order->getProductName() != "" && $order->getProductName() != NULL) {
        $tmp_productname = $order->getProductName();
    }
    $order->setProductName($tmp_productname);
    
    if ((int) $_REQUEST["delivery_amount"] > 0) {
        $calc->setAmount((int) $_REQUEST["delivery_amount"]);
    }
}

$smarty->assign('Calcs',$calcs);

$htmldump = $smarty->fetch('string:'.$datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);

?>