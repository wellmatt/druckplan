<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/basic/user/emailaddress.class.php';

if ($_REQUEST["ajax_action"] == "star_email"){
    if ($_REQUEST["mailaddress"] && $_REQUEST["user"]){
        $mailaddress = new Emailaddress((int)$_REQUEST["mailaddress"]);
        $user = new User((int)$_REQUEST["user"]);
        Emailaddress::setDefaultForUser($mailaddress,$user);
    }
}
if ($_REQUEST["ajax_action"] == "remove_email"){
    if ($_REQUEST["mailaddress"] && $_REQUEST["user"]){
        $mailaddress = new Emailaddress((int)$_REQUEST["mailaddress"]);
        $user = new User((int)$_REQUEST["user"]);
        Emailaddress::unassignFromUser($mailaddress,$user);
    }
}