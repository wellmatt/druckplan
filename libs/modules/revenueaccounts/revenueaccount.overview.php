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
                <h3 class="panel-title">
                    Erlöskonten
                    <span class="pull-right">
                        <button class="btn btn-xs btn-success" type="button" onclick="window.location.href='index.php?page=libs/modules/revenueaccounts/revenueaccount.edit.php';">
                            <span class="glyphicons glyphicons-plus"></span>
                            Hinzufügen
                        </button>
                    </span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titel</th>
                            <th>Nummer</th>
                            <th>Steuer</th>
                            <th>Porto</th>
                            <th>VU</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revenueaccounts as $revenueaccount) {?>
                            <tr class="pointer">
                                <td onclick="window.location.href='index.php?page=libs/modules/revenueaccounts/revenueaccount.edit.php&id=<?php echo $revenueaccount->getId();?>';">
                                    <?php echo $revenueaccount->getId();?>
                                </td>
                                <td onclick="window.location.href='index.php?page=libs/modules/revenueaccounts/revenueaccount.edit.php&id=<?php echo $revenueaccount->getId();?>';">
                                    <?php echo $revenueaccount->getTitle();?>
                                </td>
                                <td onclick="window.location.href='index.php?page=libs/modules/revenueaccounts/revenueaccount.edit.php&id=<?php echo $revenueaccount->getId();?>';">
                                    <?php echo $revenueaccount->getNumber();?>
                                </td>
                                <td onclick="window.location.href='index.php?page=libs/modules/revenueaccounts/revenueaccount.edit.php&id=<?php echo $revenueaccount->getId();?>';">
                                    <?php echo $revenueaccount->getTaxkey()->getValue().'% ('.$revenueaccount->getTaxkey()->getTypeText().')';?>
                                </td>
                                <td onclick="window.location.href='index.php?page=libs/modules/revenueaccounts/revenueaccount.edit.php&id=<?php echo $revenueaccount->getId();?>';">
                                    <?php if ($revenueaccount->getPostage() == 1) echo '<span class="glyphicons glyphicons-ok"></span>'; else echo '<span class="glyphicons glyphicons-remove"></span>';?>
                                </td>
                                <td onclick="window.location.href='index.php?page=libs/modules/revenueaccounts/revenueaccount.edit.php&id=<?php echo $revenueaccount->getId();?>';">
                                    <?php if ($revenueaccount->getAffiliatedcompany() == 1) echo '<span class="glyphicons glyphicons-ok"></span>'; else echo '<span class="glyphicons glyphicons-remove"></span>';?>
                                </td>
                                <td>
                                    <?php if ($revenueaccount->getDefault() == 1){?>
                                        <span class="glyphicons glyphicons-star"></span>
                                    <?php } else {?>
                                        <span class="glyphicons glyphicons-star-empty pointer" onclick="window.location.href='index.php?page=<?php echo $_REQUEST['page']; ?>&star=<?php echo $revenueaccount->getId();?>';"></span>
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