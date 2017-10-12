<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
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
require_once 'libs/basic/license/license.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
require_once 'libs/modules/filestorage/filestorage.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;
$_LICENSE = new License();
if (!$_LICENSE->isValid())
    die("No valid licensefile, please contact iPactor GmbH for further assistance");

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $filestorage = new Filestorage($id);
    if ($filestorage->getId() > 0){
        $size = $filestorage->getSize();
        $type = $filestorage->getType();
        $name = $filestorage->getName();
        $content = $filestorage->getContent();

        header("Content-length: $size");
        header("Content-type: $type");
        header("Content-Disposition: attachment; filename=$name");
        echo $content;
    } else {
        die('Fehler: Datei nicht gefunden!');
    }
}