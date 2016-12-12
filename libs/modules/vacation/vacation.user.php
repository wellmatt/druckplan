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
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/tagit/tag-it.min.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/tagit/jquery.tagit.css" media="screen" />

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Speichern','#',"$('#vacuser').submit();",'glyphicon-floppy-disk');
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="vacuser" id="vacuser">
    <input type="hidden" name="exec" value="save">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Benutzer
            </h3>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Benutzer Konfiguration
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Filter
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Suche</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="search" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="vac">
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
                            <tbody>
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
                                        <td><?php echo $tmp_vuser->getUser()->getId(); ?></td>
                                        <td><?php echo $tmp_vuser->getUser()->getNameAsLine(); ?></td>
                                        <td>
                                            <input class="form-control" type="number" step="0.5" name="vacu[<?php echo $user->getId(); ?>][days]" value="<?php echo $tmp_vuser->getDays(); ?>" <?php if (!$_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION)) echo ' readonly '; ?>>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" step="0.5" name="vacu[<?php echo $user->getId(); ?>][fromlast]" value="<?php echo $tmp_vuser->getFromLast(); ?>" <?php if (!$_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION)) echo ' readonly '; ?>>
                                        </td>
                                        <td><?php echo printPrice($tmp_vuser->getDays()+$tmp_vuser->getFromLast());?></td>
                                        <td><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 1));?></td>
                                        <td><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 2));?></td>
                                        <td><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 3));?></td>
                                        <td><?php echo printPrice(VacationEntry::getDaysByUserAndType($user, 4));?></td>
                                        <td><?php echo printPrice($tmp_vuser->getDays()+$tmp_vuser->getFromLast()-VacationEntry::getDaysByUserWithoutIll($user));?></td>
                                    </tr>
                                    <?php
                                } else {
                                    ?>
                                    <tr>
                                        <th><?php echo $user->getId(); ?></th>
                                        <th><?php echo $user->getNameAsLine(); ?></td>
                                        <th><input type="number" class="form-control" step="0.5" name="vacu[<?php echo $user->getId(); ?>][days]"></th>
                                        <th><input type="number" class="form-control" step="0.5" name="vacu[<?php echo $user->getId(); ?>][fromlast]"></th>
                                        <th>N/A</th>
                                        <th>N/A</th>
                                        <th>N/A</th>
                                        <th>N/A</th>
                                        <th>N/A</th>
                                        <th>N/A</th>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php
            $vacations = VacationEntry::getAll();
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Urlaub Übersicht
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Filter
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Suche</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="search" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="vacs">
                            <thead>
                            <tr>
                                <th><?=$_LANG->get('ID')?></th>
                                <th><?=$_LANG->get('Benutzer')?></th>
                                <th><?=$_LANG->get('Tage')?></th>
                                <th><?=$_LANG->get('Von')?></th>
                                <th><?=$_LANG->get('Bis')?></th>
                                <th><?=$_LANG->get('Status')?></th>
                                <th><?=$_LANG->get('Typ')?></th>
                                <th><?=$_LANG->get('Kommentar')?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($vacations as $vacation) {
                                if (!$_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION) && $vacation->getUser()->getId() != $_USER->getId())
                                    continue;
                                ?>
                                <tr class="pointer" <?php if ($_USER->hasRightsByGroup(GROUP::RIGHT_APPROVE_VACATION) || $vacation->getState()==VacationEntry::STATE_OPEN) echo ' onclick="callBoxFancy(\'libs/modules/vacation/vacation.new.frame.php?eventid='.$vacation->getId().'\');" ';?>>
                                    <td><?php echo $vacation->getId();?></td>
                                    <td><?php echo $vacation->getUser()->getNameAsLine();?></td>
                                    <td><?php echo $vacation->getDays();?></td>
                                    <td><?php echo date('d.m.Y',$vacation->getStart());?></td>
                                    <td><?php echo date('d.m.Y',$vacation->getEnd());?></td>
                                    <td><?php echo $vacation->getStateFormated();?></td>
                                    <td><?php echo $vacation->getTypeFormated();?></td>
                                    <td><?php echo $vacation->getComment();?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <div id='calendar'></div>
                    </div>
                    <div id="hidden_clicker" style="display:none">
                        <a id="hiddenclicker" href="http://www.google.com" >Hidden Clicker</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script type="text/javascript">
    $(document).ready(function() {
        var vac = $('#vac').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">lrtip',
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        } );
        var vacs = $('#vacs').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">lrtip',
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        } );
        $('#search').keyup(function(){
            vac.search( $(this).val() ).draw();
            vacs.search( $(this).val() ).draw();
        });
    } );
</script>
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