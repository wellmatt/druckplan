<?

error_reporting(-1);
ini_set('display_errors', 1);

chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once("libs/modules/calculation/order.class.php");
require_once("libs/modules/businesscontact/businesscontact.class.php");
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';
require_once 'libs/modules/schedule/schedule.part.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$actualTime_d = 0;
$targetTime_d = 0;
$actualTime_w = 0;
$targetTime_w = 0;
$actualTime_m = 0;
$targetTime_m = 0;

$all_schedules = Schedule::getAllSchedules(Schedule::ORDER_ID);
// print_r($all_schedules);
foreach ($all_schedules as $schedule){
   $all_schedulesparts = SchedulePart::getAllScheduleParts($schedule->getId());
   foreach ($all_schedulesparts as $schedulepart){
     $all_schedulemachines = ScheduleMachine::getAllScheduleMachines($schedulepart->getId()); 
     foreach ($all_schedulemachines as $schedulemaschine){
    	  if (date("Y-m") == date("Y-m",$schedule->getDeliveryDate())){
    		$targetTime_m += $schedulemaschine->getTargetTime();
    		$actualTime_m += $schedulemaschine->getActualTime();
    	  }
    	  if (date("Y-W") == date("Y-W",$schedule->getDeliveryDate())){
    		$targetTime_w += $schedulemaschine->getTargetTime();
    		$actualTime_w += $schedulemaschine->getActualTime();
    	  }
    	  if (date("Y-m-d") == date("Y-m-d",$schedule->getDeliveryDate())){
    		$targetTime_d += $schedulemaschine->getTargetTime();
    		$actualTime_d += $schedulemaschine->getActualTime();
    	  }
     }
   }
}

 ?>
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
 <!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery.validate.min.js"></script>
<!-- /jQuery -->
<script type="text/javascript" src="../../../jscripts/jqBarGraph.1.1.min.js"></script>
 
 <h2>Maschinen-Auslastungs-Statistik</h2>
 <div class="box1">
<table border="0">
  <tr>
    <th>Tag</th>
    <th>Woche</th>
    <th>Monat</th>
  </tr>
  <tr>
    <td> <div id="schedule_d"></div></td>
    <td> <div id="schedule_w"></div></td>
    <td> <div id="schedule_m"></div></td>
  </tr>
</table>
</div>
 



 
 <script type="text/javascript">
	$(document).ready(function() {
			arrayOfData1 = new Array(
			[<?=$targetTime_d?>,'Soll','#228B22'],
			[<?=$actualTime_d?>,'Ist','#FF4040']
			); 

			$('#schedule_d').jqBarGraph({
			   data: arrayOfData1
			});
			arrayOfData2 = new Array(
			[<?=$targetTime_w?>,'Soll','#228B22'],
			[<?=$actualTime_w?>,'Ist','#FF4040']
			); 

			$('#schedule_w').jqBarGraph({
			   data: arrayOfData2
			});
			arrayOfData3 = new Array(
			[<?=$targetTime_m?>,'Soll','#228B22'],
			[<?=$actualTime_m?>,'Ist','#FF4040']
			); 

			$('#schedule_m').jqBarGraph({
			   data: arrayOfData3
			});
	});
	


</script>