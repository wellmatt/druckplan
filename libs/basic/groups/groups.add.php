<?php
$_REQUEST["id"] = (int)$_REQUEST["id"];
$group = new Group($_REQUEST["id"]);

if ($_REQUEST["subexec"] == "save")
{
    $group->setName(trim(addslashes($_REQUEST["group_name"])));
    $group->setDescription(trim(addslashes($_REQUEST["group_description"])));
    $savemsg = getSaveMessage($group->save());
    $savemsg .= $DB->getLastError();

    if ($group->getId()>0){
        GroupRole::wipeForGroup($group);
        $newroles = $_REQUEST["role"];
        foreach ($newroles as $key => $value) {
            $array = [
                "group" => $group->getId(),
                "role" => $key
            ];
            $grouprole = new GroupRole(0, $array);
            $grouprole->save();
        }
        GroupUser::wipeForGroup($group);
        $newusers = $_REQUEST["user"];
        foreach ($newusers as $key => $value) {
            $array = [
                "group" => $group->getId(),
                "user" => $key
            ];
            $groupuser = new GroupUser(0, $array);
            $groupuser->save();
        }
    }
}
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
                <? if ($group->getId()) echo $_LANG->get('Gruppe &auml;ndern'); else echo $_LANG->get('Gruppe hinzuf&uuml;gen');?>
            </h3>
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
              <?php if ($group->getId()>0){
                  $roles = Role::fetch();
                  $rids = GroupRole::getRoleIdsForGroup($group);
                  ?>
                  </br>
                  <div class="panel panel-default">
                      <div class="panel-heading">
                          <h3 class="panel-title">Rollen</h3>
                      </div>
                      <div class="table-responsive">
                          <table class="table table-hover">
                              <thead>
                              <tr>
                                  <th>Name</th>
                                  <th>Beschreibung</th>
                                  <th>Zugewiesen</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($roles as $role) {?>
                                  <tr>
                                      <td><?php echo $role->getName();?></td>
                                      <td><?php echo $role->getDescription();?></td>
                                      <td>
                                          <div class="form-group">
                                              <label for="" class="col-sm-2 control-label"></label>
                                              <div class="col-sm-10">
                                                  <div class="checkbox">
                                                      <label>
                                                          <input type="checkbox" name="role[<?php echo $role->getId();?>]" id="role_<?php echo $role->getId();?>" value="1"
                                                              <?php if (in_array($role->getId(),$rids)){ echo ' checked ';}?>>
                                                      </label>
                                                  </div>
                                              </div>
                                          </div>
                                      </td>
                                  </tr>
                              <?php }?>
                              </tbody>
                          </table>
                      </div>
                  </div>
              <?php }?>
              <?php if ($group->getId()>0){
                  $users = User::getAllUser();
                  $uids = GroupUser::getUserIdsForGroup($group);
                  ?>
                  </br>
                  <div class="panel panel-default">
                      <div class="panel-heading">
                          <h3 class="panel-title">Benutzer</h3>
                      </div>
                      <div class="table-responsive">
                          <table class="table table-hover">
                              <thead>
                              <tr>
                                  <th>Name</th>
                                  <th>Mitglied</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($users as $user) {?>
                                  <tr>
                                      <td><?php echo $user->getNameAsLine();?></td>
                                      <td>
                                          <div class="form-group">
                                              <label for="" class="col-sm-2 control-label"></label>
                                              <div class="col-sm-10">
                                                  <div class="checkbox">
                                                      <label>
                                                          <input type="checkbox" name="user[<?php echo $user->getId();?>]" id="user_<?php echo $user->getId();?>" value="1"
                                                              <?php if (in_array($user->getId(),$uids)){ echo ' checked ';}?>>
                                                      </label>
                                                  </div>
                                              </div>
                                          </div>
                                      </td>
                                  </tr>
                              <?php }?>
                              </tbody>
                          </table>
                      </div>
                  </div>
              <?php }?>
          </form>
      </div>
</div>


