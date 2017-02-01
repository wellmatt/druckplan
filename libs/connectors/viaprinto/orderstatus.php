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
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'thirdparty/phpfastcache/phpfastcache.php';
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/chat/chat.class.php';
require_once 'libs/modules/calculation/order.class.php';

session_start();
global $_LANG;
global $_CONFIG;

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

// TODO: Abfrage Auftragsstatus mittels OrderNumber (Titel bei uns)

if ($_REQUEST["ordernumber"]){
    $order = CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_NUMBER," AND title LIKE '%{$_REQUEST["OrderNumber"]}%'");
    if (count($order) > 0){
        $order = $order[0];
        // TODO: print XML for Status Response
    } else {
        die("Kein Vorgang gefunden");
    }
} else {
    die("Keine Auftragsnummer empfangen");
}