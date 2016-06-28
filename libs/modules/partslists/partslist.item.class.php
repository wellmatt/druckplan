<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/partslists/partslist.class.php';


class PartslistItem extends Model{
    public $_table = 'partslists_items';
    public $partslist = 0;
    public $article = 0;
    public $amount = 0.00;

    protected function bootClasses()
    {
        $this->partslist = new Partslist($this->partslist);
        $this->article = new Article($this->article);
    }

    /**
     * @return PartslistItem[]
     */
    public static function getAll()
    {
        $retval = self::fetch();
        return $retval;
    }

    /**
     * @param Partslist $partslist
     * @return PartslistItem[]
     */
    public static function getAllForPartslist(Partslist $partslist)
    {
        $retval = self::fetch([
            [
                'column'=>'partslist',
                'value'=>$partslist->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return Partslist
     */
    public function getPartslist()
    {
        return $this->partslist;
    }

    /**
     * @param Partslist $partslist
     */
    public function setPartslist($partslist)
    {
        $this->partslist = $partslist;
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
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}