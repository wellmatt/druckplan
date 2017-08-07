<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


if ($_REQUEST["exec"] == "delete")
{
    $delrole = new Role($_REQUEST["delid"]);
    $delrole->delete();
}

$roles = Role::fetch();
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Rollen
            <span class="pull-right">
                <button class="btn btn-xs btn-success" type="button" onclick="window.location.href='index.php?page=libs/basic/groups/role.edit.php'">
                    <span class="glyphicons glyphicons-plus"></span> Neu
                </button>
            </span>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Beschreibung</th>
                    <th>Rechte</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($roles as $role){
                    $perms = $role->getPermissions();
                    ?>
                    <tr class="pointer" onclick="window.location.href='index.php?page=libs/basic/groups/role.edit.php&id=<?php echo $role->getId();?>'">
                        <td><?php echo $role->getName();?></td>
                        <td><?php echo $role->getDescription();?></td>
                        <td>
                            <?php
                            foreach ($perms as $perm){
                                echo '<span title="'.$perm->getDescription().'">'.$perm->getName().'</span></br>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>