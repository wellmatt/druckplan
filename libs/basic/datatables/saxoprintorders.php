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



// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'collectiveinvoice_saxoinfo' )
    ->where( 'collectiveinvoice.status', 4, '<' )
    ->where( function ( $q ) {
        if ($_REQUEST['date_min'] > 0){
            $q->where( 'collectiveinvoice_saxoinfo.compldate', $_REQUEST['date_min'], '>=' );
        }
        if ($_REQUEST['date_max'] > 0){
            $q->where( 'collectiveinvoice_saxoinfo.compldate', $_REQUEST['date_max'], '<=' );
        }
        if ($_REQUEST['saxomaterial'] != 'null'){
            $q->where( 'collectiveinvoice_saxoinfo.material', $_REQUEST['saxomaterial'], 'LIKE' );
        }
        if ($_REQUEST['saxoformat'] != 'null'){
            $q->where( 'collectiveinvoice_saxoinfo.format', $_REQUEST['saxoformat'], 'LIKE' );
        }
        if ($_REQUEST['saxoprodgrp'] != 'null'){
            $q->where( 'collectiveinvoice_saxoinfo.prodgrp', $_REQUEST['saxoprodgrp'], 'LIKE' );
        }
//        $q
//            ->where( 'age', '18', '>' )
//            ->or_where( function ( $r ) {
//                $r->where( 'name', 'Allan' );
//                $r->where( 'location', 'Edinburgh' );
//            } );
    })
    ->debug( true )
    ->fields(
        Field::inst( 'collectiveinvoice.number' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.contractid' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.referenceid' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.compldate' )->set(false)
            ->getFormatter( function ( $val, $data, $opts ) {
                return date('d.m.Y', $val);
            }),
        Field::inst( 'collectiveinvoice_saxoinfo.prodgrp' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.material' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.format' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.amount' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.chroma' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.stamp' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.form' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.bookstamp' )->set(false),
        Field::inst( 'collectiveinvoice_saxoinfo.logistic' )->set(false),
        Field::inst( 'collectiveinvoice.status' )
            ->getFormatter( function ( $val, $data, $opts ) {
                return getOrderStatus($val);
            } )
    )
    ->leftJoin( 'collectiveinvoice', 'collectiveinvoice.id', '=', 'collectiveinvoice_saxoinfo.colinvoice' )
    ->process( $_POST )
    ->json();
