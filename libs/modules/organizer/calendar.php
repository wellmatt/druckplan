<?
require_once 'event.class.php';
chdir('../../../');

global $_USER;
if((int)$_REQUEST["sel_user"] > 0)
    $sel_user = new User((int)$_REQUEST["sel_user"]);
else
	$sel_user = $_USER;

if ($_USER->hasRightsByGroup(Group::RIGHT_ALL_CALENDAR)) {
	$users = User::getAllUser(User::ORDER_LOGIN);
	?>
	<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="select_cal_user" id="select_cal_user">
	F&uuml;r Benutzer: <select name="sel_user" id="sel_user" style="width:150px" onchange="document.getElementById('select_cal_user').submit()" class="text">
		<? foreach ($users as $user) {?>
		<option value="<?=$user->getId()?>" 
		<?
			if($user->getId() == $_REQUEST["sel_user"]) 
				echo "selected>";
			elseif($user->getId() == $_USER->getId() && !$_REQUEST["sel_user"])
				echo "selected>";
		?>
		><?=$user->getFirstname()?> <?=$user->getLastname()?></option>
		<? } ?>
	</select>
	</form>
	<?
}

if ($_REQUEST["exec"] == "delevent")
{
    $_REQUEST["id"] = (int)$_REQUEST["id"];
    $event = new Event($_REQUEST["id"]);
    $savemsg = getSaveMessage($event->delete());
    $_REQUEST["exec"] = "";
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
<script>
	$(document).ready(function() {
	
		$("a#hiddenclicker").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'height'		:	800, 
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	
		var e = document.getElementById("sel_user");
		var strUser = e.options[e.selectedIndex].value;
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			//defaultDate: '2014-08-12',
			selectable: true,
			selectHelper: true,
			select: function(start, end) {
				callBoxFancy('libs/modules/organizer/calendar.newevent.php?start='+start.unix()+'&end='+end.unix());
			},
			eventClick: function(calEvent, jsEvent, view) {
				if (!calEvent.url) {
					callBoxFancy('libs/modules/organizer/calendar.newevent.php?eventid='+calEvent.id);
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
				$('#loading').toggle(bool);
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
		
	});
	
function callBoxFancy(my_href) {
	var j1 = document.getElementById("hiddenclicker");
	j1.href = my_href;
	$('#hiddenclicker').trigger('click');
}
</script>
<style>
	#calendar {
		max-width: 900px;
		margin: 0 auto;
	}
</style>

	<div id='loading'>loading...</div>

	<div id='calendar'></div>
