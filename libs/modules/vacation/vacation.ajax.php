<?php
/**
 * Created by PhpStorm.
 * User: ascherer
 * Date: 05.02.2016
 * Time: 13:37
 */


require_once 'vacation.user.class.php';
require_once 'vacation.entry.class.php';
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
require_once 'libs/modules/organizer/event_holiday.class.php';

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

if ($_REQUEST["exec"] == 'getCalEvents')
{
    // Short-circuit if the client did not give us a date range.
    if (!isset($_GET['start']) || !isset($_GET['end'])) {
        die("Please provide a date range.");
    }

    $vacations = VacationEntry::getAllForTimeframe($_REQUEST["start"],$_REQUEST["end"]);
    $output_arrays = Array();

    if($vacations) {
        foreach ($vacations as $vacation) {
            $output_arrays[] = Array (
                "id" => $vacation->getId(),
                "title" => $vacation->getUser()->getNameAsLine(),
                "start" => date("Y-m-d\TH:i:s",$vacation->getStart()),
                "end" => date("Y-m-d\TH:i:s",$vacation->getEnd()),
                "state" => $vacation->getState(),
                "userid" => $vacation->getUser()->getId(),
            );
        }
    }

    // Feiertage
    $holidays = HolidayEvent::getAllForTimeframe($_GET['start'], $_GET['end']);
    if($holidays)
    {
        foreach ($holidays as $holiday)
        {
            $begin = date("Y-m-d\TH:i:s",$holiday->getBegin());
            $end = date("Y-m-d\TH:i:s",$holiday->getEnd());
            $output_arrays[] = Array ("id" => $holiday->getId(), "title" => $holiday->getTitle(), "start" => $begin, "end" => $end, "backgroundColor" => $holiday->getColor(), "editable" => false, "holiday" => true);
        }
    }

    echo json_encode($output_arrays);
}

