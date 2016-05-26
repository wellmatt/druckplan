<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			02.12.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/timekeeping/timekeeper.class.php';
require_once 'libs/modules/schedule/schedule.class.php';

// $timer_objectID, $timer_subObjectID und $timer_moduleID muessen in der aufrufenden Datei definiert werden
// z.B. vor den require_once, wo diese Datei geladen wird
// $all_timer = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_START, 0, $timer_objectID, $timer_moduleID);

if((int)$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"] == 0){
	$display_start = '';
	$display_stop = "none";
	$display_loading = "none";
	$display_othertimer = "none";
} else {
	$active_timer = new Timekeeper($_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]);
	
	if ($timer_subObjectID == $active_timer->getSubObjectID()){
		$display_stop = "";
		$display_othertimer = "none";
		$display_loading = '';
		$display_start = 'none';
	} else {
		$display_stop = "none";
		$display_othertimer = "";
		$display_loading = 'none';
		$display_start = 'none';
	}
}

$all_planer = Schedule::getAllSchedules(Schedule::ORDER_NUMBER);
?>

<script type="text/javascript">
<? // Zeitmessung starten?>
function startTimer_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>(){

	var modID 	= document.getElementById('timer_moduleID_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>').value;
	var objID 	= document.getElementById('timer_objectID_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>').value;
	var subObjID = document.getElementById('timer_subObjectID_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>').value;
	// var planID 	= document.getElementById('timer_planing_id').value;

	// objID = planID;
	$.post("libs/modules/timekeeping/timekeeper.ajax.php", 
		{ajax_action: 'startTimer', objectID : objID, moduleID : modID, subObjectID : subObjID}, 
		 function(data) {
			// alert("-"+data+"-");
			if(data === "Timer_ON"){
				// Erstmal alle Icons u. Links auf invisible setzen
				for(var i=0;i<parent.document.getElementsByName('img_timer_start').length;i++){
		            document.getElementsByName('img_timer_start')[i].style.display='none';
		            document.getElementsByName('a_timer_stop')[i].style.display='none';
		            document.getElementsByName('img_timer_loading')[i].style.display='none';
		            document.getElementsByName('a_timer_othertimer')[i].style.display='';
		        }
				
				// Setze Start-ICON auf invisible und zeige Lade-Icon
				document.getElementById('img_timer_start_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>').style.display='none';
				//document.getElementById('timer_planing_id').style.display='none';
				// nur fuer das aktive Element die Icons anzeigen
				document.getElementById('img_timer_loading_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>').style.display='';
				document.getElementById('a_timer_stop_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>').style.display='';
				// Icon fuer anderen Timer ausblenden
				document.getElementById('a_timer_othertimer_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>').style.display='none';
			} else {
				alert('<?=$_LANG->get('Konnte Zeitmessung nicht starten!');?>');
			}
		});
}

<? // Zeitmessung stopen, ohne Eingabe eines Grundes ?>
function startStop(obj){

	var objID = document.getElementById('timer_objectID').value;
	var modID = document.getElementById('timer_moduleid').value;

	$.post("libs/modules/timekeeping/timekeeper.ajax.php", 
			{exec: 'stopTimer', objectID : objID, moduleID : modID }, 
			 function(data) {
				if(data == "true"){
					// Rufe FancyBox mit Eingabe des Grundes auf
					// Setze ICONs auf visible und zeige Lade-Icon
				} else {
					alert('<?=$_LANG->get('Konnte Zeitmessung nicht starten!');?>');
				}
			});
}
</script>



<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript">
	$(document).ready(function() {
		$("a#a_timer_stop_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>").fancybox({
		    'type'    : 'iframe'
		})
	});
	$(document).ready(function() {
		$("a#a_timer_othertimer_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>


<input type="hidden" id="timer_objectID_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>" value="<?=$timer_objectID?>">
<input type="hidden" id="timer_subObjectID_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>" value="<?=$timer_subObjectID?>">
<input type="hidden" id="timer_moduleID_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>" value="<?=$timer_moduleID?>">
<input type="hidden" id="timer_ID_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>" 
		value="<?=$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]?>">

<table>
	<colgroup>
		<col width="30px">
		<!-- col width="120px"-->
		<col width="25px">
		<col width="25px">
		<col width="25px">
	</colgroup>
	<tr>
		<td>
			<!-- img src="images/icons/clock-frame.png" alt=""/ --> <b><?=$_LANG->get('Zeitmessung');?></b>
		</td>
		<? /* td>
			<select class="text" id="timer_planing_id" name="timer_planing_id" style="width:270px;display:<?=$display_start?>;"
					onChange="" >
				<option value="0"> &lt; <?=$_LANG->get('Bitte w&auml;hlen'); ?> &gt; </option>
			<? 	foreach ($all_planer AS $plan){ ?>
					<option value="<?=$plan->getId()?>"> <?=$plan->getNumber()." ( ".$plan->getCustomer()->getNameAsLine()." )"?> </option>
			<?	} ?>
			</select>
		</td */?>
		<td>
			<span class="glyphicons glyphicons-remove" title="<?=$_LANG->get('Zeitmessung starten');?>"
				 id="img_timer_start_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>" name="img_timer_start" 
				 onclick="startTimer_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>()"
				 style="display:<?=$display_start?>; padding-left: 10px;"></span>
		</td>
		<td>
			<img src="./images/status/loading2.gif" name="img_timer_loading" 
				id="img_timer_loading_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>" style="display: <?=$display_loading?>;"> 
		</td>
		<td>
			<a  href="libs/modules/timekeeping/timekeeper.stopPlaning.iframe.php" name="a_timer_stop" 
				id="a_timer_stop_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>"
				style="display:<?=$display_stop?>;"><span class="glyphicons glyphicons-clock" title="Stop" ></span></a>
			<a  href="libs/modules/timekeeping/timekeeper.stopPlaning.iframe.php" name="a_timer_othertimer" 
				id="a_timer_othertimer_<?=$timer_moduleID?>_<?=$timer_objectID?>_<?=$timer_subObjectID?>"
				style="display:<?=$display_othertimer?>;"
				><span class="glyphicons glyphicons-exclamation-sign"
						title="<?=$_LANG->get('Andere Zeitmessung bereits aktiv');?>"></span></a>
		</td>
	</tr>
</table>