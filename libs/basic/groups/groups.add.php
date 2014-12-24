<?php
$_REQUEST["id"] = (int)$_REQUEST["id"];
$group = new Group($_REQUEST["id"]);

if ($_REQUEST["subexec"] == "adduser")
{
   $group->addMember(new User($_REQUEST["uid"]));
   $savemsg = getSaveMessage($group->save());
}

if ($_REQUEST["subexec"] == "removeuser")
{
   $group->delMember(new User($_REQUEST["uid"]));
   $savemsg = getSaveMessage($group->save());
}

if ($_REQUEST["subexec"] == "save")
{      
   $group->setName(trim(addslashes($_REQUEST["group_name"])));
   $group->setDescription(trim(addslashes($_REQUEST["group_description"])));
   $group->setRight(Group::RIGHT_URLAUB, (int)$_REQUEST["right_urlaub"]);
   $group->setRight(Group::RIGHT_MACHINE_SELECTION, (int)$_REQUEST["right_machineselection"]);
   $group->setRight(Group::RIGHT_DETAILED_CALCULATION, (int)$_REQUEST["right_detailed_calc"]);
   $group->setRight(Group::RIGHT_SEE_TARGETTIME, (int)$_REQUEST["right_targettime"]);
   $group->setRight(Group::RIGHT_PARTS_EDIT, (int)$_REQUEST["right_parts_edit"]);
   $group->setRight(Group::RIGHT_ALL_CALENDAR, (int)$_REQUEST["right_all_calendar"]);
   $savemsg = getSaveMessage($group->save());
   $savemsg .= $DB->getLastError();  
}

$users = User::getAllUser(User::ORDER_LOGIN);
?>

<table width="100%">
   <tr>
      <td width="200" class="content_header">
         <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
         <? if ($group->getId()) echo $_LANG->get('Gruppe &auml;ndern'); else echo $_LANG->get('Gruppe hinzuf&uuml;gen');?>
       </td>
      <td></td>
      <td width="200" class="content_header" align="right"><?=$savemsg?></td>
   </tr>
</table>

<div class="box1">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="group_form" onsubmit="return checkform(new Array(this.group_name, this.user_description))">
<input type="hidden" name="exec" value="edit">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=$group->getId()?>">
<table width="500px" border="0" cellpadding="0" cellspacing="0">
   <colgroup>
      <col width="180">
      <col>
   </colgroup>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('Gruppenname');?> *</td>
      <td class="content_row_clear">
         <input name="group_name" style="width:316px" class="text" value="<?=$group->getName()?>"
         onfocus="markfield(this,0)" onblur="markfield(this,1)">
      </td>
   </tr>
   <tr>
      <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung');?> *</td>
      <td class="content_row_clear">
         <textarea name="group_description" style="width:316px;height:150px" class="text" 
         onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$group->getDescription()?></textarea>
      </td>
   </tr>
</table>
<table width="500px">
   <colgroup>
      <col>
      <col width="60">
   </colgroup>
   <tr>
      <td class="content_header" colspan="2"><?=$_LANG->get('Rechte')?></td>
   </tr>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('Recht')?></td>
      <td class="content_row_header"><?=$_LANG->get('Ja/Nein')?></td>
   </tr>   
   <tr>
      <td class="content_row_clear"><?=$_LANG->get('Darf Urlaub genehmigen')?></td>
      <td class="content_row_clear"><input type="checkbox" name="right_urlaub" value="1" <? if($group->hasRight(Group::RIGHT_URLAUB)) echo "checked";?>></td>
   </tr>      
  <tr>
      <td class="content_row_clear"><?=$_LANG->get('Maschinenauswahl in Kalkulation anzeigen')?></td>
      <td class="content_row_clear"><input type="checkbox" name="right_machineselection" value="1" <? if($group->hasRight(Group::RIGHT_MACHINE_SELECTION)) echo "checked";?>></td>
   </tr>
   <tr>
      <td class="content_row_clear"><?=$_LANG->get('Ausf&uuml;hrliche Kalkulation anzeigen')?></td>
      <td class="content_row_clear"><input type="checkbox" name="right_detailed_calc" value="1" <? if($group->hasRight(Group::RIGHT_DETAILED_CALCULATION)) echo "checked";?>></td>
   </tr>
   <tr>
      <td class="content_row_clear"><?=$_LANG->get('Sollzeiten anzeigen')?></td>
      <td class="content_row_clear"><input type="checkbox" name="right_targettime" value="1" <? if($group->hasRight(Group::RIGHT_SEE_TARGETTIME)) echo "checked";?>></td>
   </tr>
  <tr>
      <td class="content_row_clear"><?=$_LANG->get('Teilauftr&auml;ge planen')?></td>
      <td class="content_row_clear"><input type="checkbox" name="right_parts_edit" value="1" <? if($group->hasRight(Group::RIGHT_PARTS_EDIT)) echo "checked";?>></td>
   </tr>
  <tr>
      <td class="content_row_clear"><?=$_LANG->get('Alle Kalender einsehen')?></td>
      <td class="content_row_clear"><input type="checkbox" name="right_all_calendar" value="1" <? if($group->hasRight(Group::RIGHT_ALL_CALENDAR)) echo "checked";?>></td>
   </tr>
   
</table>
<table width="500px">
   <colgroup>
      <col width="180">
      <col>
   </colgroup>
   <tr>
      <td class="content_row_header">&nbsp;</td>
      <td class="content_row_clear" align="right">
         <input type="submit" value=<?=$_LANG->get('Speichern')?>>
      </td>
   </tr>
</table>

</form>
</div>
<br>
<div class="box2">
<? if ($group->getId()) { ?>
<br>
<table width="500px">
   <colgroup>
      <col width="150">
      <col>
      <col width="60">
   </colgroup>
   <tr>
      <td class="content_header" colspan="2"><?=$_LANG->get('Mitglieder')?></td>
   </tr>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('Benutzername')?></td>
      <td class="content_row_header"><?=$_LANG->get('Voller Name')?></td>
      <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
   </tr>
   <? foreach($users as $u)
      {
         if ($u->isInGroup($group)) {?>
         <tr>
            <td class="content_row_clear"><?=$u->getLogin()?></td>
            <td class="content_row_clear"><?=$u->getFirstname()?> <?=$u->getLastname()?></td>
            <td class="content_row_clear"><a class="icon-link" href="#" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$group->getId()?>&subexec=removeuser&uid=<?=$u->getId()?>'"><img src="images/icons/minus.png" /></a></td>
         </tr>      
      <?}}?>
</table>
<br>
<table width="500px">
   <colgroup>
      <col width="150">
      <col>
      <col width="60">
   </colgroup>
   <tr>
      <td class="content_header" colspan="2"><?=$_LANG->get('Verf&uuml;gbare Benutzer')?></td>
   </tr>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('Benutzername')?></td>
      <td class="content_row_header"><?=$_LANG->get('Voller Name')?></td>
      <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
   </tr>
   <? foreach($users as $u)
      {
         if (!$u->isInGroup($group)) {?>
         <tr>
            <td class="content_row_clear"><?=$u->getLogin()?></td>
            <td class="content_row_clear"><?=$u->getFirstname()?> <?=$u->getLastname()?></td>
            <td class="content_row_clear"><a class="icon-link" href="#" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$group->getId()?>&subexec=adduser&uid=<?=$u->getId()?>'"><img src="images/icons/plus.png" /></a></td>
         </tr>      
      <?}}?>
</table>
</div>
<? } ?>