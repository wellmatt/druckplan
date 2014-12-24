<? 
// --------------------------------------------------------------------------------
   // Author: iPactor GmbH
   // Updated: 16.12.2013
   // Copyright: 2012-13 by iPactor GmbH. All Rights Reserved.
   // Any unauthorized redistribution, reselling, modifying or reproduction of part
   // or all of the contents in any form is strictly prohibited.
   // ----------------------------------------------------------------------------------
$pdf->SetFont($font, 'b', 13);
$pdf->Ln();
$pdf->Cell(100, 0, "{$_LANG->get("Angebotsbest&auml;tigung")} {$this->name}", 0, 0);
$pdf->Cell(100, 0, "Kunden-Nr. {$order->getCustomer()->getCustomernumber()}", 0, 1);
$pdf->Ln();

$pdf->SetFont($font, '', 10);
if ($order->getCustContactperson()->getId() > 0) {
    if ($order->getCustContactperson()->getTitle() == "Herr") {
        $anrede = $_LANG->get("Sehr geehrter");
    } else {
        $anrede = $_LANG->get("Sehr geehrte");
    }
    $pdf->Cell(100, 0, $anrede . " " . $order->getCustContactperson()
        ->getNameAsLine3() . ", ", 0, 1);
} else {
    $pdf->Cell(0, 0, $_LANG->get("Sehr geehrte Damen und Herren") . ", ", 0, 1);
}

$pdf->Cell(0, 0, $_LANG->get("vielen Dank f&uuml;r Ihren Auftrag.") . " " . $_LANG->get("Wir best&auml;tigen Ihnen wie folgt:"), 0, 1);
$pdf->Ln();
$pdf->Ln();

$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);

$sum_price = 0;
$y = 1;
$tsize = 45;
$b = 1;

foreach ($calcs as $calc) {
    $pdf->SetFont($font, 'b', 10);
    $pdf->Cell($tsize, 0, "Titel: ", 0, 0);
    $pdf->Cell($tsize, 0, "{$order->getTitle()}", 0, 1);
    $pdf->SetFont($font, '', 10);
    $pdf->Ln($b);
    
    if ($calc->getTitle() != "") {
        $pdf->Cell($tsize, 0, "Kalkulationstitel: ", 0, 0);
        $pdf->Cell($tsize, 0, "{$calc->getTitle()}", 0, 1);
        $pdf->Ln($b);
    }
    
    // Produktnamen holen oder ggf. ueberschreiben
    $tmp_productname = $order->getProduct()->getName();
    if ($order->getProductName() != "" && $order->getProductName() != NULL) {
        $tmp_productname = $order->getProductName();
    }
    
    $pdf->Cell($tsize, 0, "Produkt: ", 0, 0);
    $pdf->Cell($tsize, 0, $tmp_productname, 0, 1);
    $pdf->Ln($b);
    
    // Nachschauen, ob die Produktdetails ausgegeben werden sollen
    if ($order->getShowProduct()) {
        
        // Auflage
        $pdf->Cell($tsize, 0, "Auflage: ", 0, 0);
        $pdf->Cell($tsize, 0, printBigInt($calc->getAmount()) . " Stk.", 0, 1);
        $pdf->Ln($b);
        
        // Format
        $pdf->Cell($tsize, 0, $_LANG->get('Format geschlossen '), 0, 0);
        $pdf->Cell($tsize, 0, $calc->getProductFormatWidth() . " x " . $calc->getProductFormatHeight() . " mm", 0, 1);
        $pdf->Ln($b);
        
        $pdf->Cell($tsize, 0, $_LANG->get('Format offen '), 0, 0);
        $pdf->Cell($tsize, 0, $calc->getProductFormatWidthOpen() . " x " . $calc->getProductFormatHeightOpen() . " mm", 0, 1);
        $pdf->Ln($b);
        
        // Inhalt
        $pdf->Cell($tsize, 0, "Inhalt: ", 0, 0);
        $pdf->Cell($tsize, 0, $calc->getPaperContent()
            ->getName() . ", " . $calc->getPaperContentWeight() . " g ", 0, 1);
        $pdf->Cell($tsize, 0, "", 0, 0);
        $pdf->Cell($tsize, 0, $calc->getPagesContent() . " Seiten, " . $calc->getChromaticitiesContent()
            ->getName(), 0, 1);
        $pdf->Ln($b);
        
        if ($calc->getPaperEnvelope()->getId()) {
            $pdf->Cell($tsize, 0, "Umschlag: ", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPaperEnvelope()
                ->getName() . ", " . $calc->getPaperEnvelopeWeight() . " g ", 0, 1);
            $pdf->Cell($tsize, 0, "", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPagesEnvelope() . " Seiten, " . $calc->getChromaticitiesEnvelope()
                ->getName(), 0, 1);
            $pdf->Ln($b);
        }
        if ($calc->getPaperAddContent()->getId()) {
            $pdf->Cell($tsize, 0, "Zus. Inhalt: ", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPaperAddContent()
                ->getName() . ", " . $calc->getPaperAddContentWeight() . " g ", 0, 1);
            $pdf->Cell($tsize, 0, "", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPagesAddContent() . " Seiten, " . $calc->getChromaticitiesAddContent()
                ->getName(), 0, 1);
            $pdf->Ln($b);
        }
        if ($calc->getPaperAddContent2()->getId()) {
            $pdf->Cell($tsize, 0, "Zus. Inhalt 2: ", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPaperAddContent2()
                ->getName() . ", " . $calc->getPaperAddContent2Weight() . " g ", 0, 1);
            $pdf->Cell($tsize, 0, "", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPagesAddContent2() . " Seiten, " . $calc->getChromaticitiesAddContent2()
                ->getName(), 0, 1);
            $pdf->Ln($b);
        }
        if ($calc->getPaperAddContent3()->getId()) {
            $pdf->Cell($tsize, 0, "Zus. Inhalt 3: ", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPaperAddContent3()
                ->getName() . ", " . $calc->getPaperAddContent3Weight() . " g ", 0, 1);
            $pdf->Cell($tsize, 0, "", 0, 0);
            $pdf->Cell($tsize, 0, $calc->getPagesAddContent3() . " Seiten, " . $calc->getChromaticitiesAddContent3()
                ->getName(), 0, 1);
            $pdf->Ln($b);
        }
    }
    
    // Verarbeitung
    if ($calc->getTextProcessing() != "" && $calc->getTextProcessing() != NULL) {
        $pdf->Cell($tsize, 0, "Verarbeitung: ", 0, 0);
        $pdf->Cell($tsize, 0, $calc->getTextProcessing(), 0, 1);
        $pdf->Ln($b);
    }
    
    // ---Calculationanzeige--- //
    if (count($calc->getPositionsForDocuments()) > 0 && $calc->getPositionsForDocuments() != false) {
        foreach ($calc->getPositionsForDocuments() as $pos) {
            if ($pos->getShowQuantity() == 1) {
                $pdf->Cell(0, 0, $pos->getQuantity() . " Stk.", 0, 1, 'R');
            }
            $pdf->Cell(0, 0, $pos->getComment(), 0, 1, 'R');
            if ($pos->getShowPrice() == 1) {
                $pdf->Cell(0, 0, printPrice($pos->getCalculatedPrice()) . " " . $_USER->getClient()
                    ->getCurrency() . "", 0, 1, 'R');
            }
        }
        $pdf->Ln();
        $pdf->Ln();
    }
    
    $price_per_thousand = $calc->getSummaryPrice() / $calc->getAmount() * 1000;
    
    // Textanhängung für " " oder " pro 100Stk"
    if ($order->getShowPricePer1000() == 1) {
        $ptstk = "(" . printPrice($price_per_thousand) . " " . $_USER->getClient()->getCurrency() . " pro 1000 Stk.)";
    } else {
        $ptstk = " ";
    }
    
    $pdf->Ln();
    $pdf->SetFont($font, 'b', 10);
    $pdf->Cell($tsize, 0, "Preis netto:", 0, 0);
    $pdf->SetFont($font, '', 10);
    $pdf->Cell($tsize, 0, $ptstk, 0, 0);
    $pdf->Cell(0, 0, printPrice($calc->getSummaryPrice()) . " " . $_USER->getClient()
        ->getCurrency(), 0, 1, 'R');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
}
// Kommentar zum Dokument ausgeben
$pdf->Ln();
$pdf->Cell($tsize, 0, $order->getTextOffer(), 0, 1);
$pdf->Ln();

// Footer && Gruese
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$gruesse = $_LANG->get("Mit freundlichen Gr&uuml;&szlig;en");

$pdf->MultiCell(0, 0, $gruesse, 0, 'L');
$pdf->Ln();
$pdf->MultiCell(0, 0, $order->getInternContact()
    ->getNameAsLine(), 0, 'L');
$pdf->Ln();
?>