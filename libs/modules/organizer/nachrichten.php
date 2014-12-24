<?php 
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       01.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

if ((int)$_REQUEST["old"] != 1){

require_once 'libs/modules/organizer/mail/mail.controller.php';

} else {


require_once('nachricht.class.php');
require_once('msgFolder.class.php');

// Existieren die Standardordner?
MsgFolder::checkFolders();
$folders = MsgFolder::getSubFolders();

$_REQUEST["folder"] = (int)$_REQUEST["folder"];
if ($_REQUEST["folder"] == 0)
    $_REQUEST["folder"] = MsgFolder::getIdForName("Posteingang");


if ($_REQUEST["retval"] == 1)
    $savemsg = getSaveMessage(true);

if ($_REQUEST["exec"] == "delete")
{
    foreach(array_keys($_REQUEST) as $key)
    {
        if (preg_match("/chk_msg_(?P<id>\d+)/", $key, $match))
        {
            $msg = new Nachricht($match["id"]);
            $msg->delete();
        }
    }
} else if ($_REQUEST["exec"] == "deletesingle")
{
	echo "test</br>";
	if ($_REQUEST["messageid"] != "") {
		$msg = new Nachricht($_REQUEST["messageid"]);
		$msg->delete();
		echo "gelöscht</br>";
	}
} else if ($_REQUEST["exec"] == "markread")
{
    foreach(array_keys($_REQUEST) as $key)
    {
        if (preg_match("/chk_msg_(?P<id>\d+)/", $key, $match))
        {
            $msg = new Nachricht($match["id"]);
            $msg->setRead(true);
        }
    }
} else if ($_REQUEST["exec"] == "markunread")
{
    foreach(array_keys($_REQUEST) as $key)
    {
        if (preg_match("/chk_msg_(?P<id>\d+)/", $key, $match))
        {
            $msg = new Nachricht($match["id"]);
            $msg->setRead(false);
        }
    }
}else if ($_REQUEST["exec"] == "move_to"){
	$nachricht = new Nachricht((int)$_REQUEST["id"]);
	$nachricht->moveToFolder();
}

?>
<script type="text/javascript">
<!--
function askSubmitMsg(obj)
{
	
	if($("input:checkbox:checked").size()>0){
	if(confirm("Sind Sie sicher?")){
		obj.submit(); 
	  }
	}
	else {
		alert("Bitte eine Nachricht anklicken!");
		var folder = "<?=$_REQUEST["folder"]?>";
	    location.href = 'index.php?page=<?=$_REQUEST['page']?>&folder='+folder;
	}
}
//-->
</script>
<link rel="stylesheet" type="text/css" href="./css/mail.css" />
<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Nachrichten')?></td>
      <td><?=$savemsg?></td>
      <td width="200" class="content_header" align="right">
          <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=newmail"><img src="images/icons/mail--plus.png" /> <?=$_LANG->get('Neue Nachricht')?></a>
      </td>
   </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="msg_list_form" name="msg_list_form">
<input name="folder" value="<?=$_REQUEST["folder"]?>" type="hidden">
<div class="box1">
<table width="100%">
<tr>
<td width="20%" valign="top">
    <?php 
        foreach ($folders as $f)
        {
            echo '<p class="msg_folder pointer icon-link"
                    onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&folder='.$f->getId().'\'">
                    <img src="images/icons/folder';
            if ($_REQUEST["folder"] == $f->getId())
                echo "-open";
            echo '.png" /> '.$f->getName().'</p>';
            
            if (is_array($f->getSub()))
            {
                $subFolder = $f->getSub();
                foreach ($subFolder as $sub)
                {
                    echo '<p class="msg_subfolder pointer icon-link"
                    onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&folder='.$sub->getId().'\'">
                    <img src="images/icons/folder';
                    if ($_REQUEST["folder"] == $sub->getId())
                        echo "-open";
                    echo '.png" /> '.$sub->getName().'</p>';
                }
            }
        }
    ?>
</td>
<td width="80%" valign="top">
<?php if ($_REQUEST["exec"] == "showmail") { 
    require_once("nachrichten.showmail.php");
} else if ($_REQUEST["exec"] == "newmail") {
    require_once('nachrichten.newmail.php');
} else
{ 
    $nachrichten = Nachricht::getAllNachrichten($_REQUEST["folder"], Nachricht::ORDER_DATE);
    ?>
    <table width="100%" cellspacing="0" cellpadding="0">
        <colgroup>
            <col width="20">
            <col width="20">
            <col width="150">
            <col>
            <col width="150">
        </colgroup>
        <tr>
            <td class="content_row_header">&nbsp;</td>
            <td class="content_row_header">&nbsp;</td>
            <td class="content_row_header"><?=$_LANG->get('Von')?></td>
            <td class="content_row_header"><?=$_LANG->get('Betreff')?></td>
            <td class="content_row_header"><?=$_LANG->get('Datum')?></td>
        </tr>
    
    <?php 
        $x = 1;
        foreach ($nachrichten as $n)
        {
            $from = $n->getFrom();
            ?>
            <tr class="pointer <?=getRowColor($x)?>" id="msg_<?=$n->getId()?>" onmouseover="mark(this,0)" onmouseout="mark(this,1)">
                <td class="content_row icon-link"><input type="checkbox" id="chk_msg" name="chk_msg_<?=$n->getId()?>" value="1" /></td>
                <td class="content_row icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=showmail&folder=<?=$_REQUEST["folder"]?>&id=<?=$n->getId()?>'"><img src="images/icons/mail<?if($n->getRead()) echo "-open"?>.png" /></td>
                <td class="content_row icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=showmail&folder=<?=$_REQUEST["folder"]?>&id=<?=$n->getId()?>'"><?=$n->getFrom()->getFirstname()?> <?=$n->getFrom()->getLastname()?></td>
                <td class="content_row icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=showmail&folder=<?=$_REQUEST["folder"]?>&id=<?=$n->getId()?>'"><?=$n->getSubject()?></td>
                <td class="content_row icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=showmail&folder=<?=$_REQUEST["folder"]?>&id=<?=$n->getId()?>'"><?=date('d.m.Y - H:m:s', $n->getCreated())?></td>
            </tr>
        <? $x++;}
    ?>
    </table>
<?php  } ?>
</td>
</tr>
</table>
</div>
<br><br>
<? if($_REQUEST["exec"] != "showmail" && $_REQUEST["exec"] != "newmail") { ?>
<?=$_LANG->get('Ausgew&auml;hlte Nachrichten ')?>
<select name="exec" style="width:180px" onchange="askSubmitMsg(document.getElementById('msg_list_form'))">
    <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
    <option value="delete"><?=$_LANG->get('L&ouml;schen')?></option>
    <option value="markread"><?=$_LANG->get('Als gelesen markieren')?></option>
    <option value="markunread"><?=$_LANG->get('Als ungelesen markieren')?></option>
</select>


<?  } ?>
</form>

<?  
} 
?>