<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="msg_list_form" name="msg_list_form">
	<input name="folder" id="folder" value="<?=urlencode($currentFolder)?>" type="hidden"/>
	<input name="emailId" id="emailId" value="<?=$emailId?>" type="hidden"/>
	<input name="moveToOrderId" value="0" type="hidden" id="moveToOrderId" />
	<input name="messagecount" value="<?=count($messages)?>" type="hidden" id="messagecount" />
	<table width="100%" cellspacing="0" cellpadding="0">
		<colgroup>
			<col width="20">
			<col width="20">
			<col width="150">
			<col>
			<col width="150">
			<col width="150">
		</colgroup>
		<tr>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_header"><?=$_LANG->get('Von')?></td>
			<td class="content_row_header"><?=$_LANG->get('Betreff')?></td>
			<td class="content_row_header"><?=$_LANG->get('Datum')?></td>
			<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
		</tr>
		
<?

// print_r($messages[0]);

use Zend\Mail\Headers;

$x = 1;
foreach ($messages as $message) {
	$from = $message["from"];
	$match = array();
	preg_match('/<(.*?)>/', $from, $match);
	$fromMailOnly = $match[1];
	$subject = $message["subject"];
	$date = date('d.m.Y - H:m:s', $message["date"]);
	$link = "index.php?page=".$_REQUEST['page']."&folder=".urlencode($currentFolder)."&message=".$message["id"]."&emailId=".$emailId;
?>
		<tr class="pointer <?=getRowColor($x)?>" id="msg_<?=$message["id"]?>" onmouseover="mark(this,0)" onmouseout="mark(this,1)">
			<td class="content_row icon-link"><input type="checkbox" id="chk_msg" name="chk_msg_<?=$message["id"]?>" value="1" /></td>
			<? if ($message["flags"]["\Seen"]) { ?>
				<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><span class="glyphicons glyphicons-message-full"id="mail_img_<?=$message["id"]?>"></span></td>  <!-- document.location='<?=$link?>' -->
			<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><?=$from?></td>
				<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><?=$subject?></td>
			<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><?=$date?></td>
			<? } else { ?>
				<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><span class="glyphicons glyphicons-envelope"id="mail_img_<?=$message["id"]?>"  ></span></td>  <!-- document.location='<?=$link?>' -->
			<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><b><?=$from?></b></td>
				<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><b><?=$subject?></b></td>
			<td class="content_row icon-link" onclick="showMail(<?=$message["id"]?>)"><b><?=$date?></b></td>
			<? } ?>
			<td class="content_row icon-link">
			<a href="index.php?page=<?=$_REQUEST['page']?>&exec=newmail&subexec=forward&folder=<?=urlencode($currentFolder)?>&emailId=<?=$emailId?>&message=<?=$message["id"]?>">
				<span class="glyphicons glyphicons-message-out" title="Weiterleiten"></span></a>
			&nbsp;
			<a href="index.php?page=<?=$_REQUEST['page']?>&exec=newmail&subexec=answer&folder=<?=urlencode($currentFolder)?>&emailId=<?=$emailId?>&message=<?=$message["id"]?>">
				<span class="glyphicons glyphicons-message-new" title="Antworten"></span></a>
			&nbsp;
			<a href="index.php?page=<?=$_REQUEST['page']?>&exec=delete&folder=<?=urlencode($currentFolder)?>&emailId=<?=$emailId?>&chk_msg_<?=$message["id"]?>=1">
				<span class="glyphicons glyphicons-message-minus" title="Löschen"></span></a>
			&nbsp;
			<img src="images/status/loading2.gif" id="img_mail_loading_<?=$message["id"]?>" style="display: none;"> 
			</td>
		</tr>
		<tr id="msg_body_<?=$message["id"]?>" class="mail_body" style="display: none">
		</tr>
<?
$x++;}
?>
	</table>
<?
	echo "Seite: ";
	for($i = 1; $i <= $totalPages; $i++){
		if ($i == $page) {
			echo '<b><u>'.$i.'</u></b>';
		} else {
			echo '<a href="index.php?page='.$_REQUEST['page'].'&p='.$i.'">'.$i.'</a>';
		}
		if ($i < $totalPages-1) {
			echo " - ";
		}
	}
?>
	<br><br>
	Mail Optionen:</br>
	<select name="exec" style="width:180px" onchange="document.msg_list_form.submit();">
	    <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
	    <option value="delete"><?=$_LANG->get('L&ouml;schen')?></option>
	    <option value="markread"><?=$_LANG->get('Als gelesen markieren')?></option>
	    <option value="markunread"><?=$_LANG->get('Als ungelesen markieren')?></option>
	</select>
	&nbsp;&nbsp;</br></br>
	Mail verschieben:</br>
	<select name="move" id="move" style="width:190px">
	    <option value="">&lt; <?=$_LANG->get('Verschieben nach...')?> &gt;</option>
<?
foreach($folders as $folder) {
	if($folder != $currentFolder && $folder->isSelectable()) { // (strpos($folder, '/') == FALSE) && 
		echo '<option value="'.urlencode($folder).'">'.utf7_decode($folder->getGlobalName()).'</option>';
	}
}
?>
		<option value="_TO_CUSTOMER_">&lt; Nach Kunde verschieben... &gt;</option>
	</select>
    <select name="customerId" id="customerId">
        <option value="">&lt; Kunden wählen &gt;</option>
        <?php
        $customers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);
		foreach($customers as $cust)
		{?>
			<option value="<?=$cust->getId()?>"><?=$cust->getNameAsLine()?>, <?=$cust->getCity()?></option> 
		<?}?>
    </select>
    &nbsp;&nbsp;
    <select name="orderId" id="orderId">
        <option value="">&lt; Auftrag wählen &gt;</option>

    </select>
</form>
<script language="javascript">
function showMail(msgid){
	var el = document.getElementById('msg_body_'+msgid);
	var folder = document.getElementById('folder').value;
	var Id = document.getElementById('emailId').value;
	
	if ( el.style.display != 'none' ) {
		el.style.display = 'none';
	} else {
		document.getElementById('img_mail_loading_'+msgid).style.display = '';
		$.post("libs/modules/organizer/nachrichten.ajax.php", 
			{exec: 'getMessageContent', emailId: Id, emailFolder: '<?=$currentFolder?>', messageId: msgid}, 
			 function(data) {
				el.innerHTML = data;
				document.getElementById('img_mail_loading_'+msgid).style.display = 'none';
				document.getElementById('mail_img_'+msgid).src = 'images/icons/mail-open.png';
			});
		el.style.display = '';
	}
		
}
</script>
<script>
    $(document).ready(function() {

        var $customerSelect = $('#customerId');
        $customerSelect.hide();
        $('#move').on('change',function(e) {
            var opt = $(this).val();
            if('_TO_CUSTOMER_' == opt) {
                $customerSelect.show();
            } else {
                document.msg_list_form.submit();
            }
        });

        var $orderSelect = $('#orderId');
        $orderSelect.hide();
        $('#customerId').on('change',function(e) {
            var opt = $(this).val();
            if('' != opt) {
				//var cust_id = $('#customerId').val();
				$.post("libs/modules/organizer/nachrichten.ajax.php", 
					{exec: 'getOrdersForCustomer', cust_id : $('#customerId').val()}, 
					 function(data) {
						document.getElementById('orderId').innerHTML = data;
					});
                $orderSelect.show();
            } else if('' == opt) {
                $orderSelect.hide();
            } else {
                document.msg_list_form.submit();
            }
        });
		
        $orderSelect.on('change', function() {

            var orderTitle = $(this).find(':selected').text(),
                orderId = $(this).val();
            if('' != orderId) {

                var selectedMessages = $('input[name=^="chk_msg"]:checked'),
                    selectedMessagesCount = selectedMessages.length;

                if(!selectedMessagesCount) {
                    alert("Bitte wählen Sie zuerst die Nachricht(en) aus, die verschoben werden soll(en).");
                    return;
                }

                var msg = (selectedMessagesCount == 1)
                    ? 'Soll die gewählte Nachricht wirklich in die Dokumentenverwaltung zum Auftrag "' + orderTitle + '" verschoben werden?'
                    : 'Sollen die Nachrichten (' + selectedMessagesCount + ' E-Mails) wirklich in die Dokumentenverwaltung zum Auftrag "' + orderTitle + '" verschoben werden?';

                if(window.confirm(msg)) {
                    $('#moveToOrderId').val(orderId);
                    $('#msg_list_form').submit();
                } else {
                    // Cancel
                }
            }

        });
    });



</script>