<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
error_reporting(-1);
ini_set('display_errors', 1);
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


require_once __DIR__.'/../../../vendor/Horde/Autoloader.php';
require_once __DIR__.'/../../../vendor/Horde/Autoloader/ClassPathMapper.php';
require_once __DIR__.'/../../../vendor/Horde/Autoloader/ClassPathMapper/Default.php';

$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default(__DIR__.'/../../../vendor'));
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
/*
 * Local functions
 */
function fatal_error ( $sErrorMessage = '' )
{
    header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
    die( $sErrorMessage );
}

$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));

if ($_REQUEST["exec"] == "getMailBody" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muid"]) {
    
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
        
        $map = $part->ContentTypeMap();
        $attachments = array();
        foreach ( $map as $key => $value ) {
            $p = $part->getPart( $key );
            $disposition = $p->getDisposition();
            if ( ! in_array( $disposition, array( 'attachment', 'inline' ) ) ) {
                continue;
            }
            $name = $p->getName();
            $type = $p->getType();
            if ( 'inline' === $disposition && 'text/plain' === $type ) {
                continue;
            }
            $new_attachment = array(
                'disposition' => $disposition,
                'type' => $p->getPrimaryType(),
                'mimetype' => $type,
                'mime_id' => $key,
                'name' => $name,
            );
            $attachments[] = $new_attachment;
        }
        
        if (count($attachments)) 
        {
            $attachment_html = '<div class="row"><div class="col-md-1"><b>Anhänge:</b></div><div class="col-md-11">';
            foreach ($attachments as $attachment)
            {
                $attachment_html .= '<a href="libs/modules/mail/mail.ajax.php?exec=getAttachment&mailid='.$_REQUEST["mailid"].'&mailbox='.$_REQUEST["mailbox"].'&muid='.$_REQUEST["muid"].'&mime_id='.$attachment["mime_id"].'">' . $attachment["name"] . '</a> (<a target="_blank" href="libs/modules/mail/mail.ajax.php?exec=getAttachment&mailid='.$_REQUEST["mailid"].'&mailbox='.$_REQUEST["mailbox"].'&muid='.$_REQUEST["muid"].'&mime_id='.$attachment["mime_id"].'&preview=true">Vorschau</a>)</br>';
            }
            $attachment_html .= '</div></div>';
        } else {
            $attachment_html = '';
        }
        
        $content = "";
        $id = $part->findBody('html');
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
        
        $content .= $attachment_html;
        echo $content;
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "getAttachment" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muid"] && $_REQUEST["mime_id"]) {
    
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
        
        $uid = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
        $mime_id = $_REQUEST["mime_id"];
        
        $query = new Horde_Imap_Client_Fetch_Query();
        $query->bodyPart( $mime_id, array(
            'decode' => true,
            'peek' => true,
        )
        );
        $list = $client->fetch( $_REQUEST["mailbox"], $query, array(
            'ids' => $uid,
        )
        );
        $message = $list->first();
        
        $image_data = $message->getBodyPart( $mime_id );
        $image_data_decoded = base64_decode( $image_data );

        $p = $part->getPart( $mime_id );
        $name = $p->getName();
        
        $filename = time()."_".$name;
        if(!file_exists("docs/attachments/".$filename)) {
            $fh = fopen("docs/attachments/".$filename, "w");
            fwrite($fh, $image_data_decoded);
            fclose($fh);
        }
        
        if (!$_REQUEST["preview"])
        {
            $file = "docs/attachments/".$filename;
            header('Content-Type: "application/octet-stream"');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header("Content-Transfer-Encoding: binary");
            header('Expires: 0');
            header('Pragma: no-cache');
            header("Content-Length: ".filesize($file));
            $data = readfile($file);
            exit($data);
        } else {
            echo '<script language="JavaScript">window.location.href="../../../docs/attachments/'.$filename.'";</script>';
        }
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "mark_as_read" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muid"]) {
    
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
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
        $flag = Horde_Imap_Client::FLAG_SEEN;
        $client->store( $_REQUEST["mailbox"], array('add' => array( $flag ),'ids' => $uids,));
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "mark_as_read_multiple" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muids"]) {
    
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
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muids"] );
        $flag = Horde_Imap_Client::FLAG_SEEN;
        $client->store( $_REQUEST["mailbox"], array('add' => array( $flag ),'ids' => $uids,));
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "mark_as_unread" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muid"]) {
    
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
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
        $flag = Horde_Imap_Client::FLAG_SEEN;
        $client->store( $_REQUEST["mailbox"], array('remove' => array( $flag ),'ids' => $uids,));
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "mark_as_unread_multiple" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muids"]) {
    
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
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muids"] );
        $flag = Horde_Imap_Client::FLAG_SEEN;
        $client->store( $_REQUEST["mailbox"], array('remove' => array( $flag ),'ids' => $uids,));
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "delete" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muid"]) {
    
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
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
        $flag = Horde_Imap_Client::FLAG_DELETED;
        $client->store( $_REQUEST["mailbox"], array('add' => array( $flag ),'ids' => $uids,));
        $client->expunge($_REQUEST["mailbox"]);
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "delete_multiple" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["muids"]) {
    
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
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muids"] );
        $flag = Horde_Imap_Client::FLAG_DELETED;
        $client->store( $_REQUEST["mailbox"], array('add' => array( $flag ),'ids' => $uids,));
        $client->expunge($_REQUEST["mailbox"]);
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "copy" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["dest_mailbox"] && $_REQUEST["muid"]) {
    
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
        
        if ($_REQUEST["move"])
            $move = true;
        else
            $move = false;
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
        $flag = Horde_Imap_Client::FLAG_DELETED;
        $client->copy($_REQUEST["mailbox"], $_REQUEST["dest_mailbox"], array('ids' => $uids, 'move'=> $move));
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "copy_multiple" && $_REQUEST["mailid"] && $_REQUEST["mailbox"] && $_REQUEST["dest_mailbox"] && $_REQUEST["muids"]) {
    
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
        
        if ($_REQUEST["move"])
            $move = true;
        else
            $move = false;
        
        $uids = new Horde_Imap_Client_Ids( $_REQUEST["muids"] );
        $flag = Horde_Imap_Client::FLAG_DELETED;
        $client->copy($_REQUEST["mailbox"], $_REQUEST["dest_mailbox"], array('ids' => $uids, 'move'=> $move));
    
    } catch (Horde_Imap_Client_Exception $e) {
//         var_dump($e);
        fatal_error('Could not connect to Server!');
    }
} else if ($_REQUEST["exec"] == "getNewCount") {
//     $time_start = microtime(true);
    $mail_getNewCount = $cache->get("mail_getNewCount");
    if ($mail_getNewCount === null)
    {
        $mailadresses = $_USER->getEmailAddresses();
        if (count($mailadresses)>0)
        {
            try {
                $client = new Horde_Imap_Client_Socket(array(
                    'username' => $mailadresses[0]->getAddress(),
                    'password' => $mailadresses[0]->getPassword(),
                    'hostspec' => $mailadresses[0]->getHost(),
                    'port' => $mailadresses[0]->getPort(),
                    'secure' => 'ssl',
                    'cache' => array(
                        'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                            'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                                'dir' => '/tmp/hordecache'
                            )))
                        ))
                    )
                ));
                $query = new Horde_Imap_Client_Search_Query();
                $query->flag(Horde_Imap_Client::FLAG_SEEN, false);
                $query->intervalSearch(
                    604800,
                    Horde_Imap_Client_Search_Query::INTERVAL_YOUNGER
                );
                 
                $results = $client->search('INBOX', $query, array(
                    'sort' => array(
                        Horde_Imap_Client::SORT_FROM
                    )
                ));
                $uids = $results['match'];
                $cache->set("mail_getNewCount",count($uids), $_CONFIG->cache->mail_getNewCount);
                echo count($uids);
//                 $time_end = microtime(true);
//                 $time = $time_end - $time_start;
//                 echo "</br>done in " . $time . " seconds";
            } catch (Horde_Imap_Client_Exception $e) {
            }
        }
    } else 
    {
        echo $mail_getNewCount;
//         $time_end = microtime(true);
//         $time = $time_end - $time_start;
//         echo "</br>cached done in " . $time . " seconds";
    }
} else if ($_REQUEST["exec"] == "searchrcpt" && $_REQUEST["term"]) {
    $retval = Array();
    $bcs = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_ID," name1 LIKE '%{$_REQUEST["term"]}%' OR name2 LIKE '%{$_REQUEST["term"]}%' OR email LIKE '%{$_REQUEST["term"]}%' OR matchcode LIKE '%{$_REQUEST["term"]}%' ",BusinessContact::LOADER_BASICS);
    foreach ($bcs as $bc){
        $retval[] = Array("label" => "Geschäftskontakt: " . $bc->getNameAsLine() . " (". $bc->getEmail() . ")", "value" => $bc->getEmail());
    }
    $cps = ContactPerson::getAllContactPersons(null,ContactPerson::ORDER_ID," AND CONCAT(name1,' ',name2) LIKE '%{$_REQUEST["term"]}%' OR email LIKE '%{$_REQUEST["term"]}%' ");
    foreach ($cps as $cp){
        $retval[] = Array("label" => "Ansprechpartner: " . $cp->getNameAsLine() . " (". $cp->getEmail() . ")", "value" => $cp->getEmail());
    }
    $users = User::getAllUserFiltered(User::ORDER_ID," AND CONCAT(user_firstname,' ',user_lastname) LIKE '%{$_REQUEST["term"]}%' OR user_email LIKE '%{$_REQUEST["term"]}%' OR login LIKE '%{$_REQUEST["term"]}%' ");
    foreach ($users as $user){
        $retval[] = Array("label" => "User: " . $user->getNameAsLine2() . " (". $user->getEmail() . ")", "value" => $user->getEmail());
    }
    $groups = Group::getAllGroupsFiltered(" AND group_name LIKE '%{$_REQUEST["term"]}%' ");
    foreach ($groups as $group)
    {
        $groupmailval = "";
        foreach ($group->getMembers() as $grpmem)
        {
            $groupmailval .= $grpmem->getEmail() . ", ";
        }
        $retval[] = Array("label" => "Gruppe: " . $group->getName(), "value" => $groupmailval);
    }
    $retval = json_encode($retval);
    header("Content-Type: application/json");
    echo $retval;
}

?>

