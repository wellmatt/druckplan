<?php

//     error_reporting(-1);
//     ini_set('display_errors', 1); 
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/notifications/notification.class.php';

global $_USER;


$mynotifications = Notification::getMyNotifications();
// var_dump($mynotifications);
if (count($mynotifications) > 0){
    $html = "<table border='0' width='100%'>";
    foreach ($mynotifications as $notification){
        $html .= '<tr>';
        $html .= '<td>'.$notification->getCrtmodule().'</td>';
        $html .= '<td>'.date("d.m.Y H:i",$notification->getCrtdate()).'</td>';
        $html .= '<td><a href="'.$notification->getPath().'">'.$notification->getTitle().'</a></td>';
        $html .= '</tr>';
    }
    $html .= "</table>";
} else {
    $html = "Keine neuen Benachrichtigungen!";
}

echo $html;