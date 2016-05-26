<? // -------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       07.04.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir("../../../");
require_once("libs/basic/basic.importer.php");

$_REQUEST["exec"] = trim($_REQUEST["exec"]);
$order = new Order((int)$_REQUEST["order_id"]);

// CSS und JS laden ?>
<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<!-- /jQuery -->

<script language="javascript" src="../../../jscripts/basic.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<?
/**********************************************************
 * START			Kunden aendern
 **********************************************************/
if ($_REQUEST["exec"] == "changecustomer"){
	$svmsg = "";
	if($_REQUEST["subexec"] == "save"){
		
		$customer = new BusinessContact((int)$_REQUEST["order_customer"]);
		$order->setCustomer($customer);
		$order->setCustContactperson(new ContactPerson(0)); 
		$ret = $order->save();
		
		if($savemsg = true){
			$svmsg = getSaveMessage($ret);
			$new_adress = $customer->getAddress1();
			if($customer->getAddress2() != "")
				$new_adress .= "<br>".$customer->getAddress2();
			if($customer->getZip() || $customer->getCity())
				$new_adress .= "<br>".strtoupper($customer->getCountry()->getCode())."-".$customer->getZip()." ".$customer->getCity();
			
			$tmp_cps = $customer->getContactpersons();
			$cp_return = '<option value="0">&lt; '.$_LANG->get('Bitte w&auml;hlen').'&gt</option>';
			foreach ($tmp_cps AS $cp){
				$cp_return .= '<option value="'.$cp->getId().'">'.$cp->getNameAsLine().'</option>';
			}
			
			// $new_adress .= nl2br($customer->getAddressAsLine());  geht nicht, gibt Probleme mit Zeilenumbruechen
			
			?>
			<script language="javascript">
				parent.document.getElementById('td_customer_number').innerHTML = '<?=$customer->getCustomernumber()?>';
				parent.document.getElementById('td_customer_phone').innerHTML = '<?=$customer->getPhone()?>';
				parent.document.getElementById('td_customer_name').innerHTML = '<?=$customer->getName1()." ".$customer->getName2()?>';
				parent.document.getElementById('td_customer_adress').innerHTML = '<?=$new_adress?>';
				parent.document.getElementById('td_customer_mail').innerHTML = '<?=$customer->getEmail()?>';
				parent.document.getElementById('cust_contactperson').innerHTML = '<?=$cp_return?>';
				parent.$.fancybox.close();
			</script>
			<?
		} else {
			$svmsg = getSaveMessage($ret);
		}
	} 
	
	$all_customer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);
?>

<script language="javascript">
function searchCust(){
	var search = document.getElementById('order_search_cust').value;
	if(search != '') {
    	$.post("order.ajax.php", {exec: 'searchCust', str: search}, function(data) {
    		// Select-Feld mit Rueckgabewerten fuellen
    		document.getElementById('order_customer').innerHTML = data;
    	});
	}
}
</script>

<div class="box1" style="text-align: center;">
<br>
<form action="order.change_customer.fancy.php" method="post" name="neworder_form">
	<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
	<input type="hidden" name="subexec" value="save">
	<input type="hidden" name="order_id" value="<?=$order->getId()?>">
	<table style="width:100%">
		<colgroup>
			<col>
			<col width="330">
		</colgroup>
		<tr>
			<td class="content_row_header"><h1><?=$_LANG->get('Kunde &auml;ndern')?></h1></td>
			<td class="content_row_header" align="right"><?=$svmsg?></td>
		</tr>
	    <tr>
			<td class="content_row_clear">
		    	<input name="order_search_cust" style="width:170px" class="text" id="order_search_cust">
				<span class="glyphicons glyphicons-search pointer" onclick="searchCust()"></span>
	        </td>
	        <td class="content_row_clear" id="td-selcustomer">
	            <select name="order_customer" style="width:100%" class="text" id="order_customer">
	                <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
	                <? 
	                foreach($all_customer as $cust)
	                {?>
	                    <option value="<?=$cust->getId()?>" 
	                    	<?if ($cust->getId() == $order->getCustomer()->getId()) echo 'seected="selected"';?>
	                    	><?=$cust->getNameAsLine()?>, <?=$cust->getCity()?></option> 
	                <?}?>
	            </select>
	        </td>
	    </tr>
	    <tr>
	    	<td class="content_row_clear" colspan="2" align="right">&emsp;</td>
	    </tr>
	    <tr>
	        <td class="content_row_clear" colspan="2" align="right">
	            <input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	    </tr>
	    
	</table>
</form>
</div>
<?
}
/**********************************************************
 * ENDE			Kunden aendern
**********************************************************/
?>