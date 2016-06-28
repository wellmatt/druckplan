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
require_once 'partslist.item.class.php';


class Partslist extends Model{
    public $_table = 'partslists';
    public $title = '';
    public $price = 0.00;
    public $crtdate = 0;
    public $crtuser = 0;

    protected function bootClasses()
    {
        $this->crtuser = new User($this->crtuser);
    }

    /**
     * @return Partslist[]
     */
    public static function getAll()
    {
        $retval = self::fetch();
        return $retval;
    }

    /**
     * @return PartslistItem[]
     */
    public function getMyArticles()
    {
        return PartslistItem::getAllForPartslist($this);
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

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}