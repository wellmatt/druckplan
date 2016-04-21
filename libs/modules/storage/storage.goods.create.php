<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/suporder/suporder.class.php';
require_once 'libs/modules/storage/storage.area.class.php';
require_once 'libs/modules/storage/storage.goods.position.class.php';

if ($_REQUEST['obj']){
    $obj = $_REQUEST['obj'];
    $header = '';
    if ($_REQUEST['type']){
        $type = $_REQUEST['type'];
        if ($type == StorageGoods::TYPE_SUPORDER) {
            $header = 'Wareneingang';
            $origin = new SupOrder($obj);
        }
        if ($type == StorageGoods::TYPE_COLINV) {
            $header = 'Warenausgang';
            $origin = new CollectiveInvoice($obj);
        }
    }
} else {
    die ('Kein Objekt erhalten');
}

if ($_REQUEST["exec"] == "save"){
    if ($_REQUEST["book"]){
        foreach ($_REQUEST["book"] as $posid => $areas) {
            if ($posid>0){
                if ($type == StorageGoods::TYPE_SUPORDER)
                    $position = new SupOrderPosition($posid);
                elseif ($type == StorageGoods::TYPE_COLINV)
                    $position = new SupOrderPosition($posid);
                else break;

                $article = $position->getArticle();

                foreach ($areas as $aid => $entry) {
                    if ($aid > 0){
                        $area = new StorageArea($aid);
                        $amount = $entry["amount"];
                        $alloc = $entry["alloc"];
                        if ($amount > 0 && $alloc > 0){
                            $array = [
                                "area" => $area->getId(),
                                "article" => $article->getId(),
                                "type" => $type,
                                "origin" => $origin->getId(),
                                "origin_pos" => $position->getId(),
                                "amount" => $amount,
                                "alloc" => $alloc,
                                "crtdate" => time(),
                                "crtuser" => $_USER->getId(),
                            ];
                            $book_entry = new StorageBookEnrty(0, $array);
                            $ret = $book_entry->save();
                            if ($ret)
                                $book_entries[] = $book_entry;
                        }
                    }
                }
            }
        }
    }
}

$book_entries = StorageBookEnrty::getAllForOrigin($origin);

?>

<div id="fl_menu">
    <div class="label">Quick Move</div>
    <div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=libs/modules/storage/storage.goods.overview.php" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#goods_create').submit();">Speichern</a>
    </div>
</div>

<?php if (isset($savemsg)) { ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Hinweis!</strong> <?= $savemsg ?>
    </div>
<?php } ?>

<?php if (count($book_entries)>0){ ?>
    <div class="panel panel-default">
          <div class="panel-heading">
                <h3 class="panel-title">Erstellte Buchungen für <?php echo $origin->getNumber();?></h3>
          </div>
        <br>
        <div class="table-responsive">
        	<table class="table table-hover">
        		<thead>
        			<tr>
        				<th>ID</th>
                        <th>Lager</th>
                        <th>Artikel</th>
                        <th>Anzahl</th>
                        <th>Belegung</th>
                        <th>Datum</th>
        			</tr>
        		</thead>
        		<tbody>
                    <?php foreach ($book_entries as $booke){ ?>
        			<tr>
        				<td><?php echo $booke->getId();?></td>
                        <td><?php echo $booke->getArea()->getName();?></td>
                        <td><?php echo $booke->getArticle()->getTitle();?></td>
                        <td><?php echo $booke->getAmount();?></td>
                        <td><?php echo $booke->getAlloc();?>%</td>
                        <td><?php echo date('d.m.y H:i',$booke->getCrtdate());?></td>
        			</tr>
                    <?php } ?>
        		</tbody>
        	</table>
        </div>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $header;?> buchen</h3>
            </div>
            <div class="panel-body">

                <form action="index.php?page=<?=$_REQUEST['page']?>" id="goods_create" name="goods_create" method="post" role="form" class="form-horizontal">
                    <input type="hidden" id="obj" name="obj" value="<?=$obj?>" />
                    <input type="hidden" id="type" name="type" value="<?=$type?>" />
                    <input type="hidden" id="origin" name="origin" value="<?=$origin->getId()?>" />
                    <input type="hidden" id="exec" name="exec" value="save" />

                    <table border="0" cellpadding="3" cellspacing="1" width="100%">
                        <colgroup>
                            <col width="130">
                            <col>
                        </colgroup>
                        <tr>
                            <td class="content_header"><?=$_LANG->get('Herkunft')?>: </td>
                            <td class="content_row_clear"><?php echo $origin->getNumber();?></td>
                        </tr>
                        <tr>
                            <td class="content_header"><?=$_LANG->get('Lieferant')?>: </td>
                            <td class="content_row_clear"><?php echo $origin->getSupplier()->getNameAsLine();?></td>
                        </tr>
                    </table>
                    <br>
                    <div id="positions">
                        <?php
                        if ($type == 1){
                            include "storage.goods.create.in.php";
                        } elseif ($type == 2){

                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>