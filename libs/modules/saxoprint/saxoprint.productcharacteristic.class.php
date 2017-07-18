<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class SaxoprintProductCharacteristic{
    public $PropertyId;
    public $PropertyName;
    public $PropertyValueId;
    public $PropertyValueName;

    /**
     * SaxoprintProductCharacteristic constructor.
     * @param $PropertyId
     * @param $PropertyName
     * @param $PropertyValueId
     * @param $PropertyValueName
     */
    public function __construct($PropertyId, $PropertyName, $PropertyValueId, $PropertyValueName)
    {
        $this->PropertyId = $PropertyId;
        $this->PropertyName = $PropertyName;
        $this->PropertyValueId = $PropertyValueId;
        $this->PropertyValueName = $PropertyValueName;
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
    public function getPropertyValueId()
    {
        return $this->PropertyValueId;
    }

    /**
     * @return string
     */
    public function getPropertyValueName()
    {
        return $this->PropertyValueName;
    }
}