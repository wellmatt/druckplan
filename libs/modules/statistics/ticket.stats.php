<?
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

require_once 'libs/modules/timekeeping/timekeeper.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$total_time_d = 0;
$total_time_m = 0;
$total_time_w = 0;
$total_time_y = 0;

$durchschnitt_d = 0;
$durchschnitt_w = 0;
$durchschnitt_m = 0;
$durchschnitt_y = 0;

$count_d = 0;
$count_w = 0;
$count_m = 0;
$count_y = 0;

$string_durchschnitt_d = "Durchschnitt";
$string_durchschnitt_w = "Durchschnitt";
$string_durchschnitt_m = "Durchschnitt";
$string_durchschnitt_y = "Durchschnitt";

$string_total_time_d = "Gesamt";
$string_total_time_w = "Gesamt";
$string_total_time_m = "Gesamt";
$string_total_time_y = "Gesamt"; 

$all_tickets = Ticket::getAllTickets(Ticket::ORDER_TITLE,"");
foreach ($all_tickets as $ticket){

	if (date("YmWd") == date("YmWd",$ticket->getCrtdate())){

		$all_timekeepers = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_ID,0,$ticket->getId());
		
		foreach ($all_timekeepers as $timekeeper){
			$tmp_startdate = $timekeeper->getStartdate();
			$tmp_enddate = $timekeeper->getEnddate();
			$tmp_time = $tmp_enddate - $tmp_startdate;
			$total_time_d += $tmp_time;
		}
		$count_d++;
	}
	
	if (date("YmW") == date("d",$ticket->getCrtdate())){

		$all_timekeepers = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_ID,0,$ticket->getId());
		
		foreach ($all_timekeepers as $timekeeper){
			$tmp_startdate = $timekeeper->getStartdate();
			$tmp_enddate = $timekeeper->getEnddate();
			$tmp_time = $tmp_enddate - $tmp_startdate;
			$total_time_w += $tmp_time;
		}
		$count_w++;
	}
	
	if (date("Ym") == date("Ym",$ticket->getCrtdate())){

		$all_timekeepers = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_ID,0,$ticket->getId());
		
		foreach ($all_timekeepers as $timekeeper){
			$tmp_startdate = $timekeeper->getStartdate();
			$tmp_enddate = $timekeeper->getEnddate();
			$tmp_time = $tmp_enddate - $tmp_startdate;
			$total_time_m += $tmp_time;
		}
		$count_m++;
	}
	
	if (date("Y") == date("Y",$ticket->getCrtdate())){

		$all_timekeepers = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_ID,0,$ticket->getId());
		
		foreach ($all_timekeepers as $timekeeper){
			$tmp_startdate = $timekeeper->getStartdate();
			$tmp_enddate = $timekeeper->getEnddate();
			$tmp_time = $tmp_enddate - $tmp_startdate;
			$total_time_y += $tmp_time;
		}
		$count_y++;
	}
}
if ($count_d > 0){
	$durchschnitt_d = $total_time_d / $count_d;
	$durchschnitt_d = round($durchschnitt_d / 60,2);
	$total_time_d = round($total_time_d / 60,2);
	if ($durchschnitt_d > 60){
		$string_durchschnitt_d = "Durchschnitt (Std.)";
		$durchschnitt_d = round($durchschnitt_d / 60,2);
	} else {
		$string_durchschnitt_d = "Durchschnitt (Min.)";
	}
	if ($total_time_d > 60){
		$string_total_time_d = "Gesamt (Std.)";
		$total_time_d = round($total_time_d / 60,2);
	} else {
		$string_total_time_d = "Gesamt (Min.)";
	}


}
if ($count_w > 0){
	$durchschnitt_w = $total_time_w / $count_w;
	$durchschnitt_w = round($durchschnitt_w / 60,2);
	$total_time_w = round($total_time_w / 60,2);
	if ($durchschnitt_w > 60){
		$string_durchschnitt_w = "Durchschnitt (Std.)";
		$durchschnitt_w = round($durchschnitt_w / 60,2);
	} else {
		$string_durchschnitt_w = "Durchschnitt (Min.)";
	}
	if ($total_time_w > 60){
		$string_total_time_w = "Gesamt (Std.)";
		$total_time_w = round($total_time_w / 60,2);
	} else {
		$string_total_time_w = "Gesamt (Min.)";
	}
	
}
if ($count_m > 0){
	$durchschnitt_m = $total_time_m / $count_m;
	$durchschnitt_m = round($durchschnitt_m / 60,2);
	$total_time_m = round($total_time_m / 60,2);
	if ($durchschnitt_m > 60){
		$string_durchschnitt_m = "Durchschnitt (Std.)";
		$durchschnitt_m = round($durchschnitt_m / 60,2);
	} else {
		$string_durchschnitt_m = "Durchschnitt (Min.)";
	}
	if ($total_time_m > 60){
		$string_total_time_m = "Gesamt (Std.)";
		$total_time_m = round($total_time_m / 60,2);
	} else {
		$string_total_time_m = "Gesamt (Min.)";
		
	}
	
}
if ($count_y > 0){
	$durchschnitt_y = $total_time_y / $count_y;
	$durchschnitt_y = round($durchschnitt_y / 60,2);
	$total_time_y = round($total_time_y / 60,2);
	if ($durchschnitt_y > 60){
		$string_durchschnitt_y = "Durchschnitt (Std.)";
		$durchschnitt_y = round($durchschnitt_y / 60,2);
	} else {
		$string_durchschnitt_y = "Durchschnitt (Min.)";
	}
	if ($total_time_y > 60){
		$string_total_time_y = "Gesamt (Std.)";
		$total_time_y = round($total_time_y / 60,2);
	} else {
		$string_total_time_y = "Gesamt (Min.)";
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
 
 <h2>Ticket-Statistik</h2>
<div class="box1">
	<table border="0" width="100%">
		<tr>
			<th>Tag</th>
			<th>Woche</th>
		</tr>
		<tr align="center">
			<td valign="middle"> <div id="ticket_d" class="box2"></div></td>
			<td valign="middle"> <div id="ticket_w" class="box2"></div></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp</td>
		</tr>
		<tr>
			<th>Monat</th>
			<th>Jahr</th>
		</tr>
		<tr align="center">
			<td valign="middle"> <div id="ticket_m" class="box2"></div></td>
			<td valign="middle"> <div id="ticket_y" class="box2"></div></td>
		</tr>
	  
	</table>
</div>
 

 <script type="text/javascript">
	$(document).ready(function() {
			arrayOfData1 = new Array(
			[<?=$total_time_d?>,'<?=$string_total_time_d?>','#228B22'],
			[<?=$durchschnitt_d?>,'<?=$string_durchschnitt_d?>','#FF4040']
			); 

			$('#ticket_d').jqBarGraph({
			   data: arrayOfData1
			});
			arrayOfData2 = new Array(
			[<?=$total_time_w?>,'<?=$string_total_time_w?>','#228B22'],
			[<?=$durchschnitt_w?>,'<?=$string_durchschnitt_w?>','#FF4040']
			); 

			$('#ticket_w').jqBarGraph({
			   data: arrayOfData2
			});
			arrayOfData3 = new Array(
			[<?=$total_time_m?>,'<?=$string_total_time_m?>','#228B22'],
			[<?=$durchschnitt_m?>,'<?=$string_durchschnitt_m?>','#FF4040']
			); 
			
			$('#ticket_m').jqBarGraph({
			   data: arrayOfData2
			});
			arrayOfData3 = new Array(
			[<?=$total_time_y?>,'<?=$string_total_time_y?>','#228B22'],
			[<?=$durchschnitt_y?>,'<?=$string_durchschnitt_y?>','#FF4040']
			); 

			$('#ticket_y').jqBarGraph({
			   data: arrayOfData3
			});
	});
</script> 