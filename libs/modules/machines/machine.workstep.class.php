<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class MachineWorkstep extends Model{
    public $_table = 'machines_worksteps';

    public $machine;
    public $type = 1;
    public $title = '';
    public $timeadded = 0.0;
    public $timesaving = 0.0;
    public $essential = 0;
    public $sequence = 1;

    protected function bootClasses()
    {
        $this->machine = new Machine($this->machine);
    }

    /**
     * @return Machine
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * @param Machine $machine
     */
    public function setMachine($machine)
    {
        $this->machine = $machine;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getTimeadded()
    {
        return $this->timeadded;
    }

    /**
     * @param float $timeadded
     */
    public function setTimeadded($timeadded)
    {
        $this->timeadded = $timeadded;
    }

    /**
     * @return float
     */
    public function getTimesaving()
    {
        return $this->timesaving;
    }

    /**
     * @param float $timesaving
     */
    public function setTimesaving($timesaving)
    {
        $this->timesaving = $timesaving;
    }

    /**
     * @return int
     */
    public function getEssential()
    {
        return $this->essential;
    }

    /**
     * @param int $essential
     */
    public function setEssential($essential)
    {
        $this->essential = $essential;
    }

    /**
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }
}