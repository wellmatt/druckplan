<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


class SabreAuthenticate extends Sabre\DAV\Auth\Backend\AbstractBasic{

    /**
     * System user: contilas // password: contilas
     * @param string $user
     * @param string $password
     * @return bool
     */
    protected function validateUserPass($user, $password)
    {
        global $DB;
        if (trim($user) == "company" && (trim($password)) == "contilas"){
            return true;
        } else if (trim($user) != "" || trim($password) != "")
        {
            $sql = " SELECT id FROM user
            WHERE login = '{$user}'
            AND password = md5('{$password}')
            AND user_active > 0
            AND user_level > 0";

            // sql returns only one record -> user is valid
            if($DB->num_rows($sql) == 1)
            {
                return true;
            }
        }
        return false;
    }
}