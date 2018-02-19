<?php
/**
 *  Copyright (c) 2018 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2018
 *
 */


chdir('../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/planning/planning.job.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


$id = $_REQUEST['id'];
$type = $_REQUEST['type'];

if ($id && $type){
    switch ($type){
        case 1: // Vorgang
            $colinv = new CollectiveInvoice($id);
            if ($colinv->getId()>0)
                echo '<script>location.href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.edit.php&exec=edit&id='.$id.'";</script>';
            break;
        case 2: // Ticket
            $ticket = new Ticket($id);
            if ($ticket->getId()>0)
                echo '<script>location.href="index.php?page=libs/modules/tickets/ticket.edit.php&exec=edit&id='.$id.'";</script>';
            break;
    }
}