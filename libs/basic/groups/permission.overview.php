<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

if ($_REQUEST["exec"] == "save"){
    $array = [
        "name" => $_REQUEST["name"],
        "description" => trim(addslashes($_REQUEST["description"])),
        "slug" => $_REQUEST["slug"]
    ];
    $perm = new Permission(0, $array);
    $perm->save();
}

$perms = Permission::fetch();
?>

<?php if ($_USER->getLogin() == 'ascherer'){?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Neues Recht</h3>
        </div>
        <div class="panel-body">
            <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="permform" id="permform" method="post"
                  class="form-horizontal" role="form">
                <input type="hidden" name="exec" value="save">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" id="name" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Slug</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="slug" id="slug" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Beschreibung</label>
                    <div class="col-sm-10">
                        <textarea type="text" class="form-control" name="description" id="description"></textarea>
                    </div>
                </div>
                <button class="btn btn-xs btn-success" type="submit" onclick="">Speichern</button>
            </form>
        </div>
    </div>
<?php } ?>

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
                <?php if ($_USER->isAdmin()){ ?>
                    <th>Slug</th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($perms as $perm){?>
                    <tr>
                        <td><?php echo $perm->getName();?></td>
                        <td><?php echo $perm->getDescription();?></td>
                        <?php if ($_USER->isAdmin()){ ?>
                            <td><?php echo $perm->getSlug();?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
