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
require_once 'libs/modules/materials/material.class.php';

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
Editor::inst( $db, 'machines' )
    ->fields(
        Field::inst( 'id' )->set(false),
        Field::inst( 'title' )->set(false),
        Field::inst( 'type', 'class' )->set(false)
            ->getFormatter( function ( $val, $data, $opts ) {
                $tmp = new Machine();
                $types = $tmp->getTypes();
                foreach ($types as $type) {
                    if ($val == $type['id']){
                        switch ($type['cat']){
                            case 1:
                                return 'Agentur';
                            case 2:
                                return 'Vorstufe';
                            case 3:
                                return 'Formherstellung';
                            case 4:
                                return 'Druck';
                            case 5:
                                return 'Großformatdruck';
                            case 6:
                                return 'Weiterverarbeitung';
                        }
                    }
                }
            }),
        Field::inst( 'type' )->set(false)
            ->getFormatter( function ( $val, $data, $opts ) {
                $tmp = new Machine();
                $types = $tmp->getTypes();
                foreach ($types as $type) {
                    if ($val == $type['id']){
                        return $type['name'];
                    }
                }
            })
    )
    ->process( $_POST )
    ->json();
