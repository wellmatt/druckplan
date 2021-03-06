<?
// error_reporting(-1);
// ini_set('display_errors', 1);
require_once 'libs/modules/organizer/event.class.php';

global $_USER;

if ($_REQUEST["exec"])
{
    if ($_REQUEST["exec"] == "delevent")
    {
        $_REQUEST["id"] = (int)$_REQUEST["id"];
        $event = new Event($_REQUEST["id"]);
        $savemsg = getSaveMessage($event->delete());
        $_REQUEST["exec"] = "";
    }
}
	
?>
<div id="hidden_clicker" style="display:none">
<a id="hiddenclicker" href="http://www.google.com" >Hidden Clicker</a>
</div>
<div id='errormsg'></div>
</br>

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


<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<i class="fa fa-calendar"></i> Kalender
			</h3>
	  </div>
	  <div class="panel-body">
		  <div class="row">
			  <div class="col-md-9">
				  <div id='loading'>loading...</div>
				  <div id='calendar'></div>
			  </div>
			  <div class="col-md-3">
				  <div id="datepicker0"></div>
				  <div id="datepicker1"></div>
				  <div id="datepicker2"></div>

				  <div id="accordion">
					  <h3>Stati & Anzeige</h3>
					  <div style="padding: 10px;">
						  <p>test</p>
						  Status:<br>
						  <span class="pointer" onclick="status_all();">alle</span> | <span class="pointer" onclick="status_none();">keine</span><br>
						  <?php
						  $ticket_states = TicketState::getAllStates();
						  foreach ($ticket_states as $ticket_state){
							  if ($ticket_state->getId() != 1 && $ticket_state->getId() != 3)
								  echo '<input type="checkbox" onclick="refetchEvents();" value="'.$ticket_state->getId().'" class="chkb_legend"/> <font color="'.$ticket_state->getColorcode().'">'.$ticket_state->getTitle().'</font><br>';
						  }
						  echo '<br>';
						  echo '<input type="checkbox" onclick="refetchEvents();" value="99991" class="chkb_legend"/> Aufträge<br>';
						  echo '<input type="checkbox" onclick="refetchEvents();" value="99992" checked class="chkb_legend"/> Termine<br>';
						  echo '<input type="checkbox" onclick="refetchEvents();" value="99993" class="chkb_legend"/> Urlaub<br>';
						  ?>
					  </div>
					  <h3>Mitarbeiter</h3>
					  <div style="padding: 10px;">
						  <?php
						  if ($_USER->hasRightsByGroup(Group::RIGHT_ALL_CALENDAR) || $_USER->isAdmin()) {
							  $users = User::getAllUser(User::ORDER_LOGIN);
							  ?>
							  <div class="row">
								  <div class="col-md-12">
									  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="select_cal_user" id="select_cal_user">
										  Kalender benutzten als: <select name="sel_user" id="sel_user" onchange="refetchEvents();" class="text">
											  <? foreach ($users as $user) {?>
												  <option value="<?=$user->getId()?>"
													  <?
													  if($user->getId() == $_USER->getId())
														  echo "selected>";
													  ?>
												  ><?=$user->getFirstname()?> <?=$user->getLastname()?></option>
											  <? } ?>
										  </select>
									  </form>
								  </div>
							  </div>
							  <?
						  } else {
							  $sel_user = $_USER;
							  echo '<select name="sel_user" id="sel_user" style="display: none;"><option value="'.$_USER->getId().'" selected></option></select>';
						  }
						  ?>
						  <?php if ($_USER->hasRightsByGroup(Group::RIGHT_SEE_ALL_CALENDAR) || $_USER->isAdmin()) { ?>
							  Kalender überlappen mit:<br>
							  <?php
							  foreach ($users as $user){
								  if ($user->getId() != $_USER->getId())
									  echo '<input type="checkbox" onclick="refetchEvents();" value="'.$user->getId().'" class="chkb_usercal"/>'.$user->getNameAsLine().'<br>';
							  }
						  } ?>
					  </div>
					  <h3>Legende</h3>
					  <div style="padding: 10px;">
						  <a style="background-color:#1f698e" class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable"><div class="fc-content"><span class="fc-title">Öffentlich</span></div></a><br>
						  <a style="background-color:#3a87ad" class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable"><div class="fc-content"><span class="fc-title">Privat</span></div></a><br>
						  <a style="background-color:#2a96cc" class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable"><div class="fc-content"><span class="fc-title">Fremd</span></div></a><br>
					  </div>
				  </div>
			  </div>
		  </div>
	  </div>
</div>

<script>
	$(function() {
		$( "#datepicker0" ).datepicker({
			showWeek: true,
			showOtherMonths: false,
			selectOtherMonths: false,
			defaultDate: "-1m",
			onSelect: function(dateText, inst) {
				var date = moment(dateText, "DD.MM.YYYY");
				$('#calendar').fullCalendar( 'gotoDate', date );
			},
//			dateFormat: "mm/dd/yy"
		});
		$( "#datepicker1" ).datepicker({
			showWeek: true,
			showOtherMonths: false,
			selectOtherMonths: false,
			onSelect: function(dateText, inst) {
				var date = moment(dateText, "DD.MM.YYYY");
				$('#calendar').fullCalendar( 'gotoDate', date );
			},
//			dateFormat: "mm/dd/yy"
		});
		$( "#datepicker2" ).datepicker({
			showWeek: true,
			showOtherMonths: false,
			selectOtherMonths: false,
			defaultDate: "+1m",
			onSelect: function(dateText, inst) {
				var date = moment(dateText, "DD.MM.YYYY");
				$('#calendar').fullCalendar( 'gotoDate', date );
			},
//			dateFormat: "mm/dd/yy"
		});
	});
</script>
<script>
	$(function() {
		$( "#accordion" ).accordion();
	});
</script>
<script>
	$(document).ready(function() {


		$("a#hiddenclicker").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'height'		:	1000,
			'width'			:	1000,
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});

		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'agendaWeek,month,agendaDay'
			},
			defaultView: 'agendaWeek',
			weekNumbers: true,
			selectable: true,
			selectHelper: true,
			select: function(start, end) {
				callBoxFancy('libs/modules/organizer/calendar.newevent.php?start='+start.format()+'&end='+end.format());
			},
			eventClick: function(calEvent, jsEvent, view) {
				if (!calEvent.url && !calEvent.holiday && !calEvent.foreign) {
					callBoxFancy('libs/modules/organizer/calendar.newevent.php?eventid='+calEvent.id);
				};
			},
			editable: true,
			eventLimit: true,
			events: function(start, end, timezone, callback) {
				var strUser = $( "#sel_user" ).val();
				var states = [];
				$('.chkb_legend').each(function( index ) {
					if ( $( this ).prop( "checked" ) )
					{
						states.push(this.value);
					}
				});
				var usercal = [];
				$('.chkb_usercal').each(function( index ) {
					if ( $( this ).prop( "checked" ) )
					{
						usercal.push(this.value);
					}
				});

				$.ajax({
					url: 'libs/modules/organizer/calendar_getevents.php',
					dataType: 'json',
					data: {
						user: strUser,
						start: start.format('YYYY-MM-DD'),
						end: end.format('YYYY-MM-DD'),
						states: states,
						usercal: usercal
					},
					success: function (eventstring) {
						callback(eventstring);
					}
				});
			},
			loading: function(bool) {
				$('#loading').toggle(bool);
			},
			eventDrop: function(event, delta, revertFunc) {
				if (!confirm("Eintrag '" + event.title + "' verschieben nach \nStart: " + event.start.format() + "\nEnde: " + event.end.format() + "\nsind Sie sicher?")) {
					revertFunc();
				} else {
					$.ajax({
						url: 'libs/modules/organizer/calendar.ajax.php',
						data: 'exec=moveEvent&event_id='+ event.id +'&new_start='+ event.start.format() +'&new_end='+ event.end.format(),
						type: "GET",
						success: function(json) {
							document.getElementById('errormsg').innerHTML = json;
						}
					});
					return true;
				}
			},
			eventResize: function(event) {
				$.ajax({
					url: 'libs/modules/organizer/calendar.ajax.php',
					data: 'exec=resizeEvent&event_id='+ event.id +'&new_start='+ event.start.format() +'&new_end='+ event.end.format(),
					type: "GET",
					success: function(json) {
						document.getElementById('errormsg').innerHTML = json;
					}
				});
			},
			eventRender: function(event, element) {
				element.attr('title', event.title);
			},
			viewRender: function(view, element) {
				var moment = $('#calendar').fullCalendar('getDate');

				var m1 = moment.format('DD.MM.YYYY');
				$( "#datepicker1" ).datepicker( "setDate", m1 );

				var m0 = moment.subtract(1,'M').format('DD.MM.YYYY');
				$( "#datepicker0" ).datepicker( "setDate", m0 );

				var m2 = moment.add(2,'M').format('DD.MM.YYYY');
				$( "#datepicker2" ).datepicker( "setDate", m2 );

			},
			timeFormat: 'H:mm'
		});

	});

	function callBoxFancy(my_href) {
		var j1 = document.getElementById("hiddenclicker");
		j1.href = my_href;
		$('#hiddenclicker').trigger('click');
	}
	function refetchEvents()
	{
		$('#calendar').fullCalendar( 'refetchEvents' );
	}
	function status_all()
	{
		$('.chkb_legend').each(function( index ) {
			$( this ).prop( "checked", true )
		});
		refetchEvents();
	}

	function status_none()
	{
		$('.chkb_legend').each(function( index ) {
			$( this ).prop( "checked", false )
		});
		refetchEvents();
	}
</script>
<style>
	#calendar {
		max-width: 900px;
		margin: 0 auto;
	}
</style>


<?php
if ($_REQUEST["exec"] == "showevent" && $_REQUEST["id"]){
	echo "<script type=\"text/javascript\">$(document).ready(function() { callBoxFancy('libs/modules/organizer/calendar.newevent.php?eventid=".$_REQUEST["id"]."');});</script>";
}
?>