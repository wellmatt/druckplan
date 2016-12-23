<?php
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
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
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'libs/basic/eventqueue/eventqueue.class.php';
require_once 'libs/basic/eventqueue/eventclass.interface.php';
require_once 'libs/modules/mail/mailmassage.class.php';
require_once 'libs/modules/organizer/caldav.service.class.php';
require_once 'libs/modules/storage/storage.position.class.php';

require_once 'vendor/PEAR/Net/SMTP.php';
require_once 'vendor/PEAR/Net/Socket.php';
require_once 'vendor/Horde/Autoloader.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper/Default.php';
$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default('vendor'));
$autoloader->registerAutoloader();

require_once('vendor/simpleCalDAV/SimpleCalDAVClient.php');

error_reporting(-1);
ini_set('display_errors', 1);
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


$now = time();
$aday = 86400;
$netvalue = 0.0;
$grossvalue = 0.0;
$cost = 0.0;


$duedate = ($now + ($nettodays * $aday));

$tax = [];
$positions = Orderposition::getAllOrderposition(120);
prettyPrint($positions);
foreach ($positions as $position) {
    if ($position->getStatus() == 1 && $position->getInvrel() == 1){
        $art = new Article($position->getObjectid());
        if ($art->getOrderid() > 0){
            $netto = $position->getPrice();
            $postax = $position->getTax();
            $poscost = $position->getCost();
            $tax[$postax][] = [$netto,$poscost];
        } else {
            $netto = $position->getPrice() * $position->getAmount();
            $postax = $position->getTax();
            $poscost = $position->getCost() * $position->getAmount();
            $tax[$postax][] = [$netto,$poscost];
        }
    }
}

foreach ($tax as $mwst => $items) {
    foreach ($items as $item) {
        $netvalue += $item[0];
        $grossvalue += $item[0] * (1 + $mwst/100);
        $cost += $item[1];
    }
}
