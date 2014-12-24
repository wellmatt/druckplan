<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.11.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/tickets/ticketcomment.class.php';

/*
 * Liste der Ansprechpartner aktualisieren
 */
if ($_REQUEST["ajax_action"] == "updateCustomerCP"){
	$customer = new BusinessContact((int)$_REQUEST["customerID"]);
	$allContactPerson = ContactPerson::getAllContactPersons($customer, ContactPerson::ORDER_NAME);
	foreach ($allContactPerson as $cp){
		?><option value="<?=$cp->getId()?>"><?= $cp->getNameAsLine()?></option><?
	} 
}

/*
 * Status eine Auftrags aktualisieren
 */
if ($_REQUEST["ajax_action"] == "updateOrderStatus"){
	$order = new Order((int) $_REQUEST["orderID"]);
	echo $order->getStatusImage();
}

/*
 * Link zur Kalkulation aktualisieren 
 */
if ($_REQUEST["ajax_action"] == "updateOrderHref"){
	//$tmp_order = new Order((int)$_REQUEST["orderID"]);
	$orderID = (int)$_REQUEST["orderID"];
	echo "index.php?page=".$_REQUEST['page']."&exec=edit&id=".$orderID."&step=4";
}

/*
 * 31.01.14 gln: Kommentar zum Editieren laden 
 */
if ($_REQUEST["ajax_action"] == "updateComment"){
	$editid = (int)$_REQUEST["commentID"];
	$tktcom = new Ticketcomment($editid);
	echo $tktcom->getComment();
}

/*
 * Liste der Auftraege eines Geschaeftskontakts aktualisieren
 */
if ($_REQUEST["ajax_action"] == "updateOrderList"){
	//$customer = new BusinessContact();
	$all_orders = Order::getAllOrdersByCustomer(Order::ORDER_TITLE, (int)$_REQUEST["customerID"]);
	foreach ($all_orders as $order){
		?><option value="<?=$order->getId()?>"><?=$order->getTitle()?></option><?
	} 
}

/*
 * Liste der Planungen eines Geschaeftskontakts aktualisieren
 */
if ($_REQUEST["ajax_action"] == "updatePlanningList"){
	//$customer = new BusinessContact();
	$all_schedules = Schedule::getAllSchedulesForCustomer(Schedule::ORDER_NUMBER, (int)$_REQUEST["customerID"]);
	foreach ($all_schedules as $schedule){
		?><option value="<?=$schedule->getId()?>"><?=$schedule->getNumber()?> (<?=$schedule->getCustomer()->getNameAsLine();?>)</option><?
	} 
}

/*
 * Status-Bild einer Planungaktualisieren
 */
if ($_REQUEST["ajax_action"] == "updatePlanningStatus"){
	$tmp_planning = new Schedule((int) $_REQUEST["planID"]);
	echo $tmp_planning->getStatusImage();
}

/*
 * Link zur Planungen aktualisieren
 */
if ($_REQUEST["ajax_action"] == "updatePlanningHref"){
	//$tmp_planning = new Schedule((int)$_REQUEST["planID"]);
	$planID = (int)$_REQUEST["planID"];
	echo "index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=".$planID; 
}

/*
 * Liste des Status einer Abteilung aktualisieren (Filteroptionen)
*/
if ($_REQUEST["ajax_action"] == "updateStatusSearch"){
	$customer = new BusinessContact();
	switch ($_REQUEST["statusID"]){
		case 2 : ?>	<option value="7"><?=getTicketStatus2(7)?></option>
					<option value="2"><?=getTicketStatus2(2)?></option>
					<option value="3"><?=getTicketStatus2(3)?></option>
					<option value="4"><?=getTicketStatus2(4)?></option>
					<option value="5"><?=getTicketStatus2(5)?></option>
					<option value="6"><?=getTicketStatus2(6)?></option>
					<option value="1"><?=getTicketStatus2(1)?></option>
					<option value="0"><?=getTicketStatus2(0)?></option>
		<?		break;
		case 3 : ?>	<option value="10"><?=getTicketStatus3(10)?></option>
					<option value="3" ><?=getTicketStatus3(3)?></option>
					<option value="4" ><?=getTicketStatus3(4)?></option>
					<option value="5" ><?=getTicketStatus3(5)?></option>
					<option value="6" ><?=getTicketStatus3(6)?></option>
					<option value="7" ><?=getTicketStatus3(7)?></option>
					<option value="8" ><?=getTicketStatus3(8)?></option>
					<option value="9" ><?=getTicketStatus3(9)?></option>
					<option value="11"><?=getTicketStatus3(11)?></option>
					<option value="1" ><?=getTicketStatus3(1)?></option>
					<option value="0" ><?=getTicketStatus3(0)?></option>
		<?		break;
		case 4 : for($i=1; $i<=3;$i++){?>
					<option value="<?=$i?>" ><?=getTicketStatus4($i)?></option>
		<?		 }
				break;
	}
 
}

?>