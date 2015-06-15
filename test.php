<?php 
phpinfo();
// error_reporting(-1);
// ini_set('display_errors', 1);

// Require Library
// require_once 'libs/basic/basic.importer.php';
// require_once "thirdparty/phpfastcache/phpfastcache.php";
// require_once 'libs/modules/tickets/ticket.class.php';
// require_once 'libs/modules/tickets/ticket.category.class.php';
// simple Caching with:
// $cache = phpFastCache("memcache");
// var_dump($cache->instant->getExtendedStats('cachedump', 5, 1000));

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