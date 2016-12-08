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
require_once 'storage.area.class.php';
require_once 'storage.position.class.php';

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

if ($_REQUEST['area']){
    $storagearea = new StorageArea((int)$_REQUEST['area']);
    $positions = StoragePosition::getAllForArea($storagearea);
} else {
    die('Lagerplatz nicht gefunden');
}

?>

<div class="table-responsive">
    <table id="storage_pos" width="100%" cellpadding="0" cellspacing="0" class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Kunde</th>
                <th>Artikel</th>
                <th>Artikel Nr.</th>
                <th>Lagermenge</th>
                <th>Mindestmenge</th>
                <th>Verantwortlicher</th>
                <th>Beschreibung</th>
                <th>Bemerkung</th>
                <th>Versandart</th>
                <th>Verpackungsart</th>
                <th>Belegung</th>
            </tr>
        </thead>
        <?php
        foreach ($positions as $position) {
            ?>
            <tr class="pointer" onclick="callBoxFancyArtFrame('libs/modules/storage/storage.position.frame.php?id=<?php echo $position->getId();?>&stid=<?php echo $storagearea->getId();?>');">
                <td><?php echo $position->getId()?></td>
                <td><?php echo $position->getBusinesscontact()->getNameAsLine()?></td>
                <td><?php echo $position->getArticle()->getTitle()?></td>
                <td><?php echo $position->getArticle()->getNumber()?></td>
                <td>
                    <?php if ($position->getAmount()<=$position->getMinAmount()) echo '<span class="error" style="color: red;">';?>
                    <?php echo printPrice($position->getAmount(),0)?>
                    <?php if ($position->getAmount()<=$position->getMinAmount()) echo '</span>';?>
                </td>
                <td><?php echo printPrice($position->getMinAmount(),0)?></td>
                <td><?php echo $position->getRespuser()->getNameAsLine()?></td>
                <td title="<?php echo $position->getDescription()?>">
                    <?php if (strlen($position->getDescription())>20) echo substr($position->getDescription(),0,20).'...'; else echo $position->getDescription();?>
                </td>
                <td title="<?php echo $position->getNote()?>">
                    <?php if (strlen($position->getNote())>20) echo substr($position->getNote(),0,20).'...'; else echo $position->getNote();?>
                </td>
                <td><?php echo $position->getDispatch()?></td>
                <td><?php echo $position->getPackaging()?></td>
                <td><?php echo $position->getAllocation()?>%</td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>