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

// $order = new CollectiveInvoice();
$articles = Orderposition::getAllOrderposition($order->getId());
$drs = Array();

foreach ($articles as $opos)
{
    $opos_article = new Article($opos->getObjectid());
    if ($opos_article->getOrderid()>0)
    {
        $opos_order = new Order($opos_article->getOrderid());
        $calcs = Calculation::getAllCalculations($opos_order);
        $container = array();
        
        foreach ($calcs as $calc) {
            if ($calc->getState() && $calc->getAmount()==$opos->getQuantity())
            {
                $paperstr = $_LANG->get('Inhalt') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_CONTENT) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
                $paperstr .= $calc->getPaperContent()->getName() . " " . $calc->getPaperContentWeight() . "g";
        
                if ($calc->getPaperAddContent()->getId()) {
                    $paperstr .= "<br>" . $_LANG->get('zus. Inhalt') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
                    $paperstr .= $calc->getPaperAddContent()->getName() . " " . $calc->getPaperAddContentWeight() . "g";
                }
                if ($calc->getPaperAddContent2()->getId()) {
                    $paperstr .= "<br>";
                    $paperstr .= "<br>" . $_LANG->get('zus. Inhalt 2') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
                    $paperstr .= $calc->getPaperAddContent2()->getName() . " " . $calc->getPaperAddContent2Weight() . "g";
                }
                if ($calc->getPaperAddContent3()->getId()) {
                    $paperstr .= "<br>";
                    $paperstr .= "<br>" . $_LANG->get('zus. Inhalt 3') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
                    $paperstr .= $calc->getPaperAddContent3()->getName() . " " . $calc->getPaperAddContent3Weight() . "g";
                }
                if ($calc->getPaperEnvelope()->getId()) {
                    $paperstr .= "<br>";
                    $paperstr .= "<br>". $_LANG->get('Umschlag') . ": <b>" . $calc->getPaperCount(Calculation::PAPER_ENVELOPE) . "</b> " . $_LANG->get("B&ouml;gen / St&uuml;ck") . " ";
                    $paperstr .= $calc->getPaperEnvelope()->getName() . " " . $calc->getPaperEnvelopeWeight() . "g";
                }
        
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
                $dump["OrderTitle"] = $order->getTitle();
                $dump["ProductName"] = $opos_order->getProduct()->getName();
                $dump["Material"] = $paperstr;
                $dump["Color"] = $chrstr;
                $dump["DeliveryDate"] = $tmp_date;
                $dump["Type"] = array();
                $dump["Calc"] = $calc;
        
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
                        $temp["Type"] = $me->getMachine()->getType();
                        $tmp_addtext_fl = "";
                        $temp["Position"] = $me->getMachine()->getName();
                        $temp["ME"] = $me;
                        $temp["Calc"] = $calc;
        
                        $temp["Plates"] = "";
                        if($me->getMachine()->getType() == Machine::TYPE_CTP) {
                            $machentries2 = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID);
                            foreach($machentries2 as $me2) {
                                if($me2->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                                    switch($me2->getPart())
                                    {
                                        case Calculation::PAPER_CONTENT:
                                            $temp["Plates"] .= $_LANG->get('Anzahl Druckplatten Inhalt').": ".$calc->getPlateCount($me2)."<br>";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT:
                                            $temp["Plates"] .= $_LANG->get('Anzahl Druckplatten zus. Inhalt').": ".$calc->getPlateCount($me2)."<br>";
                                            break;
                                        case Calculation::PAPER_ENVELOPE:
                                            $temp["Plates"] .= $_LANG->get('Anzahl Druckplatten Umschlag').": ".$calc->getPlateCount($me2)."<br>";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT2:
                                            $temp["Plates"] .= $_LANG->get('Anzahl Druckplatten zus. Inhalt 2').": ".$calc->getPlateCount($me2)."<br>";
                                            break;
                                        case Calculation::PAPER_ADDCONTENT3:
                                            $temp["Plates"] .= $_LANG->get('Anzahl Druckplatten zus. Inhalt 3').": ".$calc->getPlateCount($me2)."<br>";
                                            break;
                                    }
                                }
                            }
                            $temp["Plates"] .= $_LANG->get('Anzahl Druckplatten gesamt').": ".$calc->getPlateCount();
                            $temp["Plates"] .= "<br>";
                        }
        
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
        
                        if ($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) { //  && $me->getMachine()->getUmschlUmst() > 0 && $me->getUmschlagenUmstuelpen() > 0
                            $temp["Position"] .= '<br>Druckart: ';
                            if ((int)$me->getUmschlagenUmstuelpen() == 1)
                                $temp["Position"] .= '<b>Umschlagen / Umst&uuml;lpen</b>';
                            else
                                $temp["Position"] .= '<b>Sch&ouml;n & Widerdruck</b>';
                            $temp["Position"] .= '<br>';
                        }
        
                        if ($me->getMachine()->getType() == Machine::TYPE_CUTTER && $me->getFormat_in_height() > 0 && $me->getFormat_in_width() > 0)
                            $temp["Position"] .= "<br>Eingangsbogen: ".$me->getFormat_in_height()."x".$me->getFormat_in_width();
        
                        if ($me->getMachine()->getType() == Machine::TYPE_CUTTER && $me->getFormat_out_height() > 0 && $me->getFormat_out_width() > 0)
                            $temp["Position"] .= "<br>Ausgangsbogen: ".$me->getFormat_out_height()."x".$me->getFormat_out_width();
        
        
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
                $drs[] = $container;
            }
        }
    }
}


$orderpos = $order->getPositions(false,true);


require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/factory.tmpl';
$datei = ckeditor_to_smarty($tmp);

if(trim($datei) == "")
{
    $agent = new TmplAgent();
    $datei = tmpl_to_smarty($agent->Get("default_Kalk_DR"));
}

$smarty->assign('OrderPos',$orderpos);
$smarty->assign('DelivDate', date('d.m.Y', $order->getDeliveryDate()));
$smarty->assign('Order', $order);
$smarty->assign('Drs', $drs);
$smarty->assign('Articles', $articles);

$htmldump = $smarty->fetch('string:' . $datei);

// var_dump($htmltemp);

//$pdf->SetMargins($pref->getPdf_margin_left(), $pref->getPdf_margin_top(), $pref->getPdf_margin_right(), TRUE);
// $pdf->AddPage();

$pdf->writeHTML($htmldump);

?>