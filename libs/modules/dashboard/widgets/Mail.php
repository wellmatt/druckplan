<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


?>
<link rel="stylesheet" type="text/css" href="jscripts/tagit/jquery.tagit.css" media="screen" />

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Quick Mail</h3>
	  </div>
	  <div class="panel-body">
			<form class="form-horizontal" role="form" id="quickmail" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">An</label>
                    <div class="col-sm-10">
                        <input id="mail_to" name="mail_to" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">CC</label>
                    <div class="col-sm-10">
                        <input id="mail_cc" name="mail_cc" type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Betreff</label>
                    <div class="col-sm-10">
                        <input id="mail_subject" name="mail_subject" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Text</label>
                    <div class="col-sm-10">
                        <textarea id="mail_text" name="mail_text" rows="4" cols="50" class="form-control">
                            <br>
                            <br>
                            <?php echo $_USER->getSignature();?>
                        </textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Anhang</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <label class="input-group-btn">
                                            <span class="btn btn-primary">
                                               Durchsuchen <input style="display:none;" name="mail_attachments[]" multiple="" type="file" class="form-cntrol">
                                            </span>
                            </label>
                            <input class="form-control" readonly="" type="text">
                        </div>
                    </div>
                </div>
                <button type="button" id="sendmail" class="btn btn-primary pull-right">Senden</button>
			</form>
          <input type="hidden" id="user_sig" value="<?php echo $_USER->getSignature();?>">
	  </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $("#sendmail").click(function(e) {
            if ($('#quickmail').validate()){
                e.preventDefault();
                var formData = new FormData($('#quickmail')[0]);

                $.ajax({
                    url: 'libs/modules/mail/mail.ajax.php?ajax_action=quick_send',
                    type: 'POST',
                    data: formData,
                    async: false,
                    success: function (data) {
                        $('#mail_to').tagit('removeAll');
                        $('#mail_cc').tagit('removeAll');
                        $('#mail_subject').val('');
                        tinyMCE.get('mail_text').setContent($('#user_sig').val());
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        });
        jQuery("#mail_to").tagit({
            singleField: true,
            singleFieldNode: $('#mail_to'),
            singleFieldDelimiter: ",",
            allowSpaces: false,
            minLength: 2,
            removeConfirmation: true,
            tagSource: function( request, response ) {
                $.ajax({
                    url: "libs/modules/mail/mail.ajax.php?exec=searchrcpt",
                    data: { term:request.term },
                    dataType: "json",
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                label: item.label,
                                value: item.value
                            }
                        }));
                    }
                });
            }
        });
        jQuery("#mail_cc").tagit({
            singleField: true,
            singleFieldNode: $('#mail_cc'),
            singleFieldDelimiter: ",",
            allowSpaces: false,
            minLength: 2,
            removeConfirmation: true,
            tagSource: function( request, response ) {
                $.ajax({
                    url: "libs/modules/mail/mail.ajax.php?exec=searchrcpt",
                    data: { term:request.term },
                    dataType: "json",
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                label: item.label,
                                value: item.value
                            }
                        }));
                    }
                });
            }
        });
        var mail_text = tinymce.init(
            {
                selector:'#mail_text',
                menubar: false,
                statusbar: false,
                toolbar: false
            }
        );
        $(document).on('change', ':file', function () {
            var input = $(this), numFiles = input.get(0).files ? input.get(0).files.length : 1, label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [
                numFiles,
                label
            ]);
        });
        $(':file').on('fileselect', function (event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'), log = numFiles > 1 ? numFiles + ' files selected' : label;
            if (input.length) {
                input.val(log);
            } else {
                if (log)
                    alert(log);
            }
        });
    });
</script>

<script type="text/javascript" charset="utf8" src="jscripts/tinymce/tinymce.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/tagit/tag-it.min.js"></script>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>