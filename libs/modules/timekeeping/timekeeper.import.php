<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/timekeeping/timekeeper.class.php';

// $timer_objectID und $timer_moduleID muessen in der aufrufenden Datei definiert werden
// z.B. vor den require_one, das diese Datei laedt
$all_timer = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_START, 0, $timer_objectID, $timer_moduleID);

if((int)$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"] == 0){
	$display_start = '';
	$display_stop = "none";
	$display_loading = "none";
	$display_othertimer = "none";
} else {
	$active_timer = new Timekeeper($_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]);
	
	// Nachsehen, ob die laufende Zeitmessung zu diesem Objekt (und Modul) gehoert	
	if ($active_timer->getModule() == $timer_moduleID && $active_timer->getObjectID() == $timer_objectID){
		// Zeitmessung lauft fuer dieses Objekt (und Modul)
		$display_stop = "";
		$display_othertimer = "none";
	} else {
		// Andernfalls laeuf eine Zeitmessung fur ein anderes Objekt
		$display_stop = "none";
		$display_othertimer = "";
	}
	$display_loading = '';
	$display_start = 'none';
}

?>

<script type="text/javascript">
<? // Zeitmessung starten?>
function startTimer(){

	// Diese beiden Input-Felder muessen immer in der requireden Klasse gesetzt werden, da sie immer unterschiedlich sind
	var objID = document.getElementById('timer_objectID').value;
	var modID = document.getElementById('timer_moduleID').value;

	$.post("libs/modules/timekeeping/timekeeper.ajax.php", 
			{ajax_action: 'startTimer', objectID : objID, moduleID : modID}, 
			 function(data) {
				// alert("-"+data+"-");
				if(data === "Timer_ON"){
					// Setze ICONs auf invisible und zeige Lade-Icon
					document.getElementById('img_timer_start').style.display='none';
					document.getElementById('img_timer_loading').style.display='';
					document.getElementById('a_timer_stop').style.display='';
					// document.getElementById('cometchat_trayicon_ticket').style.display='';
				} else {
					alert('<?=$_LANG->get('Konnte Zeitmessung nicht starten!');?>');
				}
			});
	// $('#cometchat_trayicon_ticket').show());
}

<? // Zeitmessung stopen, ohne Eingabe eines Grundes ?>
function stopTimer(obj){

	var objID = document.getElementById('timer_objectID').value;
	var modID = document.getElementById('timer_moduleid').value;

	$.post("libs/modules/timekeeping/timekeeper.ajax.php", 
			{exec: 'stopTimer', objectID : objID, moduleID : modID }, 
			 function(data) {
				if(data == "true"){
					// Rufe FancyBox mit Eingabe des Grundes auf
					// Setze ICONs auf visible und zeige Lade-Icon
					updateTimerTable();
				} else {
					alert('<?=$_LANG->get('Konnte Zeitmessung nicht starten!');?>');
				}
			});
	updateTimerTable();
}

<? // Zeitmessung stopen, ohne Eingabe eines Grundes ?>
function updateTimerTable(){

	var objID = document.getElementById('timer_objectID').value;
	var modID = document.getElementById('timer_moduleID').value;

	$.post("libs/modules/timekeeping/timekeeper.ajax.php", 
			{ajax_action: 'updateTimerTable', objectID : objID, moduleID : modID }, 
			 function(data) {
				if(data != ""){
					document.getElementById('table_alltimer').innerHTML = data;
					$(".edittimer").fancybox({
					    'type'    : 'iframe'
					});
				} else {
					alert('<?=$_LANG->get('Konnte Zeiten nicht aktualisieren.');?>');
				}
			});
	updateTimerTableSum();
}

<? // Summe oben setzen ?>
function updateTimerTableSum(){

	var objID = document.getElementById('timer_objectID').value;
	var modID = document.getElementById('timer_moduleID').value;

	$.post("libs/modules/timekeeping/timekeeper.ajax.php", 
			{ajax_action: 'updateTimerTableSum', objectID : objID, moduleID : modID }, 
			 function(data) {
				if(data != ""){
					document.getElementById('total_time').innerHTML = data;
				}
			});
}

<? // Zeitmessung loeschen?>
function deleteTimer(tmID){

	if (confirm("Sind Sie sicher?")){

		var objID = document.getElementById('timer_objectID').value;
		var modID = document.getElementById('timer_moduleID').value;
	
		$.post("libs/modules/timekeeping/timekeeper.ajax.php", 
				{ajax_action: 'deleteTimer', timerID: tmID, objectID : objID, moduleID : modID }, 
				 function(data) {
					if(data != ""){
						updateTimerTable();
					} else {
						alert('<?=$_LANG->get('Konnte Zeit nicht l&ouml;schen.');?>');
					}
				});
	}
}

function receiveMessage(event)
{
	if(event.data == "Update"){
		updateTimerTable();
	};
	if(event.data == "OtherTimer"){
		startTimer();
	};
}

addEventListener("message", receiveMessage, false);
</script>

<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript">
	$(document).ready(function() {
		$("a#a_timer_stop").fancybox({
		    'type'    : 'iframe'
		})
	});
	$(document).ready(function() {
		$("a#a_timer_othertimer").fancybox({
		    'type'    : 'iframe'
		})
		//startTimer();
	});
	$(document).ready(function() {
		$("a#a_addtimer").fancybox({
		    'type'    : 'iframe'
		})
	});
	$(document).ready(function() {
		$(".edittimer").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>

<input type="hidden" id="timer_objectID" value="<?=$timer_objectID?>">
<input type="hidden" id="timer_moduleID" value="<?=$timer_moduleID?>">
<input type="hidden" id="timer_ID" value="<?=$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]?>">

<table>
	<tr>
		<td width="120px">
			<img src="images/icons/clock-frame.png" alt=""/> <b><?=$_LANG->get('Zeit/Mat.');?></b>
		</td>
		<td>
			<a href="libs/modules/timekeeping/timekeeper.newTimer.iframe.php?tm_objectid=<?=$timer_objectID?>&tm_moduleid=<?=$timer_moduleID?>&modus=timer" 
				id="a_addtimer"><img src="images/icons/clock--plus.png" title="<?=$_LANG->get('Zeit von Hand eintragen');?>" ></a>
			&emsp; &emsp;
			<a href="libs/modules/timekeeping/timekeeper.newTimer.iframe.php?tm_objectid=<?=$timer_objectID?>&tm_moduleid=<?=$timer_moduleID?>&modus=article" 
				id="a_addtimer"><img src="images/icons/sticky-note--plus.png" title="<?=$_LANG->get('Artikel eintragen');?>" ></a>
		</td>
		<td width="25px"></td>
		<td width="25px">
			<img src="images/icons/hourglass--arrow.png" title="<?=$_LANG->get('Zeitmessung starten');?>" id="img_timer_start" 
				 onclick="startTimer()" alt="<?=$_LANG->get('Zeitmessung starten');?>"
				 style="display:<?=$display_start?>">
		</td>
		<td  width="25px">
			<img src="./images/status/loading2.gif" id="img_timer_loading" style="display: <?=$display_loading?>;"> 
		</td>
		<td  width="25px">
			<a  href="libs/modules/timekeeping/timekeeper.iframe.php?tm_objectid=<?=$timer_objectID?>&tm_moduleid=<?=$timer_moduleID?>" class="products" id="a_timer_stop"
				style="display:<?=$display_stop?>;"><img src="images/icons/hourglass--minus.png" title="Stop" ></a>
			<a  href="libs/modules/timekeeping/timekeeper.iframe.php?tm_objectid=<?=$timer_objectID?>&tm_moduleid=<?=$timer_moduleID?>" class="products" id="a_timer_othertimer"
				style="display:<?=$display_othertimer?>;"
				><img src="images/icons/exclamation-diamond.png" alt="<?=$_LANG->get('Achtung');?>" 
						title="<?=$_LANG->get('Andere Zeitmessung bereits aktiv');?>"/></a>
		</td>
		<td width="60px"></td>
		<td>
			<img src="images/icons/arrow-circle-double-135.png" title="<?=$_LANG->get('Zeiten aktualisieren');?>" id="img_timer_start" 
				 onclick="updateTimerTable()" alt="<?=$_LANG->get('Zeiten aktualisieren');?>" class="pointer">
		</td>
		<td id="total_time" width="120px" align="right" valign="middle"></td>
		<!-- td>
			<? /*** TODO: Zeitmessung als CSV-Export in den neuen Ordner docs/csv_files schreiben und verlinken **/?> 
			<a href="" >
			<img src="images/icons/printer.png" title="<?=$_LANG->get('Zeiten exportieren');?>" id="img_timer_print" 
				 alt="<?=$_LANG->get('Zeiten exportieren');?>" class="pointer">
			</a>
		</td -->
	</tr>
</table>
<br>
<div id="div_alltimer" > <!--  style="overflow: auto; height:<?=$div_height?>;"-->
	<table width="100%" id="table_alltimer" class="table-timer-test">
	<colgroup>
		<col width="40%">
		<col width="45%">
		<col width="15%">
	</colgroup>
	
	</table>
</div>
<script type="text/javascript">updateTimerTable()</script>
