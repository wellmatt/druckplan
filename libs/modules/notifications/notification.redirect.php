<?php

require_once 'libs/modules/notifications/notification.class.php';

if ($_REQUEST["exec"] == "redirect" && $_REQUEST["nid"]){
    $notification = new Notification((int)$_REQUEST["nid"]);
    $notification->setState(0);
    $notification->save();
    ob_clean();
    header('Location: '.$notification->getPath());
}