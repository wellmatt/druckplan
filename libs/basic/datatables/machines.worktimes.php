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
                Cachehandler::removeCache($_CONFIG->cookieSecret."_MachineWorktime_".$itemid);
            }
        }
    }
}

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'machines_worktimes' )
    ->where( 'machine', $_REQUEST['machine'] )
    ->debug( true )
    ->fields(
        Field::inst( 'id' )->set(false)->validator( 'Validate::unique' )->validator( 'Validate::numeric' ),
        Field::inst( 'machine' )->validator( 'Validate::numeric' ),
        Field::inst( 'weekday' )
            ->options( function () {
                return array(
                    array( 'value' => '1', 'label' => 'Montag' ),
                    array( 'value' => '2', 'label' => 'Dienstag' ),
                    array( 'value' => '3', 'label' => 'Mittwoch' ),
                    array( 'value' => '4', 'label' => 'Donnerstag' ),
                    array( 'value' => '5', 'label' => 'Freitag' ),
                    array( 'value' => '6', 'label' => 'Samstag' ),
                    array( 'value' => '7', 'label' => 'Sonntag' ),
                );
            })
            ->getFormatter( function ( $val, $data, $opts ) {
                switch($val){
                    case 1:
                        return 'Montag';
                    case 2:
                        return 'Dienstag';
                    case 3:
                        return 'Mittwoch';
                    case 4:
                        return 'Donnerstag';
                    case 5:
                        return 'Freitag';
                    case 6:
                        return 'Samstag';
                    case 7:
                        return 'Sonntag';
                }
            } ),
        Field::inst( 'start' )
            ->validator( 'Validate::dateFormat', array(
                'empty' => false,
                'format' => 'H:i'
            ) )
            ->getFormatter( 'Format::datetime', array( 'from' => 'H:i:s', 'to' => 'H:i' ) )
            ->setFormatter( 'Format::datetime', array( 'from' => 'H:i', 'to' => 'H:i:s' ) ),
        Field::inst( 'end' )
            ->validator( 'Validate::dateFormat', array(
                'empty' => false,
                'format' => 'H:i'
            ) )
            ->getFormatter( 'Format::datetime', array( 'from' => 'H:i:s', 'to' => 'H:i' ) )
            ->setFormatter( 'Format::datetime', array( 'from' => 'H:i', 'to' => 'H:i:s' ) )
    )
    ->process( $_POST )
    ->json();
