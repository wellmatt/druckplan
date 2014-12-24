<?php

error_reporting(-1);
ini_set('display_errors', 1);


require_once("libs/modules/calculation/order.class.php");
require_once('libs/modules/timekeeping/timekeeper.class.php');

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
$_DEBUG = new Debug();
$_LICENSE = new License();




if(!empty($_REQUEST['subExec'])) {
    if('projectTimesExportCSV' == $_REQUEST['subExec']) {
        if(!empty($_REQUEST['orderIds']) && is_array($_REQUEST['orderIds'])) {
            $orderIds = $_REQUEST['orderIds'];
            if(count($orderIds) > 0) {
                require_once $_BASEDIR . 'thirdparty/csv/php-export-data.class.php';
                $writer = new ExportDataCSV('browser', mb_strtolower('projekt-zeiten.csv'));
                $writer->initialize();
                $writer->addRow(array('Projekt-Zeiten, Stand: ' . date('d.m.Y')));
                $writer->addRow(array('Auftrag', 'Kunde', 'Ticket', 'Buchungen', 'Zeitaufwand (insg.)', 'Zeitaufwand (Sek.)'));

                foreach($orderIds as $orderId) {



                    $order = new Order($orderId);
                    $timeStats = Timekeeper::getOrderTimes($order->getId());
                    $secondsTotal = 0;

                    $writer->addRow(array($order->getTitle(), $order->getCustomer()->getNameAsLine(), '', '', '', ''));

                    foreach($timeStats as $stat) {

                        $secondsTotal += $stat['seconds'];
                        $commentsArray = explode('|', $stat['projectComments']);
                        $comments = implode(', ' . PHP_EOL, $commentsArray);
                        $writer->addRow(array('', '', $stat['tkt_title'], $comments, formatSeconds($stat['seconds']), $stat['seconds']));
                        $writer->addRow(array('Gesamt-Zeitaufwand für dieses Projekt', '', '', '', formatSeconds($secondsTotal), $secondsTotal));

                    }


                }

                $writer->finalize();
                exit;


            }
        }
    }
}
?>