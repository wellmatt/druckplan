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
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/attachment/attachment.class.php';


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

if ($_REQUEST["exec"] == "save")
{
    
    $mailadress = new Emailaddress($_REQUEST["mailid"]);
    
    $server = $mailadress->getHost();
    $port = $mailadress->getPort();
    $user = $mailadress->getLogin();
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
        $query->envelope();
    
        $uid = new Horde_Imap_Client_Ids($_REQUEST["muid"]);
    
        $list = $client->fetch($_REQUEST["mailbox"], $query, array(
            'ids' => $uid
        ));
        
        $orig_mail_from = $list->first()->getEnvelope()->from->__toString();
        $orig_mail_subject = $list->first()->getEnvelope()->subject;
        $orig_mail_date = date("d.m.Y H:i",$list->first()->getEnvelope()->date->__toString());
        $orig_mail_to = $list->first()->getEnvelope()->to->__toString();
    
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
    
        $content =  '<br><hr>'.
                    'Von: '.$orig_mail_from.'<br>
                    Gesendet: '.$orig_mail_date.'<br>
                    An: '.$orig_mail_to.'<br>
                    Betreff: '.$orig_mail_subject.'<br><br>' . $content;
        
        $comment = new Comment();
	    $comment->setState(1);
	    $comment->setVisability(Comment::VISABILITY_INTERNAL);
	    $comment->setComment($content);
	    $comment->setTitle("Kommentar aus eMail generiert");
        $comment->setCrtuser($_USER);
        $comment->setCrtdate(time());
        $comment->setModule("Ticket");
        $comment->setObjectid((int)$_REQUEST["ticket_id"]);
	    $save_ok = $comment->save();
	    
        if ($save_ok && count($attachments)>0){
            foreach ($attachments as $attachment) {
                                
                $uid = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
                $mime_id = $attachment["mime_id"];
                
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
                
                $name = $attachment["name"];
                
                $destination = __DIR__."/../../../docs/attachments/";
                
                $filename = md5($attachment["name"].time());
                $new_filename = $destination.$filename;
                
                if(!file_exists($new_filename)) {
                    $fh = fopen($new_filename, "w");
                    fwrite($fh, $image_data_decoded);
                    fclose($fh);
                }
                
                $tmp_attachment = new Attachment();
                $tmp_attachment->setCrtdate(time());
                $tmp_attachment->setCrtuser($_USER);
                $tmp_attachment->setModule("Comment");
                $tmp_attachment->setObjectid($comment->getId());
                $tmp_attachment->setFilename($filename);
                $tmp_attachment->setOrig_filename($attachment["name"]);
                $save_ok = $tmp_attachment->save();
                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                if ($save_ok === false){
                    break;
                }
            }
        }
	    if ($save_ok){
	        $tmp_ticket = new Ticket($comment->getObjectid());
            Notification::generateNotificationsFromAbo(get_class($tmp_ticket), "Comment", $tmp_ticket->getNumber(), $tmp_ticket->getId());
 	        echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='.$comment->getObjectid().'";</script>';
	    }
    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
}

?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="../../../css/main.css"/>
    <link rel="stylesheet" type="text/css" href="../../../css/menu.css"/>
    <link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>

    <!-- jQuery -->
    <link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet"/>
    <script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?= $_LANG->getCode() ?>.js"></script>
    <!-- /jQuery -->
    <!-- FancyBox -->
    <script type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
    <script type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen"/>

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
    <script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
    <script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
    <script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>
    <script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/date-uk.js"></script>

    <!-- MegaNavbar -->
    <link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
    <link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
    <script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- /MegaNavbar -->
</head>
<body>

<script language="JavaScript" >
$(function() {
	 $( "#ticket" ).autocomplete({
		 source: "../mail/mail.ajax.php?ajax_action=search_ticket",
		 minLength: 2,
		 focus: function( event, ui ) {
    		 $( "#ticket" ).val( ui.item.label );
    		 return false;
		 },
		 select: function( event, ui ) {
    		 $( "#ticket" ).val( ui.item.label );
    		 $( "#ticket_id" ).val( ui.item.value );
    		 return false;
		 }
	 });
});
</script>

<form action="mail.tocomment.php" method="post" id="tocomment_form" name="tocomment_form">
    <input type="hidden" name="exec" value="save">
    <input type="hidden" name="mailid" value="<?php echo $_REQUEST["mailid"]; ?>">
    <input type="hidden" name="mailbox" value="<?php echo $_REQUEST["mailbox"]; ?>">
    <input type="hidden" name="muid" value="<?php echo $_REQUEST["muid"]; ?>">
    <input type="hidden" id="ticket_id" name="ticket_id" value="<?php echo $_REQUEST["ticket_id"]; ?>">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Mail-to-Comment</h3>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter</h3>
                </div>
                <div class="panel-body">
                    <table>
                        <tr align="left">
                            <td>Datum (erstellt):&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_date_min" id="ajax_date_min"
                                       type="hidden" <?php if ($_SESSION['tkt_date_min']) echo 'value="' . $_SESSION['tkt_date_min'] . '"'; ?> />
                                <input name="date_min" id="date_min"
                                       style="width:70px;" <?php if ($_SESSION['tkt_date_min']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_min']) . '"'; ?>
                                       class="text"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                       title="<?= $_LANG->get('von'); ?>">&nbsp;&nbsp;
                            </td>
                            <td valign="left">
                                <input name="ajax_date_max" id="ajax_date_max"
                                       type="hidden" <?php if ($_SESSION['tkt_date_max']) echo 'value="' . $_SESSION['tkt_date_max'] . '"'; ?> />
                                bis: <input name="date_max" id="date_max"
                                            style="width:70px;" <?php if ($_SESSION['tkt_date_max']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_max']) . '"'; ?>
                                            class="text"
                                            onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                            title="<?= $_LANG->get('bis'); ?>">&nbsp;&nbsp;
                            </td>
                        </tr>
                        <tr align="left">
                            <td>Datum (fällig):&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_date_due_min" id="ajax_date_due_min"
                                       type="hidden" <?php if ($_SESSION['tkt_date_due_min']) echo 'value="' . $_SESSION['tkt_date_due_min'] . '"'; ?> />
                                <input name="date_due_min" id="date_due_min"
                                       style="width:70px;" <?php if ($_SESSION['tkt_date_due_min']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_due_min']) . '"'; ?>
                                       class="text"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                       title="<?= $_LANG->get('von'); ?>">&nbsp;&nbsp;
                            </td>
                            <td valign="left">
                                <input name="ajax_date_due_max" id="ajax_date_due_max"
                                       type="hidden" <?php if ($_SESSION['tkt_date_due_max']) echo 'value="' . $_SESSION['tkt_date_due_max'] . '"'; ?> />
                                bis: <input name="date_due_max" id="date_due_max"
                                            style="width:70px;" <?php if ($_SESSION['tkt_date_due_max']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_due_max']) . '"'; ?>
                                            class="text"
                                            onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                            title="<?= $_LANG->get('bis'); ?>">&nbsp;&nbsp;
                            </td>
                        </tr>
                        <tr align="left">
                            <td>Kategorie:&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_category" id="ajax_category"
                                       type="hidden" <?php if ($_SESSION['tkt_ajax_category']) echo ' value="' . $_SESSION['tkt_ajax_category'] . '" '; ?>/>
                                <select name="category" id="category" style="width:160px">
                                    <option
                                        value="" <?php if (!$_SESSION['tkt_ajax_category']) echo ' selected '; ?>></option>
                                    <?php
                                    $tkt_all_categories = TicketCategory::getAllCategories();
                                    foreach ($tkt_all_categories as $tkt_category) {
                                        if ($tkt_category->cansee()) {
                                            echo '<option value="' . $tkt_category->getId() . '"';
                                            if ($_SESSION['tkt_ajax_category'] == $tkt_category->getId()) {
                                                echo ' selected ';
                                            }
                                            echo '>' . $tkt_category->getTitle() . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr align="left">
                            <td>Status:&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_state" id="ajax_state"
                                       type="hidden" <?php if ($_SESSION['tkt_ajax_state']) echo ' value="' . $_SESSION['tkt_ajax_state'] . '" '; ?>/>
                                <select name="state" id="state" style="width:160px">
                                    <option value="" <?php if (!$_SESSION['tkt_ajax_state']) echo ' selected '; ?>></option>
                                    <?php
                                    $tkt_all_states = TicketState::getAllStates();
                                    foreach ($tkt_all_states as $tkt_state) {
                                        if ($tkt_state->getId() != 1) {
                                            echo '<option value="' . $tkt_state->getId() . '"';
                                            if ($_SESSION['tkt_ajax_state'] == $tkt_state->getId()) {
                                                echo ' selected ';
                                            }
                                            echo '>' . $tkt_state->getTitle() . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr align="left">
                            <td>erst. von:&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_crtuser" id="ajax_crtuser"
                                       type="hidden" <?php if ($_SESSION['tkt_ajax_crtuser']) echo ' value="' . $_SESSION['tkt_ajax_crtuser'] . '" '; ?>/>
                                <select name="crtuser" id="crtuser" style="width:160px">
                                    <option
                                        value="" <?php if (!$_SESSION['tkt_ajax_crtuser']) echo ' selected '; ?>></option>
                                    <?php
                                    $all_user = User::getAllUser(User::ORDER_NAME);
                                    foreach ($all_user as $tkt_user) {
                                        echo '<option value="' . $tkt_user->getId() . '"';
                                        if ($_SESSION['tkt_ajax_crtuser'] == $tkt_user->getId()) {
                                            echo ' selected ';
                                        }
                                        echo '>' . $tkt_user->getNameAsLine() . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr align="left">
                            <td>zugewiesen an:&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_assigned" id="ajax_assigned"
                                       type="hidden" <?php if ($_SESSION['tkt_ajax_assigned']) echo ' value="' . $_SESSION['tkt_ajax_assigned'] . '" '; ?>/>
                                <select name="assigned" id="assigned" style="width:160px">
                                    <option
                                        value="" <?php if (!$_SESSION['tkt_ajax_assigned']) echo ' selected '; ?>></option>
                                    <option disabled>-- Users --</option>
                                    <?php
                                    $all_user = User::getAllUser(User::ORDER_NAME);
                                    $all_groups = Group::getAllGroups(Group::ORDER_NAME);
                                    foreach ($all_user as $tkt_user) {
                                        echo '<option value="u_' . $tkt_user->getId() . '"';
                                        if ($_SESSION['tkt_ajax_assigned'] == 'u_' . $tkt_user->getId()) {
                                            echo ' selected ';
                                        }
                                        echo '>' . $tkt_user->getNameAsLine() . '</option>';
                                    }
                                    ?>
                                    <option disabled>-- Groups --</option>
                                    <?php
                                    foreach ($all_groups as $tkt_groups) {
                                        echo '<option value="g_' . $tkt_groups->getId() . '"';
                                        if ($_SESSION['tkt_ajax_assigned'] == 'g_' . $tkt_groups->getId()) {
                                            echo ' selected ';
                                        }
                                        echo '>' . $tkt_groups->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr align="left">
                            <td>Tourenmerkmal:&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_tourmarker" id="ajax_tourmarker"
                                       type="text" <?php if ($_SESSION['tkt_ajax_tourmarker']) echo ' value="' . $_SESSION['tkt_ajax_tourmarker'] . '" '; ?>/>
                            </td>
                        </tr>
                        <tr align="left"
                            id="tr_cl_dates" <?php if (!$_SESSION['tkt_ajax_showclosed']) echo ' style="display: none" '; ?>>
                            <td>Datum (geschlossen):&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_cl_date_min" id="ajax_cl_date_min"
                                       type="hidden" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="' . $_SESSION['tkt_cl_date_min'] . '"'; ?> />
                                <input name="date_cl_min" id="date_cl_min"
                                       style="width:70px;" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_cl_date_min']) . '"'; ?>
                                       class="text"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                       title="<?= $_LANG->get('von'); ?>">&nbsp;&nbsp;
                            </td>
                            <td valign="left">
                                <input name="ajax_cl_date_max" id="ajax_cl_date_max"
                                       type="hidden" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="' . $_SESSION['tkt_cl_date_max'] . '"'; ?> />
                                bis: <input name="date_cl_max" id="date_cl_max"
                                            style="width:70px;" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_cl_date_max']) . '"'; ?>
                                            class="text"
                                            onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                            title="<?= $_LANG->get('bis'); ?>">&nbsp;&nbsp;
                            </td>
                        </tr>
                        <tr align="left">
                            <td>ohne Fälligkeit:&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_withoutdue" id="ajax_withoutdue"
                                       type="hidden" <?php if ($_SESSION['tkt_ajax_withoutdue']) echo ' value="' . $_SESSION['tkt_ajax_withoutdue'] . '" '; else echo ' value="1" '; ?>/>
                                <input name="withoutdue" id="withoutdue" type="checkbox"
                                       value="1" <?php if ($_SESSION['tkt_ajax_showclosed'] || $_SESSION['tkt_ajax_showclosed'] == Null) echo ' checked '; ?>/>
                            </td>
                        </tr>
                        <tr align="left">
                            <td>zeige geschlossene:&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_showclosed" id="ajax_showclosed"
                                       type="hidden" <?php if ($_SESSION['tkt_ajax_showclosed']) echo ' value="' . $_SESSION['tkt_ajax_showclosed'] . '" '; ?>/>
                                <input name="showclosed" id="showclosed" type="checkbox"
                                       value="1" <?php if ($_SESSION['tkt_ajax_showclosed']) echo ' checked '; ?>/>
                            </td>
                        </tr>
                        <?php if ($_USER->isAdmin()) { ?>
                            <tr align="left">
                                <td>zeige gelöschte:&nbsp;&nbsp;</td>
                                <td valign="left">
                                    <input name="ajax_showdeleted" id="ajax_showdeleted"
                                           type="hidden" <?php if ($_SESSION['tkt_ajax_showdeleted']) echo ' value="' . $_SESSION['tkt_ajax_showdeleted'] . '" '; ?>/>
                                    <input name="showdeleted" id="showdeleted" type="checkbox"
                                           value="1" <?php if ($_SESSION['tkt_ajax_showdeleted']) echo ' checked '; ?>/>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <input name="ajax_showdeleted" id="ajax_showdeleted" type="hidden" value="0"/>
                            <input name="showdeleted" id="showdeleted" type="hidden" value="0"/>
                        <?php } ?>
                        <tr align="left">
                            <td><a onclick="TicketTableRefresh();" href="Javascript:"><span class="glyphicons glyphicons-refresh"></span> Refresh</a></td>
                        </tr>
                        </br>
                    </table>
                </div>
            </div>
        </div>
        <div class="table-responsive" style="border-top: none;">
            <table id="ticketstable" width="100%" cellpadding="0" cellspacing="0"
                   class="stripe hover row-border order-column table-hover">
                <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><?= $_LANG->get('ID') ?></th>
                    <th><?= $_LANG->get('#') ?></th>
                    <th><?= $_LANG->get('Kategorie') ?></th>
                    <th><?= $_LANG->get('Datum') ?></th>
                    <th><?= $_LANG->get('erst. von') ?></th>
                    <th><?= $_LANG->get('Fälligkeit') ?></th>
                    <th><?= $_LANG->get('Betreff') ?></th>
                    <th><?= $_LANG->get('Status') ?></th>
                    <th><?= $_LANG->get('Von') ?></th>
                    <th><?= $_LANG->get('Priorität') ?></th>
                    <th><?= $_LANG->get('Zugewiesen an') ?></th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th><?= $_LANG->get('ID') ?></th>
                    <th><?= $_LANG->get('#') ?></th>
                    <th><?= $_LANG->get('Kategorie') ?></th>
                    <th><?= $_LANG->get('Datum') ?></th>
                    <th><?= $_LANG->get('erst. von') ?></th>
                    <th><?= $_LANG->get('Fällig') ?></th>
                    <th><?= $_LANG->get('Betreff') ?></th>
                    <th><?= $_LANG->get('Status') ?></th>
                    <th><?= $_LANG->get('Von') ?></th>
                    <th><?= $_LANG->get('Priorität') ?></th>
                    <th><?= $_LANG->get('Zugewiesen an') ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</form>

<script type="text/javascript">
    jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
        var ukDatea = a.split('.');
        var ukDateb = b.split('.');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? -1 : ((x > y) ?  1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
        var ukDatea = a.split('.');
        var ukDateb = b.split('.');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
    };

    $(document).ready(function() {
        var ticketstable = $('#ticketstable').DataTable( {
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "../../../libs/modules/tickets/ticket.dt.ajax.php",
            "stateSave": false,
            "pageLength": 10,
            "aaSorting": [[ 6, "desc" ]],
            "dom": '<"clear">flrtip',
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                var iMin = document.getElementById('ajax_date_min').value;
                var iMax = document.getElementById('ajax_date_max').value;
                var iMinDue = document.getElementById('ajax_date_due_min').value;
                var iMaxDue = document.getElementById('ajax_date_due_max').value;
                var category = document.getElementById('ajax_category').value;
                var state = document.getElementById('ajax_state').value;
                var crtuser = document.getElementById('ajax_crtuser').value;
                var assigned = document.getElementById('ajax_assigned').value;
                var showclosed = document.getElementById('ajax_showclosed').value;
                var showdeleted = document.getElementById('ajax_showdeleted').value;
                var tourmarker = document.getElementById('ajax_tourmarker').value;
                var iMin_cl = document.getElementById('ajax_cl_date_min').value;
                var iMax_cl = document.getElementById('ajax_cl_date_max').value;
                var withoutdue = document.getElementById('ajax_withoutdue').value;
                aoData.push( { "name": "details", "value": "1", } );
                aoData.push( { "name": "start", "value": iMin, } );
                aoData.push( { "name": "end", "value": iMax, } );
                aoData.push( { "name": "start_due", "value": iMinDue, } );
                aoData.push( { "name": "end_due", "value": iMaxDue, } );
                aoData.push( { "name": "category", "value": category, } );
                aoData.push( { "name": "state", "value": state, } );
                aoData.push( { "name": "crtuser", "value": crtuser, } );
                aoData.push( { "name": "assigned", "value": assigned, } );
                aoData.push( { "name": "showclosed", "value": showclosed, } );
                aoData.push( { "name": "showdeleted", "value": showdeleted, } );
                aoData.push( { "name": "tourmarker", "value": tourmarker, } );
                aoData.push( { "name": "cl_start", "value": iMin_cl, } );
                aoData.push( { "name": "cl_end", "value": iMax_cl, } );
                aoData.push( { "name": "withoutdue", "value": withoutdue, } );
                $.getJSON( sSource, aoData, function (json) {
                    fnCallback(json)
                } );
            },
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "columns": [
                {
                    "className":      'details-control',
                    "orderable":      false,
                    "data":           null,
                    "defaultContent": ''
                },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ],
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        } );

        var DELAY = 500, clicks = 0, timer = null;
        $("#ticketstable tbody td:not(:first-child,:nth-child(9))").live('click', function (e) {
            var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
            var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);

            $('#ticket_id').val(aData[1]);
            $('#tocomment_form').submit();
        });
        $("#ticketstable tbody td:nth-child(9)").live('click', function(e){
            var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
            var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);
            var tktid = aData[1];

            callBoxFancytktoverview("libs/modules/tickets/ticket.summary.php?tktid="+tktid);
        });
        $("a#hiddenclickertktoverview").fancybox({
            'type'    : 'iframe',
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'padding'		:	25,
            'margin'        :   25,
            'scrolling'     :   'no',
            'width'		    :	1000,
            'onComplete'    :   function() {
                $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                    $('#fancybox-content').height($(this).contents().find('body').height()+30);
                    $('#fancybox-wrap').css('top','25px');
                });
            },
            'overlayShow'	:	true,
            'helpers'		:   { overlay:null, closeClick:true }
        });
        function callBoxFancytktoverview(my_href) {
            var j1 = document.getElementById("hiddenclickertktoverview");
            j1.href = my_href;
            $('#hiddenclickertktoverview').trigger('click');
        }

        var detailRows = [];
        $('#ticketstable tbody').on( 'click', 'tr td:first-child', function () {
            var tr = $(this).closest('tr');
            var row = ticketstable.row( tr );
            var idx = $.inArray( tr.attr('id'), detailRows );
            var control = $(this);

            if ( row.child.isShown() ) {
                tr.removeClass( 'details' );
                row.child.hide();
                detailRows.splice( idx, 1 );
            }
            else {
                tr.addClass( 'details' );
                $(this).addClass( 'details-control-loading' );
                get_child(row.data(),row,idx,tr,control);
            }
        } );

        function get_child ( d,row,idx,tr,control ) {
            var body = $.ajax({
                type: "GET",
                url: "../../../libs/modules/tickets/ticket.summary.php",
                data: { "tktid": d[1], "inline": "true" },
                success: function(data)
                {
                    row.child( '<div class="box2">'+data+'</div>' ).show();
                    $( ".details-control-loading" ).removeClass( 'details-control-loading' );
                    tr.removeClass('highlight');
                    if ( idx === -1 ) {
                        detailRows.push( tr.attr('id') );
                    }
                }
            });
        }

        $.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
        $('#date_min').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.svg",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
                    $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_min", "tkt_date_min": moment($('#date_min').val(), "DD-MM-YYYY").unix()});
                    $('#ticketstable').dataTable().fnDraw();
                }
            });
        $('#date_max').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.svg",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
                    $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_max", "tkt_date_max": moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340});
                    $('#ticketstable').dataTable().fnDraw();
                }
            });
        $.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
        $('#date_cl_min').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.svg",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    $('#ajax_cl_date_min').val(moment($('#date_cl_min').val(), "DD-MM-YYYY").unix());
                    $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_cl_date_min", "tkt_cl_date_min": moment($('#date_cl_min').val(), "DD-MM-YYYY").unix()});
                    $('#ticketstable').dataTable().fnDraw();
                }
            });
        $('#date_cl_max').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.svg",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    $('#ajax_cl_date_max').val(moment($('#date_cl_max').val(), "DD-MM-YYYY").unix()+86340);
                    $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_cl_date_max", "tkt_cl_date_max": moment($('#date_cl_max').val(), "DD-MM-YYYY").unix()+86340});
                    $('#ticketstable').dataTable().fnDraw();
                }
            });
        $('#date_due_min').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.svg",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    $('#ajax_date_due_min').val(moment($('#date_due_min').val(), "DD-MM-YYYY").unix());
                    $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_due_min", "tkt_date_due_min": moment($('#date_due_min').val(), "DD-MM-YYYY").unix()});
                    $('#ticketstable').dataTable().fnDraw();
                }
            });
        $('#date_due_max').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "../../../images/icons/calendar-blue.svg",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    $('#ajax_date_due_max').val(moment($('#date_due_max').val(), "DD-MM-YYYY").unix()+86340);
                    $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_due_max", "tkt_date_due_max": moment($('#date_due_max').val(), "DD-MM-YYYY").unix()+86340});
                    $('#ticketstable').dataTable().fnDraw();
                }
            });

        $('#category').change(function(){
            $('#ajax_category').val($(this).val());
            $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_category", "tkt_ajax_category": $(this).val()});
            $('#ticketstable').dataTable().fnDraw();
        })
        $('#state').change(function(){
            $('#ajax_state').val($(this).val());
            $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_state", "tkt_ajax_state": $(this).val()});
            $('#ticketstable').dataTable().fnDraw();
        })
        $('#crtuser').change(function(){
            $('#ajax_crtuser').val($(this).val());
            $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_crtuser", "tkt_ajax_crtuser": $(this).val()});
            $('#ticketstable').dataTable().fnDraw();
        })
        $('#assigned').change(function(){
            $('#ajax_assigned').val($(this).val());
            $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_assigned", "tkt_ajax_assigned": $(this).val()});
            $('#ticketstable').dataTable().fnDraw();
        })
        $('#withoutdue').change(function(){
            if ($('#withoutdue').prop('checked')){
                $('#ajax_withoutdue').val(1);
                $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_withoutdue", "tkt_ajax_withoutdue": "1"});
            } else {
                $('#ajax_withoutdue').val(0);
                $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_withoutdue", "tkt_ajax_withoutdue": "0"});
            }
            $('#ticketstable').dataTable().fnDraw();
        })
        $('#showclosed').change(function(){
            if ($('#showclosed').prop('checked')){
                $('#ajax_showclosed').val(1);
                $('#ajax_showdeleted').val(0);
                $('#showdeleted').prop('checked', false);
                $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showclosed", "tkt_ajax_showclosed": "1"});
            } else {
                $('#ajax_showclosed').val(0);
                $('#date_cl_min').val('');
                $('#date_cl_max').val('');
                $('#ajax_date_cl_min').val('');
                $('#ajax_date_cl_max').val('');
                $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showclosed", "tkt_ajax_showclosed": "0"});
            }
            $('#tr_cl_dates').toggle();
            $('#ticketstable').dataTable().fnDraw();
        })
        $('#showdeleted').change(function(){
            if ($('#showdeleted').prop('checked')){
                $('#tr_cl_dates').hide();
                $('#date_cl_min').val('');
                $('#date_cl_max').val('');
                $('#ajax_date_cl_min').val('');
                $('#ajax_date_cl_max').val('');
                $('#ajax_showclosed').val(0);
                $('#ajax_showdeleted').val(1);
                $('#showclosed').prop('checked', false);
                $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showdeleted", "tkt_ajax_showdeleted": "1"});
            } else {
                $('#ajax_showdeleted').val(0);
                $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showdeleted", "tkt_ajax_showdeleted": "0"});
            }
            $('#ticketstable').dataTable().fnDraw();
        })
        $('#ajax_tourmarker').change(function(){
            $('#ajax_tourmarker').val($(this).val());
            $.post("../../../libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_tourmarker", "tkt_ajax_tourmarker": $(this).val()});
            $('#ticketstable').dataTable().fnDraw();
        })


    } );
</script>