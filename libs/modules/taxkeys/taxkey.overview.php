<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'taxkey.class.php';

if ($_REQUEST["star"] > 0){
    $starttk = new TaxKey((int)$_REQUEST["star"]);
    $starttk->star();
}

if ($_REQUEST["remove"] > 0){
    $remtk = new TaxKey((int)$_REQUEST["remove"]);
    $remtk->delete();
}

if ($_REQUEST["exec"] == "save"){
    $array = [
        "value" => tofloat($_REQUEST["value"]),
        "key" => $_REQUEST["key"],
        "default" => 0,
    ];
    $taxkey = new TaxKey(0, $array);
    $ret = $taxkey->save();
    if ($ret && $_REQUEST["default"] == 1){
        $taxkey->star();
    }
}

$taxkeys = TaxKey::getAll();

?>

<?php if (isset($savemsg)) { ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Hinweis!</strong> <?= $savemsg ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Steuerschlüssel</h3>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                	  <div class="panel-heading">
                			<h3 class="panel-title">Neuer Schlüssel</h3>
                	  </div>
                	  <div class="panel-body">
                          <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="revaccform" id="revaccform" method="post" class="form-horizontal" role="form">
                              <input type="hidden" name="exec" value="save">
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Prozentsatz</label>
                                  <div class="col-sm-10">
                                      <div class="input-group">
                                          <input type="text" class="form-control" name="value">
                                          <span class="input-group-addon">%</span>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Schlüssel</label>
                                  <div class="col-sm-10">
                                      <input type="text" class="form-control" name="key">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Standard</label>
                                  <div class="col-sm-10">
                                      <div class="checkbox">
                                          <label>
                                              <input type="checkbox" name="default" id="default" value="1">
                                          </label>
                                      </div>
                                  </div>
                              </div>
                              <button class="btn btn-xs btn-success pull-right" type="submit">
                                  <span class="glyphicons glyphicons-disk"></span>
                                  Speichern
                              </button>
                          </form>
                	  </div>
                </div>
                <div class="table-responsive">
                	<table class="table table-hover">
                		<thead>
                			<tr>
                				<th>ID</th>
                				<th>Prozentsatz</th>
                				<th>Schlüssel</th>
                				<th></th>
                			</tr>
                		</thead>
                		<tbody>
                            <?php foreach ($taxkeys as $tk) {?>
                                <tr>
                                    <td><?php echo $tk->getId();?></td>
                                    <td><?php echo printPrice($tk->getValue());?>%</td>
                                    <td><?php echo $tk->getKey();?></td>
                                    <td>
                                        <?php if ($tk->getDefault() == 1){?>
                                            <span class="glyphicons glyphicons-star"></span>
                                        <?php } else {?>
                                            <span class="glyphicons glyphicons-star-empty pointer" onclick="window.location.href='index.php?page=<?php echo $_REQUEST['page']; ?>&star=<?php echo $tk->getId();?>';"></span>
                                        <?php } ?>
                                        <?php if ($tk->getDefault() == 0){?>
                                        &nbsp;<button class="btn btn-xs btn-danger" type="button" onclick="window.location.href='index.php?page=<?php echo $_REQUEST['page']; ?>&remove=<?php echo $tk->getId();?>';">
                                            Löschen
                                        </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                		</tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>
</div>