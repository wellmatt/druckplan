<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir("../../../");
require_once("config.php");
// error_reporting(-1);
// ini_set('display_errors', 1);
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'suporder.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false) {
    error_log("Login failed (basic-importer.php)");
    die("Login failed");
}

if ($_REQUEST['suporder']){
    $suporder = new SupOrder((int)$_REQUEST['suporder']);
    $positions = SupOrderPosition::getAllForSupOrder($suporder);
} else {
    die('Lagerplatz nicht gefunden');
}

?>

<table id="storage_pos" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
    <thead>
        <tr>
            <th>#</th>
            <th>Artikel</th>
            <th>Artikel #</th>
            <th>zug. VO.</th>
            <th>Menge</th>
        </tr>
    </thead>
    <?php
    foreach ($positions as $position) {
        ?>
        <tr class="pointer" onclick="callBoxFancyArtFrame('libs/modules/suporder/suporder.position.frame.php?id=<?php echo $position->getId();?>');">
            <td><?php echo $position->getId();?></td>
            <td><?php echo $position->getArticle()->getTitle();?></td>
            <td><?php echo $position->getArticle()->getNumber();?></td>
            <td><?php if($position->getColinvoice()->getId()>0) echo $position->getColinvoice()->getNumber();?></td>
            <td><?php echo $position->getAmount();?></td>
        </tr>
        <?php
    }
    ?>
</table>