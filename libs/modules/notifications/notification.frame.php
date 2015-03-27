<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
?>
 
<!-- FancyBox -->
<script	type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<!-- FancyBox -->
<script type="text/javascript">
function updateNotifications(){
	$('#notification_loading_ch').html("loading...");
    $.ajax({
        url: "libs/modules/notifications/notification.ajax.php?exec=getNotifications",
        type: "GET",
        dataType: "html",
        success: function (data) {
            $('#notification_loading_ch').html(data);
        },
        error: function (xhr, status) {
            alert("Sorry, there was a problem!");
        },
        complete: function (xhr, status) {
            //$('#showresults').slideDown('slow')
        }
    });
}
function NotificationsReadAll(){
    $.ajax({
        url: "libs/modules/notifications/notification.ajax.php?exec=readAll",
        type: "POST",
        dataType: "html",
        success: function (data) {
            updateNotifications();
            refreshNotificationCount();
        },
        error: function (xhr, status) {
            alert("Sorry, there was a problem!");
        },
        complete: function (xhr, status) {
            //$('#showresults').slideDown('slow')
        	refreshNotificationCount();
        }
    });
}
function refreshNotificationCount() {
    $.ajax({
        url: "libs/modules/notifications/notification.ajax.php?exec=getCount",
        type: "GET",
        dataType: "html",
        success: function (data) {
            if (parseInt(data) > 0)
                $('#notify_count').html(data);
        }
    });
}
setInterval( refreshNotificationCount, 2*60*1000 );
refreshNotificationCount();
</script>
<style>
	#notification_ch {
/* 		width: 300px; */
/* 		height: 300px; */
		margin: 0 auto;
	}
</style>


<table cellspacing="0" cellpadding="0" width="100%">
	<tr class="tabellenlinie">
        <td>
        	<div id='notification_loading_ch'>loading...</div>
        	<div id='notification_ch'></div>
        </td>
	</tr>
	<tr class="tabellenlinie">
		<td colspan="2">
		<center>
			<a href='#' onclick='NotificationsReadAll();'>alle als gelesen markieren</a></br>
			<a href='index.php?page=libs/modules/notifications/notification.overview.php'>alle anzeigen</a>
		</center>
		</td>
	</tr>
</table>