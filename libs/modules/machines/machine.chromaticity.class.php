<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class MachineChromaticity extends Model{
    public $_table = 'machines_chromaticities';

    public $machine;
    public $chromaticity;
    public $clickprice = 0.0;

    protected function bootClasses()
    {
        $this->machine = new Machine($this->machine);
        $this->chromaticity = new Chromaticity($this->chromaticity);
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
     * @return Chromaticity
     */
    public function getChromaticity()
    {
        return $this->chromaticity;
    }

    /**
     * @param Chromaticity $chromaticity
     */
    public function setChromaticity($chromaticity)
    {
        $this->chromaticity = $chromaticity;
    }

    /**
     * @return float
     */
    public function getClickprice()
    {
        return $this->clickprice;
    }

    /**
     * @param float $clickprice
     */
    public function setClickprice($clickprice)
    {
        $this->clickprice = $clickprice;
    }
}