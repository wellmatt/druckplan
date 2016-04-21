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
require_once 'libs/modules/storage/storage.goods.position.class.php';
require_once 'libs/modules/storage/storage.book.entry.class.php';


class StorageGoods extends Model{
    public $_table = 'storage_goods';
    public $origin = 0;
    public $type = 0;
    public $crtdate = 0;
    public $crtuser = 0;

    const TYPE_SUPORDER = 1;
    const TYPE_COLINV = 2;

    protected function bootClasses()
    {
        switch ($this->type){
            case self::TYPE_SUPORDER:
                $this->origin = new SupOrder($this->origin);
                break;
            case self::TYPE_COLINV:
                $this->origin = new CollectiveInvoice($this->origin);
                break;
        }
        $this->crtuser = new User($this->crtuser);
    }

    /**
     * @return StorageGoods[]
     */
    public static function getAllIncoming()
    {
        $retval = self::fetch([
            [
                'column'=>'type',
                'value'=>1
            ]
        ]);
        return $retval;
    }

    /**
     * @return StorageGoods[]
     */
    public static function getAllOutgoing()
    {
        $retval = self::fetch([
            [
                'column'=>'type',
                'value'=>2
            ]
        ]);
        return $retval;
    }

    /**
     * @return SupOrder|CollectiveInvoice
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param SupOrder|CollectiveInvoice $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
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