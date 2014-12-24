<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'libs/modules/organizer/event.class.php';


$tme = mktime(0,0,0,date('j'), date('n'), date('Y'));
$tmeEnd = mktime(0,0,0,date('j'), date('n'), date('Y')) + 60*60*24;
?>

<div id="hidden_clicker_ch" style="display:none">
<a id="hiddenclicker_ch" href="http://www.google.com" >Hidden Clicker</a>
</div>
<div id='errormsg'></div>


<input type="hidden" id="userid_ch" name="userid_ch" value="<?=$_USER->getId()?>">

<!-- FancyBox -->
<script	type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<!-- FancyBox -->
<link href='jscripts/calendar/fullcalendar.css' rel='stylesheet' />
<link href='jscripts/calendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='jscripts/calendar/moment.min.js'></script>
<script src='jscripts/calendar/fullcalendar.min.js'></script>
<script src='jscripts/calendar/de.js'></script>
<script>
	$(document).ready(function() {
	
		$("a#hiddenclicker_ch").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'height'		:	800, 
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	
		var strUser = document.getElementById("userid_ch").value;
		$('#calendar_ch').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			//defaultDate: '<?=date("Y")."-".date("m")."-".date("d")?>',
// 			defaultDate: '2014-09-29',
			defaultView: 'agendaDay',
			selectable: true,
			selectHelper: true,
			select: function(start, end) {
				callBoxFancy_ch('libs/modules/organizer/calendar.newevent.php?start='+start.unix()+'&end='+end.unix());
			},
			eventClick: function(calEvent, jsEvent, view) {
				if (!calEvent.url) {
					callBoxFancy_ch('libs/modules/organizer/calendar.newevent.php?eventid='+calEvent.id);
				};
			},
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			// events: {
				// url: '/libs/modules/organizer/calendar_getevents.php',
				// error: function() {
					// $('#script-warning').show();
				// }
			// },
			events: {
				url: 'libs/modules/organizer/calendar_getevents.php',
				type: 'GET',
				data: {
					user: strUser,
					custom_param2: 'somethingelse'
				},
				error: function() {
					alert('there was an error while fetching events!');
				},
				// color: 'yellow',   // a non-ajax option
				// textColor: 'black' // a non-ajax option
			},
			loading: function(bool) {
				$('#loading_ch').toggle(bool);
			},
			eventDrop: function(event, delta, revertFunc) {
				if (!confirm("Eintrag '" + event.title + "' verschieben nach " + event.start.format() + "\nsind Sie sicher?")) {
					revertFunc();
				} else {
					$.ajax({
						url: 'libs/modules/organizer/calendar.ajax.php',
						data: 'exec=moveEvent&event_id='+ event.id +'&new_start='+ event.start.unix() +'&new_end='+ event.end.unix(),
						type: "GET",
						success: function(json) {
							document.getElementById('errormsg').innerHTML = json;
						}
					});
				}
			},
			eventResize: function(event) {
				$.ajax({
					url: 'libs/modules/organizer/calendar.ajax.php',
					data: 'exec=resizeEvent&event_id='+ event.id +'&new_start='+ event.start.unix() +'&new_end='+ event.end.unix(),
					type: "GET",
					success: function(json) {
						document.getElementById('errormsg').innerHTML = json;
					}
				});
			}
		});

		$('#loading_ch').toggle(false);
		
	});
	
function callBoxFancy_ch(my_href) {
	var j1 = document.getElementById("hiddenclicker_ch");
	j1.href = my_href;
	$('#hiddenclicker_ch').trigger('click');
}

function sleep(millis, callback) {
    setTimeout(function()
            { callback(); }
    , millis);
}

function cal_refresh(){
// 	$('#calendar_ch').fullCalendar('changeView','agendaDay');
	$('#calendar_ch').fullCalendar('changeView','agendaWeek');
};
</script>
<style>
	#calendar_ch {
/* 		width: 300px; */
/* 		height: 300px; */
		margin: 0 auto;
	}
</style>


<table cellspacing="0" cellpadding="0">
	<tr class="tabellenlinie">
        <td>
        	<div id='loading_ch'>loading...</div>
        	<div id='calendar_ch'></div>
        </td>
	</tr>
	<tr class="tabellenlinie">
		<td colspan="2">
		<center>
			<a href='index.php?page=libs/modules/organizer/calendar.php'>zum Kalender</a>
		</center>
		</td>
	</tr>
</table>