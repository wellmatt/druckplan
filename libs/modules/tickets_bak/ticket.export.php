<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			19.05.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

setlocale(LC_ALL, 'de_DE');

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
$_DEBUG = new Debug();
$_LICENSE = new License();

error_reporting(-1);
ini_set('display_errors', 1);

require_once 'ticket.class.php';
//require_once 'libs/modules/organizer/nachricht.class.php';
//require_once 'libs/modules/timekeeping/timekeeper.class.php';

// dsr, Ticket-Kommentar-Export
if(isset($_REQUEST['commentexport']) && $_REQUEST['commentexport'] && !empty($_REQUEST['tktid'])) {
	/**
	 * This will initialize the csv export. It uses $all_comments to fill csv body and
	 * a download starts.
	 * For more information see https://github.com/elidickinson/php-export-data .
	 */
	require_once $_BASEDIR . 'thirdparty/csv/php-export-data.class.php';
	
	$ticket = new Ticket($_REQUEST["tktid"]);
	
	
	$all_commments = Ticketcomment::getAllTicketcomments($ticket->getId());
	
	$writer = new ExportDataCSV('browser', mb_strtolower($ticket->getTitle()) . '-comments.csv');
	$writer->initialize();
	$writer->addRow(array('Kommentare zum Ticket ' . $ticket->getTitle()));
	$writer->addRow(array('ID', 'Autor', 'Datum', 'Kommentar', 'Status'));
	foreach($all_commments as $comment) {
		/* @var $comment Ticketcomment */
		$writer->addRow(array(
				$comment->getId(),
				$comment->getCrtuser()->getNameAsLine(),
				date('d.m.Y - H:i',$comment->getCrtdate()),
				$comment->getComment(),
				$comment->getState(),
		));

	}
	$writer->finalize();
	exit;
}
?>