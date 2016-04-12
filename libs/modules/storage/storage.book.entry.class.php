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
require_once 'libs/modules/suporder/suporder.class.php';


class StorageBookEnrty extends Model
{
    public $_table = 'storage_book_entries';
    public $area = 0;
    public $article = 0;
    public $type = 0;
    public $origin = 0;
    public $amount = 0;
    public $crtdate = 0;
    public $crtuser = 0;

    protected function bootClasses()
    {
        switch ($this->type) {
            case StorageGoods::TYPE_SUPORDER:
                $this->origin = new SupOrder($this->origin);
                break;
            case StorageGoods::TYPE_COLINV:
                $this->origin = new CollectiveInvoice($this->origin);
                break;
        }
        $this->area = new StorageArea($this->area);
        $this->article = new Article($this->article);
        $this->crtuser = new User($this->crtuser);
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
     * @return CollectiveInvoice|SupOrder
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param CollectiveInvoice|SupOrder $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
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
    public function getCrtdate()
    {
        return $this->crtdate;
    }

    /**
     * @param int $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

    /**
     * @return User
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

    /**
     * @param User $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }
}