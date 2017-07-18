<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class SaxoprintSpecialColor{
    public $Chromaticity;
    public $SpecialColor;

    /**
     * SaxoprintSpecialColor constructor.
     * @param $Chromaticity
     * @param $SpecialColor
     */
    public function __construct($Chromaticity, $SpecialColor)
    {
        $this->Chromaticity = $Chromaticity;
        $this->SpecialColor = $SpecialColor;
    }

    /**
     * @return integer
     */
    public function getChromaticity()
    {
        return $this->Chromaticity;
    }

    /**
     * @return integer
     */
    public function getSpecialColor()
    {
        return $this->SpecialColor;
    }
}