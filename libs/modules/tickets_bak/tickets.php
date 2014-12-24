<? // ------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			16.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
require_once ('ticket.class.php');

if($_REQUEST["setStatus1"] != ""){
	$ticket = new Ticket((int)$_REQUEST["tktid"]);
	$ticket->setState1((int)$_REQUEST["setStatus1"]);
	$savemsg = getSaveMessage($ticket->save()).$DB->getLastError();
}
if($_REQUEST["setStatus2"] != ""){
	$ticket = new Ticket((int)$_REQUEST["tktid"]);
	$ticket->setState2((int)$_REQUEST["setStatus2"]);
	$savemsg = getSaveMessage($ticket->save()).$DB->getLastError();
}
if($_REQUEST["setStatus3"] != ""){
	$ticket = new Ticket((int)$_REQUEST["tktid"]);
	$ticket->setState3((int)$_REQUEST["setStatus3"]);
	$savemsg = getSaveMessage($ticket->save()).$DB->getLastError();
}
if($_REQUEST["setStatus4"] != ""){
	$ticket = new Ticket((int)$_REQUEST["tktid"]);
	$ticket->setState4((int)$_REQUEST["setStatus4"]);
	$savemsg = getSaveMessage($ticket->save()).$DB->getLastError();
}

switch ($_REQUEST["exec"]) {
	case "delete":
		$del_ticket = new Ticket($_REQUEST["delid"]);
		$del_ticket->delete();
		require_once 'ticket.overview.php';
		break;
	case "edit":
		// Daten setzen und speichern geschieht in der ticket.edit.php
		require_once 'ticket.edit.php';
		break;
	case "new":
		require_once 'ticket.edit.php';
		break;
	default:
		require_once 'ticket.overview.php';
		break;
}
?>