<?php
    error_reporting(-1);
    ini_set('display_errors', 1);
    
    chdir("../../../");
    
    require_once("config.php");
    require_once("libs/basic/mysql.php");
    require_once("libs/basic/globalFunctions.php");
    require_once("libs/basic/user/user.class.php");
    require_once("libs/basic/groups/group.class.php");
    require_once("libs/basic/clients/client.class.php");
    require_once("libs/basic/translator/translator.class.php");
    require_once("libs/basic/countries/country.class.php");
    require_once 'libs/modules/organizer/contact.class.php';
    require_once 'libs/modules/businesscontact/businesscontact.class.php';
    
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
    if ($_REQUEST["userid"]){
        $_USER = new User((int)$_REQUEST["userid"]);
    } else {
        $_USER = new User();
        $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
    }
    $_LANG = $_USER->getLang();
    
    if ($_USER == false){
        error_log("Login failed (basic-importer.php)");
        die("Login failed");
    }
     
    /*
     * Local functions
     */
    function fatal_error ( $sErrorMessage = '' )
    {
        header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
        die( $sErrorMessage );
    }
 
    /*
     * Paging
     */
    $iDisplayStart = $_GET['iDisplayStart'];
    $iDisplayLength = $_GET['iDisplayLength'];
    /*
     * Ordering
     */
    $sSort = SORTDATE;
    $sOrder = $_GET['iSortCol_0'];
    switch ($sOrder)
    {
        case 1:
            $sSort = Horde_Imap_Client::SORT_FROM;
            break;
        case 2:
            $sSort = Horde_Imap_Client::SORT_SUBJECT;
            break;
        case 3:
            $sSort = Horde_Imap_Client::SORT_DATE;
            break;
        default:
            $sSort = Horde_Imap_Client::SORT_DATE;
            break;
    }
    $sDir = $_GET['sSortDir_0'];
    $sSortDir = Horde_Imap_Client::SORT_REVERSE;
    switch ($sDir)
    {
        case "asc":
            $sSortDir = null;
            break;
        default:
            $sSortDir = Horde_Imap_Client::SORT_REVERSE;
            break;
    }
    /*
     * Filtering
     */
    $sSearch = "";
    $sSearch = $_GET['sSearch'];
    
    
    $mailadress = new Emailaddress($_REQUEST["mailid"]);
    
    $server = $mailadress->getHost();
    $port = $mailadress->getPort();
    $user = $mailadress->getLogin();
    $password = $mailadress->getPassword();
    
    $mailbox = $_REQUEST["mailbox"];
    
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
        $resultstotal = $client->search($mailbox);
        
        $searchquery = null;
        if ($sSearch != "")
        {
            $searchquery = new Horde_Imap_Client_Search_Query();
            
            $searchquery_text = new Horde_Imap_Client_Search_Query();
            $searchquery_text->text($sSearch,false);

            $searchquery_from = new Horde_Imap_Client_Search_Query();
            $searchquery_from->headerText("from",$sSearch);

            $searchquery_to = new Horde_Imap_Client_Search_Query();
            $searchquery_to->headerText("to",$sSearch);

            $searchquery_subject = new Horde_Imap_Client_Search_Query();
            $searchquery_subject->headerText("subject",$sSearch);
            
            $searchquery->orSearch(Array($searchquery_text,$searchquery_from,$searchquery_to,$searchquery_subject));
        }
        
        $results = $client->search($mailbox, $searchquery, array('sort' => array($sSortDir, $sSort)));
//         print_r($results);
         
        // $results['match'] contains a Horde_Imap_Client_Ids object, containing the
        // list of UIDs in the INBOX.
        $uids = $results['match'];
        $iTotal = $resultstotal['count'];
        $iFilteredTotal = $results['count'];

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        
        $query = new Horde_Imap_Client_Fetch_Query();
        $query->flags();
        $query->envelope();
        $query->structure();
        
        $i_start = (int)$iDisplayStart;
        $i_to = $i_start + (int)$iDisplayLength;
        if ($i_to>$iFilteredTotal)
            $i_to = $iFilteredTotal;
        
        for ($i = $i_start; $i < $i_to; $i++) 
        {
            $muid = new Horde_Imap_Client_Ids($uids->ids[$i]);
            
            $list = $client->fetch($mailbox, $query, array(
                'ids' => $muid
            ));
            
            $flags = $list->first()->getFlags();
            if(in_array(Horde_Imap_Client::FLAG_SEEN, $flags))
                $seen = 1;
            else
                $seen = 0;
            
            $row = Array();
            
            $options = "";

            $options = '
            <div class="btn-group" role="group">
                <div class="btn-group dropdown" style="margin-left: 0px;">
                      <button type="button" class="btn btn-xs dropdown-toggle btn-default" data-toggle="dropdown" aria-expanded="false">
                        Mehr <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="#" onclick="callBoxFancyNewMail(\'libs/modules/mail/mail.send.frame.php?preset=RE&mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\');"/>Antworten</a>
                                <a href="#" onclick="callBoxFancyNewMail(\'libs/modules/mail/mail.send.frame.php?preset=FW&mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\');"/>Weiterleiten</a>
                                <a href="#" onclick="$(\'#muid\').val('.$uids->ids[$i].');MailBoxSelectPopup();">Verschieben</a>
                                <a href="#" onclick="mail_markasunread(this,'.$_REQUEST["mailid"].',\''.$mailbox.'\','.$uids->ids[$i].');">als ungelesen markieren</a>
                                <a href="#" onclick="mail_markasread(this,'.$_REQUEST["mailid"].',\''.$mailbox.'\','.$uids->ids[$i].');">als gelesen markieren</a>
                                <a href="#" onclick="window.open(\'libs/modules/mail/mail.print.php?mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\');">Drucken</a>
                                <a href="#" onclick="parent.location.href=\'index.php?page=libs/modules/tickets/ticket.php&exec=new&frommail=true&mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\';">Ticket erstellen</a>
                                <a href="#" onclick="callBoxFancyNewMail(\'libs/modules/mail/mail.tocomment.php?mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\');">Ticket Kommentar erstellen</a>
                                <a href="#" onclick="mail_delete(this,'.$_REQUEST["mailid"].',\''.$mailbox.'\','.$uids->ids[$i].');">Löschen</a>
                            </li>
                      </ul>
                </div>
            </div>
            ';


            // Antworten
//            $options .= '<img src="images/icons/mail--pencil.png" title="antworten" class="pointer"
//                        onclick="callBoxFancyNewMail(\'libs/modules/mail/mail.send.frame.php?preset=RE&mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\');"/>&nbsp;';
            // Weiterleiten
//            $options .= '<img src="images/icons/mail--arrow.png" title="weiterleiten" class="pointer"
//                        onclick="callBoxFancyNewMail(\'libs/modules/mail/mail.send.frame.php?preset=FW&mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\');"/>&nbsp;';
            // Verschieben
//            $options .= '<img src="images/icons/mails.png" title="verschieben" class="pointer"
//                        onclick="$(\'#muid\').val('.$uids->ids[$i].');MailBoxSelectPopup();"/>&nbsp;';
            // als ungelesen
//            $options .= '<img src="images/icons/mail.png" title="als ungelesen markieren" class="pointer"
//                        onclick="mail_markasunread(this,'.$_REQUEST["mailid"].',\''.$mailbox.'\','.$uids->ids[$i].');"/>&nbsp;';
            // als gelesen
//            $options .= '<img src="images/icons/mail-open.png" title="als gelesen markieren" class="pointer"
//                        onclick="mail_markasread(this,'.$_REQUEST["mailid"].',\''.$mailbox.'\','.$uids->ids[$i].');"/>&nbsp;';
            // drucken
//            $options .= '<a href="libs/modules/mail/mail.print.php?mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'" target="_blank">
//                        <img src="images/icons/printer.png" title="Mail drucken" class="pointer"/></a>&nbsp;';
            // ticket erstellen
//            $options .= '<img src="images/icons/ticket--plus.png" title="Ticket erstellen" class="pointer"
//                        onclick="parent.location.href=\'index.php?page=libs/modules/tickets/ticket.php&exec=new&frommail=true&mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\';"/>&nbsp;';
            // ticket kommentar
//            $options .= '<img src="images/icons/ticket--arrow.png" title="als Ticket-Kommentar speichern" class="pointer"
//                        onclick="callBoxFancyNewMail(\'libs/modules/mail/mail.tocomment.php?mailid='.$_REQUEST["mailid"].'&mailbox='.$mailbox.'&muid='.$uids->ids[$i].'\');"/>&nbsp;';
            // löschen
//            $options .= '<img src="images/icons/mail--minus.png" title="löschen" class="pointer"
//                        onclick="mail_delete(this,'.$_REQUEST["mailid"].',\''.$mailbox.'\','.$uids->ids[$i].');"/>&nbsp;';
            

            $part = $list->first()->getStructure();
            $map = $part->ContentTypeMap();
            $attachments = Array();
            foreach ( $map as $key => $value ) {
                $p = $part->getPart( $key );
                $disposition = $p->getDisposition();
                if ( ! in_array( $disposition, array( 'attachment' ) ) ) {
                    continue;
                }
                $attachments[] = $p->getName();
            }
            
            $row[] = null;
            $mail_from = $list->first()->getEnvelope()->from->__toString();
            $mail_from = str_replace('"', '', $mail_from);
            $row[] = $mail_from;
            $mail_to = $list->first()->getEnvelope()->to->__toString();
            $mail_to = str_replace(",", "</br>", $mail_to);
            $row[] = $mail_to;
            
            if (count($attachments)>0)
            {
                $attach_names = implode('
', $attachments);
                $row[] = '<img src="images/icons/attachment.svg" title="'.$attach_names.'"> '.$list->first()->getEnvelope()->subject;
            }
            else 
                $row[] = $list->first()->getEnvelope()->subject;
            
            $row[] = date("d.m.Y H:i",$list->first()->getEnvelope()->date->__toString());
            $row[] = $options;
            $row[] = $uids->ids[$i];
            $row[] = $seen;

            $output['aaData'][] = $row;
        }
        
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
//         var_dump($e);
    }
     
    echo json_encode( $output );
?>
