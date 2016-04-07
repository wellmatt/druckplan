<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/article/article.pricescale.class.php';

class Import{

    /**
     * liest CSV datei ein und gibt inhalt als array zurueck
     * @param $file
     * @return array
     */
    public static function getCSV($file)
    {
        $retval = [];
        $file = fopen($file, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            $retval[] = $line;
        }
        fclose($file);
        return $retval;
    }

    /**
     * @param array $array
     * @param array $keys
     * @return bool
     */
    private static function checkNeededKeys(Array $array, Array $keys){
        if ($keys){
            foreach ($keys as $key) {
                $found = false;
                foreach ($array[0] as $item) {
                    if ($item == $key)
                        $found = true;
                }
                if (!$found)
                    return false;
            }
            return true;
        }
        return false;
    }

    /**
     * @param array $array
     * @param array $keepkeys
     * @return array
     */
    private static function dumpExessKeys(Array $array, Array $keepkeys){
        $retval = [];
        $indexi = [];

        foreach ($array[0] as $key) {
            if (in_array($key,$keepkeys)){
                $index = array_search($key, $array[0]);
                if ($index !== false) {
                    $indexi[$key] = $index;
                }
            }
        }
        unset($array[0]);

        if ($indexi){
            foreach ($array as $item) {
                $subarray = [];
                foreach ($indexi as $key => $index) {
                    $subarray[$key] = $item[$index];
                }
                $retval[] = $subarray;
            }
        }

        return $retval;
    }

    public static function ImportPriceScales($array)
    {
        $needed_keys = ['article','type','min','max','price','supplier'];
        $isok = self::checkNeededKeys($array,$needed_keys);
        if ($isok){
            $array = self::dumpExessKeys($array,$needed_keys);
            $count_success = 0;
            $count_fail = 0;

            foreach ($array as $item) {
                $create = $item;
                $pricescale = new PriceScale(0,$create);
                if ($pricescale->getArticle()->getId()>0 && $pricescale->getSupplier()->getId()>0)
                    $ret = $pricescale->save();
                else
                    $ret = false;
                unset($pricescale);
                if ($ret)
                    $count_success++;
                else
                    $count_fail++;
            }

            $alert =    '<div class="alert alert-success">'.
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.
                        '<strong>Info!</strong> Es wurden '.$count_success.' Datensätze erfolgreich Importiert<br>'.
                        'Es wurden '.$count_fail.' Datensätze nicht importiert.'.
                        '</div>';
            return $alert;

        } else {
            $alert =    '<div class="alert alert-danger">'.
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.
                        '<strong>Achtung!</strong> CSV Datei hat falsches Format oder es fehlen benötigte Spalten'.
                        '</div>';
            return $alert;
        }
    }

}