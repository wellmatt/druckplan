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
require_once 'libs/modules/accounting/invoicein.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/basic/csv/CsvWriter.class.php';
require_once 'libs/modules/saxoprint/saxoprint.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

Global $_USER;
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);


$date_min = $_REQUEST['date_min'];
$date_max = $_REQUEST['date_max'];
$saxomaterial = $_REQUEST['saxomaterial'];
$saxoformat = $_REQUEST['saxoformat'];
$saxoprodgrp = $_REQUEST['saxoprodgrp'];

$colinvs = CollectiveInvoice::getAllSaxoOpen($date_min,$date_max,$saxomaterial,$saxoformat,$saxoprodgrp);

$filename = $_USER->getId() . '-SaxoAuftraege.csv';
$csvname = './docs/'.$filename;
//build the object
$writer = new CsvWriter();
//open a file path
$writer->open($csvname, ';');
//write header array if needed
$header = [
    'VO-Nr.',
    'ContractID',
    'ReferenzID',
    'Compl.Date',
    'Material',
    'Format',
    'Auflage',
    'Farbe',
    'Stanzen',
    'Form',
    'Buchstanze',
    'Logistik',
    'Bemerkung',
    'Status'
];
$writer->writeHeader($header);
//write row data
foreach ($colinvs as $colinv) {
    $saxoinfo = $colinv->getSaxoInfo();
    $date = date('d-m-Y',$saxoinfo->getCompldate());

    $writer->writeRow(array(
        $colinv->getNumber(),
        $saxoinfo->getContractid(),
        $colinv->getSaxoid(),
        $date,
        $saxoinfo->getMaterial(),
        $saxoinfo->getFormat(),
        $saxoinfo->getAmount(),
        $saxoinfo->getChroma(),
        $saxoinfo->getStamp(),
        $saxoinfo->getForm(),
        $saxoinfo->getBookstamp(),
        $saxoinfo->getLogistic(),
        '',
        $colinv->getStatusDescription(),
    ));
}
$writer->__destruct();

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');
readfile($csvname);