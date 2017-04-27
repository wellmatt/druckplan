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
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'thirdparty/phpfastcache/phpfastcache.php';
require_once 'libs/modules/accounting/invoiceout.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/basic/csv/CsvWriter.class.php';
require_once 'thirdparty/PDFMerger.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

Global $_USER;
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

$ids = [];
if ($_REQUEST['ids']){
    $ids = explode(',',$_REQUEST['ids']);
}
if ($_REQUEST['version'] == 'email'){
    $version = Document::VERSION_EMAIL;
} else {
    $version = Document::VERSION_PRINT;
}

if (count($ids)>0){

    $pdf = new PDFMerger;

    foreach ($ids as $id) {
        $doc = new Document($id);
        $file = $doc->getFilename($version);
        $pdf->addPDF($file, 'all');
    }

    $pdf->merge('download', 'Invoices.pdf');
}

