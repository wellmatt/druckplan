<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'libs/basic/model.php';

class RevenueaccountCategory extends Model{
    public $_table = 'revenueaccounts_categories';

    public $title = '';

    /**
     * @return RevenueaccountCategory[]
     */
    public static function getAll()
    {
        return self::fetch();
    }

    /**
     * @return RevenueAccount[]
     */
    public function getRevenueaccounts()
    {
        return RevenueAccount::fetchAllForCategory($this);
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
}