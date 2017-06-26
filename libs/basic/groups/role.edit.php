<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

$role = new Role((int)$_REQUEST["id"]);

if ($_REQUEST["exec"] == "save"){
    $array = [
        "name" => $_REQUEST["name"],
        "description" => trim(addslashes($_REQUEST["description"]))
    ];
    $role = new Role((int)$_REQUEST["id"], $array);
    $role->save();
    if ($role->getId()>0){
        RolePermission::wipeForRole($role);
        $newperms = $_REQUEST["perm"];
        foreach ($newperms as $key => $value) {
            $array = [
                "role" => $role->getId(),
                "permission" => $key
            ];
            $roleperm = new RolePermission(0, $array);
            $roleperm->save();
        }
    }
}

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php if ($role->getId() > 0){?>
            Rolle - Bearbeiten
            <?php } else {?>
            Rolle - Neu
            <?php }?>
        </h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="roleform" id="roleform" method="post"
              class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $role->getName();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Beschreibung</label>
                <div class="col-sm-10">
                    <textarea type="text" class="form-control" name="description" id="description"><?php echo $role->getDescription();?></textarea>
                </div>
            </div>
            <?php if ($role->getId()>0){
                $perms = Permission::fetch();
                $slugs = $role->getSlugs();
                ?>
                </br>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Rechte</h3>
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
                                <?php foreach ($perms as $perm) {?>
                                    <tr>
                                        <td><?php echo $perm->getName();?></td>
                                        <td><?php echo $perm->getDescription();?></td>
                                        <td>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 control-label"></label>
                                                <div class="col-sm-10">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="perm[<?php echo $perm->getId();?>]" id="perm_<?php echo $perm->getId();?>" value="1"
                                                                <?php if (in_array($perm->getSlug(),$slugs)){ echo ' checked ';}?>>
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


<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/basic/groups/role.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#roleform').submit();",'glyphicon-floppy-disk');
if ($role->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/basic/groups/role.overview.php&exec=delete&delid=".$role->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>