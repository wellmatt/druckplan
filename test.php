<?php
error_reporting(-1);
ini_set('display_errors', 1);
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
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

$_CACHE = phpFastCache("memcached");

//$_CACHE->clean();

//$test = Cachehandler::fromCache('cc4c4a5dfgfca54345ddg2343f511399de3c49_User_5');
//prettyPrint($test);
//
//$m = new Memcached();
//$m->addServers(array(array('127.0.0.1',11211)));
//
//prettyPrint($m->getAllKeys());

//$bc = new BusinessContact(1);
//prettyPrint($bc);

//prettyPrint($m->getAllKeys());

$start_time = microtime(TRUE);

$user = new Calculation(435);

$end_time = microtime(TRUE);
$shorten = number_format($end_time - $start_time,4);
prettyPrint('Execution time: '.$shorten);