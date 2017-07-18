<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class SaxoprintWorkingState{
    public $WorkingState;
    public $WorkingStateText;
    public $Timestamp;

    /**
     * SaxoprintWorkingState constructor.
     * @param $WorkingState
     * @param $WorkingStateText
     * @param $Timestamp
     */
    public function __construct($WorkingState, $WorkingStateText, $Timestamp)
    {
        $this->WorkingState = $WorkingState;
        $this->WorkingStateText = $WorkingStateText;
        $this->Timestamp = $Timestamp;
    }

    /**
     * @return integer
     */
    public function getWorkingState()
    {
        return $this->WorkingState;
    }

    /**
     * @return string
     */
    public function getWorkingStateText()
    {
        return $this->WorkingStateText;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->Timestamp;
    }
}