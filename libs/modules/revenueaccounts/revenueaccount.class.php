<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'libs/basic/model.php';

class RevenueAccount extends Model{
    public $_table = 'revenueaccounts';
    public $title = '';
    public $number = 0;
    public $default = 0;
    public $taxkey;
    public $postage = 0;
    public $affiliatedcompany = 0;

    protected function bootClasses(){
        $this->taxkey = new TaxKey($this->taxkey);
    }

    /**
     * @return RevenueAccount[]
     */
    public static function getAll()
    {
        global $DB;
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

    /**
     * @return TaxKey
     */
    public function getTaxkey()
    {
        return $this->taxkey;
    }

    /**
     * @param TaxKey $taxkey
     */
    public function setTaxkey($taxkey)
    {
        $this->taxkey = $taxkey;
    }

    /**
     * @return int
     */
    public function getPostage()
    {
        return $this->postage;
    }

    /**
     * @param int $postage
     */
    public function setPostage($postage)
    {
        $this->postage = $postage;
    }

    /**
     * @return int
     */
    public function getAffiliatedcompany()
    {
        return $this->affiliatedcompany;
    }

    /**
     * @param int $affiliatedcompany
     */
    public function setAffiliatedcompany($affiliatedcompany)
    {
        $this->affiliatedcompany = $affiliatedcompany;
    }
}