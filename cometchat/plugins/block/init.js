<?php
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");

		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
		}

		foreach ($block_language as $i => $l) {
			$block_language[$i] = str_replace("'", "\'", $l);
		}
?>

/*
 * CometChat
 * Copyright (c) 2014 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){

	$.ccblock = (function () {

		var title = '<?php echo $block_language[0];?>';

        return {

			getTitle: function() {
				return title;
			},

			init: function (params) {
				var id = params.to;
				var chatroommode = params.chatroommode;
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();

				var result = confirm('<?php echo $block_language[1];?>');

				if (result) {
					$.getJSON(baseUrl+'plugins/block/index.php?action=block&callback=?', {to: id, basedata: baseData},
						function(data) {
							alert('<?php echo $block_language[2];?>');
							setTimeout(function() {
								if ($('#cometchat_user_'+id).length > 0) {
									$('#cometchat_user_'+id+' .cometchat_closebox_bottom').click();
								}
								if($('#cometchat_user_'+id+'_popup .cometchat_user_closebox').length>0){
									$('#cometchat_user_'+id+'_popup .cometchat_user_closebox').click();
								}
							}, 1000);
						}
					);
				}
			},

			addCode: function() {
                    $('#cometchat_optionsbutton_popup .cometchat_optionstyle').append('<a class="cometchat_manage_blocklist" href="javascript:void(0);" style="margin:5px;"><?php echo $block_language[5];?></a>');
			},

			blockList: function (params) {
				var windowMode = 0;
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				loadCCPopup(baseUrl+'plugins/block/index.php?basedata='+baseData+'&embed=web', 'blocks',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=500,height=150",500,150,'<?php echo $block_language[3];?>',0,0,0,0,windowMode);
			}
        };
    })();

})(jqcc);

jqcc(document).ready(function(){
	jqcc('.cometchat_manage_blocklist').live('click',function(){
		if((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self){
			var controlparameters = {"type":"plugins", "name":"ccblock", "method":"blockList", "params":{}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		} else {
			jqcc.ccblock.blockList(0);
		}
	});
});