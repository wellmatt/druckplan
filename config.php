<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
$_BASEDIR = dirname(__FILE__) . "/";

class ConfigContainer {
    function __construct() {
    }
}

$_CONFIG = new ConfigContainer();

// Lizenz
$_CONFIG->licensePath = '';
$_CONFIG->version = '3ee741d';
$_CONFIG->version_date = '2016-09-22 12:51:09';

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

function checkErrors($type, $msg, $file, $line, $context = null) {
    if ($type == 1) {
        ob_clean();
        echo "<h1>Error!</h1>";
        echo "An error occurred while executing this script. Please contact the <a href=mailto:support@contilas.de>Support</a> to report this error.";
        echo "<p />";
        echo "Here is the information provided by the script:";
        echo "<hr><pre>";
        echo "Error code: $type<br />";
        echo "Error message: $msg<br />";
        echo "Script name and line number of error: $file:$line<br />";
        $variable_state = array_pop($context);
        echo "Variable state when error occurred: ";
        print_r($variable_state);
        echo "</pre><hr>";
        ob_end_flush();
    }
}
function check_for_fatal()
{
    $error = error_get_last();
    if ( $error["type"] == E_ERROR )
        checkErrors( $error["type"], $error["message"], $error["file"], $error["line"] );
}
register_shutdown_function( "check_for_fatal" );
set_error_handler( "checkErrors" );
?>
