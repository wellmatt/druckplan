<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
// Groesse : 106x60 mm sind 301x171 Px

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Soll nur die Bottommargin auf 0 setzen
$pdf->SetPageOrientation( 'L', '', '0');

$pixel_left = 17;
$pixel_right = 220;
$font = "helvetica";

$pdf->SetMargins(5, 6, 5, true);
$pdf->AddPage();

if($_REQUEST["label_print_logo"]){
	$path = "docs/templates/etikett_logo.jpg";
	$pdf->Image($path, 61, 6, 40, 0, '', 'none','right');

}


$pdf->SetFont($font, 'b', 9);
$pdf->Cell($pixel_left, 0, "Datum: ", 0, 0);
$pdf->SetFont($font, '', 9);
$pdf->Cell($pixel_left, 0, date('d.m.Y'), 0, 1);
$pdf->Ln(1);
$pdf->SetFont($font, 'b', 9);
$pdf->Cell($pixel_left, 0, "AU-Nr.: ", 0, 0);
$pdf->SetFont($font, '', 9);
$pdf->Cell($pixel_left, 0, $order->getNumber(), 0, 1);

$pdf->Ln(3);

$pdf->SetFont($font, 'b', 11);
$pdf->Cell($pixel_left, 0, "Menge: ", 0, 0);
$pdf->Cell($pixel_left, 0, (int)$_REQUEST["label_box_amount"], 0, 1);
$pdf->SetFont($font, '', 11);

$pdf->Ln(3);

$pdf->SetFont($font, 'b', 9);
$pdf->Cell($pixel_left, 0, "Titel: ", 0, 0);
$pdf->SetFont($font, '', 9);
$pdf->Cell($pixel_left, 0, $_REQUEST["label_title"], 0, 1);
$pdf->Ln(1);
$pdf->SetFont($font, 'b', 9);
$pdf->Cell($pixel_left, 0, "Produkt: ", 0, 0);
$pdf->SetFont($font, '', 9);
$pdf->Cell($pixel_left, 0, $order->getProduct()->getDescription(), 0, 1);
$pdf->Ln(1);

$pdf->Ln(11);

$pdf->SetFont($font, '', 8);
$pdf->Cell(0, 0, "{$_USER->getClient()->getName()} // {$_USER->getClient()->getStreet1()} // {$_USER->getClient()->getPostcode()} {$_USER->getClient()->getCity()}", 0, 1);
$pdf->Ln(1);
$pdf->Cell(0, 0,"Fon {$_USER->getClient()->getPhone()} // {$_USER->getClient()->getEmail()} // {$_USER->getClient()->getWebsite()}", 0, 1);

?>