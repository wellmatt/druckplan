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
	
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			weekNumbers: true,
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
		        $.ajax({
		            url: 'libs/modules/organizer/calendar_getevents.php',
		            dataType: 'json',
		            data: {
	 					user: strUser,
		                start: start.format('YYYY-MM-DD'),
		                end: end.format('YYYY-MM-DD'),
		                states: states
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

<i class="fa fa-calendar"></i> <?=$_LANG->get('Kalender')?></br>
<div class="row">
    <div class="col-md-10">
    	<div id='loading'>loading...</div>
    	<div id='calendar'></div>
    </div>
    <div class="col-md-2">
        <table width="100%" valing="top">
            <tr>
                <td align="right">
                    <table>
                        <tr><td>Status:</td></tr>
                        <tr><td><span class="pointer" onclick="status_all();">alle</span> | <span class="pointer" onclick="status_none();">keine</span></td></tr>
                        <?php 
                        $ticket_states = TicketState::getAllStates();
                        foreach ($ticket_states as $ticket_state){
                            if ($ticket_state->getId() != 1 && $ticket_state->getId() != 3)
                                echo '<tr><td><input type="checkbox" onclick="refetchEvents();" value="'.$ticket_state->getId().'" class="chkb_legend"/> <font color="'.$ticket_state->getColorcode().'">'.$ticket_state->getTitle().'</font></td></tr>';
                        }
                        echo '<tr><td>&nbsp;</br></td></tr>';
                        echo '<tr><td><input type="checkbox" onclick="refetchEvents();" value="99991" class="chkb_legend"/> Aufträge</td></tr>';
                        echo '<tr><td><input type="checkbox" onclick="refetchEvents();" value="99992" checked class="chkb_legend"/> Termine</td></tr>';
                        ?>
                        <tr>
                            <td>
                                <?php
                                if ($_USER->hasRightsByGroup(Group::RIGHT_ALL_CALENDAR) || $_USER->isAdmin()) {
                                	$users = User::getAllUser(User::ORDER_LOGIN);
                                	?>
                                	</br>
                                	<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="select_cal_user" id="select_cal_user">
                                	Benutzer: <select name="sel_user" id="sel_user" style="width:150px" onchange="refetchEvents();" class="text">
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
                                	<?
                                } else {
                                    $sel_user = $_USER;
                                    echo '<select name="sel_user" id="sel_user" style="display: none;"><option value="'.$_USER->getId().'" selected></option></select>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
