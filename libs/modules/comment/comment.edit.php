<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			22.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
include 'libs/modules/comment/comment.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false){
	error_log("Login failed (basic-importer.php)");
	die("Login failed");
}

// error_reporting(-1);
// ini_set('display_errors', 1);

if ((int)$_REQUEST["cid"] > 0){
	$comment = new Comment((int)$_REQUEST["cid"]);
	
	if($_REQUEST["exec"] == "save"){
		if ($_REQUEST["tktc_type"] && $_REQUEST["tktc_comment"]){
		    if ((int)$_REQUEST["tktc_state"] == 1){
		        $comment->setState(0);
		    } else {
		        $comment->setState(1);
		    }
		    $comment->setVisability($_REQUEST["tktc_type"]);
		    $comment->setComment($_REQUEST["tktc_comment"]);
		    $save_ok = $comment->save();
		    echo getSaveMessage($save_ok);
		    if ($save_ok){
		        echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.reload();</script>';
		    }
		}
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<!-- /jQuery -->
<script src="../../../thirdparty/ckeditor/ckeditor.js"></script>
<script language="javascript" src="../../../jscripts/basic.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />

<script>
	$(function() {
		CKEDITOR.replace( 'tktc_comment' );
		var comment = $('#tktc_oldcomment').html();
		CKEDITOR.instances.tktc_comment.setData( comment, function()
				{
				    this.checkDirty();  // true
				});
	});
</script>

<div id="tktc_oldcomment" style="display: none;"><?php echo $comment->getComment();?></div>

<div class="box1" style="text-align: center;">
<br>
<form action="comment.edit.php" method="post" name="comment_edit">
	<input type="hidden" name="exec" value="save">
	<input type="hidden" name="cid" value="<?=$comment->getId()?>">
	<input type="hidden" name="tktid" value="<?php echo $_REQUEST["tktid"];?>">
	<table style="width:100%">
      <tr>
          <td width="25%">Status:</td>
          <td width="75%">
                <input type="checkbox" name="tktc_state" <?php if ($comment->getState() == 0) echo "checked"; ?> value="1"/> gelöscht?<br>
          </td>
      </tr>
      <tr>
          <td width="25%">Typ:</td>
          <td width="75%">
                <input type="radio" name="tktc_type" <?php if ($comment->getVisability() == Comment::VISABILITY_PUBLIC) echo "checked"; ?> value="<?php echo Comment::VISABILITY_PUBLIC;?>"> Offiz. Antwort<br>
                <input type="radio" name="tktc_type" <?php if ($comment->getVisability() == Comment::VISABILITY_INTERNAL) echo "checked"; ?> value="<?php echo Comment::VISABILITY_INTERNAL;?>"> inter. Kommentar<br>
                <input type="radio" name="tktc_type" <?php if ($comment->getVisability() == Comment::VISABILITY_PRIVATE) echo "checked"; ?> value="<?php echo Comment::VISABILITY_PRIVATE;?>"> priv. Kommentar
          </td>
      </tr>
      <tr>
          <td width="100%" colspan="2"><textarea name="tktc_comment" id="tktc_comment" rows="10" cols="80"></textarea></td>
      </tr>
	</table>
	
	<table width="100%">
	    <colgroup>
	        <col width="180">
	        <col>
	    </colgroup> 
	    <tr>
	        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="right">
	        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	    </tr>
	</table>
</form>
</div>
<?
} else {
    echo "Keine Kommentar ID angegeben!";
}
?>