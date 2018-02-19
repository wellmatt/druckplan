<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


Class CalculationService {


    public static function calcUsagePerSheet(Order $order, Calculation $calc, Machineentry $mach, $debug = false)
    {
        global $_CONFIG;
        if ($debug) prettyPrint("++## calcUsagePerSheet started... ##++");
        // other
        $part = $mach->getPart();
        $bleed = $calc->getCutFor($part);
        $pages = $calc->getPagesFor($part);
        $farbrand = $_CONFIG->farbRandBreite;
        if($calc->getColorControl() == 0){
            // Wenn der Farbrand in der Kalkulation ausgestellt ist
            $farbrand = 0;
        }
        $chroma = $calc->getChromaFor($part);
        if($chroma->getColorsBack()>0) $duplex = 1; else $duplex = 0; // check weather or not the backside is printed
        // product format
        $pwidth = $calc->getProductFormatWidthOpen();
        $pheight = $calc->getProductFormatHeightOpen();
        if ($part == Calculation::PAPER_ENVELOPE){
            $pwidth = $calc->getEnvelopeWidthOpen();
            $pheight = $calc->getEnvelopeHeightOpen();
        }
        $pwidth_closed = $calc->getProductFormatWidth();
        $pheight_closed = $calc->getProductFormatHeight();
        // paper format
        $width = $mach->getMyPaperHeight();
        $height = $mach->getMyPaperWidth();
        // reduce paper format by machine borders + reduce height by colorstripe
        $width = $width - $mach->getMachine()->getBorder_bottom() - $mach->getMachine()->getBorder_top() - $farbrand;
        $height = $height - $mach->getMachine()->getBorder_left() - $mach->getMachine()->getBorder_right();

        // how often does the closed format fit into the open
        if ($pwidth_closed < $pwidth && $pwidth_closed != 0 )
            $rows = floor(ceil($pwidth * 1.01) / $pwidth_closed);
        else
            $rows = 1;
        if ($pheight_closed < $pheight && $pheight_closed != 0 )
            $cols = floor(ceil($pheight * 1.01) / $pheight_closed);
        else
            $cols = 1;
        $closed_per_open = $rows * $cols;
        if ($debug) prettyPrint("closed_per_open: {$closed_per_open}");
        $needed_product_prints = ceil($pages / $closed_per_open);
        if ($debug) prettyPrint("needed_product_prints: {$needed_product_prints}");

        $ppp = 0; // products per paper
        $pwidth += $bleed*2; // add bleed to product width
        $pheight += $bleed*2; // add bleed to product height
        $ppp1 = floor($width / $pwidth) * floor($height / $pheight); // calc products per paper
        $ppp2 = floor($width / $pheight) * floor($height / $pwidth); // calc products per paper with width and height of product swapped
        if ($ppp1>$ppp2)
            $ppp = $ppp1;
        else
            $ppp = $ppp2;
        if ($debug) prettyPrint("ppp: {$ppp}");

        $ppp = floor($ppp / $needed_product_prints); // one side of the paper
        if ($debug) prettyPrint("ppp one side: {$ppp}");
        if ($duplex) {
            if ($debug) prettyPrint("we're also printing the backside so double ppp");
            $ppp = $ppp * 2; // add the backside of the paper
        }
        if ($debug) prettyPrint("ppp total: {$ppp}");

        return $ppp;
    }

    /**
     * @param Calculation $calc
     * @param $sizes
     * @param int $part
     * @param Machineentry $me
     * @return array|mixed
     */
    public static function SelectSmallestPaperForMaxPPP(Calculation $calc, $sizes, $part = 0, $me = null)
    {
        $array = [];
        foreach ($sizes as $size){
            $ppp = $calc->getUsagePerPaper($part, $size["height"], $size["width"], $me);
            $array[$ppp][$size["width"]*$size["height"]] = $size["width"].'x'.$size["height"];
        }
        ksort($array, SORT_NUMERIC);
        $array = $array[max(array_keys($array))];
        ksort($array, SORT_NUMERIC);
        $array = $array[min(array_keys($array))];
        return $array;
    }

    /**
     * @param $auflage // Auflage
     * @param $pages // Anzahl Seiten
     * @param $pwidth // Produktbreite
     * @param $pheight // Produkthöhe
     * @param $inkcoverage // Farbdeckung aus Produkt
     * @param $colorcount // Anzahl Farben
     * @param $sorts // Anzahl Sorten
     * @param int $bleed // Anschnitt
     * @return float
     */
    public static function CalculateColorUsed($auflage, $pages, $pwidth, $pheight, $inkcoverage, $colorcount, $sorts = 1, $bleed = 0)
    {
        $debug = false;
        $perf = new Perferences();
        $inkusage = $perf->getInkusage();

        // Fläche m2 = Produkt Länge m * Produkt Breite m * Seiten (inkl. Anschnitt)
        $m2 = (($pwidth+2*$bleed)/1000) * (($pheight+2*$bleed)/1000) * $pages;
        if ($debug){
            prettyPrint('(($pwidth+2*$bleed)/1000) * (($pheight+2*$bleed)/1000) * $pages');
            prettyPrint("(({$pwidth}+2*{$bleed})/1000) * (({$pheight}+2*{$bleed})/1000) * {$pages}");
            prettyPrint("m2 = {$m2}");
        }

        // Farbverbrauch g = Bedruckte Fläche m2 * Farbdeckung % * Farbverbrauch g/m2 * Auflage * Sorten
        $ink = $m2 * ($inkcoverage/100) * $inkusage * $auflage * $sorts;
        if ($debug) {
            prettyPrint('$m2 * ($inkcoverage/100) * $inkusage * $auflage * $sorts');
            prettyPrint("{$m2} * ({$inkcoverage}/100) * {$inkusage} * {$auflage} * {$sorts}");
            prettyPrint("ink = {$ink}");
        }

        // Farbverbrauch * Anzahl Farben
        if ($debug){
            prettyPrint('$ink * $colorcount');
            prettyPrint("{$ink} * {$colorcount}");
        }
        $ink = $ink * $colorcount;
        if ($debug){
            prettyPrint("ink = {$ink}");
        }

        // Ausgabe Farbverbrauch in g
        return $ink;
    }

    /**
     * @param $auflage // Auflage
     * @param $pages // Anzahl Seiten
     * @param $pwidth // Produktbreite
     * @param $pheight // Produkthöhe
     * @param $finishingcoverage // Lackdeckung aus Produkt
     * @param $sorts // Anzahl Sorten
     * @param int $bleed // Anschnitt
     * @return float
     */
    public static function CalculateFinishUsed($auflage, $pages, $pwidth, $pheight, $finishingcoverage, $sorts = 1, $bleed = 0)
    {
        $debug = false;
        $perf = new Perferences();
        $inkusage = $perf->getFinishingusage();

        // Fläche m2 = Produkt Länge m * Produkt Breite m * Seiten (inkl. Anschnitt)
        $m2 = (($pwidth+2*$bleed)/1000) * (($pheight+2*$bleed)/1000) * $pages;
        if ($debug){
            prettyPrint('(($pwidth+2*$bleed)/1000) * (($pheight+2*$bleed)/1000) * $pages');
            prettyPrint("(({$pwidth}+2*{$bleed})/1000) * (({$pheight}+2*{$bleed})/1000) * {$pages}");
            prettyPrint("m2 = {$m2}");
        }

        // Farbverbrauch g = Bedruckte Fläche m2 * Farbdeckung % * Farbverbrauch g/m2 * Auflage * Sorten
        $ink = $m2 * ($finishingcoverage/100) * $inkusage * $auflage * $sorts;
        if ($debug) {
            prettyPrint('$m2 * ($inkcoverage/100) * $inkusage * $auflage * $sorts');
            prettyPrint("{$m2} * ({$finishingcoverage}/100) * {$inkusage} * {$auflage} * {$sorts}");
            prettyPrint("ink = {$ink}");
        }

        // Ausgabe Farbverbrauch in g
        return $ink;
    }


    public static function CalculateAndSave(Order $order, Calculation $calc, $sets, $step, $request = [], $origcalc = 0)
    {
        if (count($sets)) {
            foreach ($sets as $func => $value) {
                $calc->$func($value);
            }
            $calc->save();
        }

        if ($step == 2){
            self::Step2Run($order,$calc);
        } elseif ($step == 3){
            self::Step3Run($order,$calc,$request);
        } elseif ($step == 4){
            self::Step4Run($order,$calc,$origcalc);
        }
    }

    public static function Step2Run(Order $order, Calculation $calc)
    {
        $perference = new Perferences();
        $machines = $order->getProduct()->getMachines();
        $contentarray = Calculation::contentArrayAsso();
        $entries = [];
        $mids = [];

//        Machineentry::deleteAllPrinterForCalc($calc->getId());
        Machineentry::deleteAllForCalc($calc->getId());
        foreach ($machines as $m)
        {
            if($order->getProduct()->isDefaultMachine($m, $calc->getAmount()))
            {
                foreach ([Calculation::PAPER_CONTENT,Calculation::PAPER_ADDCONTENT,Calculation::PAPER_ADDCONTENT2,Calculation::PAPER_ADDCONTENT3,Calculation::PAPER_ENVELOPE] as $currpartid) {
                    if ($calc->getPagesFor($currpartid)){

                        if($m->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $m->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                        {
                            if($m->ColorPossible($calc->{$contentarray[$currpartid]['chr']}())){ // Standart Maschine kann ausgewaehlte Farbe drucken
                                $me = new Machineentry();
                                $me->setPart($currpartid);
                                $me->setMachine($m);
                                $me->setMachineGroup($m->getGroup()->getId());
                                $me->setCalcId($calc->getId());
                            } else { //Standart-Maschine kann ausgewaehlte Farbe nicht drucken, also eine andere setzen
                                $alt_machines = Machine::getAlternativMachines($m, $calc->{$contentarray[$currpartid]['chr']}()->getId());
                                if (count($alt_machines)){ //Falls es eine Maschine gibt, die die Farbe kann => setzen
                                    $me = new Machineentry();
                                    $me->setPart($currpartid);
                                    $me->setMachine($alt_machines[0]);
                                    $me->setMachineGroup($m->getGroup()->getId());
                                    $me->setCalcId($calc->getId());
                                }
                            }
                            if (!isset($me))
                                continue;

                            $sizes = $calc->getPaperContent()->getAvailablePaperSizesForMachine($m, $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->{$contentarray[$currpartid]['id']}()->getRolle(), $calc->getProductFormatHeightOpen());
                            $small_array = CalculationService::SelectSmallestPaperForMaxPPP($calc, $sizes, $currpartid, $me);
                            $smallest_paper = explode('x',$small_array);
                            $calc->{$contentarray[$currpartid]['setPaperHeight']}($smallest_paper[1]);
                            $calc->{$contentarray[$currpartid]['setPaperWidth']}($smallest_paper[0]);
                            $calc->{$contentarray[$currpartid]['setFormatIn']}($smallest_paper[0].'x'.$smallest_paper[1]);
                            $grant = tofloat($perference->getZuschussProDP() * $calc->getPlateCount($me)) + ($calc->getPaperCount($currpartid) / 100 * $perference->getZuschussPercent());
                            $calc->{$contentarray[$currpartid]['setPaperGrant']}($grant);

                            // Lackberechnung
                            if ($me->getMachine()->getFinish() && $me->getFinishing()->getId()>0){
                                $finish = CalculationService::CalculateFinishUsed(
                                    $calc->getAmount(),
                                    $calc->getPagesFor($currpartid),
                                    $calc->getProductFormatWidth(),
                                    $calc->getProductFormatHeight(),
                                    $order->getProduct()->getFinishingcoverage(),
                                    $calc->getSorts(),
                                    $calc->{$contentarray[$currpartid]['cut']}()
                                );
                                $calc->{$contentarray[$currpartid]['setFinishused']}($finish);
                                $calc->{$contentarray[$currpartid]['setFinish']}($me->getFinishing());
                            }

                            // Farbberechnung
                            $ink = CalculationService::CalculateColorUsed(
                                $calc->getAmount(),
                                $calc->getPagesFor($currpartid),
                                $calc->getProductFormatWidth(),
                                $calc->getProductFormatHeight(),
                                $order->getProduct()->getInkcoverage(),
                                $calc->{$contentarray[$currpartid]['chr']}()->getColorsFront(),
                                $calc->getSorts(),
                                $calc->{$contentarray[$currpartid]['cut']}()
                            );
                            $calc->{$contentarray[$currpartid]['setInkused']}($ink);

                            if ($me->getMachine()->getFinish()){
                                $me->setFinishingcoverage($order->getProduct()->getFinishingcoverage());
                            }
                            $entries[] = $me;
                            unset($sizes);
                            unset($me);
                        } else if($m->getType() == Machine::TYPE_FOLDER) // Falzmaschine
                        {
                            $me = new Machineentry();
                            $me->setMachine($m);
                            $me->setMachineGroup($m->getGroup()->getId());
                            $me->setCalcId($calc->getId());
                            $me->setPart($currpartid);
                            $me->setFoldtype($calc->getFolding());
                            $entries[] = $me;
                            unset($me);
                        } else if($m->getType() == Machine::TYPE_CUTTER) // Schneidemaschine
                        {
                            $me = new Machineentry();
                            $me->setMachine($m);
                            $me->setMachineGroup($m->getGroup()->getId());
                            $me->setCalcId($calc->getId());
                            $me->setPart($currpartid);
                            $me->setFoldtype($calc->getFolding());
                            $me->setCutter_cuts($me->calcCuts());
                            $entries[] = $me;
                            unset($me);
                        } else {
                            if (!in_array($m->getId(),$mids)){
                                $me = new Machineentry();
                                $me->setMachine($m);
                                $me->setMachineGroup($m->getGroup()->getId());
                                $me->setCalcId($calc->getId());
                                $entries[] = $me;
                                $mids[] = $m->getId();
                                unset($me);
                            }
                        }
                    }
                }
            }
        }
        $calc->save();
        foreach ($entries as $entry) {
            if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
                $entry->setDpgrant($perference->getZuschussProDP());
                $entry->setPercentgrant($perference->getZuschussPercent());
            }
            $entry->save();
            $entry->setTime(tofloat($entry->getMachine()->getRunningTime($entry)));
            $entry->setPrice(tofloat($entry->getMachine()->getMachinePrice($entry)));
            $entry->save();
        }

    }

    public static function Step3Run(Order $order, Calculation $calc, $request)
    {
        $perference = new Perferences();
        $contentarray = Calculation::contentArrayAsso();

//        Machineentry::deleteAllPrinterForCalc($calc->getId());
        Machineentry::deleteAllForCalc($calc->getId());
        foreach(array_keys($request) as $key) // hier wird fuer alle Schluessel die Verarbeitung gestartet
        {
            if(preg_match("/mach_id_(?P<id>\d+)/", $key, $m))
            {
                $id = $m["id"];
                if($request["mach_id_{$id}"] != "" && $request["mach_id_{$id}"] != 0)
                {
                    $entry = new Machineentry($id);
                    $entry->setCalcId($calc->getId());
                    $entry->setPart((int)$request["mach_part_{$id}"]);
                    $entry->setMachine(new Machine($request["mach_id_{$id}"]));
                    $entry->setMachineGroup($entry->getMachine()->getGroup()->getId());
                    $entry->setFinishing(new Finishing((int)$request["mach_finishing_{$id}"]));
                    $entry->setUmschlagenUmstuelpen((int)$request["umschl_umst_{$id}"]);
                    $entry->setInfo(trim(addslashes($request["mach_info_{$id}"])));
                    $entry->setColor_detail(trim(addslashes($request["mach_color_detail_{$id}"])));
                    $entry->setAddworkeramount((int)$request["mach_addworkeramount_{$id}"]);
                    if ((int)$request["mach_usageoverride_{$id}"] > 0)
                        $entry->setUsageoverride((int)$request["mach_usageoverride_{$id}"]);
                    else
                        $entry->setUsageoverride(0);
                    $entry->setFinishingcoverage(tofloat($request["mach_finishingcoverage_{$id}"]));
                    if (isset($request["mach_supplierprice_{$id}"]))
                        $entry->setSupplierPrice((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $request["mach_supplierprice_{$id}"]))));
                    if (isset($request["mach_supplierid_{$id}"]))
                        $entry->setSupplierID((int)$request["mach_supplierid_{$id}"]);
                    if (isset($request["mach_supplierstatus_{$id}"]))
                        $entry->setSupplierStatus((int)$request["mach_supplierstatus_{$id}"]);
                    if (isset($request["mach_supplierinfo_{$id}"]))
                        $entry->setSupplierInfo(trim(addslashes($request["mach_supplierinfo_{$id}"])));
                    if (isset($request["mach_dopnutz_{$id}"]))
                        $entry->setDoubleutilization($request["mach_dopnutz_{$id}"]);
                    if (isset($request["mach_inlineheften_{$id}"]))
                        $entry->setInlineheften((float)$request["mach_inlineheften_{$id}"]);
                    if (isset($request["mach_senddate_{$id}"]))
                    {
                        if($request["mach_senddate_{$id}"] != ""){
                            $tmp_date = explode('.', trim(addslashes($request["mach_senddate_{$id}"])));
                            $tmp_date = mktime(2,0,0,$tmp_date[1],$tmp_date[0],$tmp_date[2]);
                        } else {
                            $tmp_date = 0;
                        }
                    } else {
                        $tmp_date = 0;
                    }
                    $entry->setSupplierSendDate($tmp_date);
                    if (isset($request["mach_receivedate_{$id}"]))
                    {
                        if($request["mach_receivedate_{$id}"] != ""){
                            $tmp_date = explode('.', trim(addslashes($request["mach_receivedate_{$id}"])));
                            $tmp_date = mktime(2,0,0,$tmp_date[1],$tmp_date[0],$tmp_date[2]);
                        } else {
                            $tmp_date = 0;
                        }
                    } else {
                        $tmp_date = 0;
                    }
                    $entry->setSupplierReceiveDate($tmp_date);
                    $entry->setSpecial_margin((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $request["mach_special_margin_{$id}"]))));
                    $entry->setSpecial_margin_text($request["mach_special_margin_text_{$id}"]);

                    // Falls Druckmaschine -> Papiergroesse setzen
                    if ($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                    {
                        $entry->setDigigrant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $request["mach_digigrant_{$id}"]))));
                        $entry->setPercentgrant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $request["mach_percentgrant_{$id}"]))));
                        $entry->setDpgrant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $request["mach_dpgrant_{$id}"]))));
                        $entry->setRoll_dir((int)$request["mach_roll_dir_{$id}"]);
                        if (isset($request["mach_labelcount_{$id}"]))
                            $entry->setLabelcount($request["mach_labelcount_{$id}"]);
                        if (isset($request["mach_labelradius_{$id}"]))
                            $entry->setLabelradius(tofloat($request["mach_labelradius_{$id}"]));
                        if (isset($request["mach_rollcount_{$id}"]))
                            $entry->setRollcount($request["mach_rollcount_{$id}"]);
                        if (isset($request["mach_corediameter_{$id}"]))
                            $entry->setCorediameter(tofloat($request["mach_corediameter_{$id}"]));
                        if (isset($request["mach_rolldiameter_{$id}"]))
                            $entry->setRolldiameter(tofloat($request["mach_rolldiameter_{$id}"]));

                        $sizes = $request["mach_papersize_{$id}"];
                        $sizes = explode("x", $sizes);

                        $calc->{$contentarray[$entry->getPart()]['setPaperHeight']}($sizes[1]);
                        $calc->{$contentarray[$entry->getPart()]['setPaperWidth']}($sizes[0]);

                        if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
                            $grant = ($entry->getDpgrant() * $calc->getPlateCount($entry)) + ($calc->getPaperCount($entry->getPart()) / 100 * $entry->getPercentgrant());
                            $calc->{$contentarray[$entry->getPart()]['setPaperGrant']}($grant);
                        } else {
                            $calc->{$contentarray[$entry->getPart()]['setPaperGrant']}($order->getProduct()->getGrantPaper()+$entry->getDigigrant()); //Zuschuss
                        }

                        // Lackberechnung
                        if ($entry->getMachine()->getFinish() && $entry->getFinishing()->getId()>0){
                            $finish = CalculationService::CalculateFinishUsed(
                                $calc->getAmount(),
                                $calc->getPagesFor($entry->getPart()),
                                $calc->getProductFormatWidth(),
                                $calc->getProductFormatHeight(),
                                $order->getProduct()->getFinishingcoverage(),
                                $calc->getSorts(),
                                $calc->{$contentarray[$entry->getPart()]['cut']}()
                            );
                            $calc->{$contentarray[$entry->getPart()]['setFinishused']}($finish);
                            $calc->{$contentarray[$entry->getPart()]['setFinish']}($entry->getFinishing());
                        } else {
                            $calc->{$contentarray[$entry->getPart()]['setFinishused']}(0);
                            $calc->{$contentarray[$entry->getPart()]['setFinish']}(new Finishing(0));
                        }
                        // Farbberechnung
                        $ink = CalculationService::CalculateColorUsed(
                            $calc->getAmount(),
                            $calc->getPagesFor($entry->getPart()),
                            $calc->getProductFormatWidth(),
                            $calc->getProductFormatHeight(),
                            $order->getProduct()->getInkcoverage(),
                            $calc->{$contentarray[$entry->getPart()]['chr']}()->getColorsFront(),
                            $calc->getSorts(),
                            $calc->{$contentarray[$entry->getPart()]['cut']}()
                        );
                        $calc->{$contentarray[$entry->getPart()]['setInkused']}($ink);
                    } else if ($entry->getMachine()->getType() == Machine::TYPE_CUTTER){
                        if ($request["mach_format_in_{$id}"] != "" && $request["mach_format_out_{$id}"] != ""){
                            $tmp_cut_format_in = explode("x", $request["mach_format_in_{$id}"]);
                            $tmp_cut_format_out = explode("x", $request["mach_format_out_{$id}"]);
                            $entry->setFormat_in_width((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_in[0]))));
                            $entry->setFormat_in_height((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_in[1]))));
                            $entry->setFormat_out_width((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_out[0]))));
                            $entry->setFormat_out_height((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_out[1]))));
                        }
                        $entry->setRoll_dir((int)$request["mach_roll_dir_{$id}"]);
                        $entry->setCutter_cuts((int)$request["mach_cutter_cuts_{$id}"]);
                    } else if ($entry->getMachine()->getType() == Machine::TYPE_FOLDER){
                        $entry->setFoldtype(new Foldtype((int)$request["mach_foldtype_{$id}"]));
                    }

                    if($request["mach_time_{$id}"] > 0 && !$calc->getCalcAutoValues())
                        $entry->setTime((int)$request["mach_time_{$id}"]);
                    else
                        $entry->setTime($entry->getMachine()->getRunningTime($entry));
                    if($entry->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL){
                        $entry->setPrice((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $request["mach_manprice_{$id}"]))));
                    } else {
                        $entry->setPrice($entry->getMachine()->getMachinePrice($entry));
                    }
                    $entry->save();
                }
            }
        }
        $calc->save();

        $machineentries = Machineentry::getAllMachineentries($calc->getId());
        foreach ($machineentries as $machineentry) {
            if ($calc->getCalcAutoValues()){
                $machineentry->setTime($machineentry->getMachine()->getRunningTime($machineentry));
            }
            if ($machineentry->getMachine()->getPriceBase() != Machine::PRICE_VARIABEL){
                $machineentry->setPrice($machineentry->getMachine()->getMachinePrice($machineentry));
            }
            if ($calc->getCalcAutoValues() || $machineentry->getMachine()->getPriceBase() != Machine::PRICE_VARIABEL)
                $machineentry->save();
        }

        $calc->setPricesub(tofloat($calc->getSubTotal()));
        $calc->setPricetotal(tofloat($calc->getSummaryPrice()));
        $calc->save();
    }

    public static function Step4Run(Order $order, Calculation $calc, $origcalc)
    {
        $origcalc = new Calculation($origcalc);
        $perference = new Perferences();
        $machines = $order->getProduct()->getMachines();
        $contentarray = Calculation::contentArrayAsso();
        $entries = [];
        $mids = [];

        Machineentry::deleteAllForCalc($calc->getId());
        $hasCTP = false;
        $needsCTP = false;
        // Preise und Zeiten aktualisieren
        foreach(Machineentry::getAllMachineentries($origcalc->getId()) as $me)
        {
            if(!$order->getProduct()->isDefaultMachine($me->getMachine(), $calc->getAmount())
                && $order->getProduct()->isDefaultMachine($me->getMachine(), $origcalc->getAmount()))
            {
                // Maschine ist fuer diese Auflage nicht Standard, fuer die kleinere jedoch schon
                //  -> neue Maschine finden und setzen
                $groupMachs = Machine::getAllMachines(Machine::ORDER_ID, $me->getMachineGroup());
                foreach($groupMachs as $gm)
                {
                    if($order->getProduct()->isDefaultMachine($gm, $calc->getAmount()))
                    {
                        $me->clearId();
                        $me->setCalcId($calc->getId());
                        $me->setMachine($gm);
                        if($me->getFinishing() && !$me->getMachine()->getFinish())
                            $me->setFinishing(new Finishing(0));
                        $me->save();

                        if($me->getMachine()->getType() == Machine::TYPE_CTP)
                            $hasCTP = true;

                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                        {
                            $currpartid = $me->getPart();
                            if ($calc->getPagesFor($currpartid)){
                                $sizes = $calc->getPaperContent()->getAvailablePaperSizesForMachine($me->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->{$contentarray[$currpartid]['id']}()->getRolle(), $calc->getProductFormatHeightOpen());
                                $small_array = CalculationService::SelectSmallestPaperForMaxPPP($calc, $sizes, $currpartid, $me);
                                $smallest_paper = explode('x',$small_array);
                                $calc->{$contentarray[$currpartid]['setPaperHeight']}($smallest_paper[1]);
                                $calc->{$contentarray[$currpartid]['setPaperWidth']}($smallest_paper[0]);
                                $calc->{$contentarray[$currpartid]['setFormatIn']}($smallest_paper[0].'x'.$smallest_paper[1]);

                                if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
                                    $grant = ($me->getDpgrant() * $calc->getPlateCount($me)) + ($calc->getPaperCount($me->getPart()) / 100 * $me->getPercentgrant());
                                    $calc->{$contentarray[$me->getPart()]['setPaperGrant']}($grant);
                                } else {
                                    $calc->{$contentarray[$me->getPart()]['setPaperGrant']}($order->getProduct()->getGrantPaper()+$me->getDigigrant()); //Zuschuss
                                }
                            }
                            // Lackberechnung
                            if ($me->getMachine()->getFinish() && $me->getFinishing()->getId()>0){
                                $finish = CalculationService::CalculateFinishUsed(
                                    $calc->getAmount(),
                                    $calc->getPagesFor($currpartid),
                                    $calc->getProductFormatWidth(),
                                    $calc->getProductFormatHeight(),
                                    $order->getProduct()->getFinishingcoverage(),
                                    $calc->getSorts(),
                                    $calc->{$contentarray[$currpartid]['cut']}()
                                );
                                $calc->{$contentarray[$currpartid]['setFinishused']}($finish);
                                $calc->{$contentarray[$currpartid]['setFinish']}($me->getFinishing());
                            } else {
                                $calc->{$contentarray[$currpartid]['setFinishused']}(0);
                                $calc->{$contentarray[$currpartid]['setFinish']}(new Finishing(0));
                            }
                            // Farbberechnung
                            $ink = CalculationService::CalculateColorUsed(
                                $calc->getAmount(),
                                $calc->getPagesFor($currpartid),
                                $calc->getProductFormatWidth(),
                                $calc->getProductFormatHeight(),
                                $order->getProduct()->getInkcoverage(),
                                $calc->{$contentarray[$currpartid]['chr']}()->getColorsFront(),
                                $calc->getSorts(),
                                $calc->{$contentarray[$currpartid]['cut']}()
                            );
                            $calc->{$contentarray[$currpartid]['setInkused']}($ink);

                            if ($me->getMachine()->getFinish()){
                                $me->setFinishingcoverage($order->getProduct()->getFinishingcoverage());
                            }

                        }

                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                            $needsCTP = true;
                    }
                }
            } else
            {
                $me->clearId();
                $me->setCalcId($calc->getId());
                $me->save();

                if($me->getMachine()->getType() == Machine::TYPE_CTP)
                    $hasCTP = true;

            }
            unset($me);
        }

        // Plattenbelichter fehlt
        if($needsCTP && !$hasCTP)
        {
            // CTP suchen
            foreach(Machine::getAllMachines(Machine::ORDER_ID) as $mach)
            {
                if($mach->getType() == Machine::TYPE_CTP && $order->getProduct()->isDefaultMachine($mach, $calc->getAmount()))
                {
                    $me = new Machineentry();
                    $me->setMachine($mach);
                    $me->setMachineGroup($mach->getGroup()->getId());
                    $me->setCalcId($calc->getId());
                    $me->save();
                }
            }
        }
        $calc->save();

        foreach (Machineentry::getAllMachineentries($calc->getId()) as $entry) {
            if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
                $entry->setDpgrant($perference->getZuschussProDP());
                $entry->setPercentgrant($perference->getZuschussPercent());
            }
            $entry->save();
            $entry->setTime(tofloat($entry->getMachine()->getRunningTime($entry)));
            $entry->setPrice(tofloat($entry->getMachine()->getMachinePrice($entry)));
            $entry->save();
        }

        $calc->setPricesub(tofloat($calc->getSubTotal()));
        $calc->setPricetotal(tofloat($calc->getSummaryPrice()));
        $calc->save();

    }
}