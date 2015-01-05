<?php

//--------------------------------------------------------------------------------------------------
// This script reads event data from a JSON file and outputs those events which are within the range
// supplied by the "start" and "end" GET parameters.
//
// An optional "timezone" GET parameter will force all ISO8601 date stings to a given timezone.
//
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

error_reporting(-1);
ini_set('display_errors', 1);

// Require our Event class and datetime utilities
require_once 'event.class.php';
chdir ("../../../");
require_once 'libs/basic/user/user.class.php';
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once("libs/basic/groups/group.class.php");
require_once("libs/modules/businesscontact/contactperson.class.php");

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Short-circuit if the client did not give us a date range.
if (!isset($_GET['start']) || !isset($_GET['end']) || !isset($_GET['user'])) {
	die("Please provide a date range.");
}

$user = new User($_GET['user']);

// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
// Since no timezone will be present, they will parsed as UTC.
// $range_start = parseDateTime($_GET['start']);
// $range_end = parseDateTime($_GET['end']);

$events = Event::getAllEventsTimeframe($_GET['start'], $_GET['end'], $user, $selectOtherDates = true);


// print_r($events);

$output_arrays = Array();
 
if($events) {
	foreach ($events as $event) {
		$begin = date("Y-m-d\TH:i:s",$event->getBegin());
		$end = date("Y-m-d\TH:i:s",$event->getEnd());
		if(strpos($event->getTitle(),"[TICKET]")!==false) {
			if ($user->getCalTickets() == 1) {
				$output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "allDay" => "true", "url" => "index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=".$event->getTicket()->getId());
			}
		} elseif(strpos($event->getTitle(),"[AUFTRAG]")!==false) {
			if ($user->getCalOrders() == 1) {
				$output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "allDay" => "true", "url" => "index.php?page=libs/modules/calculation/order.php&exec=edit&id=".$event->getOrder()->getId()."&step=4");
			}
		} else {
			$output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "end" => $end);
		}
	}
}

if ($user->getCalBirthday() == 1) {
	$all_contactperson = ContactPerson::getAllContactPersons();
	foreach ($all_contactperson as $cp) {
		
		$birthdate = $cp->getBirthDate();
		$birth_day = date('d',$birthdate);
		$birth_moth = date('m',$birthdate);
		$birth_year = date('Y',$birthdate);
		
		$age = (date("md", date("U", mktime(0, 0, 0, $birth_moth, $birth_day, $birth_year))) > date("md")
			? ((date("Y") - $birth_year) - 1)
			: (date("Y") - $birth_year));
		$age++;
			
		$birthday_title = "Geb.: " . $cp->getNameAsLine2() . " (".$age.")";	
			
		if ($birth_moth >= date('m',time())) {
			if ($birth_day >= date('d',time())) {
				$output_arrays[] = Array ("id" => "0", "title" => $birthday_title, "start" => date("Y-m-d\TH:i:s",mktime(12, 0, 0, $birth_moth, $birth_day, date('Y',time()))), 
				"allDay" => "true", "editable" => "false", "url" => "index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid=".$cp->getId()."&id=".$cp->getBusinessContactId());
			}
		} else {
			$output_arrays[] = Array ("id" => "0", "title" => $birthday_title, "start" => date("Y-m-d\TH:i:s",mktime(12, 0, 0, $birth_moth, $birth_day, date('Y',time())+1)), 
			"allDay" => "true", "editable" => "false", "url" => "index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid=".$cp->getId()."&id=".$cp->getBusinessContactId());
		}
	}
}

// Send JSON to the client.
echo json_encode($output_arrays);