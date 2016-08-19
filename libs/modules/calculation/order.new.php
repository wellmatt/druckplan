<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
// $customers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST);

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
	else if (title=="")
	{
		alert(titel);
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
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                Kalkulation anlegen
                <span class="pull-right">
                  <?=$savemsg?>
                </span>
            </h3>
	  </div>
	  <div class="panel-body">
          <form action="index.php?page=<?=$_REQUEST['page']?>" class="form-horizontal" method="post" name="neworder_form" id="neworder_form" onSubmit="return validateForm()">
              <input type="hidden" name="exec" value="edit">
              <input type="hidden" name="createNew" value="1">
              <div class="form-group">
                  <label for="" class="col-sm-3 control-label">Titel</label>
                  <div id="td-title" class="col-sm-9">
                      <input name="order_title" id="" value="" class="form-control">
                  </div>
              </div>
              <input type="hidden" name="order_customer" id="order_customer" value="0">
              <input type="hidden" name="order_contactperson" id="order_contactperson" value="0">
              <br>
              <span class="pull-right">
                  <button class="btn btn-default btn-success" type="submit">
                      <?= $_LANG->get('Anlegen') ?>
                  </button>
              </span>
          </form>
	  </div>

    <script>
        $("#neworder_form").validate();
    </script>
</div>
