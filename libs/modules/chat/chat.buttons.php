<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			10.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

?>
<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		$("a#send_chat_mail").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>
<!-- /FancyBox -->
<h1><center>
	<?=$_LANG->get('Aktuelle Chats');?>
	<a href="libs/modules/chat/newchat.fancy.php" id="send_chat_mail">
		<img src="images/page/icon_chatoch.png" title="<?=$_LANG->get('Chat-Nachricht schreiben')?>">
	</a>
	</center>
</h1>