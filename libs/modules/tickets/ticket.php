<? // ------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			16.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
require_once ('libs/modules/tickets/ticket.class.php');

Global $_USER;

switch ($_REQUEST["exec"]) {
	case "delete":
		$del_ticket = new Ticket($_REQUEST["tktid"]);
		$del_ticket->delete();
		$ticketlog = new TicketLog();
		$ticketlog->setCrtusr($_USER);
		$ticketlog->setDate(time());
		$ticketlog->setTicket($del_ticket);
		$ticketlog->setEntry('Ticket gelöscht</br>');
		$ticketlog->save();
		require_once 'ticket.overview.php';
		break;
	case "close":
	    $close_ticket = new Ticket($_REQUEST["tktid"]);
	    $close_state = new TicketState(3);
	    $close_ticket->setClosedate(time());
	    $close_ticket->setCloseuser($_USER);
	    $close_ticket->setState($close_state);
	    $close_ticket->save();
		$ticketlog = new TicketLog();
		$ticketlog->setCrtusr($_USER);
		$ticketlog->setDate(time());
		$ticketlog->setTicket($close_ticket);
		$ticketlog->setEntry('Ticket geschlossen</br>');
		$ticketlog->save();
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