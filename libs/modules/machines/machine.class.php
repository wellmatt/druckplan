<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class Machine extends Model{
    public $state = 1;
    public $class = 0;
    public $type = 0;
    public $title = '';
    public $pricebase = 0;
    public $papersizeheight = 0.0;
    public $papersizewidth = 0.0;
    public $papersizeminheight = 0.0;
    public $papersizeminwidth = 0.0;
    public $description = '';
    public $model = '';
    public $constructionyear = 0;
    public $interfaceurl = '';

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
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param int $class
     */
    public function setClass($class)
    {
        $this->class = $class;
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
     * @return int
     */
    public function getPricebase()
    {
        return $this->pricebase;
    }

    /**
     * @param int $pricebase
     */
    public function setPricebase($pricebase)
    {
        $this->pricebase = $pricebase;
    }

    /**
     * @return float
     */
    public function getPapersizeheight()
    {
        return $this->papersizeheight;
    }

    /**
     * @param float $papersizeheight
     */
    public function setPapersizeheight($papersizeheight)
    {
        $this->papersizeheight = $papersizeheight;
    }

    /**
     * @return float
     */
    public function getPapersizewidth()
    {
        return $this->papersizewidth;
    }

    /**
     * @param float $papersizewidth
     */
    public function setPapersizewidth($papersizewidth)
    {
        $this->papersizewidth = $papersizewidth;
    }

    /**
     * @return float
     */
    public function getPapersizeminheight()
    {
        return $this->papersizeminheight;
    }

    /**
     * @param float $papersizeminheight
     */
    public function setPapersizeminheight($papersizeminheight)
    {
        $this->papersizeminheight = $papersizeminheight;
    }

    /**
     * @return float
     */
    public function getPapersizeminwidth()
    {
        return $this->papersizeminwidth;
    }

    /**
     * @param float $papersizeminwidth
     */
    public function setPapersizeminwidth($papersizeminwidth)
    {
        $this->papersizeminwidth = $papersizeminwidth;
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
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return int
     */
    public function getConstructionyear()
    {
        return $this->constructionyear;
    }

    /**
     * @param int $constructionyear
     */
    public function setConstructionyear($constructionyear)
    {
        $this->constructionyear = $constructionyear;
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
}