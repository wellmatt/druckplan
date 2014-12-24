<? 
// --------------------------------------------------------------------------------
   // Author: iPactor GmbH
   // Updated: 16.12.2013
   // Copyright: 2012-13 by iPactor GmbH. All Rights Reserved.
   // Any unauthorized redistribution, reselling, modifying or reproduction of part
   // or all of the contents in any form is strictly prohibited.
   // ----------------------------------------------------------------------------------
$font = "helvetica";

$pdf->SetFont($font, 'b', 13);
$pdf->Ln();
$pdf->Cell(100, 0, "Rechnung {$this->name}  ", 0, 0);
$pdf->Cell(100, 0, "Kunden-Nr. {$order->getCustomer()->getCustomernumber()}", 0, 1);
$pdf->Ln();
$pdf->SetFont($font, '', 10);

$headtext = $_LANG->get("Wir danken f&uuml;r Ihr Vertrauen und erlauben uns auf Basis unserer Allgemeinen Gesch&auml;ftsbedingungen");
$headtext .= $_LANG->get(' den Auftrag wie folgt zu berechnen') . ": ";

$pdf->MultiCell(0, 0, $headtext, 0, 'L');

// Angebotsinhalt
$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
$sum_price = 0;
$y = 1;
$tsize = 45;
$b = 1;

foreach ($calcs as $calc) {
    $pdf->Ln();
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
    
    // damit der Wert fuer tmp_amount initialisiert ist (falls DEtails nicht angezeigt werden sollen)
    $tmp_amount = $calc->getAmount();
    
    // Nachschauen, ob die Produktdetails ausgegeben werden sollen
    if ($order->getShowProduct()) {
        
        // Auflage
        $pdf->Cell($tsize, 0, "Auflage: ", 0, 0);
        if ((int) $_REQUEST["invoice_amount"] == 0) {
            $pdf->Cell($tsize, 0, printBigInt($calc->getAmount()) . " Stk.", 0, 1);
            $tmp_amount = $calc->getAmount();
        } else {
            $pdf->Cell($tsize, 0, printBigInt((int) $_REQUEST["invoice_amount"]) . " Stk.", 0, 1);
            $tmp_amount = (int) $_REQUEST["invoice_amount"];
        }
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
    
    if ((int) $_REQUEST["invoice_update_price"] == 1) {
        // Wenn Haekchen gesetzt, dann Preis auf neue Menge umrechnen
        $tmp_price = $calc->getSummaryPrice() * ((int) $_REQUEST["invoice_amount"] / $calc->getAmount());
    } else {
        $tmp_price = $calc->getSummaryPrice();
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
    $pdf->Cell(0, 0, printPrice($tmp_price) . " " . $_USER->getClient()
        ->getCurrency(), 0, 1, 'R');
    $pdf->Ln($b);
    $sum_price += $tmp_price;
}

$taxes = $order->getProduct()->getTaxes() * $sum_price / 100;
$doc_sum = $sum_price + $taxes;

unset($data);
$data = Array();
$x = 0;

if ($order->getDeliveryTerms()->getId() > 0) {
    
    if ($order->getDeliveryCost() > 0) {
        $pdf->Cell($tsize, 0, $_LANG->get("Lieferkosten"), 0, 0);
        $pdf->Cell(0, 0, printPrice($order->getDeliveryCost()) . " " . $_USER->getClient()
            ->getCurrency(), 0, 1, 'R');
        $pdf->Ln($b);
    }
    
    if ($order->getDeliveryTerms()->getTax() == $order->getProduct()->getTaxes()) { // Produkt und Lieferbed. haben selben MWST-Satz
        $taxes += $order->getDeliveryTerms()->getTax() * $order->getDeliveryCost() / 100;
    } else {
        $taxes2 = $order->getDeliveryTerms()->getTax() * $order->getDeliveryCost() / 100;
        if ($taxes2 > 0) {
            $pdf->Cell($tsize, 0, "MwST. " . $order->getDeliveryTerms()
                ->getTax() . " % ", 0, 0);
            $pdf->Cell(0, 0, printPrice($taxes2) . " " . $_USER->getClient()
                ->getCurrency(), 0, 1, 'R');
            $pdf->Ln($b);
        }
    }
}
$pdf->Cell($tsize, 0, "MwST. " . $order->getProduct()
    ->getTaxes() . " % ", 0, 0);
$pdf->Cell(0, 0, printPrice($taxes) . " " . $_USER->getClient()
    ->getCurrency(), 0, 1, 'R');
$pdf->Ln($b);
$pdf->SetFont($font, 'b', 10);
$pdf->Cell($tsize, 0, $_LANG->get('Gesamtsumme Brutto'), 0, 0);
$pdf->Cell(0, 0, printPrice($doc_sum) . " " . $_USER->getClient()
    ->getCurrency(), 0, 1, 'R');
$pdf->Ln($b);
$pdf->SetFont($font, '', 10);
$pdf->Ln();
$pdf->Ln(8);
$pdf->MultiCell(0, 0, $order->getTextInvoice(), 0, 'L');
$pdf->Ln(8);
?>