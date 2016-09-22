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
            'type'          :   'iframe',
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'width'         :   1024,
            'height'		:	768,
            'scrolling'     :   'yes',
            'overlayShow'	:	true,
            'helpers'		:   { overlay:null, closeClick:true }
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
                Vorgang anlegen
                <span class="pull-right">
                    <?=$savemsg?>
                    <a href="libs/modules/businesscontact/businesscontact.add.fancy.php" id="add_new_client">
                        <button class="btn btn-xs btn-success" type="button">
                            <span class="glyphicons glyphicons-user"></span>
                            <?=$_LANG->get('Neuen Kunden anlegen')?>
                        </button>
                    </a>
                </span>
            </h3>
	  </div>
	  <div class="panel-body">
          <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" name="neworder_form" id="neworder_form" onSubmit="return validateForm()">
              <input type="hidden" name="exec" value="edit">
              <input type="hidden" name="createNew" value="1">

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Kunden suchen</label>
                  <div class="col-sm-3">
                      <input type="text" name="search" id="search" class="form-control" required>
                      <input type="hidden" name="order_customer" id="order_customer" required>
                      <input type="hidden" name="order_contactperson" id="order_contactperson" required>
                      <input type="hidden" name="order_startart" id="order_startart" value="<?php echo $_REQUEST["startart"];?>">
                  </div>
                  <div class="col-sm-7">
                     <span class="pull-right">
                          <button class="btn btn-origin btn-sucess" type="submit">
                              <?=$_LANG->get('Auswählen')?>
                          </button>
                     </span>
                  </div>
              </div>
          </form>
	  </div>
</div>
<script>
    $("#neworder_form").validate();
</script>
