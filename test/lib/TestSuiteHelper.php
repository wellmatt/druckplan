<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class TestSuiteHelper{
    private $DB;


    /**
     * TestSuiteHelper constructor.
     * @param $DB DBMysql
     */
    public function __construct($DB)
    {
        $this->DB = $DB;
    }

    public function generateError($class, $test, $message, $more = '')
    {
        return [ $class, $test, $message, $more ];
    }

    public function BusinessContactGetRandom()
    {
        $sql = "SELECT id FROM businesscontact WHERE active = 1";
        $res = $this->DB->select($sql);
        if (count($res) > 0){
            return array_rand($res);
        } else {
            return false;
        }
    }

    public function UserGetRandom()
    {
        $sql = "SELECT id FROM user WHERE user_active = 1";
        $res = $this->DB->select($sql);
        if (count($res) > 0){
            return array_rand($res);
        } else {
            return false;
        }
    }

    public function ArticleGetRandom()
    {
        $sql = "SELECT id FROM article WHERE `status` = 1";
        $res = $this->DB->select($sql);
        if (count($res) > 0){
            return array_rand($res);
        } else {
            return false;
        }
    }
}