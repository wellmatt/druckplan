<?php 
error_reporting(-1);
ini_set('display_errors', 1);
require_once 'libs/basic/basic.importer.php';
require_once './libs/modules/documents/document.class.php';
require_once './libs/modules/personalization/personalization.class.php';
require_once './libs/modules/personalization/personalization.item.class.php';
require_once './libs/modules/personalization/personalization.order.class.php';
require_once './libs/modules/personalization/personalization.orderitem.class.php';
require_once 'libs/basic/basic.importer.php';
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/tickets/ticket.category.class.php';
require_once 'libs/modules/article/article.class.php';



$now = strtotime("+60 Minutes");
$now = date ("Y-m-d H:i:s",$now);

var_dump($now);
?>