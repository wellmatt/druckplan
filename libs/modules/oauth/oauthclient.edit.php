<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/oauth/oauthclient.class.php';


$oauthclient = new oAuthClient(urldecode($_REQUEST["id"]));
if ($_REQUEST["exec"] == "edit")
{
    $array = [
        'name' => $_REQUEST["name"],
    ];
    if (!$_REQUEST['id'] || $_REQUEST['id'] == ''){
        $array['secret'] = generateRandomString(40);
    }
    $oauthclient = new oAuthClient($_REQUEST["id"], $array);
    $oauthclient->save();
    $oacscopes = [];
    if ($_REQUEST["scopes"]){
        foreach ($_REQUEST["scopes"] as $index => $scope) {
            $oacscopes[] = $scope;
        }
    }
    $oauthclient->saveScopes($oacscopes);
    $oauthclient = new oAuthClient($oauthclient->getId());
}
if ($_REQUEST["exec"] == "delete"){
    $oauthclient->delete();
    echo '<script>window.location.href=\'index.php?page=libs/modules/oauth/oauthadmin.php\';</script>';
}
$scopes = oAuthScope::getAll();
?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/oauth/oauthadmin.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#oaclient_edit').submit();",'glyphicon-floppy-disk');
if ($_USER->isAdmin() && $oauthclient->getId() != ''){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".urlencode($oauthclient->getId())."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" name="oaclient_edit" id="oaclient_edit">
    <input type="hidden" name="exec" value="edit">
    <input type="hidden" name="id" value="<?php echo urldecode($oauthclient->getId());?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">oAuth
                Client<?php if ($oauthclient->getId() != '') echo ' - ' . $oauthclient->getName(); ?></h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $oauthclient->getName();?>" placeholder="Name">
                </div>
            </div>
            <?php if ($oauthclient->getId() != ''){?>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">ID</label>
                <div class="col-sm-10 form-text">
                    <?php echo $oauthclient->getId();?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Secret</label>
                <div class="col-sm-10 form-text">
                    <?php echo $oauthclient->getSecret();?>
                </div>
            </div>
            <?php } ?>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Scopes</label>
                <div class="col-sm-10">
                    <select multiple name="scopes[]" id="scopes" class="form-control" size='<?php echo count($scopes);?>'>
                        <?php
                        foreach ($scopes as $scope) {
                            ?>
                            <option value="<?php echo $scope->getId(); ?>" <?php if ($oauthclient->hasScope($scope->getId())) echo ' selected ';?>><?php echo $scope->getDescription(); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    * Mehrfachauswahl möglich (STRG+Klick)
                </div>
            </div>
        </div>
    </div>
</form>
