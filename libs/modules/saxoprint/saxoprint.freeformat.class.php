<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class SaxoprintFreeFormat{
    public $PropertyId;
    public $PropertyName;
    public $Value;

    /**
     * SaxoprintFreeFormat constructor.
     * @param $PropertyId
     * @param $PropertyName
     * @param $Value
     */
    public function __construct($PropertyId, $PropertyName, $Value)
    {
        $this->PropertyId = $PropertyId;
        $this->PropertyName = $PropertyName;
        $this->Value = $Value;
    }

    /**
     * @return integer
     */
    public function getPropertyId()
    {
        return $this->PropertyId;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->PropertyName;
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->Value;
    }
}