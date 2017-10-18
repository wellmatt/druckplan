<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'machine.production.information.class.php';
require_once 'machine.chromaticity.class.php';
require_once 'machine.locktime.class.php';
require_once 'machine.qualifiedgroup.class.php';
require_once 'machine.qualifieduser.class.php';
require_once 'machine.workstep.class.php';
require_once 'machine.worktimes.class.php';

class Machine extends Model{
    public $_table = 'machines';

    public $state = 1;
    public $type = 0;
    public $prodinfo;
    public $title = '';
    public $description = '';
    public $interfaceurl = '';

    public $_types = [
        [ 'id' => 1, 'cat' => 1, 'name' => 'Fotostudio'],
        [ 'id' => 2, 'cat' => 1, 'name' => 'Texten'],
        [ 'id' => 3, 'cat' => 1, 'name' => 'Reinzeichnen'],
        [ 'id' => 4, 'cat' => 1, 'name' => 'Konzeptionieren'],
        [ 'id' => 5, 'cat' => 2, 'name' => 'Datenverarbeitung'],
        [ 'id' => 6, 'cat' => 3, 'name' => 'Montage'],
        [ 'id' => 7, 'cat' => 3, 'name' => 'CTF'],
        [ 'id' => 8, 'cat' => 3, 'name' => 'CTP'],
        [ 'id' => 9, 'cat' => 3, 'name' => 'Kopierrahmen'],
        [ 'id' => 10, 'cat' => 3, 'name' => 'Entwicklung'],
        [ 'id' => 11, 'cat' => 4, 'name' => 'Bogenoffset'],
        [ 'id' => 12, 'cat' => 4, 'name' => 'Rollenoffset'],
        [ 'id' => 13, 'cat' => 4, 'name' => 'Rollentiefdruck'],
        [ 'id' => 14, 'cat' => 4, 'name' => 'Rollenhochdruck'],
        [ 'id' => 15, 'cat' => 4, 'name' => 'Siebdruck'],
        [ 'id' => 16, 'cat' => 4, 'name' => 'Transferdruck'],
        [ 'id' => 17, 'cat' => 4, 'name' => 'Buchdruck'],
        [ 'id' => 18, 'cat' => 5, 'name' => 'Rolleninkjet'],
        [ 'id' => 19, 'cat' => 5, 'name' => 'Bogeninkjet'],
        [ 'id' => 20, 'cat' => 5, 'name' => 'Platteninkjet'],
        [ 'id' => 21, 'cat' => 6, 'name' => 'Schneidemaschine'],
        [ 'id' => 22, 'cat' => 6, 'name' => 'Falzmaschine'],
        [ 'id' => 23, 'cat' => 6, 'name' => 'Rüttler'],
        [ 'id' => 24, 'cat' => 6, 'name' => 'Stanze'],
        [ 'id' => 25, 'cat' => 6, 'name' => 'Tiegel'],
        [ 'id' => 26, 'cat' => 6, 'name' => 'Zylinder'],
        [ 'id' => 27, 'cat' => 6, 'name' => 'Kaschiermaschine'],
        [ 'id' => 28, 'cat' => 6, 'name' => 'Bohrer'],
        [ 'id' => 29, 'cat' => 6, 'name' => 'Sammelhefter'],
        [ 'id' => 30, 'cat' => 6, 'name' => 'Turmsammler'],
        [ 'id' => 31, 'cat' => 6, 'name' => 'Klebebinder'],
        [ 'id' => 32, 'cat' => 6, 'name' => 'Trimmer'],
        [ 'id' => 33, 'cat' => 6, 'name' => 'Perforiermaschine'],
        [ 'id' => 34, 'cat' => 6, 'name' => 'Cellophaniermaschine'],
        [ 'id' => 35, 'cat' => 6, 'name' => 'Heftmaschine'],
        [ 'id' => 36, 'cat' => 6, 'name' => 'Spiralbinder'],
        [ 'id' => 37, 'cat' => 6, 'name' => 'Themobinder'],
    ];

    protected function bootClasses()
    {
        $this->prodinfo = new MachineProductionInformation($this->prodinfo);
    }

    /**
     * @return string
     */
    public function getTypename()
    {
        $retval = 'Unbekannt';
        foreach ($this->_types as $type) {
            if ($this->type == $type['id']){
                $retval = $type['name'];
                break;
            }
        }
        return $retval;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getInterfaceurl()
    {
        return $this->interfaceurl;
    }

    /**
     * @param string $interfaceurl
     */
    public function setInterfaceurl($interfaceurl)
    {
        $this->interfaceurl = $interfaceurl;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * @param array $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }

    /**
     * @return MachineProductionInformation
     */
    public function getProdinfo()
    {
        return $this->prodinfo;
    }

    /**
     * @param MachineProductionInformation $prodinfo
     */
    public function setProdinfo($prodinfo)
    {
        $this->prodinfo = $prodinfo;
    }
}