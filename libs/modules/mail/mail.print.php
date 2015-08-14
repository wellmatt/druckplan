<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
// error_reporting(-1);
// ini_set('display_errors', 1);
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/article/article.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";


require_once 'vendor/Horde/Autoloader.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper/Default.php';

$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default('vendor'));
$autoloader->registerAutoloader();

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$cache = phpFastCache("memcache");

$mailadress = new Emailaddress($_REQUEST["mailid"]);

$server = $mailadress->getHost();
$port = $mailadress->getPort();
$user = $mailadress->getAddress();
$password = $mailadress->getPassword();

try {
    /* Connect to an IMAP server.
     *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
     *     connect to a POP3 server instead. */
    $client = new Horde_Imap_Client_Socket(array(
        'username' => $user,
        'password' => $password,
        'hostspec' => $server,
        'port' => $port,
        'secure' => 'ssl',

        // OPTIONAL Debugging. Will output IMAP log to the /tmp/foo file
    //             'debug' => '/tmp/foo',

        // OPTIONAL Caching. Will use cache files in /tmp/hordecache.
        // Requires the Horde/Cache package, an optional dependency to
        // Horde/Imap_Client.
        'cache' => array(
            'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                    'dir' => '/tmp/hordecache'
                )))
            ))
        )
    ));

    $query = new Horde_Imap_Client_Fetch_Query();
    $query->structure();

    $uid = new Horde_Imap_Client_Ids($_REQUEST["muid"]);

    $list = $client->fetch($_REQUEST["mailbox"], $query, array(
        'ids' => $uid
    ));

    $part = $list->first()->getStructure();

    $content = "";
    $id = $part->findBody('html');
    if ($id == NULL)
        $id = $part->findBody();
    if ($id != NULL)
    {
        $body = $part->getPart($id);

        $query2 = new Horde_Imap_Client_Fetch_Query();
        $query2->bodyPart($id, array(
            'decode' => true,
            'peek' => false
        ));

        $list2 = $client->fetch($_REQUEST["mailbox"], $query2, array(
            'ids' => $uid
        ));

        $message2 = $list2->first();
        $content = $message2->getBodyPart($id);
        if (!$message2->getBodyPartDecode($id)) {
            $body->setContents($content);
            $content = $body->getContents();
        }

        $content = strip_tags( $content, '<img><p><br><i><b><u><em><strong><strike><font><span><div><style><a>' );
        $content = trim( $content );
        $charset = $body->getCharset();
        if ( 'iso-8859-1' === $charset ) {
            $content = utf8_encode( $content );
        } elseif ( function_exists( 'iconv' ) ) {
            $content = iconv( $charset, 'UTF-8', $content );
        }
    }
} catch (Horde_Imap_Client_Exception $e) {
    fatal_error('Could not connect to Server!');
}

?>

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/ticket.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

<script type="text/javascript" language="JavaScript">
function printPage() {
    focus();
    if (window.print) 
    {
        jetztdrucken = confirm('Seite drucken ?');
        if (jetztdrucken) 
            window.print();
    }
}
</script>

<?
/**************************************************************************
 ******* 				HTML-Bereich								*******
 *************************************************************************/?>
<body OnLoad="printPage()">
<div class="demo">	
<?php echo $content; ?>
</div>