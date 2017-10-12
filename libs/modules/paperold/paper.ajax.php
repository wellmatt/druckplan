<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

chdir ("../../../");
require_once 'libs/basic/user/user.class.php';
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once("libs/basic/groups/group.class.php");
require_once("libs/modules/businesscontact/contactperson.class.php");
require_once 'libs/modules/organizer/urlaub.class.php';
require_once 'libs/modules/organizer/event_holiday.class.php';
require_once 'libs/modules/paper/paper.class.php';

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

if ($_REQUEST["exec"] == 'getAllPaper')
{
    // Short-circuit if the client did not give us a date range.
    if (!isset($_GET['query'])) {
        $query = false;
    } else {
        $query = true;
    }

    $papers = Paper::getAllPapersByName(Paper::ORDER_NAME,$_REQUEST['query']);
    $json = [];
    foreach ($papers as $paper) {
        $json[] = ['id'=>$paper->getId(),'name'=>$paper->getName()];
    }
    echo json_encode($json);
}

