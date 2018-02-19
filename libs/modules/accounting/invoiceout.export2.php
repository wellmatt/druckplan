<?php
/**
 *  Copyright (c) 2018 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2018
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
require_once 'libs/modules/accounting/revert.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

Global $_USER;
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);


$objects = [];
if (strlen($_REQUEST['param'])>0){
    $params = json_decode($_REQUEST['param']);
    if (count($params)>0){
        foreach ($params as $param) {
            if ($param[1] == 1)
                $objects[] = new InvoiceOut($param[0]);
            elseif ($param[1] == 2)
                $objects[] = new Revert($param[0]);
        }
    }
}

if (count($objects)>0){
    $filename = $_USER->getId() . '-Rechnungsausgang.txt';
    $csvname = './docs/'.$filename;
    //build the object
    $writer = new CsvWriter();
    //open a file path
    $writer->open($csvname, "\t", 'r+', '"');
    //write header array if needed
    $header = [
        'Belegdatum',
        'Belegnummernkreis',
        'Belegnummer',
        'Kundennummer',
        'Buchungstext',
        'Buchungsbetrag',
        'Sollkonto',
        'Habenkonto',
        'Währung'
    ];
    $writer->writeHeader($header);
    //write row data
    foreach ($objects as $object) {

        $vz = '';
        if (is_a($object,'Revert'))
            $vz = '-';

        if ($object->getPayeddate() > 0)
            $payeddate = date('d.m.y',$object->getPayeddate());
        else
            $payeddate = '';

        $writer->writeRow(array(
            date('d.m.y',$object->getCrtdate()), // Belegdatum
            'AR', // Belegnummernkreis
            $object->getNumber(), // Belegnummer
            $object->colinv->getCustomer()->getCustomernumber(), // Kundennummer
            $object->colinv->getCustomer()->getNameAsLine(), // Buchungstext
            $vz.$object->getGrossvalue(), // Buchungsbetrag
            '', // Sollkonto
            '4400', // Habenkonto
            'EUR' // Währung
        ));

    }
    $writer->__destruct();

    // workaround for enclosure
    $file_contents = file_get_contents($csvname);
    $file_contents = str_replace('"',"",$file_contents);
    file_put_contents($csvname,$file_contents);

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename='.$filename);
    header('Pragma: no-cache');
    readfile($csvname);
}