<?
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/paper/paper.class.php';
require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

// Generierung der Ausgabe
$calcs = Calculation::getAllCalculations($order);
$container = array();

foreach ($calcs as $calc) {
    if ($calc->getState()) {
        
        $zip = $order->getCustomer()->getAddress1()."<br>";
        $zip .= strtoupper($order->getCustomer()->getCountry()->getCode()) . "-" . $order->getCustomer()->getZip() . " " . $order->getCustomer()->getCity();
            
        $paperstr = $_LANG->get('Inhalt') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_CONTENT) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
        $paperstr .= $calc->getPaperContent()->getName() . " " . $calc->getPaperContentWeight();
        echo $paperstr."<br><br>";
        
        if ($calc->getPaperAddContent()->getId()) {
            $paperstr .= "<br>" . $_LANG->get('zus. Inhalt') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperAddContent()->getName() . " " . $calc->getPaperAddContentWeight();
        }
        if ($calc->getPaperAddContent2()->getId()) {
            $paperstr .= "<br>" . $_LANG->get('zus. Inhalt 2') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperAddContent2()->getName() . " " . $calc->getPaperAddContent2Weight();
        }
        if ($calc->getPaperAddContent3()->getId()) {
            $paperstr .= "<br>" . $_LANG->get('zus. Inhalt 3') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperAddContent3()->getName() . " " . $calc->getPaperAddContent3Weight();
        }
        if ($calc->getPaperEnvelope()->getId()) {
            $paperstr .= "<br>". $_LANG->get('Umschlag') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ENVELOPE) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
            $paperstr .= $calc->getPaperEnvelope()->getName() . " " . $calc->getPaperEnvelopeWeight();
        }      
        echo $paperstr;
                
        $chrstr = "Inhalt: " . $calc->getChromaticitiesContent()->getName();
        $fixhtml = strlen($chrstr);
        if ($calc->getChromaticitiesAddContent()->getId())
            $chrstr .= "<br>zus. Inhalt: " . $calc->getChromaticitiesAddContent()->getName();
        if ($calc->getChromaticitiesAddContent2()->getId())
            $chrstr .= "<br>zus. Inhalt 2: " . $calc->getChromaticitiesAddContent2()->getName();
        if ($calc->getChromaticitiesAddContent3()->getId())
            $chrstr .= "<br>zus. Inhalt 3: " . $calc->getChromaticitiesAddContent3()->getName();
        if ($calc->getChromaticitiesEnvelope()->getId())
            $chrstr .= "<br>Umschlag: " . $calc->getChromaticitiesEnvelope()->getName();   
        
        // Lieferdatum ausgeben, wenn gesetzt
        $tmp_date = "";
        if ($order->getDeliveryDate() > 0) {
            $tmp_date = date('d.m.Y', $order->getDeliveryDate());
        }

        // -------------------------------------------------------------------------

        $dump = array();
        $dump["CustomerName"] = $order->getCustomer()->getNameAsLine();
        $dump["CustomerAddress"] = $zip;
        $dump["CustomerEmail"] = $order->getCustomer()->getEmail();
        $dump["CustomerPhone"] = $order->getCustomer()->getPhone();
        $dump["CustomerFax"] = $order->getCustomer()->getFax();
        $dump["CustomerWebsite"] = $order->getCustomer()->getWeb();
        $dump["CustomerNote"] = $order->getCustomer();
        $dump["OrderTitle"] = $order->getTitle();
        $dump["ProductName"] = $order->getProduct()->getName();
        $dump["Material"] = $paperstr;
        $dump["Color"] = $chrstr;
        $dump["DeliveryDate"] = $tmp_date;
        $dump["Type"] = array();
        
        $machgroups = MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION);
        foreach ($machgroups as $mg) {
            $machentries = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_GROUP, $mg->getId());
            
            if (!empty($machentries)) {
                $machinedump = array();
                $machinedump["Name"] =  $mg->getName();
                $machinedump["Machine"] = array();
            }
            foreach ($machentries as $me) {
                
                $temp = array();
                $tmp_addtext_fl = "";
                $temp["Position"] = $me->getMachine()->getName();
                
                if ($me->getPart() == Calculation::PAPER_CONTENT) {
                    $temp["Position"] .= " (Inhalt)";
                } else if ($me->getPart() == Calculation::PAPER_ADDCONTENT) {
                        $temp["Position"] .= " (zus. Inhalt)";
                } else if ($me->getPart() == Calculation::PAPER_ADDCONTENT2) {
                            $temp["Position"] .= " (zus. Inhalt 2)";
                } else if ($me->getPart() == Calculation::PAPER_ADDCONTENT3) {
                                $temp["Position"] .= " (zus. Inhalt 3)";
                } else if ($me->getPart() == Calculation::PAPER_ENVELOPE) {
                                    $temp["Position"] .= " (Umschlag)";
                }
                
                if ($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET && $me->getMachine()->getUmschlUmst() > 0 && $me->getUmschlagenUmstuelpen() > 0) {
                    $paper = $calc->getPaperContent();
                    $direction = $paper->getPaperDirection($calc, $me->getPart());
                    if ($direction == Paper::PAPER_DIRECTION_SMALL)
                        $temp["Position"] .= "<br><b>umstülpen</b>";
                    else
                        $temp["Position"] .= "<br><b>umschlagen</b>";
                }
                
                if ($jobrow["pos_plandate"] > 0) {
                    $temp["Date"] = date('d.m.Y', $jobrow["pos_plandate"]);
                } else {
                    $temp["Date"] = " ";
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
                    $temp["Comment"] = $jobrow["pos_notes"] . "<br>" . $tmp_addtext_fl;
                } else {
                    $temp["Comment"] = $tmp_addtext_fl;
                }
                $temp["Time"] = $me->getTime();
                $machinedump["Machine"][] = $temp;
            }
            if (!empty($machentries))
                $dump["Type"][] = $machinedump;
        }
        $container[] = $dump;
    }
}

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/factory.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('Calcs', $calcs);
$smarty->assign('Container', $container);

$htmldump = $smarty->fetch('string:' . $datei);

// var_dump($htmltemp);

$pdf->SetMargins($pref->getPdf_margin_left(), $pref->getPdf_margin_top(), $pref->getPdf_margin_right(), TRUE);
$pdf->AddPage();

$pdf->writeHTML($htmldump);

?>