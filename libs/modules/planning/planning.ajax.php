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
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/planning/planning.job.class.php';

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

if ($_REQUEST["exec"] == "getCalEvents")
{
    // Short-circuit if the client did not give us a date range.
    if (!isset($_GET['start']) || !isset($_GET['end'])) {
        die("Please provide a date range.");
    }
    
    $user = new User($_GET['user']);
    
    // Parse the start/end parameters.
    // These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
    // Since no timezone will be present, they will parsed as UTC.
    // $range_start = parseDateTime($_GET['start']);
    // $range_end = parseDateTime($_GET['end']);
    $job_arr = Array();
    $jobs = PlanningJob::getAllJobs(" AND start>=".$_GET['start']." AND end<=".$_GET['end']);
    foreach ($jobs as $job)
    {
        $date = mktime(0,0,0,date(m,$job->getDate()),date(d,$job->getDate()),date(Y,$job->getDate()));
        $job_arr[$date] = 0;
        if ($job->getTime()>0)
            $job_arr[$date] += $job->getTime();
        elseif ($job->getPlannedTime()>0)
            $job_arr[$date] += $job->getPlannedTime();
    }
    
    $output_array = Array();
    // $output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "end" => $end);
    //$output_arrays[] = Array ("id" => $event->getId(), "title" => $event->getTitle(), "start" => $begin, "allDay" => "true",
    //                    "url" => "index.php?page=libs/modules/calculation/order.php&exec=edit&id=".$event->getOrder()->getId()."&step=4", "textColor" => '#fff', "editable" => false);

    
    
    // Send JSON to the client.
    echo json_encode($output_array);
}
