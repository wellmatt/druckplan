<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			24.05.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
?>

<style type="text/css">@import url(plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
 
<!-- Third party script for BrowserPlus runtime (Google Gears included in Gears runtime now) -->
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
 
<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<script type="text/javascript" src="plupload/js/i18n/de.js"></script>

<script type="text/javascript">
var conf_step = $('#confstep').val();
$('#confstep').change(function(){
    conf_step = $('#confstep').val();
});

// Convert divs to queue widgets when the DOM is ready
$(function() {
    $("#uploader").pluploadQueue({
        // General settings
        runtimes : 'gears,flash,silverlight,browserplus,html5',
        url : 'do_upload.php',
        max_file_size : '5gb',
        chunk_size : '10mb',
        unique_names : true,
  
        // Flash settings
        flash_swf_url : 'plupload/js/plupload.flash.swf',
 
        // Silverlight settings
        silverlight_xap_url : 'plupload/js/plupload.silverlight.xap',

        multipart: true,
        multipart_params: {'confstep': conf_step}
    });
 
    // Client side form validation
    $('form').submit(function(e) {
        var uploader = $('#uploader').pluploadQueue();
 
        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });
	                 
            uploader.start();
        } else {
            alert('Bitte min. eine Datei selektieren.');
        }
 
        return false;
    });
});
</script>
<div class="box2">
	<form action="index.php" method="post">
	    <div id="uploader">
	        <p>Ihr Browser unterst&uuml;zt leider kein Gears, Flash, Silverlight oder HTML5</p>
	    </div>
	    <!-- 
	    <b>Vertraulichkeit:</b>
	     <select class="text" name="confstep" id="confstep" style="width:150px">
	         <option value="4">&Ouml;ffentlich</option>
	         <option value="3">Intern</option>
	         <option value="2">Vertraulich</option>
	         <option value="1">Geheim</option>
	     </select>-->
	</form>
	Hinweise: Maximale Dateigr&ouml;&szlig;e 5 GB
</div>