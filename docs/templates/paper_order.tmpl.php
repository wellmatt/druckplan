<? 
// -------------------------------------------------------------------------------
   // Author: iPactor GmbH
   // Updated: 05.10.2013
   // Copyright: 2013 by iPactor GmbH. All Rights Reserved.
   // Any unauthorized redistribution, reselling, modifying or reproduction of part
   // or all of the contents in any form is strictly prohibited.
   // ----------------------------------------------------------------------------------
$calc = new Calculation($order->getPaperOrderCalc());
$tmp_supplier = new BusinessContact($order->getPaperOrderSupplier());
$tmp_paper = new Paper($this->getPaperOrderPid());

if ($withheader) {
    if ($version == self::VERSION_EMAIL) {
        $pdf->Image("docs/templates/logo.jpg", 0, 5, 500, 0, '', 'none', 'center');
    }
    $pdf->SetMargins(30, 30, 30, TRUE);
    
    // Anschift
    $streets = $_USER->getClient()->getStreets();
    $hline = "{$_USER->getClient()->getName()}, ";
    $hline .= $streets[0] . ", ";
    $hline .= "{$_USER->getClient()->getPostcode()} {$_USER->getClient()->getCity()}";
    $pdf->Ln(8);
    $pdf->Ln(8);
    $pdf->SetFont($font, 'U', 8);
    $pdf->Cell(0, 0, $hline, 0, 1);
    $pdf->SetFont($font, '', 8);
    $pdf->Ln(8);
    
    $pdf->SetFont($font, '', 11);
    $pdf->Cell(0, 0, $order->getCustomer()
        ->getNameAsLine(), 0, 1);
    $pdf->MultiCell(0, 0, $order->getCustomer()->getAddressAsLine(), 0,'L');
    
    $pdf->Ln(14);
    $pdf->Ln(14);
    
    $columsize = 47;
    $pdf->Cell($columsize, 0, $_LANG->get('Ihre Nachricht'), 0, 0);
    $pdf->Cell($columsize, 0, $_LANG->get('Ihr Zeichen'), 0, 0);
    $pdf->Cell($columsize, 0, $_LANG->get('Unser Zeichen'), 0, 0);
    $pdf->Cell($columsize, 0, $_LANG->get('Datum'), 0, 1);
    
    $pdf->Cell($columsize, 0, $order->getCustMessage(), 0, 0);
    $pdf->Cell($columsize, 0, $order->getCustSign(), 0, 0);
    $pdf->Cell($columsize, 0, substr($order->getInternContact()
        ->getFirstname(), 0, 2) . substr($order->getInternContact()
        ->getLastname(), 0, 2), 0, 0);
    $pdf->Cell($columsize, 0, date('d.m.Y'), 0, 1);
}

$pdf->SetFont($font, 'b', 14);
$pdf->Ln();
$pdf->Cell(100, 0, "Papier Bestellung Nr. {$this->name} ", 0, 0);
$pdf->Cell(0, 0, "Kunden-Nr.  {$tmp_supplier->getNumberatcustomer()}", 0, 1);
$pdf->Ln();
$pdf->SetFont($font, '', 10);
$pdf->Cell(0, 0, "Sehr geehrte Damen und Herren,", 0, 1);
$pdf->Ln();
$pdf->Cell(0, 0, "hiermit erhalten Sie die Bestellung über '{$tmp_paper->getName()}'", 0, 1);
$pdf->Ln();

$pdf->SetFont($font, 'b', 10);
$pdf->Cell($tsize, 0, $_LANG->get('Produkt'), 0, 0);
$pdf->Cell(0, 0, $tmp_paper->getName(), 0, 1);

$pdf->Cell($tsize, 0, $_LANG->get('Menge'), 0, 0);
$pdf->Cell(0, 0, $order->getPaperOrderBoegen(), 0, 1);

$pdf->Cell($tsize, 0, $_LANG->get('Preis'), 0, 0);
$pdf->Cell(0, 0, $order->getPaperOrderPrice() . $_USER->getClient()->getCurrency(), 0, 1);

$pdf->SetFont($font, '', 10);
$pdf->Ln(14);
$pdf->Ln(14);
$pdf->Cell($tsize, 0,"Für Rückfragen stehen wir Ihnen gerne zur Verügung.", 0, 1);
$pdf->Ln(10);
$pdf->Ln(10);
$pdf->Cell($tsize, 0,"Mit freundlichen Grüßen", 0, 1);
$pdf->Cell($tsize, 0,"{$_USER->getFirstname()} {$_USER->getLastname()}", 0, 1);
?>