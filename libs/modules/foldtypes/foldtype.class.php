<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class FoldType extends Model{
    public $_table = 'foldtypes';

    public $name = '';
    public $description = '';
    public $type = 0;
    public $imageid = 0;
    public $breaks = 0;

    const TYPE_Kreuzfalz = 1;
    const TYPE_Wickelfalz = 2;
    const TYPE_Zickzackfalz = 3;
    const TYPE_Fensterfalz = 4;
    const TYPE_Parallelmittenfalz = 5;

    public $_types = [
        ['id' => 1, 'name' => 'Kreuzfalz'],
        ['id' => 2, 'name' => 'Wickelfalz'],
        ['id' => 3, 'name' => 'Zickzackfalz'],
        ['id' => 4, 'name' => 'Fensterfalz'],
        ['id' => 5, 'name' => 'Parallelmittenfalz'],
    ];

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
     * @return int
     */
    public function getImageid()
    {
        return $this->imageid;
    }

    /**
     * @param int $imageid
     */
    public function setImageid($imageid)
    {
        $this->imageid = $imageid;
    }

    /**
     * @return int
     */
    public function getBreaks()
    {
        return $this->breaks;
    }

    /**
     * @param int $breaks
     */
    public function setBreaks($breaks)
    {
        $this->breaks = $breaks;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->_types;
    }
}