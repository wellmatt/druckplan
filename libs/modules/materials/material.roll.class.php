<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.class.php';

class MaterialRoll extends Material{
    public $_table = 'material_rolls';
    public $type = 2;

    public $info = '';                  // Info
    public $number = '';                // Artikelnummer beim Lieferanten
    public $weight = 0;                 // Grammatur
    public $width = 0.0;                // Breite
    public $length = 0.0;               // Länge der Rolle
    public $direction = 0;              // Laufrichtung
    public $color = '';                 // Farbe
    public $weightper = 0.0;            // Gewicht
    public $ream = 0;                   // Verpackte Menge

    const DIR_SB = 1;                   // Schmale Bahn
    const DIR_BB = 2;                   // Breite Bahn

    public function updateFromIgepa(MaterialPaperIgepa $igepa)
    {
        $this->setName(utf8_encode($igepa->bezeichnung));
        $this->setInfo(utf8_encode($igepa->zusatzbezeichnung));
        $this->setWeight($igepa->grammatur);
        $this->setWidth($igepa->papierbreite);
        $this->setLength($igepa->rollenlaenge);
        $this->setDirection($igepa->direction());
        $this->setColor(utf8_encode($igepa->farbe));
        $this->save();
    }

    /**
     * @param string $number
     * @return MaterialRoll
     */
    public static function getForNumber($number)
    {
        $retval = self::fetchSingle([
            [
                'column'=>'number',
                'value'=>$number
            ]
        ]);
        return $retval;
    }

    /**
     * @return MaterialRoll[]
     */
    public static function getAll()
    {
        $retval = self::fetch();
        return $retval;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param float $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param int $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return float
     */
    public function getWeightper()
    {
        return $this->weightper;
    }

    /**
     * @param float $weightper
     */
    public function setWeightper($weightper)
    {
        $this->weightper = $weightper;
    }

    /**
     * @return int
     */
    public function getReam()
    {
        return $this->ream;
    }

    /**
     * @param int $ream
     */
    public function setReam($ream)
    {
        $this->ream = $ream;
    }
}
