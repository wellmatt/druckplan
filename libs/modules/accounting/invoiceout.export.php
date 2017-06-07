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
        'Kunden-Nr.',
        'Debitor-Nr.',
        'Erstellt',
        'Zahlbar bis',
        'Bezahlt am',
        'Buchungsperiode',
        'Status',
        'Bemerkung'
    ];
    $writer->writeHeader($header);
    //write row data
    foreach ($objects as $object) {
        if ($object->getPayeddate() > 0)
            $payeddate = date('d.m.y',$object->getPayeddate());
        else
            $payeddate = '';

        switch($object->getStatus()){
            case 0:
                $status = 'gelöscht';
                break;
            case 1:
                $status = 'offen';
                break;
            case 2:
                $status = 'bezahlt';
                break;
            case 3:
                $status = 'storniert';
                break;
            default:
                $status = 'Unbekannt';
                break;
        }

        $periode = '';
        if (is_a($object,'InvoiceOut')){
            if ($object->getColinv()->getDeliverydate()>0)
                $periode = date('Y',$object->getColinv()->getDeliverydate()).'/'.date('m',$object->getColinv()->getDeliverydate());
            else
                $periode = date('Y',$object->getColinv()->getDate()).'/'.date('m',$object->getColinv()->getDate());
        } else {
            $periode = date('Y',$object->getCrtdate()).'/'.date('m',$object->getCrtdate());
        }

        $writer->writeRow(array(
            $object->getNumber(),
            $object->colinv->getTitle(),
            $object->getNetvalue(),
            ($object->getGrossvalue() - $object->getNetvalue()),
            $object->getGrossvalue(),
            $object->colinv->getCustomer()->getNameAsLine(),
            $object->colinv->getCustomer()->getCustomernumber(),
            $object->colinv->getCustomer()->getDebitor(),
            date('d.m.y',$object->getCrtdate()),
            date('d.m.y',$object->getDuedate()),
            $payeddate,
            $periode,
            $status,
            ''
        ));
    }
    $writer->__destruct();

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename='.$filename);
    header('Pragma: no-cache');
    readfile($csvname);
}