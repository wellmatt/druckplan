<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/storage/storage.position.class.php';

class StorageArea extends Model {
    public $_table = 'storage_areas';
    public $name = '';
    public $description = '';
    public $location = '';
    public $corridor = '';
    public $shelf = '';
    public $line = '';
    public $layer = '';


    /**
     * Override default delete to also delete all associated StoragePositions
     */
    public function delete()
    {
        $positions = StoragePosition::getAllForArea($this);
        foreach ($positions as $position) {
            $position->delete();
        }
        parent::delete();
    }

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
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getCorridor()
    {
        return $this->corridor;
    }

    /**
     * @param string $corridor
     */
    public function setCorridor($corridor)
    {
        $this->corridor = $corridor;
    }

    /**
     * @return string
     */
    public function getShelf()
    {
        return $this->shelf;
    }

    /**
     * @param string $shelf
     */
    public function setShelf($shelf)
    {
        $this->shelf = $shelf;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @param string $layer
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;
    }
}