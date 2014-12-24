<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
	<input type="hidden" name="exec" value="newmail">
	<input type="hidden" name="subexec" value="send">
	<div class="newmailHeader">
    	<table width="100%">
	        <colgroup>
    	        <col width="100">
    	   	     <col>
	        </colgroup>
        	<tr>
            	<td><b><?=$_LANG->get('Empf&auml;nger')?></b></td>
            	<td id="td_mail_to" name="td_mail_to">
            		<input name="mail_to" style="width:100%" value="">
            	</td>
            </tr>
        	<tr>
            	<td><b><?=$_LANG->get('Betreff')?></b></td>
            	<td id="td_mail_subject" name="td_mail_subject">
            		<input name="mail_subject" style="width:100%" value="">
            	</td>
        	</tr>
		</table>
	</div>
	<div class="newmailBody">
    	<textarea name="mail_body" style="height:350px;width:100%"></textarea>
	</div>
	<input type="submit" value="<?=$_LANG->get('Abschicken')?>">
</form>
