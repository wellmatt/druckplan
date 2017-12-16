<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'thirdparty/phpfastcache/phpfastcache.php';
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/chat/chat.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
error_reporting(-1);
ini_set('display_errors', 1);

session_start();
global $_LANG;
global $_CONFIG;

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$_USER = new User((int)$_CONFIG->dpv3user);

function createTicket(BusinessContact $customer, ContactPerson $custcp, $title, $duedate, User $assigned, TicketCategory $ticketCategory, TicketState $ticketState, TicketPriority $ticketPriority, $timeplanned, $comment){
    global $_USER;
    $ticket = new Ticket();
    $ticket->setCustomer($customer);
    $ticket->setCustomer_cp($custcp);
    $ticket->setTitle($title);
    $ticket->setDuedate($duedate);
    $ticket->setAssigned_user($assigned);
    $ticket->setCategory($ticketCategory);
    $ticket->setState($ticketState);
    $ticket->setPriority($ticketPriority);
    $ticket->setSource(Ticket::SOURCE_JOB);
    $ticket->setPlanned_time($timeplanned);
    $ticket->setCrtuser($_USER);

    $save_ok = $ticket->save();
    if ($save_ok)
    {
        $comment = new Comment();
        $comment->setCrtuser($_USER);
        $comment->setCrtdate(time());
        $comment->setModule("Ticket");
        $comment->setObjectid($ticket->getId());
        $comment->setTitle("aus Lekorimport generiert");
        $comment->setVisability(Comment::VISABILITY_INTERNAL);
        $comment->setComment($comment);
        $comment->save();
        return $ticket->getId();
    }
    return 'false';
}

$reqparams = [ "custid", "cpid", "title", "duedate", "assignedid", "ticketcategoryid", "ticketstateid", "ticketpriorityid", "timeplanned", "comment" ];
$error = false;

foreach ($reqparams as $reqparam) {
    if (!isset($_REQUEST[$reqparam])){
        $error = true;
    }
}

if ($error === false){
    // validate
    $customer = new BusinessContact($_REQUEST["custid"]);
    if ($customer->getId() == 0) $error = true;

    if ($_REQUEST["cpid"] == 0){
        $cp = ContactPerson::getMainContact($customer);
        if ($cp->getId() == 0) $error = true;
    } else {
        $cp = new ContactPerson($_REQUEST["cpid"]);
        if ($cp->getId() == 0) $error = true;
    }

    $title = $_REQUEST['title'];
    $duedate = $_REQUEST['duedate'];
    $assigned = new User($_REQUEST["assignedid"]);
    if ($assigned->getId() == 0) $error = true;
    $ticketcategory = new TicketCategory($_REQUEST["ticketcategoryid"]);
    if ($ticketcategory->getId() == 0) $error = true;
    $ticketstate = new TicketState($_REQUEST["ticketstateid"]);
    if ($ticketstate->getId() == 0) $error = true;
    $ticketpriority = new TicketPriority($_REQUEST["ticketpriorityid"]);
    if ($ticketpriority->getId() == 0) $error = true;
    $timeplanned = $_REQUEST['timeplanned'];
    $comment = base64_decode($_REQUEST['comment']);

    $response = createTicket($customer, $cp, $title, $duedate, $assigned, $ticketcategory, $ticketstate, $ticketpriority, $timeplanned, $comment);
    echo $response;
} else {
    echo 'false';
}