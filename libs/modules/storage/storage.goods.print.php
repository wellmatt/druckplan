<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
//error_reporting(-1);
//ini_set('display_errors', 1);
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/storage/storage.position.class.php';
require_once 'libs/modules/storage/storage.book.entry.class.php';
require_once 'libs/modules/suporder/suporder.class.php';
require_once 'libs/modules/storage/storage.area.class.php';
require_once 'libs/modules/storage/storage.goods.position.class.php';

$storage = [];
$articles = [];
if ($_REQUEST['obj']){
    $obj = $_REQUEST['obj'];
    if ($_REQUEST['type']){
        $type = $_REQUEST['type'];
        if ($type == StorageGoods::TYPE_SUPORDER) {
            $origin = new SupOrder($obj);
            $positions = SupOrderPosition::getAllForSupOrder($origin);

            foreach ($positions as $position) {
                $article = $position->getArticle();
                if ($article->getUsesstorage()) {
                    $storages = StorageArea::getStoragesPrioArticle($article);
                    $bookamount = StorageBookEnrty::calcutateToBookAmount($position);
                    if ($bookamount > 0) {
                        if (count($storages['prio'])>0){
                            foreach ($storages['prio'] as $storage) {
                                $this_storage = new StorageArea($storage['id']);
                                $this_position = new StoragePosition($storage['posid']);

                                $storage[$this_storage->getId()][] = ['article'=>$article, 'amount'=>$this_position->getAmount()];
                                $articles[] = ['article'=>$article, 'amount'=>$this_position->getAmount()];
                            }
                        }
                    }
                }
            }
        }
        if ($type == StorageGoods::TYPE_COLINV) {
            $origin = new CollectiveInvoice($obj);
            $positions = Orderposition::getAllOrderposition($origin->getId());

            foreach ($positions as $position) {
                if ($position->getType() == 1 || $position->getType() == 2) {
                    $article = new Article($position->getObjectid());
                    if ($article->getUsesstorage()) {
                        $storages = StorageArea::getStoragesPrioArticle($article);
                        $bookamount = StorageBookEnrty::calcutateToBookAmount($position);
                        if ($bookamount > 0) {
                            foreach ($storages['prio'] as $storage) {
                                $this_storage = new StorageArea($storage['id']);
                                $this_position = new StoragePosition($storage['posid']);

                                $storage[$this_storage->getId()][] = ['article'=>$article, 'amount'=>$position->getQuantity()];
                                $articles[] = ['article'=>$article, 'amount'=>$position->getQuantity()];
                            }
                        }
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="../../../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/ticket.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
    <link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <script type="text/javascript" language="JavaScript">
        function printPage() {
            focus();
            if (window.print)
            {
                jetztdrucken = confirm('Seite drucken ?');
                if (jetztdrucken)
                    window.print();
            }
        }
    </script>

    <?
    /**************************************************************************
     ******* 				HTML-Bereich								*******
     *************************************************************************/?>
<body OnLoad="printPage()">

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Packzettel zu <?php echo $origin->getNumber();?></h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Artikelliste</h3>
            </div>
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Artikel</th>
                        <th>Nummer</th>
                        <th>Menge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $art) {?>
                        <tr>
                            <td><?php echo $art['article']->getTitle();?></td>
                            <td><?php echo $art['article']->getNumber();?></td>
                            <td><?php echo $art['amount'];?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php foreach ($storage as $id => $sa) {
            if ($id > 0){
                $area = new StorageArea($id);
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Lager: <?php echo $area->getName();?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-2"><span style="font-weight: bold;">Ort: </span> <?=$area->getLocation()?></div>
                            <div class="col-md-2"><span style="font-weight: bold;">Gang: </span> <?=$area->getCorridor()?></div>
                            <div class="col-md-2"><span style="font-weight: bold;">Regal: </span> <?=$area->getShelf()?></div>
                            <div class="col-md-2"><span style="font-weight: bold;">Reihe: </span> <?=$area->getLine()?></div>
                            <div class="col-md-2"><span style="font-weight: bold;">Ebene: </span> <?=$area->getLayer()?></div>
                            <div class="col-md-2">
                                <span style="font-weight: bold;">Priorit&auml;t</span>
                                <?php if ($area->getPrio() == 0) echo 'Niedrig';?>
                                <?php if ($area->getPrio() == 1) echo 'Mittel';?>
                                <?php if ($area->getPrio() == 2) echo 'Hoch';?>
                            </div>
                        </div>
                    </div>
                    <table class="table table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Nummer</th>
                            <th>Menge</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($sa as $item) {
                            $article = $item['article'];
                            $amount = $item['amount'];
                            ?>
                            <tr>
                                <td><?php echo $article->getTitle();?></td>
                                <td><?php echo $article->getNumber();?></td>
                                <td><?php echo $amount;?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>