<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class MachineGroup extends Model{
    public $_table = 'machinegroups';

    public $name = '';
    public $type = 1;

    const TYPE_AGENTUR = 1;
    const TYPE_VORSTUFE = 2;
    const TYPE_FORMHERSTELLUNG = 3;
    const TYPE_DRUCK = 4;
    const TYPE_GROSSFORMATDRUCK = 5;
    const TYPE_WEITERVERARBEITUNG = 6;
    const TYPE_LETTERSHOP = 7;
    const TYPE_VERPACKUNG = 8;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
}