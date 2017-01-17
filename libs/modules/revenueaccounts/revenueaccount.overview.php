<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'revenueaccount.class.php';

if ($_REQUEST["star"] > 0){
    $startrevacc = new RevenueAccount((int)$_REQUEST["star"]);
    $startrevacc->star();
}

if ($_REQUEST["remove"] > 0){
    $startrevacc = new RevenueAccount((int)$_REQUEST["remove"]);
    $startrevacc->delete();
}

if ($_REQUEST["exec"] == "save"){
    $array = [
        "title" => $_REQUEST["title"],
        "default" => $_REQUEST["default"],
        "number" => intval($_REQUEST["number"]),
    ];
    $revaccount = new RevenueAccount(0, $array);
    $ret = $revaccount->save();
}

$revenueaccounts = RevenueAccount::getAll();

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
                <h3 class="panel-title">Erlöskonten</h3>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                	  <div class="panel-heading">
                			<h3 class="panel-title">Neues Konto</h3>
                	  </div>
                	  <div class="panel-body">
                          <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="revaccform" id="revaccform" method="post" class="form-horizontal" role="form">
                              <input type="hidden" name="exec" value="save">
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Titel</label>
                                  <div class="col-sm-10">
                                      <input type="text" class="form-control" name="title" id="title">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Nummer</label>
                                  <div class="col-sm-10">
                                      <input type="text" class="form-control" name="number" id="number">
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
                				<th>Titel</th>
                				<th>Nummer</th>
                				<th></th>
                			</tr>
                		</thead>
                		<tbody>
                            <?php foreach ($revenueaccounts as $revenueaccount) {?>
                                <tr>
                                    <td><?php echo $revenueaccount->getId();?></td>
                                    <td><?php echo $revenueaccount->getTitle();?></td>
                                    <td><?php echo $revenueaccount->getNumber();?></td>
                                    <td>
                                        <?php if ($revenueaccount->getDefault() == 1){?>
                                            <span class="glyphicons glyphicons-star"></span>
                                        <?php } else {?>
                                            <span class="glyphicons glyphicons-star-empty pointer" onclick="window.location.href='index.php?page=<?php echo $_REQUEST['page']; ?>&star=<?php echo $revenueaccount->getId();?>';"></span>
                                        <?php } ?>
                                        <?php if ($revenueaccount->getDefault() == 0){?>
                                            &nbsp;<button class="btn btn-xs btn-danger" type="button" onclick="window.location.href='index.php?page=<?php echo $_REQUEST['page']; ?>&remove=<?php echo $revenueaccount->getId();?>';">
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