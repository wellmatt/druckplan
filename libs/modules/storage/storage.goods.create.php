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
            $positions = SupOrderPosition::getAllForSupOrder($origin);
        }
        if ($type == StorageGoods::TYPE_COLINV) {
            $header = 'Warenausgang';
            $origin = new CollectiveInvoice($obj);
            $positions = Orderposition::getAllOrderposition($origin->getId());
        }
    }
} else {
    die ('Kein Objekt erhalten');
}
if ($_REQUEST["exec"] == "save"){
    if ($_REQUEST["book"]){
        foreach ($_REQUEST["book"] as $posid => $areas) {
            if ($posid>0){
                if ($type == StorageGoods::TYPE_SUPORDER) {
                    $position = new SupOrderPosition($posid);
                    $article = $position->getArticle();
                }
                elseif ($type == StorageGoods::TYPE_COLINV) {
                    $position = new Orderposition($posid);
                    $article = new Article($position->getObjectId());
                }
                else break;


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
    $remaining = 0;
    foreach ($positions as $position){
        // calculate if position is fully booked or needs additional
        $remaining += StorageBookEnrty::calcutateToBookAmount($position);
    }
    if ($remaining == 0){ // check if the whole origin object has been booked and if so ajust status
        if ($type == StorageGoods::TYPE_SUPORDER){
            $origin->setStatus(3);
            $origin->save();
        } elseif ($type == StorageGoods::TYPE_COLINV){
            $origin->setStatus(6);
            $origin->save();
        }
    }
}

$book_entries = StorageBookEnrty::getAllForOrigin($origin);

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/storage/storage.goods.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#goods_create').submit();",'glyphicon-floppy-disk');

echo $quickmove->generate();
// end of Quickmove generation ?>

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
                <h3 class="panel-title">
                    <?php echo $header;?> buchen
                    <span class="pull-right"><button class="btn btn-xs btn-default" onclick="window.open('libs/modules/storage/storage.goods.print.php?obj=<?php echo $obj;?>&type=<?php echo $type;?>');">Packzettel</button></span>
                </h3>
            </div>
            <div class="panel-body">

                <form action="index.php?page=<?=$_REQUEST['page']?>" id="goods_create" name="goods_create" method="post" role="form" class="form-horizontal">
                    <input type="hidden" id="obj" name="obj" value="<?=$obj?>" />
                    <input type="hidden" id="type" name="type" value="<?=$type?>" />
                    <input type="hidden" id="origin" name="origin" value="<?=$origin->getId()?>" />
                    <input type="hidden" id="exec" name="exec" value="save" />
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Herkunft</label>
                        <div class="col-sm-4 form-text" >
                            <?php echo $origin->getNumber();?>
                        </div>
                    </div>
                    <?php if (is_a($origin,'SupOrder')){?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Lieferant</label>
                            <div class="col-sm-4 form-text">
                                <?php echo $origin->getSupplier()->getNameAsLine();?>
                            </div>
                        </div>
                    <?php } else if (is_a($origin,'CollectiveInvoice')){?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Kunde</label>
                            <div class="col-sm-4 form-text">
                                <?php echo $origin->getCustomer()->getNameAsLine();?>
                            </div>
                        </div>
                    <?php }?>
                    <br>
                    <div id="positions">
                        <?php
                        if ($type == 1){
                            include "storage.goods.create.in.php";
                        } elseif ($type == 2){
                            include "storage.goods.create.out.php";
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>