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
require_once 'partslist.class.php';

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

$partslist = new Partslist((int)$_REQUEST['list']);

if ($partslist->getId()>0){
    $artlist = $partslist->getMyArticles();
} else {
    $artlist = null;
}

?>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Nummer</th>
            <th>Anzahl</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($artlist){
            foreach ($artlist as $item) {
                ?>
                <tr class="pointer" onclick="callBoxFancyArtFrame('libs/modules/partslists/partslist.item.frame.php?id=<?php echo $item->getId();?>');">
                    <td><?php echo $item->getId()?></td>
                    <td><?php echo $item->getArticle()->getTitle()?></td>
                    <td><?php echo $item->getArticle()->getNumber()?></td>
                    <td><?php echo printPrice($item->getAmount(),2)?></td>
                </tr>
                <?php
            }
        }?>
        </tbody>
    </table>
</div>
