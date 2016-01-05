

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="./css/main.css" />
<link rel="stylesheet" type="text/css" href="./css/ticket.css" />
<link rel="stylesheet" type="text/css" href="./css/menu.css" />
<link rel="stylesheet" type="text/css" href="./css/main.print.css" media="print"/>

<!-- jQuery -->
<link type="text/css" href="jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="jscripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="jscripts/moment/moment-with-locales.min.js"></script>
<!-- /jQuery -->
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


<script>
$(document).ready(function() {
    $.ajax({
        url: 'http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic',
        dataType: 'json',
        success: function (eventstring) {
            $('#events').html(eventstring);
        }
    });
});
</script>

<div id="events"></div>

<?php 

?>