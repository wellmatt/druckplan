<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'libs/basic/model.php';
require_once 'revenueaccount.category.class.php';

class RevenueAccount extends Model{
    public $_table = 'revenueaccounts';

    public $title = '';
    public $revenueaccountcategory;
    public $number = 0;
    public $default = 0;
    public $taxkey;
    public $postage = 0;
    public $affiliatedcompany = 0;

    protected function bootClasses(){
        $this->taxkey = new TaxKey($this->taxkey);
        $this->revenueaccountcategory = new RevenueaccountCategory($this->revenueaccountcategory);
    }

    /**
     * @return RevenueAccount[]
     */
    public static function getAll()
    {
        global $DB;
        return self::fetch();
    }

    /**
     * @param $category RevenueaccountCategory
     * @return RevenueAccount[]
     */
    public static function fetchAllForCategory(RevenueaccountCategory $category)
    {
        $ret = self::fetch([
            [
                "column" => "revenueaccountcategory",
                "value" => $category->getId()
            ]
        ]);
        return $ret;
    }

    /**
     * @param $revenueaccountCategory RevenueaccountCategory
     * @param $taxKey TaxKey
     * @return RevenueAccount
     */
    public static function fetchForCategoryAndTaxkeyOrDefault(RevenueaccountCategory $revenueaccountCategory, TaxKey $taxKey)
    {
        $ret = self::fetchSingle([
            [
                "column" => "revenueaccountcategory",
                "value" => $revenueaccountCategory->getId()
            ],
            [
                "column" => "taxkey",
                "value" => $taxKey->getId()
            ]
        ]);
        if ($ret->getId() == 0){
            return self::fetchDefaultForCategory($revenueaccountCategory);
        } else {
            return $ret;
        }
    }

    /**
     * @param $revenueaccountCategory RevenueaccountCategory
     * @param $taxKey TaxKey
     * @return RevenueAccount
     */
    public static function fetchAffiliatedForCategoryAndTaxkeyOrDefault(RevenueaccountCategory $revenueaccountCategory, TaxKey $taxKey)
    {
        $ret = self::fetchSingle([
            [
                "column" => "revenueaccountcategory",
                "value" => $revenueaccountCategory->getId()
            ],
            [
                "column" => "taxkey",
                "value" => $taxKey->getId()
            ],
            [
                "column" => "affiliatedcompany",
                "value" => 1
            ]
        ]);
        if ($ret->getId() == 0){
            return self::fetchForCategoryAndTaxkeyOrDefault($revenueaccountCategory, $taxKey);
        } else {
            return $ret;
        }
    }

    /**
     * @param $category RevenueaccountCategory
     * @return RevenueAccount
     */
    public static function fetchDefaultForCategory(RevenueaccountCategory $category)
    {
        $ret = self::fetchSingle([
            [
                "column" => "revenueaccountcategory",
                "value" => $category->getId()
            ],
            [
                "column" => "default",
                "value" => 1
            ]
        ]);
        return $ret;
    }

    public function star()
    {
        $allincategory = RevenueAccount::fetchAllForCategory($this->revenueaccountcategory);
        foreach ($allincategory as $item) {
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

    /**
     * @return RevenueaccountCategory
     */
    public function getRevenueaccountcategory()
    {
        return $this->revenueaccountcategory;
    }

    /**
     * @param RevenueaccountCategory $revenueaccountcategory
     */
    public function setRevenueaccountcategory($revenueaccountcategory)
    {
        $this->revenueaccountcategory = $revenueaccountcategory;
    }
}