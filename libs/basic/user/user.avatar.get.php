<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

function defaultimg(){
    $str = file_get_contents('images/icons/image.png');
    $im = imagecreatefromstring($str);
    if ($im !== false) {
        header('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);
    }
    else {
        echo 'An error occurred.';
    }
}

if ($_REQUEST['uid'] && $_REQUEST['uid'] > 0){
    $user = new User((int)$_REQUEST['uid']);
    if ($user->getAvatar() != null && $user->getAvatar() != ''){
        $data = $user->getAvatar();

        $im = imagecreatefromstring($data);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
        } else {
            defaultimg();
        }
    } else {
        defaultimg();
    }
} else {
    defaultimg();
}
