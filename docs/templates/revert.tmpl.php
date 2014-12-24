<? 
// --------------------------------------------------------------------------------
   // Author: iPactor GmbH
   // Updated: 19.09.2012
   // Copyright: 2012 by iPactor GmbH. All Rights Reserved.
   // Any unauthorized redistribution, reselling, modifying or reproduction of part
   // or all of the contents in any form is strictly prohibited.
   // ----------------------------------------------------------------------------------
require_once ('libs/modules/paymentterms/paymentterms.class.php');

$pdf->SetFont($font, 'b', 14);
$pdf->Ln();
$pdf->Cell(100, 0, "Angebot {$this->name}", 0, 0);
$pdf->Cell(100, 0, "Kunden-Nr {$order->getCustomer()->getCustomernumber()}", 0, 1);
$pdf->Ln();
$pdf->SetFont($font, '', 10);
$pdf->Cell(100, 0, "Sehr geehrte Damen und Herren,", 0, 1);
$pdf->Ln();
$pdf->Cell(100, 0, "hiermit erhalten Sie eine Gutschrift mit folgenden Positionen:", 0, 1);
$pdf->Ln();

// Tabellenkopf

$tablesize = Array(
    15,
    50,
    20,
    20,
    30,
    30
);
$pdf->Cell($tablesize[0], 0, $_LANG->get('Pos.'), 1, 0, 'C');
$pdf->Cell($tablesize[1], 0, $_LANG->get('Beschreibung'), 1, 0);
$pdf->Cell($tablesize[2], 0, $_LANG->get('Menge'), 1, 0);
$pdf->Cell($tablesize[3], 0, $_LANG->get('USt.'), 1, 0);
$pdf->Cell($tablesize[4], 0, $_LANG->get('EP (Netto)'), 1, 0, 'R');
$pdf->Cell($tablesize[5], 0, $_LANG->get('GP (Netto)'), 1, 1, 'R');

// Tabelleninhalt

$orderpos = $order->getPositions();
$x = 0;
$sum = 0;
$gesnetto = 0;
$taxes = Array();
foreach ($orderpos as $op) {
    if ($op->getRevrel() == 1) {
        $pdf->Cell($tablesize[0], 0, ($x + 1), 0, 0, 'C');
        
        // Fix wegen der Multicell
        $y_start = $pdf->GetY();
        $pdf->MultiCell($tablesize[1], 0, $op->getComment(), 0, 'L', 0, 2);
        
        $y_end = $pdf->GetY();
        $pdf->MultiCell($tablesize[2], 0, $op->getQuantity(), 0, 'L', 0, 0, $pdf->GetX(), $y_start);
        
        $pdf->Cell($tablesize[3], 0, $op->getTax() . " % ", 0, 0);
        $pdf->Cell($tablesize[4], 0, printPrice($op->getPrice()) . " {$_USER->getClient()->getCurrency()}", 0, 0, 'R');
        $pdf->Cell($tablesize[5], 0, printPrice($op->getNetto()) . " {$_USER->getClient()->getCurrency()}", 0, 1, 'R');
        // Fix wegen der Multicell
        $pdf->SetY($y_end);
        
        $x ++;
        $gesnetto += $op->getNetto();
        $taxes[$op->getTax()] += $op->getNetto() / 100 * $op->getTax();
    }
}
$pdf->Ln(10);
$pdf->Ln(10);

// --------------------------- Gesamtsummen + Steuern ------------------------------------
// Gesamtpreis (Netto)
$tablesize = Array(
    120,
    0
);

$sum = $gesnetto;
$pdf->SetFont($font, 'b', 10);
$pdf->Cell($tablesize[0], 0, $_LANG->get('Gesamtsumme (Netto)'), 0, 0, 'R');
$pdf->Cell($tablesize[1], 0, printPrice($gesnetto) . " {$_USER->getClient()->getCurrency()}", 0, 1, 'R');
$pdf->SetFont($font, '', 10);

// Steuern
foreach ($taxes as $key => $t) {
    $pdf->Cell($tablesize[0], 0, $_LANG->get('USt.') . " ({$key}%)", 0, 0, 'R');
    $pdf->Cell($tablesize[1], 0, printPrice($t) . " {$_USER->getClient()->getCurrency()}", 0, 1, 'R');
    $sum += $t;
}

// Gesammtpreis (Brutto)
$pdf->SetFont($font, 'b', 10);
$pdf->Cell($tablesize[0], 0, $_LANG->get('Gesamtsumme (Brutto)'), 0, 0, 'R');
$pdf->Cell($tablesize[1], 0, printPrice($sum) . " {$_USER->getClient()->getCurrency()}", 0, 1, 'R');
$pdf->SetFont($font, '', 10);
$pdf->Ln();

// --------------------------------- Fußblock -------------------------------------------
$pdf->MultiCell(0, 0, $_LANG->get("Mit freundlichen Gr&uuml;&szlig;en"), 0, 'L');
$pdf->MultiCell(0, 0, "{$_USER->getFirstname()} {$_USER->getLastname()}", 0, 'L');
$pdf->Ln();
$pdf->SetFont($font, 'b', 10);
$pdf->MultiCell(0, 0, $_USER->getClient()
    ->getName(), 0, 'L');
$pdf->SetFont($font, '', 10);
$pdf->Ln();

// Werte setzen
$this->setPriceNetto($gesnetto);
$this->setPriceBrutto($sum);
$this->setPayable(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + ($tmp_nettodays * 86400));
?>