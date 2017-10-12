<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class MachineWorktime extends Model{
    public $_table = 'machines_worktimes';

    public $machine;
    public $weekday = 0;
    public $starting = 0;
    public $ending = 0;

    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;
    const DAY_SUNDAY = 7;

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
    public function getWeekday()
    {
        return $this->weekday;
    }

    /**
     * @param int $weekday
     */
    public function setWeekday($weekday)
    {
        $this->weekday = $weekday;
    }

    /**
     * @return int
     */
    public function getStarting()
    {
        return $this->starting;
    }

    /**
     * @param int $starting
     */
    public function setStarting($starting)
    {
        $this->starting = $starting;
    }

    /**
     * @return int
     */
    public function getEnding()
    {
        return $this->ending;
    }

    /**
     * @param int $ending
     */
    public function setEnding($ending)
    {
        $this->ending = $ending;
    }
}