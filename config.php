<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
$_BASEDIR = dirname(__FILE__) . "/";
require_once($_BASEDIR."/libs/basic/config.php");

$_CONFIG = new ConfigContainer();

// Lizenz
$_CONFIG->licensePath = '';

// Datenbank Webserver
require_once 'config.setup.php';

$_CONFIG->lectorDB = new ConfigContainer();
$_CONFIG->lectorDB->host = ''; 
$_CONFIG->lectorDB->name = '';
$_CONFIG->lectorDB->user = '';
$_CONFIG->lectorDB->pass = '';

$_CONFIG->ezPdf = new ConfigContainer();
$_CONFIG->ezPdf->doUtf8Convert = true;

$_CONFIG->pathTranslations = './lang/';// Pfad zu Übersetungen, / am ende ist wichtig.
$_CONFIG->NumberOfPrintingmachines = 99;// Anzahl der erlaubten Druckmaschinen
$_CONFIG->farbRandBreite = 5;// Breite Farbrand in mm
$_CONFIG->anschnitt = 3;// Anschnitt in mm
$_CONFIG->docsBaseDir = 'docs/';// Verzeichnis der Dokumente
$_CONFIG->logTranslations = false;// Nicht gefundene Übersetzungen protokollieren
$_CONFIG->shopActivation = true;//Aktivitaet des OnlineShops

$_CONFIG->cache = new ConfigContainer();
$_CONFIG->cache->mail_getNewCount = 60*5; // New Mail Count in Menubar
$_CONFIG->cache->user_contruct = 60*5; // User Class Data Caching
$_CONFIG->cache->menu = 60*15; // User Menu Caching

error_reporting(E_ALL &~E_NOTICE & ~E_DEPRECATED);
?>
