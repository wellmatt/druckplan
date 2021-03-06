<?php

//--------------------------------------------------------------------------------------------------
// This script reads event data from a JSON file and outputs those events which are within the range
// supplied by the "start" and "end" GET parameters.
//
// An optional "timezone" GET parameter will force all ISO8601 date stings to a given timezone.
//
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

// error_reporting(-1);
// ini_set('display_errors', 1);

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
require_once 'libs/modules/vacation/vacation.entry.class.php';
require_once 'libs/modules/vacation/vacation.user.class.php';

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Short-circuit if the client did not give us a date range.
if (!isset($_GET['start']) || !isset($_GET['end']) || !isset($_GET['user'])) {
	die("Please provide a date range.");
}

$newline = '
';

$user = new User($_GET['user']);
$usercal = $_REQUEST["usercal"];

$events = Event::getAllEventsTimeframe($_GET['start'], $_GET['end'], $user, $selectOtherDates = true, $_REQUEST["states"]);
$holidays = HolidayEvent::getAllForTimeframe($_GET['start'], $_GET['end']);

$output_arrays = Array();

if($events) {
	foreach ($events as $event) {
		$begin = date("Y-m-d\TH:i:s",$event->getBegin());
		$end = date("Y-m-d\TH:i:s",$event->getEnd());
		if(strpos($event->getTitle(),"[TICKET]")!==false) {
			if ($user->getCalTickets() == 1) {
				$output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "end" => $end, 
				    "url" => "index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$event->getTicket()->getId(), 
				    "backgroundColor" => $event->getTicket()->getState()->getColorcode(),
				    "textColor" => '#fff',
				    "editable" => false);
			}
		} elseif(strpos($event->getTitle(),"[AUFTRAG]")!==false) {
			if ($user->getCalOrders() == 1) {
				$output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "allDay" => "true", 
				    "url" => "index.php?page=libs/modules/calculation/order.php&exec=edit&id=".$event->getOrder()->getId()."&step=4", "textColor" => '#fff', "editable" => false);
			}
		} else {
			$cpstring = '';
			if (count($event->getParticipantsExt()>0)) {
				foreach ($event->getParticipantsExt() as $item) {
					$cp = new ContactPerson((int)$item);
					$cpstring .= $cp->getNameAsLine().$newline;
				}
			}
		    if ($event->getPublic())
		        $output_arrays[] = Array ("id" => $event->getId(), "title" => $cpstring.$event->getTitle(), "start" => $begin, "end" => $end, "backgroundColor" => "#1f698e");
		    else 
		        $output_arrays[] = Array ("id" => $event->getId(), "title" => $cpstring.$event->getTitle(), "start" => $begin, "end" => $end);
		}
	}
}
// Feiertage
if($holidays)
{
    foreach ($holidays as $holiday)
    {
		$begin = date("Y-m-d\TH:i:s",$holiday->getBegin());
		$end = date("Y-m-d\TH:i:s",$holiday->getEnd());
        $output_arrays[] = Array ("id" => $holiday->getId(), "title" => $holiday->getTitle(), "start" => $begin, "end" => $end, "backgroundColor" => $holiday->getColor(), "editable" => false, "holiday" => true);
    }
}
// Urlaube
if (in_array("99993", $_REQUEST["states"]))
{
    {
		$urlaube = VacationEntry::getAllForTimeframe($_GET['start'], $_GET['end']);
        if ($urlaube)
        {
            foreach ($urlaube as $urlaub)
            {
    		    $begin = date("Y-m-d\TH:i:s",$urlaub->getStart());
    		    $end = date("Y-m-d\TH:i:s",$urlaub->getEnd());
                $output_arrays[] = Array ("id" => $urlaub->getId(), "title" => "Urlaub: ".$urlaub->getUser()->getNameAsLine(), "start" => $begin, "end" => $end, "backgroundColor" => "#ff8888", "editable" => false, "holiday" => true);
            }
        }
    }
}

if (count($usercal)>0)
{
    foreach ($usercal as $foreigncal)
    {
        $foreignuser = new User($foreigncal);
        $events = Event::getAllEventsTimeframe($_GET['start'], $_GET['end'], $foreignuser, $selectOtherDates = true, $_REQUEST["states"]);
        
        if($events) {
            foreach ($events as $event) {
                $begin = date("Y-m-d\TH:i:s",$event->getBegin());
                $end = date("Y-m-d\TH:i:s",$event->getEnd());
                if(strpos($event->getTitle(),"[TICKET]")!==false) {
                    if ($user->getCalTickets() == 1) {
                        $output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "end" => $end,
                            "url" => "index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$event->getTicket()->getId(),
                            "backgroundColor" => $event->getTicket()->getState()->getColorcode(),
                            "textColor" => '#fff',
                            "editable" => false,
                            "foreign" => true);
                    }
                } elseif(strpos($event->getTitle(),"[AUFTRAG]")!==false) {
                    if ($user->getCalOrders() == 1) {
                        $output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "allDay" => "true",
                            "url" => "index.php?page=libs/modules/calculation/order.php&exec=edit&id=".$event->getOrder()->getId()."&step=4", "textColor" => '#fff', "editable" => false, "foreign" => true, "backgroundColor" => "#2a96cc");
                    }
                } else {
                    $output_arrays[] = Array ("id" => $event->getId(), "title" => $foreignuser->getNameAsLine(), "start" => $begin, "end" => $end, "foreign" => true, "editable" => false, "backgroundColor" => "#2a96cc");
                }
            }
        }
    }
}

if ($user->getCalBirthday() == 1) {
    $start = explode("-",$_GET['start']);
    $end = explode("-",$_GET['end']);
    $start = mktime(0,0,0, $start[1], $start[2], $start[0]);
    $end = mktime(0,0,0, $end[1], $end[2], $end[0])+60*60*24;
    
	$all_contactperson = ContactPerson::getAllContactPersonsBDay($start,$end);
	foreach ($all_contactperson as $cp) {
		$birthdate = $cp->getBirthDate();
		if ($birthdate!=0)
		{
    		$birth_day = date('d',$birthdate);
    		$birth_moth = date('m',$birthdate);
    		$birth_year = date('Y',$birthdate);
    		
    		$age = (date("md", date("U", mktime(0, 0, 0, $birth_moth, $birth_day, $birth_year))) > date("md")
    			? ((date("Y") - $birth_year))
    			: (date("Y") - $birth_year) +1);
    			
    		if ($birth_moth >= date('n',time())) {
    		    $birthday_title = "Geb.: " . $cp->getNameAsLine2() . " (".$age.")";
    				$output_arrays[] = Array ("id" => "0", "title" => $birthday_title, "start" => date("Y-m-d\TH:i:s",mktime(12, 0, 0, $birth_moth, $birth_day, date('Y',time()))),
    				"allDay" => "true", "editable" => "false", 
    				    "url" => "index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid=".$cp->getId()."&id=".$cp->getBusinessContactId(),
    				    "textColor" => '#fff',
    				    "editable" => false);
    		} else {
    		    $age++;
    		    $birthday_title = "Geb.: " . $cp->getNameAsLine2() . " (".$age.")";
    			$output_arrays[] = Array ("id" => "0", "title" => $birthday_title, "start" => date("Y-m-d\TH:i:s",mktime(12, 0, 0, $birth_moth, $birth_day, date('Y',time())+1)), 
    			"allDay" => "true", "editable" => "false", 
    			    "url" => "index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid=".$cp->getId()."&id=".$cp->getBusinessContactId(),
    			    "textColor" => '#fff',
    				"editable" => false);
    		}
		}
	}
}
echo json_encode($output_arrays);