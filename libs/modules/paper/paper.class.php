<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';
require_once 'paper.igepa.class.php';

class Paper extends Model{
    public $_table = 'papers';

    const DIR_SB = 1;                   // Schmale Bahn
    const DIR_BB = 2;                   // Breite Bahn

    public $name = '';                  // Name
    public $info = '';                  // Info
    public $number = '';                // Artikelnummer beim Lieferanten
    public $attributes = '';            // Eigenschaften
    public $weight = 0;                 // Grammatur
    public $width = 0.0;                // Breite
    public $height = 0.0;               // Höhe
    public $direction = 0;              // Laufrichtung
    public $color = '';                 // Farbe
    public $weightper1000 = 0.0;        // Gewicht pro 1000 Bogen
    public $ream = 0;                   // Verpackte Menge

    public $article;                    // zugehöriger Artikel

    protected function bootClasses(){
        $this->article = new Article($this->article);
    }

    /**
     * @param string $number
     * @return Paper
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

    public function updateFromIgepa(PaperIgepa $igepa)
    {
        $this->setName(utf8_encode($igepa->bezeichnung));
        $this->setInfo(utf8_encode($igepa->zusatzbezeichnung));
        $this->setWeight($igepa->grammatur);
        $this->setWidth($igepa->papierbreite);
        $this->setHeight($igepa->papierhoehe);
        $this->setDirection($igepa->direction());
        $this->setColor(utf8_encode($igepa->farbe));
        $this->save();
    }

    /**
     * @return Paper[]
     */
    public static function getAll()
    {
        $retval = self::fetch();
        return $retval;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
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
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
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
    public function getWeightper1000()
    {
        return $this->weightper1000;
    }

    /**
     * @param float $weightper1000
     */
    public function setWeightper1000($weightper1000)
    {
        $this->weightper1000 = $weightper1000;
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

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
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
}