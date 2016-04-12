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


class StorageGoodsPosition extends Model{
    public $_table = 'storage_positions';
    public $goods = 0;
    public $article = 0;
    public $amount = 0;

    protected function bootClasses()
    {
        $this->goods = new StorageGoods($this->goods);
        $this->article = new Article($this->article);
    }

    /**
     * @param StorageGoods $storageGoods
     * @return StorageGoodsPosition[]
     */
    public static function getAllForGoods(StorageGoods $storageGoods)
    {
        $retval = self::fetch([
            [
                'column'=>'goods',
                'value'=>$storageGoods->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return StorageGoods
     */
    public function getGoods()
    {
        return $this->goods;
    }

    /**
     * @param StorageGoods $goods
     */
    public function setGoods($goods)
    {
        $this->goods = $goods;
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
}