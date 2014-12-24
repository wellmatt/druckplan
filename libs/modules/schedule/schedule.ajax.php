<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       30.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/schedule/schedule.part.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';

if(file_exists('libs/modules/calculation/order.class.php'))
    require_once 'libs/modules/calculation/order.class.php';

session_start();

// error_reporting(-1);
// ini_set('display_errors', 1);

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if($_REQUEST["exec"] == "setstatus")
{
    $newval = (int)$_REQUEST["newval"];
    
    if($_REQUEST["mode"] == "jobpartfinish")
    {
        $part = new SchedulePart((int)$_REQUEST["id"]);
        $part->setFinished($newval);
        if($part->save())
            echo "1";
        else 
            echo "0";
    } else
    {
        $sched = new Schedule((int)$_REQUEST["id"]);
        
        if($_REQUEST["mode"] == "dtp")
        {
            $sched->setStatusDtp($newval);
        } else if($_REQUEST["mode"] == "paper")
        {
            $sched->setStatusPaper($newval);
        } else if($_REQUEST["mode"] == "finish")
        {

            $sched->setFinished($newval);
        } else if($_REQUEST["mode"] == "status")
        {
            $sched->setStatus($newval);
            $sched->setFinished(0);
            if ($newval == 4){
            	$sched->setFinished(1);
            }
        }

        if($sched->getDruckplanId())
        {
            $order = new Order($sched->getDruckplanId());
            if($sched->getStatusDtp() > 0 || $sched->getStatusPaper() > 0)
            {
                if($sched->getFinished() == 1)
                    $order->setStatus(5); // Erledigt
                else
                    $order->setStatus(4); // In Produktion
            }
            else
                $order->setStatus(3); // Auftrag erteilt
            
            $order->save();
        }
            
        if($sched->save())
            echo "1";
        else
            echo "0";   
    }
}

if($_REQUEST["exec"] == "getMachineTime")
{

    $deadline = explode(".", trim(addslashes($_REQUEST["deadline"])));
    $deadline = mktime(0,0,0,$deadline[1], $deadline[0], $deadline[2]);
    
    $sm = new ScheduleMachine((int)$_REQUEST["sm_id"]);
    $sm->setMachine(new Machine((int)$_REQUEST["machine_id"]));
    $sm->setTargetTime((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["addvalue"]))));
    $sm->setDeadline($deadline);
    $sm->save();
    
    $hours = ScheduleMachine::getMachineTimeForDay($sm->getDeadline(), $sm->getMachine()->getId());
    
    echo '<span ';
    if($hours > $sm->getMachine()->getMaxHours() && $sm->getMachine()->getMaxHours() > 0) echo 'class="error"'; else echo 'class="ok"';
    echo '>'.printPrice($hours).' '.$_LANG->get('Std.');
    echo '</span>';
    echo '&nbsp;&nbsp;&nbsp;';
}

if($_REQUEST["exec"] == "setcounter")
{
    if(trim(addslashes($_REQUEST["mode"]))=="start")
    {
        $_SESSION["timer"][(int)$_REQUEST["ID"]] = time();
    } else
    {
        $timediff = time() - $_SESSION["timer"][(int)$_REQUEST["ID"]];
        $timediff = sprintf("%.2f", $timediff / 60 / 60);
        $_SESSION["timer"][(int)$_REQUEST["ID"]] = "";
        echo $timediff;
    }
}

if($_REQUEST["exec"] == "setMachineTimes")
{

    $sm = new ScheduleMachine((int)$_REQUEST["machineId"]);
    $sm->setActualTime((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["actualTime"]))));
    $sm->setDownTime((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["downTime"]))));
    $sm->setDownTimeType((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["downTimeType"]))));
    if($sm->save())
        echo "1";
    else 
        echo "0";
    echo $DB->getLastError();
    
}

if($_REQUEST["exec"] == "checkDate")
{
    
    require_once 'Date/Holidays.php';
    require_once 'Date/Holidays/Filter/'.$_USER->getClient()->getCountry()->getNameInt().'/Official.php';
    
    $deadline = explode(".", trim(addslashes($_REQUEST["deadline"])));
    $deadline = mktime(0,0,0,$deadline[1], $deadline[0], $deadline[2]);

    if(isWeekend(date('N', $deadline)))
        echo $_LANG->get('Achtung')." ".$_LANG->get('Wochenende')."\n";
     
    // Feiertage vorher berechnen. Sehr performancelastig, daher nur einmal am Anfang.
    //set up filter
    $filter = new Date_Holidays_Filter_Germany_Official();
    //then the driver
    $driver = &Date_Holidays::factory($_USER->getClient()->getCountry()->getNameInt(), date('Y', $deadline));
    if($driver->isHoliday($deadline, $filter))
        echo $_LANG->get('Achtung')." ".$_LANG->get('Feiertag')."\n";
}

if($_REQUEST["exec"] == "setSchedMachFinished")
{
    $sm = new ScheduleMachine((int)$_REQUEST["smid"]);
    $sm->setFinished((int)$_REQUEST["val"]);
    if ($sm->save())
        echo "1";
    else 
        echo "0";
}

?>