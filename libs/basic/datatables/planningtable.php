<?php
/**
 *  Copyright (c) 2018 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2018
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
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'libs/basic/eventqueue/eventqueue.class.php';
require_once 'libs/basic/eventqueue/eventclass.interface.php';
require_once 'libs/modules/mail/mailmassage.class.php';
require_once 'libs/modules/organizer/caldav.service.class.php';
require_once 'libs/modules/storage/storage.position.class.php';
require_once 'libs/modules/attachment/attachment.class.php';
require_once 'libs/modules/planning/planning.job.class.php';

require_once 'vendor/PEAR/Net/SMTP.php';
require_once 'vendor/PEAR/Net/Socket.php';
require_once 'vendor/Horde/Autoloader.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper/Default.php';
$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default('vendor'));
$autoloader->registerAutoloader();

require_once('vendor/simpleCalDAV/SimpleCalDAVClient.php');

error_reporting(-1);
ini_set('display_errors', 1);
session_start();

global $_CONFIG;
global $_USER;
$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
if ($_USER == false){
    error_log("Login failed (basic-importer.php)");
    die("Login failed");
}
$ClientId = $_USER->getClient()->getId();
$_LANG = $_USER->getLang();

include( "jscripts/datatableeditor/Editor-1.6.1/php/DataTables.php" );

use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate;


if ($_POST){
    if (count($_POST["data"]) > 0){
        foreach ($_POST["data"] as $item => $values) {
            if (strpos($item,"row_") !== false){
                $itemid = str_replace("row_","",$item);
                Cachehandler::removeCache($_CONFIG->cookieSecret."_PlanningJob_".$itemid);
            }
        }
    }
}

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'planning_jobs' )
    ->where( 'id', $_REQUEST['plid'] )
    ->fields(
        Field::inst( 'id' )->set(false)->validator( 'Validate::unique' )->validator( 'Validate::numeric' ),
        Field::inst( 'sequence' )
            ->validator( 'Validate::numeric' ),
        Field::inst( 'id', '1' )->getFormatter( function ( $val, $data, $opts ) { return ''; }),
        Field::inst( 'id', '2' )->getFormatter( function ( $val, $data, $opts ) { return ''; })
    )
    ->process( $_POST );

echo '{"data":[]}';

//$job = new PlanningJob($_REQUEST['plid']);
//$rowkeyed = PlanningJob::getPlanningRowForTable($job);
//
//$row['DT_RowId'] = $rowkeyed['id'];
//$row['details'] = '';
//$row['sequence'] = $rowkeyed['sequence'];
//$row['id'] = $rowkeyed['id'];
//$row['name'] = $rowkeyed['name'];
//$row['user'] = $rowkeyed['user'];
//$row['vonr'] = $rowkeyed['vonr'];
//$row['ticketnr'] = $rowkeyed['ticketnr'];
//$row['date'] = $rowkeyed['date'];
//$row['date_prod'] = $rowkeyed['date_prod'];
//$row['date_deliv'] = $rowkeyed['date_deliv'];
//$row['tplanned'] = $rowkeyed['tplanned'];
//$row['tactual'] = $rowkeyed['tactual'];
//$row['state'] = '<span style="font-size: medium; background-color: '.$rowkeyed['statecolor'].'" class="label">'.$rowkeyed['state'].'</span>';
//$row['calc_material'] = $rowkeyed['calc_material'];
//$row['calc_weight'] = $rowkeyed['calc_weight'];
//$row['calc_chroma'] = $rowkeyed['calc_chroma'];
//$row['calc_size'] = $rowkeyed['calc_size'];
//$row['calc_prodformat'] = $rowkeyed['calc_prodformat'];
//$row['calc_prodformatopen'] = $rowkeyed['calc_prodformatopen'];
//$row['calc_ppp'] = $rowkeyed['calc_ppp'];
//$row['calc_papercount'] = $rowkeyed['calc_papercount'];
//$row['note'] = $rowkeyed['note'];
//$row['void'] = $rowkeyed['void'];
//$row['ticketid'] = $rowkeyed['ticketid'];
//
////$resp = ["data"=>[$row]];
//// {"data":[{"DT_RowId":"row_244","id":"244","sequence":"3"}]}
//$resp = ["data"=>[$row]];
//echo json_encode($resp);