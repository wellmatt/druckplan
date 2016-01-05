<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       18.06.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
// $customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);

?>

<script language="javascript">
titel = "<?=$_LANG->get('Bitte geben Sie einen Titel ein!')?>";
customer = "<?=$_LANG->get('Bitte w&auml;hlen Sie einen Kunden aus!')?>";
function validateForm()
{

	var title = document.forms["neworder_form"]["order_title"].value;
	var cust = document.forms["neworder_form"]["order_customer"].value;

	if (cust=="")
	{
		alert(unescape(customer));
		return false;
	}
}
</script>

<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		$("a#add_new_client").fancybox({
		    'type'    : 'iframe'
		});
	});
</script>
<!-- /FancyBox -->
 <script>
$(function() {
   $( "#search" ).autocomplete({
        delay: 0,
        source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer_and_cp',
		minLength: 2,
		dataType: "json",
        select: function(event, ui) {
			$('#order_customer').val(ui.item.bid);
			$('#order_contactperson').val(ui.item.cid);
			document.getElementById('neworder_form').submit();
        }
    });
});
</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Vorgang anlegen')?></td>
      <td align="right"><?=$savemsg?></td>
      <td width="200" class="content_header">
      	
      	<a href="libs/modules/businesscontact/businesscontact.add.fancy.php" id="add_new_client">
      		<img src="images/icons/user-business.png" title="<?=$_LANG->get('Neuen Kunden anlegen')?>">
      		<?=$_LANG->get('Neuen Kunden anlegen')?>
      	</a>
      </td>
   </tr>
</table>

<div class="box1">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="neworder_form" id="neworder_form" onSubmit="return validateForm()">
<input type="hidden" name="exec" value="edit">
<input type="hidden" name="createNew" value="1">
<table width="100%">
    <colgroup>
        <col width="180">
        <col width="280">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Kunden suchen')?></td>
        <td id="td-selcustomer">
			<input type="text" name="search" id="search" style="width:280px" required>
			<input type="hidden" name="order_customer" id="order_customer" required>
			<input type="hidden" name="order_contactperson" id="order_contactperson" required>
			<input type="hidden" name="order_startart" id="order_startart" value="<?php echo $_REQUEST["startart"];?>">
        </td>
    </tr>
    <tr>
        <td colspan="2" align="right">
            <input type="submit" value="<?=$_LANG->get('Auswählen')?>">
        </td>
    </tr>
    
</table>
</form>
<script>
  $("#neworder_form").validate();
</script>
</div>