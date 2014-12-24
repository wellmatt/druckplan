<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			10.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';

$save_retval = false;
if ($_REQUEST["exec"]=="save"){
	$new_chat = new Chat();
	$new_chat->setTo(new User((int)$_REQUEST["send_to"]));
	$new_chat->setComment(trim(addslashes($_REQUEST["send_comment"])));
	$new_chat->setTitle(trim(addslashes($_REQUEST["send_title"])));
	$new_chat->setFrom($_USER);
	$save_retval = $new_chat->save();	
}

if($save_retval){ ?>
	<span style="text-align:center;font-weight: bold; color: #009640; font-size: 14px;">
	<br/><br/><br/><br/>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
	<?=$_LANG->get('Nachricht erfolgreich versandt')?>...</span>
<?
} else {

$all_user = User::getAllUser(); 
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="./../../../css/main.css" />
</head>
<body>
<script language="javascript" src="../../../jscripts/basic.js"></script>
<h1><?=$_LANG->get('Chat-Nachricht senden')?></h1>
<form enctype="multipart/form-data" action="newchat.fancy.php" method="post" 
	  onSubmit="return checkform(new Array(this.send_to, this.send_comment))">
	<input type="hidden" name="exec" value="save" />
	<div class="box1">
	<table width="100%">
		<colgroup>
			<col width="120">
		</colgroup>
		<tr>
			<td><?=$_LANG->get('Empf&auml;nger');?></td>
			<td>
				<select type="text" id="send_to" name="send_to" style="width:200px"
						onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
					<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
					<? 	foreach ($all_user as $us){?>
						<option value="<?=$us->getId()?>"><?= $us->getNameAsLine()?></option>
					<?	} //Ende ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?=$_LANG->get('Titel');?></td>
			<td>
				<input type="text" class="text" name="send_title" style="width:200px">
			</td>
		</tr>
		<tr>
			<td><?=$_LANG->get('Nachricht');?></td>
			<td>
				<textarea class="text" name="send_comment"  style="width:400px; height:70px;"></textarea>
			</td>
		</tr>
		<tr>
			<td>&ensp;</td>
			<td align="right">
				<input type="submit" class="button" value="<?=$_LANG->get('Senden')?>">
			</td>
		</tr>
	</table>
    </div>
</form>
</body>
</html>
<?
}

?>