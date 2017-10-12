<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.class.php';

class MaterialPacking extends Material{
    public $_table = 'material_packings';
    public $type = 6;

    public $width_inside = 0.0;
    public $width_outside = 0.0;
    public $height_inside = 0.0;
    public $height_outside = 0.0;
    public $length_inside = 0.0;
    public $length_outside = 0.0;
    public $weight = 0.0;
    public $maxweight = 0.0;
    public $stapelheight = 0.0;

    /**
     * @return float
     */
    public function getWidthInside()
    {
        return $this->width_inside;
    }

    /**
     * @param float $width_inside
     */
    public function setWidthInside($width_inside)
    {
        $this->width_inside = $width_inside;
    }

    /**
     * @return float
     */
    public function getWidthOutside()
    {
        return $this->width_outside;
    }

    /**
     * @param float $width_outside
     */
    public function setWidthOutside($width_outside)
    {
        $this->width_outside = $width_outside;
    }

    /**
     * @return float
     */
    public function getHeightInside()
    {
        return $this->height_inside;
    }

    /**
     * @param float $height_inside
     */
    public function setHeightInside($height_inside)
    {
        $this->height_inside = $height_inside;
    }

    /**
     * @return float
     */
    public function getHeightOutside()
    {
        return $this->height_outside;
    }

    /**
     * @param float $height_outside
     */
    public function setHeightOutside($height_outside)
    {
        $this->height_outside = $height_outside;
    }

    /**
     * @return float
     */
    public function getLengthInside()
    {
        return $this->length_inside;
    }

    /**
     * @param float $length_inside
     */
    public function setLengthInside($length_inside)
    {
        $this->length_inside = $length_inside;
    }

    /**
     * @return float
     */
    public function getLengthOutside()
    {
        return $this->length_outside;
    }

    /**
     * @param float $length_outside
     */
    public function setLengthOutside($length_outside)
    {
        $this->length_outside = $length_outside;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return float
     */
    public function getMaxweight()
    {
        return $this->maxweight;
    }

    /**
     * @param float $maxweight
     */
    public function setMaxweight($maxweight)
    {
        $this->maxweight = $maxweight;
    }

    /**
     * @return float
     */
    public function getStapelheight()
    {
        return $this->stapelheight;
    }

    /**
     * @param float $stapelheight
     */
    public function setStapelheight($stapelheight)
    {
        $this->stapelheight = $stapelheight;
    }
}
