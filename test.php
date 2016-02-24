<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="./css/main.css" />
    <link rel="stylesheet" type="text/css" href="./css/matze.css" />
    <link rel="stylesheet" type="text/css" href="./css/ticket.css" />
    <link rel="stylesheet" type="text/css" href="./css/menu.css" />
    <link rel="stylesheet" type="text/css" href="./css/main.print.css" media="print"/>

    <!-- jQuery -->
    <link type="text/css" href="jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="jscripts/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="jscripts/jquery/js/jquery.blockUI.js"></script>
    <script language="JavaScript" src="./jscripts/jquery/local/jquery.ui.datepicker-de.js"></script>
    <script type="text/javascript" src="jscripts/jquery.validate.min.js"></script>
    <script type="text/javascript" src="jscripts/moment/moment-with-locales.min.js"></script>
    <!-- /jQuery -->
    <!-- FancyBox -->
    <script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
    <script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
    <!-- /FancyBox -->
    <script language="javascript" src="jscripts/basic.js"></script>
    <script language="javascript" src="jscripts/loadingscreen.js"></script>

    <!-- MegaNavbar -->
    <link href="thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
    <link rel="stylesheet" type="text/css" href="thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
    <script src="thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- /MegaNavbar -->

    <!-- PACE -->
    <script src="jscripts/pace/pace.min.js"></script>
    <link href="jscripts/pace/pace-theme-big-counter.css" rel="stylesheet" />
    <!-- /PACE -->
</head>
<body>

<script>
    $(function() {
        $( "#mailtable tr" ).draggable({
            appendTo: "body",
            helper: "clone"
        });
        $( ".folderspan" ).droppable({
            activeClass: "ui-state-default",
            hoverClass: "ui-state-hover",
            drop: function( event, ui ) {
                alert($(this).text());
                $( "<li></li>" ).text( ui.draggable.text() ).appendTo( this );
            }
        });
    });
</script>

<div class="row">
    <div class="col-md-3">
        <div class="box2">
            <ul class="list-unstyled">
                <span class="label label-info" style="text-align: right;">test@gmx.de</span></br></br>
                <li>
                    <span class="folderspan label label-default label-success pointer">Inbox</span>
                </li>
            </ul>
        </div></br>
    </div>
    <div class="col-md-9">
        <div class="box1">
            <table id="mailtable" width="100%" cellpadding="0" cellspacing="0" class="display stripe hover row-border order-column">
                <thead>
                <tr>
                    <th></th>
                    <th>Von</th>
                    <th>An</th>
                    <th>Betreff</th>
                    <th>Datum</th>
                    <th>Optionen</th>
                </tr>
                </thead>
                <tr>
                    <th>ID</th>
                    <th>Von</th>
                    <th>An</th>
                    <th>Betreff</th>
                    <th>Datum</th>
                    <th>Optionen</th>
                </tr>
                <tfoot>
                <tr>
                    <th></th>
                    <th>Von</th>
                    <th>An</th>
                    <th>Betreff</th>
                    <th>Datum</th>
                    <th>Optionen</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>