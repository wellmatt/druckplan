<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
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
        'column'=>'crtdate',
        'value'=>$datemax,
        'operator'=>'<='
    ];
}
if ($datemin > 0){
    $filter[] = [
        'column'=>'crtdate',
        'value'=>$datemin,
        'operator'=>'>='
    ];
}

$invoiceouts = InvoiceOut::fetch($filter);

$filename = $_USER->getId() . '-Rechnungsausgang.csv';
$csvname = './docs/'.$filename;
//build the object
$writer = new CsvWriter();
//open a file path
$writer->open($csvname, ';');
//write header array if needed
$header = [
    'Re-Nr.',
    'Auftragstitel',
    'Betrag Netto',
    'MWST',
    'Betrag Brutto',
    'Kunde',
    'Debitor-Nr.',
    'Erstellt',
    'Zahlbar bis',
    'Bezahlt am',
    'Bemerkung'
];
$writer->writeHeader($header);
//write row data
foreach ($invoiceouts as $invoiceout) {
    if ($invoiceout->getPayeddate() > 0)
        $payeddate = date('d.m.y',$invoiceout->getPayeddate());
    else
        $payeddate = '';
    $writer->writeRow(array(
        $invoiceout->getNumber(),
        $invoiceout->colinv->getTitle(),
        $invoiceout->getNetvalue(),
        ($invoiceout->getGrossvalue() - $invoiceout->getNetvalue()),
        $invoiceout->getGrossvalue(),
        $invoiceout->colinv->getCustomer()->getNameAsLine(),
        $invoiceout->colinv->getCustomer()->getDebitor(),
        date('d.m.y',$invoiceout->getCrtdate()),
        date('d.m.y',$invoiceout->getDuedate()),
        $payeddate,
        ''
    ));
}
$writer->__destruct();

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');
readfile($csvname);