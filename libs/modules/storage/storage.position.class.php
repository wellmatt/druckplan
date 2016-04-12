<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/storage/storage.area.class.php';
require_once 'libs/modules/storage/storage.goods.class.php';

class StoragePosition extends Model
{
    public $_table = 'storage_positions';
    public $area = 0;
    public $article = 0;
    public $businesscontact = 0;
    public $amount = 0;
    public $min_amount = 0;
    public $respuser = 0;
    public $description = '';
    public $note = '';
    public $dispatch = '';
    public $packaging = '';
    public $allocation = '';

    protected function bootClasses()
    {
        $this->area = new StorageArea($this->area);
        $this->businesscontact = new BusinessContact($this->businesscontact);
        $this->respuser = new User($this->respuser);
        $this->article = new Article($this->article);
    }

    /**
     * @param StorageArea $storagearea
     * @return StoragePosition[]
     */
    public static function getAllForArea(StorageArea $storagearea)
    {
        $retval = self::fetch([
            [
                'column'=>'area',
                'value'=>$storagearea->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * Holt die aktuelle Belegung des Lagerplatzes aus der DB
     * @param StorageArea $storageArea
     * @return int
     */
    public static function getAllocationForArea(StorageArea $storageArea)
    {
        global $DB;
        $retval = 0;
        $sql = "SELECT SUM(allocation) as allocation FROM storage_positions WHERE area = {$storageArea->getId()}";
        if($DB->num_rows($sql)){
            $r = $DB->select($sql);
            $retval = $r[0]['allocation'];
        }
        return $retval;
    }

    /**
     * @return StorageArea
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param StorageArea $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return BusinessContact
     */
    public function getBusinesscontact()
    {
        return $this->businesscontact;
    }

    /**
     * @param BusinessContact $businesscontact
     */
    public function setBusinesscontact($businesscontact)
    {
        $this->businesscontact = $businesscontact;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getMinAmount()
    {
        return $this->min_amount;
    }

    /**
     * @param int $min_amount
     */
    public function setMinAmount($min_amount)
    {
        $this->min_amount = $min_amount;
    }

    /**
     * @return User
     */
    public function getRespuser()
    {
        return $this->respuser;
    }

    /**
     * @param User $respuser
     */
    public function setRespuser($respuser)
    {
        $this->respuser = $respuser;
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
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getDispatch()
    {
        return $this->dispatch;
    }

    /**
     * @param string $dispatch
     */
    public function setDispatch($dispatch)
    {
        $this->dispatch = $dispatch;
    }

    /**
     * @return string
     */
    public function getPackaging()
    {
        return $this->packaging;
    }

    /**
     * @param string $packaging
     */
    public function setPackaging($packaging)
    {
        $this->packaging = $packaging;
    }

    /**
     * @return string
     */
    public function getAllocation()
    {
        return $this->allocation;
    }

    /**
     * @param string $allocation
     */
    public function setAllocation($allocation)
    {
        $this->allocation = $allocation;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }
}