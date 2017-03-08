<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
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
                Cachehandler::removeCache($_CONFIG->cookieSecret."_Orderposition_".$itemid);
            }
        }
    }
}

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'collectiveinvoice_orderposition' )
    ->where( 'collectiveinvoice_orderposition.status', 0, '>' )
    ->where( 'collectiveinvoice_orderposition.collectiveinvoice', $_REQUEST['collectiveinvoice'] )
    ->debug( true )
    ->fields(
        Field::inst( 'collectiveinvoice_orderposition.id' )->set(false)->validator( 'Validate::unique' )->validator( 'Validate::numeric' ),
        Field::inst( 'collectiveinvoice_orderposition.status' )
            ->options( function () {
                return array(
                    array( 'value' => '1', 'label' => 'aktiv' ),
                    array( 'value' => '2', 'label' => 'deaktiviert' ),
                    array( 'value' => '0', 'label' => 'gelöscht' ),
                );
            })
            ->getFormatter( function ( $val, $data, $opts ) {
                switch($val){
                    case 0:
                        return 'gelöscht';
                    case 1:
                        return 'aktiv';
                    case 2:
                        return 'deaktiviert';
                }
            } ),
        Field::inst( 'collectiveinvoice_orderposition.quantity' )
            ->validator( 'Validate::numeric' )
            ->getFormatter( 'Format::toDecimalChar' )
            ->setFormatter( 'Format::fromDecimalChar' ),
        Field::inst( 'collectiveinvoice_orderposition.price' )
            ->validator( 'Validate::numeric' )
            ->getFormatter( 'Format::toDecimalChar' )
            ->setFormatter( 'Format::fromDecimalChar' ),
        Field::inst( 'taxkeys.value' )->set(false),
        Field::inst( 'collectiveinvoice_orderposition.taxkey' )
            ->options( Options::inst()
                ->table( 'taxkeys' )
                ->value( 'id' )
                ->label( 'value' )
                ->render( function ( $row ) {
                    return printPrice($row['value']).'%';
                } )
            )
            ->getFormatter( function ( $val, $data, $opts ) {
                return printPrice($data["taxkeys.value"]).'%';
            } )
            ->setFormatter( 'Format::fromDecimalChar' ),
        Field::inst( 'collectiveinvoice_orderposition.comment' ),
        Field::inst( 'collectiveinvoice_orderposition.type' )->set(false)
            ->getFormatter( function ( $val, $data, $opts ) {
                switch($val){
                    case 0:
                        return 'Manuell';
                    case 1:
                        return 'Artikel (Kalk)';
                    case 2:
                        return 'Artikel';
                    case 3:
                        return 'Perso';
                }
            } ),
        Field::inst( 'collectiveinvoice_orderposition.file_attach' )->set(false),
        Field::inst( 'collectiveinvoice_orderposition.perso_order' )->set(false),
        Field::inst( 'article.title' )->set(false),
        Field::inst( 'personalization_orders.title' )->set(false),
        Field::inst( 'collectiveinvoice_orderposition.object_id', 'title' )->set(false)->getFormatter( function ( $val, $data, $opts ) {
            if($data["collectiveinvoice_orderposition.type"] == 1 || $data["collectiveinvoice_orderposition.type"] == 2) {
                return $data["article.title"];
            } else if ($data["collectiveinvoice_orderposition.type"] == 3){
                return $data["personalization_orders.title"];
            } else {
                return "Manuell";
            }
        }),
        Field::inst( 'collectiveinvoice_orderposition.cost', 'options' )->set(false)->getFormatter( function ( $val, $data, $opts ) {
            $ret = '';
            if($data["collectiveinvoice_orderposition.type"] == 1) {
                $tmp_art = new Article($data["collectiveinvoice_orderposition.object_id"]);
                if ($tmp_art->getOrderid() > 0){
                    $ret .= '<button type="button" class="btn btn-default btn-sm" onclick="callBoxFancyContentPdf(\'libs/modules/collectiveinvoice/contentpdf.upload.frame.php?opid='.$data["collectiveinvoice_orderposition.id"].'\');">
                        <span class="glyphicons glyphicons-file-import pointer" title="PDF Inhalte"></span>
                        PDF
                    </button>';

                    $contentpdfs = ContentPdf::getAllForOrderposition(new Orderposition((int)$data["collectiveinvoice_orderposition.id"]));
                    if (count($contentpdfs)>0){
                        $ret .= '<button type="button" class="btn btn-default btn-sm" onclick="window.location.href=\'libs/basic/files/downloadzip.php?opid='.$data["collectiveinvoice_orderposition.id"].'\';">
                            <span class="filetypes filetypes-zip pointer" title="Download Zip"></span>
                            Zip
                        </button>';
                    }
                }
            }

            if ($data["collectiveinvoice_orderposition.file_attach"] > 0){
                $tmp_attach = new Attachment((int)$data["collectiveinvoice_orderposition.file_attach"]);
                $ret .= '<button class="btn btn-default btn-sm pointer" type="button" title="Angehängte Datei herunterladen" onclick="window.open(\''.Attachment::FILE_DESTINATION.$tmp_attach->getFilename().'\');">
                    <span class="glyphicons glyphicons-cd"></span>
                </button>';
            } elseif ($data["collectiveinvoice_orderposition.perso_order"] > 0){
                $perso_order = new Personalizationorder((int)$data["collectiveinvoice_orderposition.perso_order"]);
                $docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER,
                    "requestId" => $perso_order->getId(),
                    "module" => Document::REQ_MODULE_PERSONALIZATION));
                if (count($docs) > 0)
                {
                    $tmp_id = $ClientId;
                    $hash = $docs[0]->getHash();
                    $ret .= '<button class="btn btn-default btn-sm pointer" type="button" title="Download mit Hintergrund" onclick="window.open(\'./docs/personalization/'.$tmp_id.'.per_'.$hash.'_e.pdf\');">
													<span class="glyphicons glyphicons-cd">
                    </button>
                    <button class="btn btn-default btn-sm pointer" type="button" title="Download ohne Hintergrund" onclick="window.open(\'./docs/personalization/'.$tmp_id.'.per_'.$hash.'_p.pdf\');">
													<span class="glyphicons glyphicons-cd">
                    </button>';
                }
            }
            return $ret;
        } ),
        Field::inst( 'collectiveinvoice_orderposition.sequence' )->validator( 'Validate::numeric' )
    )
    ->on( 'preCreate', function ( $editor, $values ) {
        // On create update all the other records to make room for our new one
        $editor->db()
            ->query( 'update', 'collectiveinvoice_orderposition' )
            ->set( 'sequence', 'sequence+1', false )
            ->where( 'sequence', $values['sequence'], '>=' )
            ->exec();
    } )
    ->on( 'preRemove', function ( $editor, $id, $values ) {
        // On remove, the sequence needs to be updated to decrement all rows
        // beyond the deleted row. Get the current reading order by id (don't
        // use the submitted value in case of a multi-row delete).
        $order = $editor->db()
            ->select( 'collectiveinvoice_orderposition', 'sequence', array('id' => $id) )
            ->fetch();

        $editor->db()
            ->query( 'update', 'collectiveinvoice_orderposition' )
            ->set( 'sequence', 'sequence-1', false )
            ->where( 'sequence', $order['sequence'], '>' )
            ->exec();
    } )
    ->leftJoin( 'article', 'article.id', '=', 'collectiveinvoice_orderposition.object_id' )
    ->leftJoin( 'personalization_orders', 'personalization_orders.id', '=', 'collectiveinvoice_orderposition.object_id' )
    ->leftJoin( 'taxkeys', 'taxkeys.id', '=', 'collectiveinvoice_orderposition.taxkey' )
    ->process( $_POST )
    ->json();
