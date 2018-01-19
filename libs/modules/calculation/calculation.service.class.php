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
     * @return array|mixed
     */
    public static function SelectSmallestPaperForMaxPPP(Calculation $calc, $sizes, $part = 0)
    {
        $array = [];
        foreach ($sizes as $size){
            $ppp = $calc->getUsagePerPaper($part,$size["height"],$size["width"]);
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
}