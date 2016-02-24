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
require_once 'libs/modules/calculation/calculation.machineentry.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
global $_USER;

/**
 * @param array $array
 * @param string $value
 * @param bool $asc - ASC (true) or DESC (false) sorting
 * @param bool $preserveKeys
 * @return array
 * */
function sortBySubValue($array, $value, $asc = true, $preserveKeys = false)
{
    if (is_object(reset($array))) {
        $preserveKeys ? uasort($array, function ($a, $b) use ($value, $asc) {
            return $a->{$value} == $b->{$value} ? 0 : ($a->{$value} - $b->{$value}) * ($asc ? 1 : -1);
        }) : usort($array, function ($a, $b) use ($value, $asc) {
            return $a->{$value} == $b->{$value} ? 0 : ($a->{$value} - $b->{$value}) * ($asc ? 1 : -1);
        });
    } else {
        $preserveKeys ? uasort($array, function ($a, $b) use ($value, $asc) {
            return $a[$value] == $b[$value] ? 0 : ($a[$value] - $b[$value]) * ($asc ? 1 : -1);
        }) : usort($array, function ($a, $b) use ($value, $asc) {
            return $a[$value] == $b[$value] ? 0 : ($a[$value] - $b[$value]) * ($asc ? 1 : -1);
        });
    }
    return $array;
}

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
if ($_REQUEST["exec"] == "ajax_getDisabledDates")
{
    $disabledDates = Array();
    $valid_users = Array();
    $objectid = $_REQUEST["objectid"];
    $type = $_REQUEST["type"];
    $amount = $_REQUEST["amount"];
    $req_date = $_REQUEST["date"];
    $req_date_mktime = mktime(0,0,0,date("m",strtotime($req_date)),date("d",strtotime($req_date)),date("Y",strtotime($req_date)));
    
    $start = strtotime('-2 month',$req_date_mktime);
    $end = strtotime('+2 month',$req_date_mktime);
    
    $arrayRange=array();
    $dateFrom=$start;
    $dateTo=$end;
    
    if ($dateTo>=$dateFrom)
    {
        $arrayRange[$dateFrom]["time"] = 0;
        foreach (USER::getAllUser() as $user)
            $arrayRange[$dateFrom]["user"][$user->getId()] = $user->getWorkingtimeForDay($dateFrom);
        while ($dateFrom<$dateTo)
        {
            $dateFrom+=86400;
            $arrayRange[$dateFrom]["time"] = 0;
            foreach (USER::getAllUser() as $user)
                $arrayRange[$dateFrom]["user"][$user->getId()] = $user->getWorkingtimeForDay($dateFrom);
        }
    }
    
    
    $jobs = PlanningJob::getAllJobs(" AND start>".$start." AND end<".$end);
    foreach ($jobs as $job)
    {
        $date = mktime(0,0,0,date("m",$job->getStart()),date("d",$job->getStart()),date("Y",$job->getStart()));
        if ($job->getTime()>0)
        {
            $arrayRange[$date]["time"] += ($job->getTime() * 60 * 60);
            $arrayRange[$date]["user"][$job->getAssigned_user()->getId()] -= ($job->getTime() * 60 * 60);
        }
        elseif ($job->getPlannedTime()>0)
        {
            $arrayRange[$date]["time"] += ($job->getPlannedTime() * 60 * 60);
            $arrayRange[$date]["user"][$job->getAssigned_user()->getId()] -= ($job->getPlannedTime() * 60 * 60);
        }
    }
    
    if ($type == "ME"){
        $me = new Machineentry($objectid);
        $machine = $me->getMachine();
        $dateFrom=$start;
        $dateTo=$end;
        if ($dateTo>=$dateFrom)
        {
            $arrayRange[$dateFrom]["time"] = 0;
            $total_seconds = $machine->getRunningtimeForDay($dateFrom);
            $total_seconds -= $arrayRange[$dateFrom]["time"];
            if ($total_seconds < ($amount*60*60))
                $disabledDates[] = date("d.m.Y",$dateFrom);
            else
            {
                $valid_user = false;
                foreach ($arrayRange[$dateFrom]["user"] as $userid => $usr_time)
                {
                    if ($usr_time > ($amount*60*60))
                    {
                        $valid_user = true;
                        $user_obj = new User($userid);
                        if ($dateFrom == $req_date_mktime)
                            $valid_users[] = Array( "id" => $userid, "name" => $user_obj->getNameAsLine(), "time" => $usr_time );
                    }
                }
                if (!$valid_user)
                    $disabledDates[] = date("d.m.Y",$dateFrom);
            }
            while ($dateFrom<$dateTo)
            {
                $dateFrom+=86400;
                $arrayRange[$dateFrom]["time"] = 0;
                $total_seconds = $machine->getRunningtimeForDay($dateFrom);
                $total_seconds -= $arrayRange[$dateFrom]["time"];
                if ($total_seconds < ($amount*60*60))
                    $disabledDates[] = date("d.m.Y",$dateFrom);
                else
                {
                    $valid_user = false;
                    foreach ($arrayRange[$dateFrom]["user"] as $userid => $usr_time)
                    {
                        if ($usr_time > ($amount*60*60))
                        {
                            $valid_user = true;
                            $user_obj = new User($userid);
                            if ($dateFrom == $req_date_mktime)
                                $valid_users[] = Array( "id" => $userid, "name" => $user_obj->getNameAsLine(), "time" => $usr_time );
                        }
                    }
                    if (!$valid_user)
                        $disabledDates[] = date("d.m.Y",$dateFrom);
                }
            }
        }
    } else if ($type == "OP")
    {
        $op = new Orderposition($objectid);
        $jobart = new Article($op->getObjectid());
        $dateFrom=$start;
        $dateTo=$end;
        if ($dateTo>=$dateFrom)
        {
            $valid_user = false;
            foreach ($arrayRange[$dateFrom]["user"] as $userid => $usr_time)
            {
                if ($usr_time > ($amount*60*60))
                {
                    $valid_user = true;
                    $user_obj = new User($userid);
                    if ($dateFrom == $req_date_mktime)
                        $valid_users[] = Array( "id" => $userid, "name" => $user_obj->getNameAsLine(), "time" => $usr_time );
                }
            }
            if (!$valid_user)
                $disabledDates[] = date("d.m.Y",$dateFrom);
            while ($dateFrom<$dateTo)
            {
                $dateFrom+=86400;
                $valid_user = false;
                foreach ($arrayRange[$dateFrom]["user"] as $userid => $usr_time)
                {
                    if ($usr_time > ($amount*60*60))
                    {
                        $valid_user = true;
                        $user_obj = new User($userid);
                        if ($dateFrom == $req_date_mktime)
                            $valid_users[] = Array( "id" => $userid, "name" => $user_obj->getNameAsLine(), "time" => $usr_time );
                    }
                }
                if (!$valid_user)
                    $disabledDates[] = date("d.m.Y",$dateFrom);
            }
        }
    }
    
    $valid_users = sortBySubValue($valid_users, 'time', true, false);
    
    $output_array = Array( "valid_users" => $valid_users, "disabledDates" => $disabledDates );
    echo json_encode($output_array);
}
if ($_REQUEST["exec"] == "ajax_getJobDataForOverview")
{
    if ($_REQUEST["id"])
    {
        echo '<table width="100%">';
        echo '<tr>';
        echo '<td>ID</td>';
        echo '<td>Objekt</td>';
        echo '<td>MA</td>';
        echo '<td>Ticket</td>';
        echo '<td>Prod. Beginn</td>';
        echo '<td>S-Zeit</td>';
        echo '<td>I-Zeit</td>';
        echo '<td>Status</td>';
        echo '</tr>';
        $pjs = PlanningJob::getAllJobs(" AND object = {$_REQUEST["id"]} ");
        foreach ($pjs as $pj)
        {
            echo '<tr>';
            
            echo '<td>#'.$pj->getId().'</td>';
            if ($pj->getType()==2)
                echo '<td>'.$pj->getArtmach()->getName().'</td>';
            else
                echo '<td>'.$pj->getArtmach()->getTitle().'</td>';
            if ($pj->getAssigned_user()->getId()>0)
                echo '<td>'.$pj->getAssigned_user()->getNameAsLine().'</td>';
            else 
                echo '<td>'.$pj->getAssigned_group()->getName().'</td>';
            echo '<td><a target="_blank" href="index.php?page=libs/modules/tickets/ticket.php&exec=edit&returnhome=1&tktid='.$pj->getTicket()->getId().'">#'.$pj->getTicket()->getNumber().'</a></td>';
            echo '<td>'.date("d.m.Y H:i",$pj->getTicket()->getDuedate()).'</td>';
            echo '<td>'.number_format($pj->getTplanned(), 2, ",", "").'</td>';
            if ($pj->getTactual()>$pj->getTplanned())
                $style = ' style="background-color: red;"';
            echo '<td '.$style.'>'.number_format($pj->getTactual(), 2, ",", "").'</td>';
            echo '<td><span style="display: inline-block; vertical-align: top; background-color: '.$pj->getTicket()->getState()->getColorcode().'" class="label">';
            echo $pj->getTicket()->getState()->getTitle().'</span></td>';
            
            echo '</tr>';
        }
        echo '</table>';
    }
}
if ($_REQUEST["exec"] == "ajax_getJobsForCal")
{
    $start = strtotime($_REQUEST["start"]);
    $end = strtotime($_REQUEST["end"]);
    $type = substr($_REQUEST["artmach"], 0, 1);
    $artmach = substr($_REQUEST["artmach"], 1);
    $output_arrays = Array();
    if ($type == "0")
        $pjs = PlanningJob::getAllJobs();
    
    if ($type == "K")
    {
        $pjs = PlanningJob::getAllJobs(" AND type = 2 AND artmach = {$artmach}");
    }
    if ($type == "V")
    {
        $pjs = PlanningJob::getAllJobs(" AND type = 1 AND artmach = {$artmach}");
    }
    
    foreach ($pjs as $pj)
    {
        if ($pj->getType() == PlanningJob::TYPE_K)
        {
            $title = $pj->getObject()->getNumber() . ': ' . $pj->getArtmach()->getName() . "\n" . $pj->getAssigned_user()->getNameAsLine();
            $color = $pj->getArtmach()->getColor();
        } else {
            $title = $pj->getObject()->getNumber() . ': ' . $pj->getArtmach()->getTitle() . "\n" . $pj->getAssigned_user()->getNameAsLine();
            $color = '3a87ad';
        }
        $begin = date("Y-m-d\TH:i:s",$pj->getStart());
        $end = date("Y-m-d\TH:i:s",$pj->getEnd());
        $output_arrays[] = Array ("id" => $pj->getId(), 
                                  "title" => $title, 
                                  "start" => $begin, 
                                  "end" => $end,
                                  "url" => "index.php?page=libs/modules/planning/planning.job.php&type=".$type."&id=".$pj->getObject()->getId(), 
                                  "textColor" => $color, 
                                  "editable" => false
                                  );
    }
    
    echo json_encode($output_arrays);
}
if ($_REQUEST["exec"] == "ajax_MoveJobs")
{
    $jobarr = $_REQUEST["pjtomove"];
    if (count($jobarr)>0)
    {
        foreach ($jobarr as $job => $date)
        {
            $tmp_job = new PlanningJob($job);
            $tmp_job->setStart(strtotime($date));
            $tmp_job->save();
            $tmp_ticket = $tmp_job->getTicket();
            $logentry = 'Fälligkeit von '. date('d.m.Y H:i',$tmp_ticket->getDuedate()) . " >> " . date('d.m.Y H:i',strtotime($date)) . ' über Planungstabelle geändert';
            $tmp_ticket->setDuedate(strtotime($date));
            $tmp_ticket->save();
            $ticketlog = new TicketLog();
            $ticketlog->setCrtusr($_USER);
            $ticketlog->setDate(time());
            $ticketlog->setTicket($tmp_ticket);
            $ticketlog->setEntry($logentry);
            $ticketlog->save();
        }
    }
}



