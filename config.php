<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------

//set_include_path(get_include_path().':./pear/PEAR');
$_BASEDIR = dirname(__FILE__) . "/";		// Webserver
$_DOMAIN = "http://dev.mein-druckplan.de/";
//$_BASEDIR = "C:/Zend/Apache2/htdocs/kleindruck/";						// lokal
require_once($_BASEDIR."/libs/basic/config.php");

$_CONFIG = new ConfigContainer();

// Lizenz
$_CONFIG->licensePath = 'test.zl';

// Datenbank Webserver
$_CONFIG->db = new ConfigContainer();
$_CONFIG->db->host = 'localhost';
$_CONFIG->db->name = 'contilas2';
$_CONFIG->db->user = 'contilas2';
$_CONFIG->db->pass = 'contilas2';

$_CONFIG->lectorDB = new ConfigContainer();
$_CONFIG->lectorDB->host = ''; 
$_CONFIG->lectorDB->name = '';
$_CONFIG->lectorDB->user = '';
$_CONFIG->lectorDB->pass = '';

$_CONFIG->ezPdf = new ConfigContainer();
$_CONFIG->ezPdf->doUtf8Convert = true;

$_CONFIG->gidVerarbeitung = 3;
$_CONFIG->gidEndverarbeitung = 5;

// Mandanten?
$_CONFIG->enableClients = true;

// Schl�ssel f�r Userdaten
$_CONFIG->cookieSecret = 'cc4c4a5dfgfca54345ddg2343f511399de3c49';

// Pfad zu �bersetungen, / am ende ist wichtig.
$_CONFIG->pathTranslations = './lang/';

// Anzahl der erlaubten Druckmaschinen
$_CONFIG->NumberOfPrintingmachines = 99;

// Breite Farbrand in mm
$_CONFIG->farbRandBreite = 5;

// Anschnitt in mm
$_CONFIG->anschnitt = 3;

// PID des Papiermoduls
$_CONFIG->paperPid = 21;

// PID des Nachrichtenmoduls
$_CONFIG->mailPid = 108; // 15;

// PID des Kalendermoduls
$_CONFIG->calendarPid = 74; // 17;

// PID des Auftragmoduls
$_CONFIG->orderPid = 26;

// PID des Planungsmoduls
$_CONFIG->planPid = 33;

// PID des Gesch�ftskontaktemoduls
$_CONFIG->businesscontactPid = 72; // 20;

// PID des Schedulsmoduls
$_CONFIG->schedulePid = 33;

// PID des Sammelrechnungsmoduls
$_CONFIG->collectivePid = 46;

// PID des Lagers
$_CONFIG->warehouseId = 50;

// PID der Tickets 
$_CONFIG->ticketId = 106; // 58;

// PID der Uploads
$_CONFIG->uploadId = 52;

// PID der Uploads
$_CONFIG->chatPid = 110; // 66;

// PID des Mahnungsmoduls
$_CONFIG->invoicewarningID = 120;

// Verzeichnis der Dokumente
$_CONFIG->docsBaseDir = 'docs/';

// Nicht gefundene �bersetzungen protokollieren
$_CONFIG->logTranslations = false;

//Aktivitaet des OnlineShops
$_CONFIG->shopActivation = true;

$_CONFIG->cache = new ConfigContainer();
$_CONFIG->cache->mail_getNewCount = 60*5; // New Mail Count in Menubar
$_CONFIG->cache->user_contruct = 60*5; // User Class Data Caching
$_CONFIG->cache->menu = 60*15; // User Menu Caching

// error_reporting
error_reporting(E_ALL &~E_NOTICE & ~E_DEPRECATED);
// error_reporting(-1);

?>
