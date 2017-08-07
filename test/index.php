<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
if (!strstr(__DIR__, "contilas2"))
{
    die('Cannot be run outside of testsystem!');
}

error_reporting(-1);
ini_set('display_errors', 1);
require_once("../vendor/autoload.php");
require_once("lib/Testsuite.class.php");

// Contilas System Internals

chdir('../');
require_once("./config.php");
require_once("./libs/basic/mysql.php");
require_once("./libs/basic/debug.php");
require_once("./libs/basic/globalFunctions.php");
require_once("./libs/basic/user/user.class.php");
require_once("./libs/basic/groups/group.class.php");
require_once("./libs/basic/clients/client.class.php");
require_once("./libs/basic/translator/translator.class.php");
require_once("./libs/basic/countries/country.class.php");
require_once("./libs/basic/license/license.class.php");
require_once("./vendor/autoload.php");
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/associations/association.class.php';
require_once 'libs/modules/timer/timer.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'libs/modules/api/api.class.php';
require_once 'libs/basic/quickmove.class.php';
require_once 'libs/modules/dashboard/dashboard.class.php';
require_once 'libs/basic/eventqueue/eventqueue.class.php';
require_once 'libs/basic/files/file.class.php';
require_once 'libs/modules/organizer/caldav.event.class.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';
require_once 'libs/modules/costobjects/costobject.class.php';
require_once 'libs/modules/revenueaccounts/revenueaccount.class.php';
require_once 'libs/modules/accounting/receipt.class.php';
require_once 'libs/modules/textblocks/textblock.class.php';

session_start();
global $_LANG;
global $_CONFIG;
global $_HELPER;
global $_USER;

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

if ($_USER == false){
    dd("Login failed");
}
$_LANG = $_USER->getLang();

include('header.php');
// End System Internals


$testsuite = new TestSuite();
$testsuite->runTests();
$keys = array_keys($testsuite->results);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">System-Test</h3>
    </div>
    <div class="panel-body">
        <?php
        foreach ($keys as $test) {
            $results = $testsuite->results[$test]?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?php echo $test;?> - Error: <?php echo count($results['error']);?> // Success: <?php echo count($results['success']);?>
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Test</th>
                                <th>Message</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($results['error'] as $row){?>
                            <tr class="danger">
                                <td><?php echo $row[1];?></td>
                                <td><?php echo $row[2];?></td>
                                <td><?php echo $row[3];?></td>
                            </tr>
                        <?php }
                        foreach ($results['success'] as $row){?>
                            <tr class="success">
                                <td><?php echo $row[1];?></td>
                                <td><?php echo $row[2];?></td>
                                <td><?php echo $row[3];?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
