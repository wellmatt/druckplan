<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.class.php';

class MaterialPrintingplate extends Material{
    public $_table = 'material_printingplates';
    public $type = 3;

    public $width = 0.0;
    public $height = 0.0;
    public $thickness = 0.0;

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
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return float
     */
    public function getThickness()
    {
        return $this->thickness;
    }

    /**
     * @param float $thickness
     */
    public function setThickness($thickness)
    {
        $this->thickness = $thickness;
    }
}
