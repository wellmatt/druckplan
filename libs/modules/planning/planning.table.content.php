<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/planning/planning.job.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$date_start = strtotime($_REQUEST["start"]);
$date_end = strtotime($_REQUEST["end"]);

$type = substr($_REQUEST["artmach"], 0, 1);
$artmach = substr($_REQUEST["artmach"], 1);
$print = $_REQUEST["print"];
$vo = $_REQUEST["vo"];


$jobs = PlanningJob::getPlanningTable($date_start,$date_end,$artmach,$vo);
//prettyPrint($jobs);
?>


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Jobs</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered" id="pltable">
            <thead>
                <tr>
                    <th></th>
                    <th>Prio</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>MA</th>
                    <th>Vorgang</th>
                    <th>Ticket</th>
                    <th>Datum</th>
                    <th>Prod. Datum</th>
                    <th>Lief. Datum</th>
                    <th>S-Zeit</th>
                    <th>I-Zeit</th>
                    <th>Status</th>
                    <th>Material</th>
                    <th>Grammatur</th>
                    <th>Farbigkeit</th>
                    <th>Bogengr.</th>
                    <th>Format</th>
                    <th>Format Offen</th>
                    <th>Nutzen</th>
                    <th>Druckbogen</th>
                    <th>Bemerkung</th>
                    <th>VoId</th>
                    <th>TicketId</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($jobs as $job){?>
                    <tr id="row_<?php echo $job['id'];?>">
                        <td></td>
                        <td><?php echo $job['sq'];?></td>
                        <td><?php echo $job['id'];?></td>
                        <td><?php echo $job['name'];?></td>
                        <td><?php echo $job['user'];?></td>
                        <td><?php echo $job['vonr'];?></td>
                        <td><?php echo $job['ticketnr'];?></td>
                        <td><?php echo $job['date'];?></td>
                        <td><?php echo $job['date_prod'];?></td>
                        <td><?php echo $job['date_deliv'];?></td>
                        <td><?php echo $job['tplanned'];?></td>
                        <td><?php echo $job['tactual'];?></td>
                        <td><span style="font-size: medium; background-color: <?php echo $job['statecolor'];?>" class="label"><?php echo $job['state'];?></span></td>
                        <td><?php echo $job['calc_material'];?></td>
                        <td><?php echo $job['calc_weight'];?></td>
                        <td><?php echo $job['calc_chroma'];?></td>
                        <td><?php echo $job['calc_size'];?></td>
                        <td><?php echo $job['calc_prodformat'];?></td>
                        <td><?php echo $job['calc_prodformatopen'];?></td>
                        <td><?php echo $job['calc_ppp'];?></td>
                        <td><?php echo $job['calc_papercount'];?></td>
                        <td><?php echo $job['note'];?></td>
                        <td><?php echo $job['void'];?></td>
                        <td><?php echo $job['ticketid'];?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<script>
    var editor;
    var table;

    $(document).ready(function() {
        editor = new $.fn.dataTable.Editor( {
            ajax: "libs/basic/datatables/planningtable.php",
            table: "#pltable",
            fields: [
                {
                    label: "Id:",
                    name: "id"
                },
                {
                    label: "Sequenz:",
                    name: "sequence"
                }
            ]
        } );

        // Activate an inline edit on click of a table cell
        $('#pltable').on( 'click', 'tbody td.editable', function (e) {
            editor.inline( this, {
                onBlur: 'submit'
            } );
        } );

        table = $('#pltable').DataTable( {
            dom: 'rt<"clear">',
            order: [[8, 'desc'],[1, 'asc']],
            pageLength: -1,
            rowGroup: {
                endRender: function ( rows, group ) {
                    var totals = rows
                        .data()
                        .pluck(10)
                        .reduce( function (a, b) {
                            return parseFloat(a) + parseFloat(b.toString().replace(".","").replace(",","."));
                        }, 0);
                    var totali = rows
                        .data()
                        .pluck(11)
                        .reduce( function (a, b) {
                            return parseFloat(a) + parseFloat(b.toString().replace(".","").replace(",","."));
                        }, 0);

                    return $('<tr/>')
                        .append( '<td colspan="4"></td>' )
                        .append( '<td colspan="2" style="text-align: right"><b>Zeiten am '+group+'</b></td>' )
                        .append( '<td>'+$.fn.dataTable.render.number('.', ',', 2, '').display( totals )+'</td>' )
                        .append( '<td>'+$.fn.dataTable.render.number('.', ',', 2, '').display( totali )+'</td>' )
                        .append( '<td colspan="4"></td>' );
                },
                dataSrc: 7
            },
            columns: [
                {
                    "className":      'details-control',
                    "orderable":      false,
                    "data":           null,
                    "defaultContent": ''
                },
                { data: "sequence", editField: "sequence", className: "editable" },
                null,
                null,
                { visible: false },
                { visible: false },
                { visible: false },
                { visible: false },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                { visible: false },
                { visible: false },
                { visible: false },
                { visible: false },
                { visible: false },
                { visible: false },
                { visible: false },
                { visible: false }
            ]
        } );

        // Add event listener for opening and closing details
        $('#pltable tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        } );
    } );

    /* Formatting function for row details - modify as you need */
    function format ( d ) {
        // `d` is the original data object for the row
        var child = '';

        child += '<table border="0" width="100%">';
        child += '<tr style="vertical-align: top;"><td width="50%">';
        child += '<table border="0" width="100%">';


        child += '<tr>'+
            '<td><b>MA:</b></td>'+
            '<td>'+d[4]+'</td>'+
            '</tr>'+
            '<tr>'+
            '<td><b>Vorgang:</b></td>'+
            '<td><a target="_blank" href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid='+d[22]+'">'+d[5]+'</a></td>'+
            '</tr>'+
            '<tr>'+
            '<td><b>Ticket:</b></td>'+
            '<td><a target="_blank" href="index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='+d[23]+'">'+d[6]+'</a></td>'+
            '</tr>';
        child += '<td>&nbsp;</td>';
        child += '<tr><td><b>Optionen:</b></td>';
        child += '<td><a class="pointer" onclick="popupshow(event,'+d[2]+');"><b>Verschieben</b></a> <span id="movetext_'+d[2]+'"></span></td>'
        child += '</tr><tr><td></td>';
        child += '<td><a class="pointer" onclick="callBoxFancytktoverview(\'libs/modules/tickets/ticket.summary.php?tktid='+d[23]+'\');"><b>Ticket Summary</b></a></td>';
        child += '</td></tr>';
        child += '</table>';
        child += '</td>';

        if (d[15] != '') {
            child += '<td width="50%">';
            child += '<table border="0" width="100%">';
            child += '<tr>' +
                '<td><b>Bogenformat:</b></td>' +
                '<td>' + d[16] + '</td>' +
                '</tr>' +
                '<tr>' +
                '<td><b>Format:</b></td>' +
                '<td>' + d[17] + '</td>' +
                '</tr>' +
                '<tr>' +
                '<td><b>Format Offen:</b></td>' +
                '<td>' + d[18] + '</td>' +
                '</tr>' +
                '<tr>' +
                '<td><b>Nutzen:</b></td>' +
                '<td>' + d[19] + '</td>' +
                '</tr>' +
                '<tr>' +
                '<td><b>Bogen:</b></td>' +
                '<td>' + d[20] + '</td>' +
                '</tr>';
            child += '</table>';
            child += '</td>';
        }
        child += '</tr>';
        child += '</table>';
        return child;
    }

    $("a#hiddenclickertktoverview").fancybox({
        'type'    : 'iframe',
        'transitionIn'	:	'elastic',
        'transitionOut'	:	'elastic',
        'speedIn'		:	600,
        'speedOut'		:	200,
        'padding'		:	25,
        'margin'        :   25,
        'scrolling'     :   'no',
        'width'		    :	1000,
        'onComplete'    :   function() {
            $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                $('#fancybox-content').height($(this).contents().find('body').height()+30);
                $('#fancybox-wrap').css('top','25px');
            });
        },
        'overlayShow'	:	true,
        'helpers'		:   { overlay:null, closeClick:true }
    });
    function callBoxFancytktoverview(my_href) {
        var j1 = document.getElementById("hiddenclickertktoverview");
        j1.href = my_href;
        $('#hiddenclickertktoverview').trigger('click');
    }
</script>
<div id="hidden_clicker" style="display:none">
    <a id="hiddenclickertktoverview" href="http://www.google.com" >Hidden Clicker</a>
</div>