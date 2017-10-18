<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


Class CalculationService {
    /**
     * @param $width
     * @param $height
     * @param $pwidth
     * @param $pheight
     * @param float $bleed
     * @param int $direction
     * @param int $pages
     * @param int $setmax
     * @return int
     */
    public static function ProductsPerPaperSimple($width, $height, $pwidth, $pheight, $bleed = 0.0, $direction = 0, $pages = 0, $setmax = 0)
    {
        $ppp = 0; // products per paper
        $pwidth += $bleed*2; // add bleed to product width
        $pheight += $bleed*2; // add bleed to product height
        $ppp1 = floor($width / $pwidth) * floor($height / $pheight); // calc products per paper
        $ppp2 = floor($width / $pheight) * floor($height / $pwidth); // calc products per paper with width and height of product swapped
        if ($ppp1>$ppp2)
            $ppp = $ppp1;
        else
            $ppp = $ppp2;

        if ($setmax){
            $product_max = floor($pages / 4);
            if ($ppp > $product_max)
                $ppp = $product_max;
        }

        return $ppp;
    }

    /**
     * @param $sizes
     * @param $pwidth
     * @param $pheight
     * @param int $bleed
     * @return string
     */
    public static function SelectSmallestPaperForMaxPPP($sizes, $pwidth, $pheight, $bleed = 0)
    {
        $array = [];
        foreach ($sizes as $size){
            $ppp = self::ProductsPerPaperSimple($size["width"],$size["height"],$pwidth,$pheight,$bleed);
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
        $perf = new Perferences();
        $inkusage = $perf->getInkusage();

        // Fläche m2 = Produkt Länge m * Produkt Breite m * Seiten (inkl. Anschnitt)
        $m2 = (($pwidth+2*$bleed)/1000) * (($pheight+2*$bleed)/1000) * $pages;

        // Farbverbrauch g = Bedruckte Fläche m2 * Farbdeckung % * Farbverbrauch g/m2 * Auflage * Sorten
        $ink = $m2 * ($inkcoverage/100) * $inkusage * $auflage * $sorts;

        // Farbverbrauch * Anzahl Farben
        $ink = $ink * $colorcount;

        // Ausgabe Farbverbrauch in g
        return $ink;
    }
}