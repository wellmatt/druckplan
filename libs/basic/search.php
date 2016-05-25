<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       15.10.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
//TOOD: Pr�fen, ob die Hidden-Felder noch gebraucht werden
?>

<script language="javascript">
    function setValues(submit){

        var search = document.getElementById('mainsearch_string').value;

    	document.getElementById('hidden_oid').value=search;
    	document.getElementById('hidden_title').value=search;
    	document.getElementById('hidden_inv').value=search;
    	document.getElementById('hidden_cust').value=search;

        if (submit = 1){
            document.getElementById('mainsearch_form').submit();
        }
      	return true;
    }
</script>

<table>
	<tr>
		<td>
<img alt="" src="images/page/icon_geschaeftskontakte.png" title="<?=$_LANG->get('Gesch&auml;ftskontakte');?>" class="quicklink" 
	 onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php'">
	 
<img alt="" src="images/page/icon_kalender.png" title="<?=$_LANG->get('Kalender');?>" class="quicklink" 
	 onclick="document.location='index.php?page=libs/modules/organizer/calendar.php'">

<img alt="" src="images/page/icon_ticketliste.png" title="<?=$_LANG->get('Tickets');?>" class="quicklink" 
	 onclick="document.location='index.php?page=libs/modules/tickets/tickets.php'" >

<img alt="" src="images/page/icon_auftraege.png" title="<?=$_LANG->get('Auftr&auml;ge');?>" class="quicklink" 
	 onclick="document.location='index.php?page=libs/modules/calculation/order.php'" >
	 
<img alt="" src="images/page/icon_planung.png" title="<?=$_LANG->get('Planung');?>" class="quicklink"  
	 onclick="document.location='index.php?page=libs/modules/schedule/schedule.php'">
	 
<img alt="" src="images/page/icon_ticket_neu.png" title="<?=$_LANG->get('Neues Ticket erstellen');?>" class="quicklink" 
	 onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=new'">
		</td>
		<td align="right">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="mainsearch_form" id="mainsearch_form" 
		onsubmit="return setvalues()">
	<?//Hidden-Felder f�r die Suche-Eintr�ge auf der Startseite?>
	<input type="hidden" name="pid" id="hidden_pid" value="0">
	<input type="hidden" name="submit_search" id="hidden_sub" value="true">
	<input type="hidden" name="header_search" id="hidden_hs" value="true">
	<input type="hidden" name="search_orderId" id="hidden_oid" value ="">
	<input type="hidden" name="search_orderTitle" id="hidden_title" value ="">
	<input type="hidden" name="search_invoiceId" id="hidden_inv" value ="">
	<input type="hidden" name="search_orderCustomer" id="hidden_cust" value ="">

	
    <?=$_LANG->get('Suche')?>
    <input 	id="mainsearch_string" name="mainsearch_string" style="width:200px" 
    		value="<?=$_REQUEST["mainsearch_string"] ?>" >&nbsp;
	<span class="glyphicons glyphicons-search pointer"
		onclick="setValues(1)" alt="">
	</span>

</form>
		</td>
	</td>
</table>




	 

	 
<!-- img style="cursor: pointer;" alt="" src="images/page/quick_new_upload.png" title="<?=$_LANG->get('Datei bereitstellen');?>"
	 onclick="document.location='index.php?pid=<?=$_CONFIG->uploadId?>&doupload=1'"-->

	 
<!-- img style="cursor: pointer;" alt="" src="images/page/quick_new_warehouse.png" title="<?=$_LANG->get('Neuen Lagerplatz erstellen');?>"
	 onclick="document.location='index.php?pid=<?=$_CONFIG->warehouseId?>&exec=edit&subexec=new'"-->