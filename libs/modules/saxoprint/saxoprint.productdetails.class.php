<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class SaxoprintProductDetails{
    public $Circulation;
    public $ProductCharacteristics;
    public $SpecialColors;
    public $FreeFormats;

    /**
     * SaxoprintProductDetails constructor.
     * @param $Circulation integer
     * @param $ProductCharacteristics SaxoprintProductCharacteristic[]
     * @param $SpecialColors SaxoprintSpecialColor[]
     * @param $FreeFormats SaxoprintFreeFormat[]
     */
    public function __construct($Circulation, $ProductCharacteristics, $SpecialColors, $FreeFormats)
    {
        $this->Circulation = $Circulation;
        $this->ProductCharacteristics = $ProductCharacteristics;
        $this->SpecialColors = $SpecialColors;
        $this->FreeFormats = $FreeFormats;
    }

    /**
     * @return integer
     */
    public function getCirculation()
    {
        return $this->Circulation;
    }

    /**
     * @return SaxoprintProductCharacteristic[]
     */
    public function getProductCharacteristics()
    {
        return $this->ProductCharacteristics;
    }

    /**
     * @return SaxoprintSpecialColor[]
     */
    public function getSpecialColors()
    {
        return $this->SpecialColors;
    }

    /**
     * @return SaxoprintFreeFormat[]
     */
    public function getFreeFormats()
    {
        return $this->FreeFormats;
    }
}