<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			19.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/timer/timer.class.php';

if ($_REQUEST['module'] && $_REQUEST['objectid']){
    if ($_REQUEST["ajax_action"] == "start"){
        $timer = new Timer();
        $timer->start($_REQUEST['module'], $_REQUEST['objectid']);
        $timer->save();
        echo $timer->getId();
    }
    if ($_REQUEST["ajax_action"] == "stop"){
        $timer = Timer::getLastUsed();
        if ($timer->getState() == Timer::TIMER_RUNNING){
            $timer->stop();
            $timer->save();
            echo $timer->getId();
        } else {
            echo "0";
        }
    }
}


?>