<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

require_once 'libs/basic/model.php';

class RevenueAccount extends Model{
    public $_table = 'revenueaccounts';
    public $title = '';
    public $number = 0;
    public $default = 0;

    /**
     * @return RevenueAccount[]
     */
    public static function getAll()
    {
        return self::fetch();
    }

    public function star()
    {
        $all = self::getAll();
        foreach ($all as $item) {
            if ($item->getDefault() == 1) {
                $item->setDefault(0);
                $item->save();
            }
        }
        $this->default = 1;
        $this->save();
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
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param int $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }
}