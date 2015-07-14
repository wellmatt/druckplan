<?php 
require_once 'libs/basic/basic.importer.php';
require_once './libs/modules/documents/document.class.php';
require_once './libs/modules/personalization/personalization.class.php';
require_once './libs/modules/personalization/personalization.item.class.php';
require_once './libs/modules/personalization/personalization.order.class.php';
require_once './libs/modules/personalization/personalization.orderitem.class.php';

// phpinfo();
// error_reporting(-1);
// ini_set('display_errors', 1);

// Require Library
require_once 'libs/basic/basic.importer.php';
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/tickets/ticket.category.class.php';
require_once 'libs/modules/article/article.class.php';
// simple Caching with:
// $cache = phpFastCache("memcache");


$start = mktime(0,0,0,date("m"),date("d"),date("Y"));
$end = strtotime('+2 month',$start);

$time_start = microtime(true);


$arrayRange=array();
$dateFrom=$start;
$dateTo=$end;

if ($dateTo>=$dateFrom)
{
    array_push($arrayRange,date('Y-m-d',$dateFrom));
    while ($dateFrom<$dateTo)
    {
        $dateFrom+=86400;
        array_push($arrayRange,date('Y-m-d',$dateFrom));
    }
}



$time_end = microtime(true);
$time = $time_end - $time_start;
echo "done in " . $time . " seconds</br>";

$time_start = microtime(true);

$arrayRange2=array();

for ( $i = $start; $i <= $end; $i = $i + 86400 ) {
    $date = mktime(0,0,0,date("m",$i),date("d",$i),date("Y",$i));
    array_push($arrayRange2,date('Y-m-d',$date));
}


$time_end = microtime(true);
$time = $time_end - $time_start;
echo "done in " . $time . " seconds";


// Cachehandler::removeCache("menu_getcached_5");

// $ticket = new Ticket(5);
// $ticket = Cachehandler::fromCache("test_ticket");

// var_dump($ticket);
// Cachehandler::toCache("test_ticket", $ticket);

// die();

// $time_start = microtime(true);
// $tcs = $cache->get("tcs");
// $cache->driver_clean();

// if($tcs == null) {
//     $tcs = new User(5);
// 	var_dump($tcs);
//     $time_end = microtime(true);
//     $time = $time_end - $time_start;
//     echo "done in " . $time . " seconds";
//     $cache->set("tcs",$tcs , 30);
// } else {
//     var_dump($tcs);
//     $time_end = microtime(true);
//     $time = $time_end - $time_start;
//     echo "cached done in " . $time . " seconds";
// }

?>