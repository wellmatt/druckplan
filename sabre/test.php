<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

error_reporting(-1);
ini_set('display_errors', 1);
chdir('../');
require_once 'config.php';
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once("libs/basic/user/user.class.php");


// Autoloader
require_once 'vendor/autoload.php';
require_once 'sabre/libs/SabreAuthenticate.class.php';


$DB = new DBMysql();
$dbok = $DB->connect($_CONFIG->db);
if (!$dbok)
    die("Fehler beim Verbinden mit der Datenbank");


date_default_timezone_set('Europe/Berlin');

/**
 * Make sure this setting is turned on and reflect the root url for your WebDAV
 * server.
 *
 * This can be for example the root / or a complete path to your server script.
 */
$baseUri = '/sabre/server.php';

/**
 * Database
 *
 * Feel free to switch this to MySQL, it will definitely be better for higher
 * concurrency.
 */
$pdo = new PDO('mysql:dbname='.$_CONFIG->db->name.';host='.$_CONFIG->db->host, $_CONFIG->db->user, $_CONFIG->db->pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

/**
 * Mapping PHP errors to exceptions.
 *
 * While this is not strictly needed, it makes a lot of sense to do so. If an
 * E_NOTICE or anything appears in your code, this allows SabreDAV to intercept
 * the issue and send a proper response back to the client (HTTP/1.1 500).
 */
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");


/**
 * The backends. Yes we do really need all of them.
 *
 * This allows any developer to subclass just any of them and hook into their
 * own backend systems.
 */
$principalBackend = new \Sabre\DAVACL\PrincipalBackend\PDO($pdo, 'sabre_principals', 'sabre_groupmembers');
$carddavBackend   = new \Sabre\CardDAV\Backend\PDO($pdo, 'sabre_addressbooks', 'sabre_cards', 'sabre_addressbookchanges');
$caldavBackend    = new \Sabre\CalDAV\Backend\PDO($pdo, 'sabre_calendars', 'sabre_calendarobjects', 'sabre_calendarchanges', "sabre_calendarsubscriptions", "sabre_schedulingobjects");

/**
 * The directory tree
 *
 * Basically this is an array which contains the 'top-level' directories in the
 * WebDAV server.
 */
$nodes = [
    // /principals
    new \Sabre\CalDAV\Principal\Collection($principalBackend),
    // /calendars
    new \Sabre\CalDAV\CalendarRoot($principalBackend, $caldavBackend),
    // /addressbook
    new \Sabre\CardDAV\AddressBookRoot($principalBackend, $carddavBackend),
];

// The object tree needs in turn to be passed to the server class
$server = new \Sabre\DAV\Server($nodes);
if (isset($baseUri)) $server->setBaseUri($baseUri);

// Plugins
$server->addPlugin(new \Sabre\CalDAV\Plugin());
$server->addPlugin(new \Sabre\CardDAV\Plugin());
$server->addPlugin(new \Sabre\DAVACL\Plugin());
$server->addPlugin(new \Sabre\DAV\Sync\Plugin());

// And off we go!

prettyPrint($server);