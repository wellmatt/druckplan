<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.paper.class.php';

class MaterialPaperIgepa{
    public $artikelnummer;
    public $materialgruppe;
    public $bezeichnung;
    public $zusatzbezeichnung;
    public $papierbreite;
    public $papierhoehe;
    public $rollenlaenge;
    public $grammatur;
    public $laufrichtung;
    public $farbs;
    public $farbe;
    public $papiera;
    public $abpapiermenge1;
    public $papierpreis1;
    public $abpapiermenge2;
    public $papierpreis2;
    public $abpapiermenge3;
    public $papierpreis3;
    public $abpapiermenge4;
    public $papierpreis4;
    public $abpapiermenge5;
    public $papierpreis5;
    public $abpapiermenge6;
    public $papierpreis6;
    public $abpapiermenge7;
    public $papierpreis7;
    public $abpapiermenge8;
    public $papierpreis8;
    public $preismenge;
    public $mengeneinheit;
    public $lieferantadressnummer;
    public $mengenschluessel;
    public $lenr;
    public $preiseinheit;
    public $farbsaettigung;
    public $papierart;

    /**
     * PaperIgepa constructor.
     * @param $artikelnummer
     * @param $materialgruppe
     * @param $bezeichnung
     * @param $zusatzbezeichnung
     * @param $papierbreite
     * @param $papierhoehe
     * @param $rollenlaenge
     * @param $grammatur
     * @param $laufrichtung
     * @param $farbs
     * @param $farbe
     * @param $papiera
     * @param $abpapiermenge1
     * @param $papierpreis1
     * @param $abpapiermenge2
     * @param $papierpreis2
     * @param $abpapiermenge3
     * @param $papierpreis3
     * @param $abpapiermenge4
     * @param $papierpreis4
     * @param $abpapiermenge5
     * @param $papierpreis5
     * @param $abpapiermenge6
     * @param $papierpreis6
     * @param $abpapiermenge7
     * @param $papierpreis7
     * @param $abpapiermenge8
     * @param $papierpreis8
     * @param $preismenge
     * @param $mengeneinheit
     * @param $lieferantadressnummer
     * @param $mengenschluessel
     * @param $lenr
     * @param $preiseinheit
     * @param $farbsaettigung
     * @param $papierart
     */
    public function __construct($artikelnummer, $materialgruppe, $bezeichnung, $zusatzbezeichnung, $papierbreite, $papierhoehe, $rollenlaenge, $grammatur, $laufrichtung, $farbs, $farbe, $papiera, $abpapiermenge1, $papierpreis1, $abpapiermenge2, $papierpreis2, $abpapiermenge3, $papierpreis3, $abpapiermenge4, $papierpreis4, $abpapiermenge5, $papierpreis5, $abpapiermenge6, $papierpreis6, $abpapiermenge7, $papierpreis7, $abpapiermenge8, $papierpreis8, $preismenge, $mengeneinheit, $lieferantadressnummer, $mengenschluessel, $lenr, $preiseinheit, $farbsaettigung, $papierart)
    {
        $this->artikelnummer = $artikelnummer;
        $this->materialgruppe = $materialgruppe;
        $this->bezeichnung = $bezeichnung;
        $this->zusatzbezeichnung = $zusatzbezeichnung;
        $this->papierbreite = $papierbreite;
        $this->papierhoehe = $papierhoehe;
        $this->rollenlaenge = $rollenlaenge;
        $this->grammatur = $grammatur;
        $this->laufrichtung = $laufrichtung;
        $this->farbs = $farbs;
        $this->farbe = $farbe;
        $this->papiera = $papiera;
        $this->abpapiermenge1 = $abpapiermenge1;
        $this->papierpreis1 = $papierpreis1;
        $this->abpapiermenge2 = $abpapiermenge2;
        $this->papierpreis2 = $papierpreis2;
        $this->abpapiermenge3 = $abpapiermenge3;
        $this->papierpreis3 = $papierpreis3;
        $this->abpapiermenge4 = $abpapiermenge4;
        $this->papierpreis4 = $papierpreis4;
        $this->abpapiermenge5 = $abpapiermenge5;
        $this->papierpreis5 = $papierpreis5;
        $this->abpapiermenge6 = $abpapiermenge6;
        $this->papierpreis6 = $papierpreis6;
        $this->abpapiermenge7 = $abpapiermenge7;
        $this->papierpreis7 = $papierpreis7;
        $this->abpapiermenge8 = $abpapiermenge8;
        $this->papierpreis8 = $papierpreis8;
        $this->preismenge = $preismenge;
        $this->mengeneinheit = $mengeneinheit;
        $this->lieferantadressnummer = $lieferantadressnummer;
        $this->mengenschluessel = $mengenschluessel;
        $this->lenr = $lenr;
        $this->preiseinheit = $preiseinheit;
        $this->farbsaettigung = $farbsaettigung;
        $this->papierart = $papierart;
    }

    public function checkValid()
    {
        if ($this->artikelnummer == '')
            return false;
        if ($this->bezeichnung == '')
            return false;
        if ($this->papierbreite == '')
            return false;
        if ($this->papierhoehe == '')
            return false;
        if ($this->grammatur == '')
            return false;
        if ($this->laufrichtung == '')
            return false;
        if ($this->farbe == '')
            return false;
        if ($this->abpapiermenge1 == '')
            return false;
        if ($this->papierpreis1 == '')
            return false;
        return true;
    }

    public function checkValidRoll()
    {
        if ($this->artikelnummer == '')
            return false;
        if ($this->bezeichnung == '')
            return false;
        if ($this->papierbreite == '')
            return false;
        if ($this->grammatur == '')
            return false;
        if ($this->laufrichtung == '')
            return false;
        if ($this->farbe == '')
            return false;
        if ($this->abpapiermenge1 == '')
            return false;
        if ($this->papierpreis1 == '')
            return false;
        return true;
    }

    public function checkUpdate(MaterialPaper $paper)
    {
        if ($paper->getName() != utf8_encode($this->bezeichnung)) {
            return true;
        }
        if ($paper->getInfo() != utf8_encode($this->zusatzbezeichnung)) {
            return true;
        }
        if ($paper->getWeight() != $this->grammatur) {
            return true;
        }
        if ($paper->getWidth() != tofloat($this->papierbreite)) {
            return true;
        }
        if ($paper->getHeight() != tofloat($this->papierhoehe)) {
            return true;
        }
        if ($paper->getDirection() != $this->direction()) {
            return true;
        }
        if ($paper->getColor() != utf8_encode($this->farbe)) {
            return true;
        }
        return false;
    }

    public function checkUpdateRoll(MaterialRoll $paper)
    {
        if ($paper->getName() != utf8_encode($this->bezeichnung)) {
            return true;
        }
        if ($paper->getInfo() != utf8_encode($this->zusatzbezeichnung)) {
            return true;
        }
        if ($paper->getWeight() != $this->grammatur) {
            return true;
        }
        if ($paper->getWidth() != tofloat($this->papierbreite)) {
            return true;
        }
        if ($paper->getLength() != tofloat($this->rollenlaenge)) {
            return true;
        }
        if ($paper->getDirection() != $this->direction()) {
            return true;
        }
        if ($paper->getColor() != utf8_encode($this->farbe)) {
            return true;
        }
        return false;
    }

    public function getPrices()
    {
        $retval = [];

        $arr = [
            ['from' => (int)$this->abpapiermenge1, 'price' => tofloat($this->papierpreis1)],
            ['from' => (int)$this->abpapiermenge2, 'price' => tofloat($this->papierpreis2)],
            ['from' => (int)$this->abpapiermenge3, 'price' => tofloat($this->papierpreis3)],
            ['from' => (int)$this->abpapiermenge4, 'price' => tofloat($this->papierpreis4)],
            ['from' => (int)$this->abpapiermenge5, 'price' => tofloat($this->papierpreis5)],
            ['from' => (int)$this->abpapiermenge6, 'price' => tofloat($this->papierpreis6)],
            ['from' => (int)$this->abpapiermenge7, 'price' => tofloat($this->papierpreis7)],
            ['from' => (int)$this->abpapiermenge8, 'price' => tofloat($this->papierpreis8)]
        ];

        if ($arr[0]['from'] == $arr[1]['from'])
            return [['from' => (int)$this->abpapiermenge1, 'to' => 2147483647, 'price' => tofloat($this->papierpreis1)]];
        else
            $retval[] = [['from' => (int)$this->abpapiermenge1, 'to' => (int)$this->abpapiermenge2-1, 'price' => tofloat($this->papierpreis1)]];

        for ($i = 1; $i < 9; $i++){
            if ($arr[$i]['from'] > $arr[$i-1]['from']){ // if volume is greater than previous
                if ($arr[$i+1]['from'] > $arr[$i]['from']){ // if next volume is greater than this
                    $retval[] = ['from' => $arr[$i]['from'], 'to' => $arr[$i+1]['from']-1, 'price' => $arr[$i]['price']];
                } else { // there are no more different volumes so add end volume
                    $retval[] = ['from' => $arr[$i]['from'], 'to' => 2147483647, 'price' => $arr[$i]['price']];
                    break;
                }
            }
        }

        return $retval;
    }

    public function direction()
    {
        switch ($this->laufrichtung){
            case 'SB':
                return MaterialPaper::DIR_SB;
            case 'BB':
                return MaterialPaper::DIR_BB;
            default:
                return MaterialPaper::DIR_SB;
        }
    }

}