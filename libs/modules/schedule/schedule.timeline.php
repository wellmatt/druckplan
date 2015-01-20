<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       28.03.2012
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
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/schedule/schedule.part.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';

// error_reporting(-1);
// ini_set('display_errors', 1);

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

function cmpByPrio($a, $b)
{
    if($a->getPriority() == $b->getPriority())
        return 0;
    return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
}




$all_machines = Machine::getAllMachines(Machine::ORDER_NAME);
$all_schedules = Array();
$sched_machines = Array();
$tmp_mach_ids = Array();

$id = 1;
foreach ($all_machines as $machine){
    $days = ScheduleMachine::getOpenScheduledDays($machine->getId(), 99);
    if (count($days)>0){
        $intime = false;
        foreach ($days as $day){
            if (mktime (0,0,2, date("n",$day), date("j",$day), date("Y",$day)) >=
                mktime (0,0,1, date("n",time()), date("j",time()), date("Y",time())) &&
        
                mktime (0,0,1, date("n",$day+86400), date("j",$day+86400), date("Y",$day+86400)) <=
                mktime (0,0,2, date("n",time()+86400), date("j",time()+86400), date("Y",time()+86400))){
                $intime = true;
            }
        }
        if ($intime){
            $all_schedules[] = Array("id"=>$id, "text"=>$machine->getName(), "start_date"=>date("d-m-Y",time()),
                "duration"=>"168", "progress"=>0, "open"=>true);
            $tmp_mach_ids[$machine->getId()] = $id;
            $id++;
        }
        
        foreach ($days as $day){
            if (mktime (0,0,2, date("n",$day), date("j",$day), date("Y",$day)) >= 
                mktime (0,0,1, date("n",time()), date("j",time()), date("Y",time())) && 
                
                mktime (0,0,1, date("n",$day+86400), date("j",$day+86400), date("Y",$day+86400)) <= 
                mktime (0,0,2, date("n",time()+86400), date("j",time()+86400), date("Y",time()+86400))){
                $smentries = Array();
                foreach(ScheduleMachine::getPartsForDay($day, $machine->getId()) as $part)
                {
                    foreach(ScheduleMachine::getSmEntriesForMachineAndPart($part->getId(), $machine->getId()) as $sm)
                    {
                        $smentries[] = $sm;
                    }
                }
                usort($smentries, "cmpByPrio");
            
                unset($sm); unset($part);
                foreach($smentries as $sm)
                {
                    $part = new SchedulePart($sm->getSchedulePartId());
                    $sched = new Schedule($part->getScheduleId());
                    $all_schedules[] = Array("id"=>$id, "text"=>$sched->getNumber().": ".$sched->getObject(), "start_date"=>date("d-m-Y",$day),
                                             "duration"=>$sm->getTargetTime(), "parent"=>$tmp_mach_ids[$sm->getMachine()->getId()], "progress"=>(int)$sm->getFinished(), "open"=>true);
                    $id++;
                }
            }
        }
    }
}

// $tmp = Array();
// for ($i = 0; $i < 3; $i++){
//     $tmp[] = $all_schedules[$i];
// }

$schedules = Array("data"=>$all_schedules);
$sched_json = json_encode($schedules);

// echo $sched_json;

?>



<!DOCTYPE html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>Planungstafel</title>
    <script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
    <script src="../../../jscripts/dhtmlx/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
    <script src="../../../jscripts/dhtmlx/locale/locale_de.js" charset="utf-8"></script>
    <link rel="stylesheet" type="text/css" href="../../../jscripts/dhtmlx/dhtmlxgantt.css" />

	<style type="text/css">
		html, body{ height:100%; padding:0px; margin:0px; overflow: hidden;}
	</style>
</head>
<body>
	<div class="mygantt" style='width:100%; height:100%;'></div>
	<script type="text/javascript">
        <?php /* var demo_tasks = <?php echo $sched_json;?>; */ ?>
        var demo_tasks = <?php echo $sched_json;?>;
//         var demo_tasks = {"data":[{"id":1,"text":"VO-14-04-0065: Neuer Nummernkreis","start_date":"11-04-2014 12:00","duration":"0.08","progress":0,"open":true},{"id":2,"text":"VO-12-04-0132: Jahresheft","start_date":"11-04-2014 16:00","duration":"1.23","progress":0,"open":true}]};
		$(".mygantt").dhx_gantt({
			scale_unit:"day",
			date_scale:"%F %d",
			scale_height:54,
			min_column_width:50,
			duration_unit:"hour",
			buttons_left:["dhx_cancel_btn"],
			buttons_right:[],
			autosize:"y",
			subscales:[{unit:"hour", step:1, date:"%H:%i"}],
			show_task_cells:false,
			static_background:true,
// 			start_date: new Date(2013, 04, 01),
			data:demo_tasks
		});
	</script>
</body>