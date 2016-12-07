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

// Login
$vp_user = User::getAllUserFiltered(User::ORDER_ID," AND login = 'viaprinto' ");
if (count($vp_user) == 1){
    $_USER = $vp_user[0];
    $postData = file_get_contents('php://input');
    $xml = simplexml_load_string($postData);
    if ($xml){
        $colinv = new CollectiveInvoice();
        $colinv->setClient(new Client(1));
        $colinv->setBusinesscontact(new BusinessContact(1));
        $colinv->setCustContactperson(new ContactPerson(1));
        $colinv->setTitle($xml->Order->OrderNumber->__toString());
        $shippingaddress = "{$xml->ShipAddresses->Address->Company->__toString()}\r\n";
        $shippingaddress .= "{$xml->ShipAddresses->Address->FirstName->__toString()} {$xml->ShipAddresses->Address->LastName->__toString()}\r\n";
        $shippingaddress .= "{$xml->ShipAddresses->Address->Street->__toString()}\r\n";
        $shippingaddress .= "{$xml->ShipAddresses->Address->Zip->__toString()} {$xml->ShipAddresses->Address->City->__toString()}\r\n";
        $shippingaddress .= "{$xml->ShipAddresses->Address->Email->__toString()}\r\n";
        $colinv->setComment($shippingaddress);

        $article = "Format: {$xml->Article->Format->__toString()}<br>";
        $article .= "SpecialFormat: {$xml->Article->SpecialFormat->__toString()}<br>";
        $article .= "Papier: {$xml->Article->Papier->__toString()}<br>";
        $article .= "Farben: {$xml->Article->Farben->__toString()}<br>";
        $article .= "Verarbeitung: {$xml->Article->Verarbeitung->__toString()}<br>";
        $article .= "Veredelung: {$xml->Article->Veredelung->__toString()}<br>";
        $article .= "Veredelungseigenschaft: {$xml->Article->Veredelungseigenschaft->__toString()}<br>";
        $article .= "http://viaprinto.de/{$xml->Article->Content_PDF->__toString()}";
        $orderpos = new Orderposition();
        $orderpos->setComment($article);
        $orderpos->setType(Orderposition::TYPE_MANUELL);
        $orderpos->setQuantity($xml->Article->Quantity->__toString());
        $orderpos->setObjectid(0);
        $orderpos->setStatus(1);
        $res = $colinv->save();
        if ($res){
            $orderpos->setCollectiveinvoice($colinv->getId());
            $opres = Orderposition::saveMultipleOrderpositions([$orderpos]);
        }
    } else {
        die("Fehler beim lesen der XML");
    }
} else {
    die("Kein Benutzer 'viaprinto' gefunden");
}

if (!$opres || $opres == NULL || !isset($opres)){
    die ("Fehler aufgetreten");
} else {
    echo "Auftrag erstellt!";
}