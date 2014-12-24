<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			01.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
$font = "helvetica";

$pdf->SetFont($font, 'b', 13);
$pdf->Ln();
$pdf->Cell(100, 0, "Mahnung {$this->name} ", 0, 0);
$pdf->Cell(100, 0, "Kunden-Nr. {$order->getCustomer()->getId()}", 0, 1);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont($font, '', 10);
$pdf->Cell(0, 0, $_LANG->get("Sehr geehrte Damen und Herren") . ", ", 0, 1);
$pdf->Ln();

// Inhalt der Mahnung
$pdf->MultiCell(0, 0, $_REQUEST["warn_text"], 0, 'L');
$pdf->Ln();

// Footer
$pdf->MultiCell(0, 0, $_LANG->get("Mit freundlichen Gr&uuml;&szlig;en"),0, 'L');
$pdf->MultiCell(0, 0, "{$_USER->getFirstname()} {$_USER->getLastname()}", 0, 'L');
$pdf->Ln();
$pdf->SetFont($font, 'b', 10);
$pdf->MultiCell(0, 0, $_USER->getClient()->getName(), 0, 'L');
$pdf->SetFont($font, '', 10);
$pdf->Ln();

?>