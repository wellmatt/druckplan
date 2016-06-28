<?php
/**
 * Created by PhpStorm.
 * User: ascherer
 * Date: 05.02.2016
 * Time: 11:44
 */
require_once 'vacation.user.class.php';
require_once 'vacation.entry.class.php';

if($_REQUEST["exec"] == "save")
{
    $vacus = $_REQUEST["vacu"];
    if (count($vacus)>0)
        foreach ($vacus as $userid => $vacu)
        {
            $tmp_vacu = VacationUser::getByUser(new User((int)$userid));
            $tmp_vacu->setDays(tofloat($vacu["days"]));
            $tmp_vacu->setFromLast(tofloat($vacu["fromlast"]));
            $tmp_vacu->setUser(new User((int)$userid));
            $tmp_vacu->save();
        }
}

$vac_user = VacationUser::getAll();
$users = User::getAllUser();

?>

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

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Speichern','#',"$('#vacuser').submit();",'glyphicon-floppy-disk');
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                Benutzer
            </h3>
	  </div>
    </br>
    <div class="table-responsive">
        <h3>Benutzer Konfiguration</h3>
        <table class="table table-hover">
                <thead>
                    <tr>
                        <td class="content_row_header"><?=$_LANG->get('ID')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Name')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Tage/Jahr')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Tage übernommen')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Gasamt verf.')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Urlaub')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Überstunden')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Krankheit')?></td>
                        <td class="content_row_header"><?=$_LANG->get('Sonstiges')?></td>
                        <td class="content_row_header"><?=$_LANG->get('verbleibend')?></td>
                    </tr>
                </thead>
                <?php
                foreach($users as $user)
                {
                    if (!$_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION) && $user->getId() != $_USER->getId())
                        continue;

                    $tmp_vuser = new VacationUser();
                    foreach ($vac_user as $vuser)
                    {
                        if ($vuser->getUser()->getId() == $user->getId())
                            $tmp_vuser = $vuser;
                    }
                    if ($tmp_vuser->getId() != 0)
                    {
                        ?>
                        <tr>
                            <td class="content_row_header"><?php echo $tmp_vuser->getUser()->getId(); ?></td>
                            <td class="content_row_header"><?php echo $tmp_vuser->getUser()->getNameAsLine(); ?></td>
                            <td class="content_row_header">
                                <input type="number" step="0.5" name="vacu[<?php echo $user->getId(); ?>][days]" value="<?php echo $tmp_vuser->getDays(); ?>" <?php if (!$_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION)) echo ' readonly '; ?>>
                            </td>
                            <td class="content_row_header">
                                <input type="number" step="0.5" name="vacu[<?php echo $user->getId(); ?>][fromlast]" value="<?php echo $tmp_vuser->getFromLast(); ?>" <?php if (!$_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION)) echo ' readonly '; ?>>
                            </td>
                            <td class="content_row_header"><?php echo printPrice($tmp_vuser->getDays()+$tmp_vuser->getFromLast());?></td>
                            <td class="content_row_header"><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 1));?></td>
                            <td class="content_row_header"><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 2));?></td>
                            <td class="content_row_header"><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 3));?></td>
                            <td class="content_row_header"><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 4));?></td>
                            <td class="content_row_header"><?php echo printPrice($tmp_vuser->getDays()+$tmp_vuser->getFromLast()-VacationEntry::getDaysByUser($user));?></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td class="content_row_header"><?php echo $user->getId(); ?></td>
                            <td class="content_row_header"><?php echo $user->getNameAsLine(); ?></td>
                            <td class="content_row_header"><input type="number" step="0.5" name="vacu[<?php echo $user->getId(); ?>][days]"></td>
                            <td class="content_row_header"><input type="number" step="0.5" name="vacu[<?php echo $user->getId(); ?>][fromlast]"></td>
                            <td class="content_row_header">N/A</td>
                            <td class="content_row_header">N/A</td>
                            <td class="content_row_header">N/A</td>
                            <td class="content_row_header">N/A</td>
                            <td class="content_row_header">N/A</td>
                            <td class="content_row_header">N/A</td>
                        </tr>
                        <?php
                    }
                }
                ?>
        </table>
    </div>
    </br>
    <?php
    $vacations = VacationEntry::getAll();
    ?>
    <div class="table-responsive">
        <h3>Urlaub Übersicht</h3>
    	<table class="table table-hover">
            <thead>
            <tr>
                <td class="content_row_header"><?=$_LANG->get('ID')?></td>
                <td class="content_row_header"><?=$_LANG->get('Benutzer')?></td>
                <td class="content_row_header"><?=$_LANG->get('Tage')?></td>
                <td class="content_row_header"><?=$_LANG->get('Von')?></td>
                <td class="content_row_header"><?=$_LANG->get('Bis')?></td>
                <td class="content_row_header"><?=$_LANG->get('Status')?></td>
                <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
                <td class="content_row_header"><?=$_LANG->get('Kommentar')?></td>
            </tr>
            </thead>
            <?php
            foreach ($vacations as $vacation) {
                if (!$_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION) && $vacation->getUser()->getId() != $_USER->getId())
                    continue;
                ?>
                <tr class="pointer" <?php if ($_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION) || $vacation->getState()==VacationEntry::STATE_OPEN) echo ' onclick="callBoxFancy(\'libs/modules/vacation/vacation.new.frame.php?eventid='.$vacation->getId().'\');" ';?>>
                    <td class="content_row_header"><?php echo $vacation->getId();?></td>
                    <td class="content_row_header"><?php echo $vacation->getUser()->getNameAsLine();?></td>
                    <td class="content_row_header"><?php echo $vacation->getDays();?></td>
                    <td class="content_row_header"><?php echo date('d.m.Y',$vacation->getStart());?></td>
                    <td class="content_row_header"><?php echo date('d.m.Y',$vacation->getEnd());?></td>
                    <td class="content_row_header"><?php echo $vacation->getStateFormated();?></td>
                    <td class="content_row_header"><?php echo $vacation->getTypeFormated();?></td>
                    <td class="content_row_header"><?php echo $vacation->getComment();?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
    </br>
    <div class="table-responsive">
    	<table class="table table-hover">
        <div id='calendar'></div>
    </div>
    <div id="hidden_clicker" style="display:none">
        <a id="hiddenclicker" href="http://www.google.com" >Hidden Clicker</a>
    </div>
</div>

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
            timezone: "local",
            selectable: true,
            selectHelper: true,
            select: function(start, end) {
                callBoxFancy('libs/modules/vacation/vacation.new.frame.php?start='+start.unix()+'&end='+end.unix());
                $('#calendar').fullCalendar('unselect');
            },
            eventClick: function(calEvent, jsEvent, view) {
                if (!calEvent.holiday <?php if (!$_USER->hasRightsByGroup(Group::RIGHT_APPROVE_VACATION)) echo '&& calEvent.userid == '.$_USER->getId().' && calEvent.state == 1';?>) {
                    callBoxFancy('libs/modules/vacation/vacation.new.frame.php?eventid='+calEvent.id);
                };
            },
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: 'libs/modules/vacation/vacation.ajax.php',
                    dataType: 'json',
                    data: {
                        exec: 'getCalEvents',
                        start: start.format('YYYY-MM-DD'),
                        end: end.format('YYYY-MM-DD'),
                    },
                    success: function (eventstring) {
                        callback(eventstring);
                    }
                });
            },
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
</script>