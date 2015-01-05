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

// Vorbereitung der Ausgabe
$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
foreach ($calcs as $calc)
{
    // Produktnamen holen oder ggf. ueberschreiben
    $tmp_productname = $order->getProduct()->getName();
    if ($order->getProductName() != "" && $order->getProductName() != NULL) {
        $tmp_productname = $order->getProductName();
    }
    $order->setProductName($tmp_productname);
}

// Einbindung der generellen Variablen im Templatesystem

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/offerconfirm.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('Calcs',$calcs);

// var_dump ($datei);

$htmldump = $smarty->fetch('string:'.$datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);
?>
