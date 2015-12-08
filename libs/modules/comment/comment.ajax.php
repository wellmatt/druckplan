<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			19.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/attachment/attachment.class.php';

/*
 * Suchfeld BusinessContacts
 */
if ($_REQUEST["ajax_action"] == "removeAttach" && $_REQUEST["attachid"]){
    $attachment = new Attachment((int)$_REQUEST["attachid"]);
    $attachment->delete();
}
if ($_REQUEST["ajax_action"] == "removeArt" && $_REQUEST["artid"]){
    $c_article = new CommentArticle((int)$_REQUEST["artid"]);
    $c_article->delete();
}
if ($_REQUEST["ajax_action"] == "editArt" && $_REQUEST["artid"] && $_REQUEST["artamount"]){
    $artamount = tofloat(urldecode($_REQUEST["artamount"]));
    $c_article = new CommentArticle((int)$_REQUEST["artid"]);
    $c_article->setAmount($artamount);
    $c_article->save();
}
?>