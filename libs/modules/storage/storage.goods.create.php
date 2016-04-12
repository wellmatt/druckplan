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
        if ($type == 1) {
            $header = 'Wareneingang';
            $origin = new SupOrder($obj);
        }
        if ($type == 2) {
            $header = 'Warenausgang';
            $origin = new CollectiveInvoice($obj);
        }
    }
} else {
    die ('Kein Objekt erhalten');
}

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

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $header;?> buchen</h3>
            </div>
            <div class="panel-body">

                <form action="index.php?page=<?=$_REQUEST['page']?>" id="goods_create" name="goods_create" method="post" role="form" class="form-horizontal">
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
                </form>
                <br>
                <div id="positions">
                    <?php
                    if ($type == 1){
                        include "storage.goods.create.in.php";
                    } elseif ($type == 2){

                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>