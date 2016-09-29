<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/collectiveinvoice/contentpdf.class.php';
require_once 'libs/basic/files/file.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

if ($_REQUEST["opid"]){
    $orderposition = new Orderposition((int)$_REQUEST["opid"]);
    $colinv = new CollectiveInvoice($orderposition->getCollectiveinvoice());
    $contentpdfs = ContentPdf::getAllForOrderposition($orderposition);
    $files = [];
    foreach ($contentpdfs as $contentpdf) {
        switch ($contentpdf->part){
            case 1:
                $content = 'Inhalt 1';
                break;
            case 2:
                $content = 'Inhalt 2';
                break;
            case 3:
                $content = 'Umschlag';
                break;
            case 4:
                $content = 'Inhalt 3';
                break;
            case 5:
                $content = 'Inhalt 4';
                break;
        }
        $files[] = ['path'=>$contentpdf->getFile()->getFileUrl(),'name'=>$content.'/Seite_'.$contentpdf->getPagenum().'.pdf'];
    }

    $file = tempnam("tmp", "zip");
    $zip = new ZipArchive();
    $zip->open($file, ZipArchive::OVERWRITE);

    // Stuff with content
    foreach ($files as $pdf) {
//        $zip->addFile('../../../'.$pdf);
        $zip->addFile($pdf['path'],$pdf['name']);
    }

    // Close and send to users
    $zip->close();
    header('Content-Type: application/zip');
    $length = filesize($file);
    header('Content-Length: ' . $length);
    header('Content-Disposition: attachment; filename="'.$colinv->getNumber().'.zip"');

    readfile($file);
//    unlink($file);
} else {
    die('Keine Auftragsposition angegeben!');
}