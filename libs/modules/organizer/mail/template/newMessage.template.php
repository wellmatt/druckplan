<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.03.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
?>
<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		/*
		*   Examples - images
		*/

		$("a#add_to").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>
<!-- /FancyBox -->

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
	<input type="hidden" name="exec" value="newmail">
	<input type="hidden" name="subexec" value="send">
	<input type="hidden" name="folder" value="<?=urlencode($currentFolder)?>">
	<input type="hidden" name="emailId" value="<?=$emailId?>">
	<div class="newmailHeader">
    	<table width="100%">
	        <colgroup>
    	        <col width="100">
    	   	     <col>
	        </colgroup>
        	<tr>
            	<td><b><?=$_LANG->get('Absender')?></b></td>
            	<td id="td_mail_from" name="td_mail_from">
					<select name="mail_from" style="width:180px">
					<?
					foreach($userEmailAdresses as $emailAddress) {
						echo '<option value="'.$emailAddress->getAddress().'">'.$emailAddress->getAddress().'</option>';
					}
					?>
					</select>
	           	</td>
            </tr>
	        <tr>
            	<td><b><?=$_LANG->get('Empf&auml;nger')?></b></td>
            	<td id="td_mail_to" name="td_mail_to">
            		<input name="mail_to" id="mail_to" value="<?=$recevier?>" style="width:95%" value="">
            		<a href="libs/modules/organizer/nachrichten.addemail.fancy.php" id="add_to" class="icon-link"
            			><img src="images/icons/plus-white.png" title="<?=$_LANG->get('Hinzuf&uuml;gen')?>"></a>
            	</td>
            </tr>
        	<tr>
            	<td><b><?=$_LANG->get('Betreff')?></b></td>
            	<td id="td_mail_subject" name="td_mail_subject">
            		<input name="mail_subject" value="<?=$subject?>" style="width:100%" value="">
            	</td>
        	</tr>
		</table>
	</div>
	<div class="newmailBody">
    	<textarea name="mail_body" style="height:350px;width:100%"><?=$body?></textarea>
	</div>
	<input type="submit" value="<?=$_LANG->get('Abschicken')?>">
</form>
