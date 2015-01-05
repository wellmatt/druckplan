<?
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/paper/paper.class.php';

$linestr = "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - " . "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -";
$calcs = Calculation::getAllCalculations($order);
$first = true;
$tsize = 45;
$b = 1;
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(15, 50, 10, true);
$pdf->AddPage();
$pdf->SetFont($font, '', 10);

// Tabellenkopfgrˆﬂe
$tablesize = Array(
    60,
    25,
    60,
    20
);


foreach ($calcs as $calc) {
    if ($calc->getState()) {
        
        $pdf->Ln();
        $tmp_margin = $pdf->getMargins();
        $tmp_margin = $tmp_margin['left'] - 5;
        $pdf->SetMargins($tmp_margin);
        $pdf->Ln(0);
        $tmp_margin = $pdf->getMargins();
        $tmp_margin = $tmp_margin['left'] + 5;
        $pdf->SetMargins($tmp_margin);
        
        if($first)
        {
            $pdf->Cell($tsize, 0, "Kundendaten:", 0, 0);
            $pdf->SetFont($font, 'b', 16);
            $pdf->MultiCell(0, 0, "Angebot {$order->getNumber()}", 0, 'R', 0, 1);
            $pdf->SetFont($font, '', 10);
        }else{
            $pdf->Cell(0, 0, "Kundendaten:", 0, 1); 
        }
        $pdf->Ln($b);
        
        $pdf->Cell($tsize, 0, "Kunde", 0, 0);
        $pdf->Cell(0, 0, $order->getCustomer()
            ->getNameAsLine(), 0, 1);
        $pdf->Ln($b);
        
        $pdf->Cell($tsize, 0, "Adresse", 0, 0);
        $pdf->Cell(0, 0, $order->getCustomer()
            ->getAddress1(), 0, 1);
        $zip = strtoupper($order->getCustomer()
            ->getCountry()
            ->getCode()) . "-" . $order->getCustomer()->getZip() . " " . $order->getCustomer()->getCity();
        $pdf->Cell($tsize, 0, "", 0, 0);
        $pdf->Cell(0, 0, $zip, 0, 1);
        $pdf->Ln($b);
        
        if (trim($order->getCustomer()->getEmail()) != "") {
            $pdf->Cell($tsize, 0, "E-Mail", 0, 0);
            $pdf->Cell(0, 0, $order->getCustomer()
                ->getEmail(), 0, 1);
            $pdf->Ln($b);
        }
        
        if (trim($order->getCustomer()->getPhone()) != "") {
            $pdf->Cell($tsize, 0, "Telefon", 0, 0);
            $pdf->Cell(0, 0, $order->getCustomer()
                ->getPhone(), 0, 1);
            $pdf->Ln($b);
        }
        
        if (trim($order->getCustomer()->getFax()) != "") {
            $pdf->Cell($tsize, 0, "Fax", 0, 0);
            $pdf->Cell(0, 0, $order->getCustomer()
                ->getFax(), 0, 1);
            $pdf->Ln($b);
        }
        
        if (trim($order->getCustomer()->getWeb()) != "") {
            $pdf->Cell($tsize, 0, "Webseite", 0, 0);
            $pdf->Cell(0, 0, $order->getCustomer()
                ->getWeb(), 0, 1);
            $pdf->Ln($b);
        }
        
        if (trim($order->getNotes()) != "") {
            $pdf->Cell($tsize, 0, "Bemerkungen", 0, 0);
            $pdf->MultiCell(0, 0, $order->getCustomer(), 0, 'L');
            $pdf->Ln($b);
        }
        $pdf->Ln();
        
        // --------------------------------------------------------------------------
        $pdf->SetMargins($pdf->getMargins()['left'] - 5);
        $pdf->Ln(0);
        $pdf->SetMargins($pdf->getMargins()['left'] + 5);
        $pdf->Cell(0, 0, $linestr, 0, 1);
        $pdf->Ln($b);
        
        $pdf->Cell($tsize, 0, "Auftragstitel", 0, 0);
        $pdf->Cell(0, 0, $order->getTitle(), 0, 1);
        $pdf->Ln($b);
        
        $pdf->Cell($tsize, 0, "Produkt", 0, 0);
        $pdf->Cell(0, 0, $order->getProduct()
            ->getName(), 0, 1);
        $pdf->Ln($b);
        
        $pdf->Cell($tsize, 0, "Material", 0, 0);
        $paperstr = $_LANG->get('Inhalt') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_CONTENT) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
        $paperstr .= $calc->getPaperContent()->getName() . " " . $calc->getPaperContentWeight();
      
        if ($calc->getPaperAddContent()->getId()) {
            $paperstr = "\n" . $_LANG->get('zus. Inhalt') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperAddContent()->getName() . " " . $calc->getPaperAddContentWeight();
        }
        if ($calc->getPaperAddContent2()->getId()) {
            $paperstr .= "\n" . $_LANG->get('zus. Inahlt 2') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperAddContent2()->getName() . " " . $calc->getPaperAddContent2Weight();
        }
        if ($calc->getPaperAddContent3()->getId()) {
            $paperstr .= "\n" . $_LANG->get('zus. Inahlt 3') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperAddContent3()->getName() . " " . $calc->getPaperAddContent3Weight();
        }
        if ($calc->getPaperEnvelope()->getId()) {
            $paperstr = "\n" . $_LANG->get('Umschlag') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ENVELOPE) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperEnvelope()->getName() . " " . $calc->getPaperEnvelopeWeight();
        }      
        $pdf->writeHTMLCell(0, 0, '', '', $paperstr, 0, 1);
        $pdf->Ln($b);
        
        $pdf->Cell($tsize, 0, "Farben", 0, 0);
        $chrstr = "Inhalt: " . $calc->getChromaticitiesContent()->getName();
        $fixhtml = strlen($chrstr);
        if ($calc->getChromaticitiesAddContent()->getId())
            $chrstr .= "\nzus. Inhalt: " . $calc->getChromaticitiesAddContent()->getName();
        if ($calc->getChromaticitiesAddContent2()->getId())
            $chrstr .= "\nzus. Inhalt 2: " . $calc->getChromaticitiesAddContent2()->getName();
        if ($calc->getChromaticitiesAddContent3()->getId())
            $chrstr .= "\nzus. Inhalt 3: " . $calc->getChromaticitiesAddContent3()->getName();
        if ($calc->getChromaticitiesEnvelope()->getId())
            $chrstr .= "\nUmschlag: " . $calc->getChromaticitiesEnvelope()->getName();
        $pdf->writeHTMLCell(0, 0, '', '', $chrstr, 0, 1);
        $pdf->Ln($b);        
        
        // Lieferdatum ausgeben, wenn gesetzt
        $tmp_date = "";
        if ($order->getDeliveryDate() > 0) {
            $tmp_date = date('d.m.Y', $order->getDeliveryDate());
        }
        
        $pdf->Cell($tsize, 0, $_LANG->get('Liefertermin'), 0, 0);
        $pdf->Cell(0, 0, $tmp_date, 0, 1);
        // --------------------------------------------------------------------------
        
        $pdf->SetMargins($pdf->getMargins()['left'] - 5);
        $pdf->Ln(0);
        $pdf->SetMargins($pdf->getMargins()['left'] + 5);
        $pdf->Cell(0, 0, $linestr, 0, 1);
        $pdf->Ln($b);
        
        $pdf->Ln(12);
        
        // -------------------------------------------------------------------------
        // Tabellenkopf
        
        // //Tabellengrˆﬂe einsehen:
        // $pdf->Cell($tablesize[0], 0, $_LANG->get('Position'), 1, 0);
        // $pdf->Cell($tablesize[1], 0, $_LANG->get('Datum'), 1, 0);
        // $pdf->Cell($tablesize[2], 0, $_LANG->get('Bemerkung'), 1, 0);
        // $pdf->Cell($tablesize[3], 0, $_LANG->get('Zeit.'), 1, 1);
        
        $machgroups = MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION);
        foreach ($machgroups as $mg) {
            $machentries = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_GROUP, $mg->getId());
            
            if (!empty($machentries)) {
                $pdf->SetMargins($pdf->getMargins()['left'] - 5);
                $pdf->Ln(0);
                $pdf->SetMargins($pdf->getMargins()['left'] + 5);
                $pdf->writeHTMLCell(0, 0, '', '', "<b>{$mg->getName()}</b>", 0, 1);
                $pdf->Ln($b);
            }
            
            
            foreach ($machentries as $me) {
                $tmp_addtext_fl = "";
                $temp["Position"] = $me->getMachine()->getName();
                
                if ($me->getPart() == Calculation::PAPER_CONTENT) {
                    $temp["Position"] .= " (Inhalt)";
                } else 
                    if ($me->getPart() == Calculation::PAPER_ADDCONTENT) {
                        $temp["Position"] .= " (zus. Inhalt)";
                    } else 
                        if ($me->getPart() == Calculation::PAPER_ADDCONTENT2) {
                            $temp["Position"] .= " (zus. Inhalt 2)";
                        } else 
                            if ($me->getPart() == Calculation::PAPER_ADDCONTENT3) {
                                $temp["Position"] .= " (zus. Inhalt 3)";
                            } else 
                                if ($me->getPart() == Calculation::PAPER_ENVELOPE) {
                                    $temp["Position"] .= " (Umschlag)";
                                }
                
                if ($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET && $me->getMachine()->getUmschlUmst() > 0 && $me->getUmschlagenUmstuelpen() > 0) {
                    $paper = $calc->getPaperContent();
                    $direction = $paper->getPaperDirection($calc, $me->getPart());
                    if ($direction == Paper::PAPER_DIRECTION_SMALL)
                        $temp["Position"] .= " \n<b>umst√ºlpen</b>";
                    else
                        $temp["Position"] .= " \n<b>umschlagen</b>";
                }
                
                if ($jobrow["pos_plandate"] > 0) {
                    $temp["Datum"] = date('d.m.Y', $jobrow["pos_plandate"]);
                } else {
                    $temp["Datum"] = " ";
                }
                
                if ($mg->getType() == MachineGroup::TYPE_EXTERN) {
                    if ($me->getSupplierSendDate() > 0) {
                        $tmp_addtext_fl .= $_LANG->get('Versand') . ": " . date('d.m.Y', $me->getSupplierSendDate()) . ", ";
                    }
                    if ($me->getSupplierReceiveDate() > 0) {
                        $tmp_addtext_fl .= $_LANG->get('Zur&uuml;ck') . ": " . date('d.m.Y', $me->getSupplierReceiveDate()) . ", ";
                    }
                    $tmp_addtext_fl .= $me->getSupplierInfo();
                }
                
                if (trim($jobrow["pos_notes"]) != "") {
                    $temp["Bemerkung"] = $jobrow["pos_notes"] . "\n" . $tmp_addtext_fl;
                } else {
                    $temp["Bemerkung"] = $tmp_addtext_fl;
                }
                
                $temp["Zeit"] = $me->getTime() . " Min.";
                
                // Fix wegen der html Cell um ‹berlaufe ¸ber andere Texte zu verhindern.
                $y_start = $pdf->GetY();
                $pdf->writeHTMLCell($tablesize[0], 0, '', '', $temp["Position"], 0, 2);
                $y_end = $pdf->GetY();
                
                $pdf->writeHTMLCell($tablesize[1], 0, $pdf->GetX(), $y_start, $temp["Datum"], 0, 2);
                if($y_end < $pdf->GetY())
                    $y_end = $pdf->GetY();
                
                $pdf->writeHTMLCell($tablesize[2], 0, $pdf->GetX(), $y_start, $temp["Bemerkung"], 0, 2);
                if($y_end < $pdf->GetY())
                    $y_end = $pdf->GetY();
                $pdf->writeHTMLCell($tablesize[3], 0, $pdf->GetX(), $y_start, $temp["Zeit"], 0, $ln = 1, $fill = false, $reseth = true, $align = 'R', $autopadding = true);
                $pdf->Ln($b);
                
//                 if ($me->getColor_detail() != "" && $me->getColor_detail() != FALSE){
//                     $y_start = $pdf->GetY();
//                     $pdf->writeHTMLCell($tablesize[0], 0, '', '', "Farbton: " . $me->getColor_detail(), 0, 2);
//                     $y_end = $pdf->GetY();
//                 }
                
                // Fix wegen der html Cell um ‹berlaufe ¸ber andere Texte zu verhindern.
                $pdf->SetY($y_end);
            }              
        }
        
        $pdf->SetMargins($pdf->getMargins()['left'] - 5);
        $pdf->Ln(0);
        $pdf->SetMargins($pdf->getMargins()['left'] + 5);
        $pdf->Cell(0, 0, $linestr, 0, 1);
        $pdf->Ln($b);
        
        $first = false;
    }
}

?>