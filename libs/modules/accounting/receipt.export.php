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
require_once 'receipt.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

Global $_USER;
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

$datemax = $_REQUEST["datemax"];
$datemin = $_REQUEST["datemin"];

$filter = [];
if ($datemax > 0){
    $filter[] = [
        'column'=>'date',
        'value'=>$datemax,
        'operator'=>'<='
    ];
}
if ($datemin > 0){
    $filter[] = [
        'column'=>'date',
        'value'=>$datemin,
        'operator'=>'>='
    ];
}

$receipts = Receipt::fetch($filter);

$filename = $_USER->getId() . '-Buchungsexport.csv';
$csvname = './docs/'.$filename;
//build the object
$writer = new CsvWriter();
//open a file path
$writer->open($csvname, ';');
//write header array if needed
$header = [
    'Dok-Nr.',
    'Typ',
    'Betrag Netto',
    'Betrag Brutto',
    'Erstellt',
    'Exportiert'
];
$writer->writeHeader($header);
//write row data
foreach ($receipts as $receipt) {

    if ($receipt->getOriginType() == Receipt::ORIGIN_INVOICE) {
        $type = 'Rechnung';
        $vorzeichen = '';
    } else {
        $type = 'Gutschrift';
        $vorzeichen = '-';
    }
    if ($receipt->getExported()>0)
        $exported = date('d.m.y',$receipt->getExported());
    else
        $exported = '';

    $writer->writeRow(array(
        $receipt->getNumber(),
        $type,
        $vorzeichen.$receipt->getOrigin()->getNetvalue(),
        $vorzeichen.$receipt->getOrigin()->getGrossvalue(),
        date('d.m.y',$receipt->getDate()),
        $exported
    ));
}
$writer->__destruct();

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');
readfile($csvname);