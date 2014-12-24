<? 
// --------------------------------------------------------------------------------
   // Author: iPactor GmbH
   // Updated: 19.09.2012
   // Copyright: 2012 by iPactor GmbH. All Rights Reserved.
   // Any unauthorized redistribution, reselling, modifying or reproduction of part
   // or all of the contents in any form is strictly prohibited.
   // ----------------------------------------------------------------------------------
$font = "helvetica";

$pdf->SetFont($font, 'b', 13);
$pdf->Ln();
$pdf->Cell(100, 0, "Lieferschein Nr. {$this->name} ", 0, 0);
$pdf->Cell(100, 0, "Kunden-Nr. {$order->getCustomer()->getId()}", 0, 1);
$pdf->SetFont($font, '', 12);
$pdf->Cell(0, 0, "Auftrag: {$order->getTitle()}", 0, 1);
$pdf->Ln(13);

$pdf->SetFont($font, '', 10);
$pdf->Cell(0, 0, $_LANG->get("Sehr geehrte Damen und Herren"), 0, 1);
$pdf->Ln();
$pdf->Cell(0, 0, $_LANG->get("hiermit erhalten Sie den Lieferschein zum Auftrag {$order->getNumber()}"), 0, 1);
$pdf->Ln();
// ----------------------------------------------------------------------------------
// Tabellenkopf

$tablesize = Array(
    15,
    130,
    20,
);
$pdf->Cell($tablesize[0], 0, $_LANG->get('Pos.'), 1, 0, 'C');
$pdf->Cell($tablesize[1], 0, $_LANG->get('Beschreibung'), 1, 0);
$pdf->Cell($tablesize[2], 0, $_LANG->get('Menge'), 1, 1);

// Tabelleninhalt

$orderpos = $order->getPositions();
$x = 0;
$sum = 0;
$gesnetto = 0;
$taxes = Array();
foreach ($orderpos as $op) {
    $pdf->Cell($tablesize[0], 0, ($x + 1), 0, 0, 'C');
    
    //Fix wegen der Multicell
    $y_start = $pdf->GetY();
    $pdf->MultiCell($tablesize[1], 0, $op->getComment(), 0, 'L', 0, 2);
    
    $y_end = $pdf->GetY();
    $pdf->MultiCell($tablesize[2], 0, $op->getQuantity(), 0, 'L', 0, 1, $pdf->GetX(), $y_start);
    
    //Fix wegen der Multicell
    $pdf->SetY($y_end);
    
    $x++;
}
$pdf->Ln(10);
$pdf->Ln(10);

// Fußblock

$pdf->MultiCell(0, 0, $_LANG->get("Ware bleibt bis zur vollst&auml;ndigen Bezahlung unser Eigentum."), 0, 'L');
$pdf->Ln();
$pdf->MultiCell(0, 0, $_LANG->get("Ware unbesch&auml;digt erhalten"), 0, 'L');
$pdf->Ln(15);
$pdf->MultiCell(0, 0, $_LANG->get("___________________________"), 0, 'L');
$pdf->Ln();
$pdf->MultiCell(0, 0, $_LANG->get("F&uuml;r R&uuml;ckfragen stehen wir Ihnen selbstverst&auml;ndlich gerne zur Verf&uuml;gung."), 0, 'L');
$pdf->Ln();
$pdf->MultiCell(0, 0, $_LANG->get("Mit freundlichen Gr&uuml;&szlig;en"), 0, 'L');
$pdf->MultiCell(0, 0, "{$_USER->getFirstname()} {$_USER->getLastname()}", 0, 'L');
$pdf->Ln();
$pdf->SetFont($font, 'b', 10);
$pdf->MultiCell(0, 0, "{$_USER->getClient()->getName()}", 0, 'L');
$pdf->SetFont($font, '', 10);
$pdf->Ln();
?>