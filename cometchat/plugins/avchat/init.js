<?php
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");

		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
		}

		foreach ($avchat_language as $i => $l) {
			$avchat_language[$i] = str_replace("'", "\'", $l);
		}

		if ($videoPluginType == 3) {
			$width = 330; 
			$height = 330;
		} else {
			$width = 434;
			$height = 356;
		}
?>

/*
 * CometChat
 * Copyright (c) 2014 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){   
  
		$.ccavchat = (function () {
		var title = '<?php echo $avchat_language[0];?>';
		var type = '<?php echo $videoPluginType;?>';
		var lastcall = 0;
                if(type == 3) {allowresize = 0} else {allowresize = 1}

        return {

			getTitle: function() {
				return title;	
			},

			init: function (id, mode) {
				var currenttime = new Date();
				currenttime = parseInt(currenttime.getTime()/1000);
				if (currenttime-lastcall > 10) {
					if (typeof mode == 'undefined') {
                                                baseUrl = $.cometchat.getBaseUrl();
						baseData = $.cometchat.getBaseData();
						$.getJSON(baseUrl+'plugins/avchat/index.php?action=request&callback=?', {to: id, basedata: baseData});
						if (jqcc.cometchat.getThemeArray('buddylistIsDevice',id) == 1) { 
							jqcc.ccmobilenativeapp.sendnotification('<?php echo $avchat_language[5];?>', id, jqcc.cometchat.getName(jqcc.cometchat.getThemeVariable('userid')));	
						}
					} else {
						baseUrl = $.cometchat.getBaseUrl();
						baseData = $.cometchat.getBaseData();
						if (type == '5') {
							$.ajax({
								url : baseUrl+'plugins/avchat/index.php?chatroommode=1&action=request&callback=?',
								type : 'GET',
								data : {to: id, basedata: baseData},
								dataType : 'text',
								success : function(data) {
									var flag = data.split('^');
									if (flag[0] == '1'){
										alert('<?php echo $avchat_language[27];?> '+flag[1]);
									}
								},
								error : function(data) {
								}
							});
						} else {

							$.getJSON(baseUrl+'plugins/avchat/index.php?chatroommode=1&action=request&callback=?', {to: id, basedata: baseData});
							$[$.cometchat.getChatroomVars('calleeAPI')].loadCCPopup(baseUrl+'plugins/avchat/index.php?action=call&chatroommode=1&grp='+id+'&basedata='+baseData, 'audiovideochat',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $width;?>,height=<?php echo $height;?>",<?php echo $width;?>,<?php echo $height;?>,'<?php echo $avchat_language[8];?>',1,1,allowresize,1); 
							
						}
                                        }
                                                lastcall = currenttime;
				} else {
					alert('<?php echo $avchat_language[1];?>');
				}
			},

			accept: function (id,grp,join_url,start_url,mode) {
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
				if (typeof mode == 'undefined') {
					$.getJSON(baseUrl+'plugins/avchat/index.php?action=accept&callback=?', {to: id, start_url:start_url, grp: grp, basedata: baseData});
				} else {
					if (type == '5') {
						$.ajax({
							url : baseUrl+'plugins/avchat/index.php?chatroommode=1&action=accept&callback=?',
							type : 'GET',
							data : {to: id, basedata: baseData},
							dataType : 'text',
							success : function(data) {
								var flag = data.split('^');
								if (flag[0] == '1'){
									alert('<?php echo $avchat_language[27];?> '+flag[1]);
								}
							},
							error : function(data) {
							}
						});
					} else {
						$.getJSON(baseUrl+'plugins/avchat/index.php?chatroommode=1&action=accept&callback=?', {to: id, start_url:start_url, grp: grp, basedata: baseData});
					}
				}
				if(type == "5") { 
					window.open(join_url, 'audiovideochat','width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>, scrollbars=yes, resizable=yes');
				} else {
					loadCCPopup(baseUrl+'plugins/avchat/index.php?action=call&grp='+grp+'&basedata='+baseData, 'audiovideochat',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>",<?php echo $camWidth;?>,<?php echo $camHeight;?>,'<?php echo $avchat_language[8];?>',0,1,allowresize,1);
				}
			},

			accept_fid: function (id,grp,start_url) {
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				if(type == "5") {
					window.open(start_url, 'audiovideochat','width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>,scrollbars=yes, resizable=yes');
				} else {
					loadCCPopup(baseUrl+'plugins/avchat/index.php?action=call&grp='+grp+'&basedata='+baseData, 'audiovideochat',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>",<?php echo $camWidth;?>,<?php echo $camHeight;?>,'<?php echo $avchat_language[8];?>',0,1,allowresize,1);
				}
			},
                        
                       join: function (id) {
                                baseUrl = $.cometchat.getBaseUrl();
                                basedata = $.cometchat.getBaseData();
                                $[$.cometchat.getChatroomVars('calleeAPI')].loadCCPopup(baseUrl+'plugins/avchat/index.php?action=call&chatroommode=1&type=0&join=1&grp='+id+'&basedata='+basedata, 'broadcast',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>",<?php echo $camWidth;?>,<?php echo $camHeight;?>,'<?php echo $avchat_language[8];?>',1,1,allowresize,1); 
                        }

        };
    })();
 
})(jqcc);