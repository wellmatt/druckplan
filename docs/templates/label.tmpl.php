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

// Groesse : 106x60 mm sind 301x171 px

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/label.tmpl';
$datei = ckeditor_to_smarty($tmp);

// Labeldaten

$smarty->assign('Amount', (int)$_REQUEST["label_box_amount"]);

$smarty->assign('Title', $_REQUEST["label_title"]);

$smarty->assign('Productdesc',$order->getProduct()->getDescription());

$htmldump = $smarty->fetch('string:'.$datei);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Soll nur die Bottommargin auf 0 setzen
$pdf->SetPageOrientation( 'L', '', '0');

$pdf->SetMargins(5, 6, 5, true);
$pdf->AddPage();

if($_REQUEST["label_print_logo"]){
    $path = "docs/templates/etikett_logo.jpg";
    $pdf->Image($path, 61, 6, 40, 0, '', 'none','right');

}

$pdf->writeHTML($htmldump);
?>