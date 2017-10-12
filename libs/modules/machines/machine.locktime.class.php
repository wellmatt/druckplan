<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class MachineLocktime extends Model{
    public $_table = 'machines_locktimes';

    public $machine;
    public $title = '';
    public $type = 1;
    public $datestart = 0;
    public $dateend = 0;
    public $repeat = 0;
    public $repeatday = 0;

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
}