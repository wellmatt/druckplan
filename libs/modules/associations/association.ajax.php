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
require_once 'libs/modules/organizer/event.class.php';

// error_reporting(-1); 
// ini_set('display_errors', 1);
 
/*
 * Suchfelder
 */

if ($_REQUEST["ajax_action"] == "delete_asso"){
    if ($_REQUEST["id"])
    {
        $del_asso = new Association((int)$_REQUEST["id"]);
        $del_asso->delete();
    }
}
if ($_REQUEST["ajax_action"] == "search_calc"){
    $retval = Array();
    $orders = Order::getAllOrders(Order::ORDER_NUMBER, " AND (number LIKE '%{$_REQUEST['term']}%' OR title LIKE '%{$_REQUEST['term']}%') ");
    foreach ($orders as $order){
        $retval[] = Array("label" => $order->getNumber(), "value" => $order->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_colinv"){
    $retval = Array();
    $colinvoices = CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_NUMBER, " AND (number LIKE '%{$_REQUEST['term']}%' OR title LIKE '%{$_REQUEST['term']}%') ");
    foreach ($colinvoices as $colinvoice){
        $retval[] = Array("label" => $colinvoice->getNumber(), "value" => $colinvoice->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_colinv2"){
	$retval = Array();
	$retval = CollectiveInvoice::searchByNumberOrTitle($_REQUEST['term']);
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_event"){
    $retval = Array();
    $events = Event::getAllEventsForHome($order = self::ORDER_BEGIN, $_REQUEST['term']);
    foreach ($events as $event){
        $retval[] = Array("label" => $event->getTitle(), "value" => $event->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_schedule"){
    $retval = Array();
    $schedules = Schedule::getAllSchedulesForHome(Schedule::ORDER_NUMBER, $_REQUEST['term']);
    foreach ($schedules as $schedule){
        $retval[] = Array("label" => $schedule->getNumber() . " - " . $schedule->getObject(), "value" => $schedule->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_maschine"){
    $retval = Array();
    $machines = Machine::getAllMachines(Machine::ORDER_NAME, 0, " AND name LIKE '%{$_REQUEST['term']}%' ");
    foreach ($machines as $machine){
        $retval[] = Array("label" => $machine->getName(), "value" => $machine->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_ticket"){
    $retval = Array();
    $tickets = Ticket::getAllTickets(" WHERE state > 0 AND (title LIKE '%{$_REQUEST['term']}%' OR number LIKE '%{$_REQUEST['term']}%') ");
    foreach ($tickets as $ticket){
        $retval[] = Array("label" => $ticket->getNumber() . " - " . $ticket->getTitle(), "value" => $ticket->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_all"){
    $retval = Array();
    $tickets = Ticket::getAllTickets(" WHERE state > 0 AND (title LIKE '%{$_REQUEST['term']}%' OR number LIKE '%{$_REQUEST['term']}%') ");
    foreach ($tickets as $ticket){
        $retval[] = Array("type" => get_class($ticket), "label" => "Ticket: " . $ticket->getNumber() . " - " . $ticket->getTitle() . " (" . date("d.m.y",$ticket->getCrtdate()) . ")", "value" => $ticket->getId());
	} 
	
	$machines = Machine::getAllMachines(Machine::ORDER_NAME, 0, " AND name LIKE '%{$_REQUEST['term']}%' ");
	foreach ($machines as $machine){
	    $retval[] = Array("type" => get_class($machine), "label" => "Maschine: " . $machine->getName(), "value" => $machine->getId());
	}
	
	$events = Event::getAllEventsForHome(Event::ORDER_BEGIN, $_REQUEST['term']);
	foreach ($events as $event){
	    $retval[] = Array("type" => get_class($event), "label" => "Event: " . $event->getTitle(), "value" => $event->getId());
	}
	
	$colinvoices = CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_NUMBER, " AND (number LIKE '%{$_REQUEST['term']}%' OR title LIKE '%{$_REQUEST['term']}%') ");
	foreach ($colinvoices as $colinvoice){
	    $retval[] = Array("type" => get_class($colinvoice), "label" => "Vorgang: " . $colinvoice->getNumber() . " - " . $colinvoice->getTitle() . " (" . date("d.m.y",$colinvoice->getCrtdate()) . ")", "value" => $colinvoice->getId());
	}

	$orders = Order::getAllOrders(Order::ORDER_NUMBER, " AND (number LIKE '%{$_REQUEST['term']}%' OR title LIKE '%{$_REQUEST['term']}%') ");
	foreach ($orders as $order){
	    $retval[] = Array("type" => get_class($order), "label" => "Kalkulation: " . $order->getNumber() . " - " . $order->getTitle() . " (" . date("d.m.y",$order->getCrtdat()) . ")", "value" => $order->getId());
	}
	
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
?>