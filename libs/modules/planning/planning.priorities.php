<?php
/**
 *  Copyright (c) 2018 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2018
 *
 */

require_once 'planning.job.class.php';

$date_past = mktime(0,0,0); // -259200
$date = mktime(0,0,0,date('m',$date_past),date('d',$date_past),date('Y',$date_past));
$pl_artmachs = PlanningJob::getUniqueArtmach();

if ($_REQUEST["exec"] == "save") {
//    if ($_REQUEST["column"])
//        foreach ($_REQUEST["column"] as $id => $item) {
//            $column = new MarketingColumn((int)$id);
//            $column->setTitle($item['title']);
//            $column->setSort((int)$item['sort']);
//            $column->setList(new MarketingList($_REQUEST["id"]));
//            $column->save();
//        }
//    if ($_REQUEST["removed"])
//        foreach ($_REQUEST["removed"] as $item) {
//            $delcolumn = new MarketingColumn((int)$item);
//            $delcolumn->delete();
//        }
//    if ($_REQUEST["listname"]) {
//        $tmp_list = new MarketingList($_REQUEST["id"]);
//        $tmp_list->setTitle($_REQUEST["listname"]);
//        $tmp_list->save();
//    }
}

?>
<style>
    #sortable {
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 250px;
    }

    #sortable li {
        margin: 0 5px 5px 5px;
        padding: 5px;
        font-size: 1.2em;
        height: 1.5em;
    }

    html > body #sortable li {
        height: 1.5em;
        line-height: 1.2em;
    }

    .ui-state-highlight {
        height: 1.5em;
        line-height: 1.2em;
    }

    .ui-state-default{
        width: auto !important;
        height: auto !important;
    }

    .content {
        width: auto !important;
    }
</style>


<?php // Qickmove generation
//$quickmove = new QuickMove();
//$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
//$quickmove->addItem('Speichern', '#', "$('#form').submit();", 'glyphicon-floppy-disk');
//echo $quickmove->generate();
// end of Quickmove generation
?>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" class="form-horizontal" name="form" id="form">
    <input type="hidden" name="exec" value="save"/>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Filter
                <span class="pull-right">
                    <button type="button" class="btn btn-sm btn-success" id="show">Anwenden</button>
                </span>
            </h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Datum</label>
                <div class="col-sm-4">
                    <input type="text" id="date" name="date"
                           class="form-control format-d-m-y divider-dot highlight-days-67 no-locale no-transparency dateselect"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)"
                           value="<? echo date('d.m.Y', $date); ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Object</label>
                <div class="col-sm-10">
                    <select name="artmach" id="artmach" class="form-control">
                        <? foreach ($pl_artmachs['machines'] as $mach) { ?>
                            <option value="<?= $mach->getId() ?>"><?= $mach->getName() ?></option>
                        <? } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Jobs
            </h3>
        </div>
        <div class="panel-body">
            <div id="jobbox"></div>
        </div>
    </div>
</form>

<script>
    $(function () {

        $('#date').datetimepicker({
            lang:'de',
            i18n:{
                de:{
                    months:[
                        'Januar','Februar','März','April',
                        'Mai','Juni','Juli','August',
                        'September','Oktober','November','Dezember',
                    ],
                    dayOfWeek:[
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            closeOnDateSelect:true,
            timepicker:false,
            format:'d.m.Y'
        });

        $.ajaxSetup({
            cache:false
        });
        $( "#show" ).click(function() {
            load_content();
        });
    });
    function unblock(){
        $('#jobbox').unblock();
    }

    function load_content(){
        $('#jobbox').block({ message: '<h3><img src="images/page/busy.gif"/> einen Augenblick...</h3>' });
        $('#jobbox').load( "libs/modules/planning/planning.priorities.content.php?date="+$('#date').val()+"&artmach="+$('#artmach').val(), null, unblock );
    }
</script>