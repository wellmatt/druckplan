<?
require_once 'libs/modules/planning/planning.job.class.php';
$pl_artmachs = PlanningJob::getUniqueArtmach();
?>
<table width="100%">
    <tr>
        <td width="200" class="content_header" valign="top"><i class="fa fa-calendar"></i> <?=$_LANG->get('Planungs-Kalender')?></td>
    </tr>
</table>

<center>
    <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="select_cal_artmach" id="select_cal_artmach">
        <select name="artmach" id="artmach" style="width:150px" onchange="document.getElementById('select_cal_artmach').submit()" class="text">
        <option value="0" <?php if (!$_REQUEST["artmach"]) echo 'selected';?>>alle</option>
        <option value="" disabled>Maschinen</option>
    	<? foreach ($pl_artmachs['machines'] as $mach) {?>
    	<option value="K<?=$mach->getId()?>" 
    	<?
    		if($mach->getId() == substr($_REQUEST["artmach"], 1)) 
    			echo " selected ";
    	?>
    	><?=$mach->getName()?></option>
    	<? } ?>
        <option value="" disabled>Artikel</option>
    	<? foreach ($pl_artmachs['articles'] as $art) {?>
    	<option value="V<?=$art->getId()?>" 
    	<?
    		if($art->getId() == substr($_REQUEST["artmach"], 1)) 
    			echo " selected ";
    	?>
    	><?=$art->getTitle()?></option>
    	<? } ?>
        </select>
    </form>
    <?php if($_REQUEST["artmach"]){?>
    <a href="#" onclick="DoExport();">Export</a>
    <?php }?>
</center>

<script type="text/javascript">
function DoExport()
{
	var location = 'libs/modules/planning/planning.export.php?id=<?php echo substr($_REQUEST["artmach"], 1);?>&type=<?php if (substr($_REQUEST["artmach"], 0, 1) == "V") echo PlanningJob::TYPE_V; elseif (substr($_REQUEST["artmach"], 0, 1) == "K") echo PlanningJob::TYPE_K;?>';
	var viewStartDate = moment($('#calendar').fullCalendar('getView').intervalStart._d).format('X');
	var viewEndDate = moment($('#calendar').fullCalendar('getView').intervalEnd._d).format('X');
	location += '&start=' + viewStartDate;
	location += '&end=' + viewEndDate;
	window.open(location,'_blank');
}
</script>
<div id="cal_start" style="display:none"></div>
<div id="cal_end" style="display:none"></div>

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
	
		var e = document.getElementById("artmach");
		var artmach = e.options[e.selectedIndex].value;
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			weekNumbers: true,
			slotDuration: '00:15:00',
			selectable: false,
			selectHelper: false,
			editable: false,
			eventLimit: true,
			eventRender: function(event, element, view) {
			    $(element).tooltip({title: event.title});
			},
			events: {
				url: 'libs/modules/planning/planning.ajax.php',
				type: 'GET',
				data: {
					exec: "ajax_getJobsForCal",
					artmach: artmach
				},
				error: function() {
					alert('there was an error while fetching events!');
				}
			},
			loading: function(bool) {
				$('#loading').toggle(bool);
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
