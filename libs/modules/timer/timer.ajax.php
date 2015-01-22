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
require_once 'libs/modules/perferences/perferences.class.php';

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
            
            $perf = new Perferences();
            if ($perf->getDefault_ticket_id() > 0){
                $tmp_def_ticket = new Ticket($perf->getDefault_ticket_id());
                $tmp_ticket_id = $tmp_def_ticket->getId();
            
                $logintimer = new Timer();
                $logintimer->setObjectid($tmp_ticket_id);
                $logintimer->setModule("Ticket");
                $now = time();
                $logintimer->setStarttime($now);
                $logintimer->setState(Timer::TIMER_RUNNING);
                $logintimer->save();
            }
            
        } else {
            echo "0";
        }
    }
}


?>