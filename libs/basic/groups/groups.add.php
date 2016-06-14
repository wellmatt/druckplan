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
   $group->setRight(Group::RIGHT_SEE_ALL_CALENDAR, (int)$_REQUEST["right_see_all_calendar"]);
   $group->setRight(Group::RIGHT_EDIT_BC, (int)$_REQUEST["right_edit_bc"]);
   $group->setRight(Group::RIGHT_DELETE_BC, (int)$_REQUEST["right_delete_bc"]);
   $group->setRight(Group::RIGHT_EDIT_CP, (int)$_REQUEST["right_edit_cp"]);
   $group->setRight(Group::RIGHT_DELETE_CP, (int)$_REQUEST["right_delete_cp"]);
   $group->setRight(Group::RIGHT_DELETE_SCHEDULE, (int)$_REQUEST["right_delete_schedule"]);
   $group->setRight(Group::RIGHT_DELETE_ORDER, (int)$_REQUEST["right_delete_order"]);
   $group->setRight(Group::RIGHT_DELETE_COLINV, (int)$_REQUEST["right_delete_colinv"]);
   $group->setRight(Group::RIGHT_COMBINE_COLINV, (int)$_REQUEST["right_combine_colinv"]);
   $group->setRight(Group::RIGHT_TICKET_CHANGE_OWNER, (int)$_REQUEST["right_ticket_change_owner"]);
   $group->setRight(Group::RIGHT_ASSO_DELETE, (int)$_REQUEST["right_asso_delete"]);
   $group->setRight(Group::RIGHT_NOTES_BC, (int)$_REQUEST["right_notes_bc"]);
   $group->setRight(Group::RIGHT_APPROVE_VACATION, (int)$_REQUEST["right_approve_vacation"]);
    $group->setRight(Group::RIGHT_TICKET_EDIT_INTERNAL, (int)$_REQUEST["right_ticket_edit_internal"]);
    $group->setRight(Group::RIGHT_TICKET_EDIT_OFFICAL, (int)$_REQUEST["right_ticket_edit_offical"]);
   $savemsg = getSaveMessage($group->save());
   $savemsg .= $DB->getLastError();  
}

$users = User::getAllUser(User::ORDER_LOGIN);
?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#group_form').submit();",'glyphicon-floppy-disk');

if ($group->getId()>0){
    $quickmove->addItem('Löschen', '#',"askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$group->getId()."');", 'glyphicon-trash', true);
}

echo $quickmove->generate();
// end of Quickmove generation ?>


<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
                <? if ($group->getId()) echo $_LANG->get('Gruppe &auml;ndern'); else echo $_LANG->get('Gruppe hinzuf&uuml;gen');?>
	  </div>
	  <div class="panel-body">
          <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="group_form" name="group_form"
                class="form-horizontal" role="form" onsubmit="return checkform(new Array(this.group_name, this.group_description))">
              <input type="hidden" name="exec" value="edit">
              <input type="hidden" name="subexec" value="save">
              <input type="hidden" name="id" value="<?=$group->getId()?>">

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Gruppenname</label>
                  <div class="col-sm-10">
                      <input name="group_name" type="text" class="form-control" value="<?=$group->getName()?>"
                             onfocus="markfield(this,0)" onblur="markfield(this,1)">
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Beschreibung</label>
                  <div class="col-sm-10">
                     <textarea name="group_description" type="text" class="form-control"
                               onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$group->getDescription()?></textarea>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Recht</label>
                  <label for="" class="col-sm-1 control-label">Ja/Nein</label>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Darf Urlaub genehmigen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_urlaub" value="1" <? if($group->hasRight(Group::RIGHT_URLAUB)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Maschinenauswahl in Kalkulation anzeigen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_machineselection" value="1" <? if($group->hasRight(Group::RIGHT_MACHINE_SELECTION)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Ausführliche Kalkulation anzeigen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_detailed_calc"  value="1" <? if($group->hasRight(Group::RIGHT_DETAILED_CALCULATION)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Sollzeiten anzeigen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_targettime" value="1" <? if($group->hasRight(Group::RIGHT_SEE_TARGETTIME)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Teilaufträge plane</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_parts_edit" value="1" <? if($group->hasRight(Group::RIGHT_PARTS_EDIT)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Fremde Kalender benutzen / bearbeiten</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_all_calendar" value="1" <? if($group->hasRight(Group::RIGHT_ALL_CALENDAR)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Alle Kalender einsehen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control"  name="right_see_all_calendar" value="1" <? if($group->hasRight(Group::RIGHT_SEE_ALL_CALENDAR)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Geschäftskontakte bearbeiten</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_edit_bc" value="1" <? if($group->hasRight(Group::RIGHT_EDIT_BC)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Geschäftskontakte löschen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control"  name="right_delete_bc" value="1" <? if($group->hasRight(Group::RIGHT_DELETE_BC)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Ansprechpartner bearbeiten</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control"  name="right_edit_cp" value="1" <? if($group->hasRight(Group::RIGHT_EDIT_CP)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Ansprechpartner löschen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control"  name="right_delete_cp" value="1" <? if($group->hasRight(Group::RIGHT_DELETE_CP)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Kalkulationen löschen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_delete_order" value="1" <? if($group->hasRight(Group::RIGHT_DELETE_ORDER)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Vorgänge löschen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control"  name="right_delete_colinv" value="1" <? if($group->hasRight(Group::RIGHT_DELETE_COLINV)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Planung löschen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_delete_schedule" value="1" <? if($group->hasRight(Group::RIGHT_DELETE_SCHEDULE)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Vorgänge zusammenführen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_combine_colinv" value="1" <? if($group->hasRight(Group::RIGHT_COMBINE_COLINV)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Ticket Ersteller ändern</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_ticket_change_owner" value="1" <? if($group->hasRight(Group::RIGHT_TICKET_CHANGE_OWNER)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Ticket-Kommentar 'Intern' ändern</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_ticket_edit_internal" value="1" <? if($group->hasRight(Group::RIGHT_TICKET_EDIT_INTERNAL)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Ticket-Kommentar 'Offiziell' ändern</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_ticket_edit_offical" value="1" <? if($group->hasRight(Group::RIGHT_TICKET_EDIT_OFFICAL)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Verknüpfung löschen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_asso_delete" value="1" <? if($group->hasRight(Group::RIGHT_ASSO_DELETE)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Zugriff auf GK-Notizen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control" name="right_notes_bc" value="1" <? if($group->hasRight(Group::RIGHT_NOTES_BC)) echo "checked";?>>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Urlaube genehmingen</label>
                  <div class="col-sm-1">
                      <input type="checkbox" class="form-control"  name="right_approve_vacation" value="1" <? if($group->hasRight(Group::RIGHT_APPROVE_VACATION)) echo "checked";?>>
                  </div>
              </div>
          </form>
	  </div>
</div>




<div class="panel panel-default">
      <? if ($group->getId()) { ?>
	  <div class="panel-heading">
			<h3 class="panel-title">
                Mitglieder
            </h3>
	  </div>
	  <div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover">
                       <colgroup>
                          <col width="150">
                          <col>
                          <col width="60">
                       </colgroup>
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
                                <td class="content_row_clear"><a class="icon-link" href="#" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$group->getId()?>&subexec=removeuser&uid=<?=$u->getId()?>'"><span class="glyphicons glyphicons-minus pointer"></span></a></td>
                             </tr>
                          <?}}?>
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
                                <td class="content_row_clear"><a class="icon-link" href="#" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$group->getId()?>&subexec=adduser&uid=<?=$u->getId()?>'"><span class="glyphicons glyphicons-plus pointer"></span></a></td>
                            </tr>
                        <?}}?>
                </table>
            </div>
      </div>
          <? } ?>
</div>

