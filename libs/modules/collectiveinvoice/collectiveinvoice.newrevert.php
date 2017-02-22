<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'libs/modules/accounting/revert.class.php';

if ($_REQUEST["subexec"] == "save"){
    $positions = $_REQUEST["revamount"];
    if (count($positions) > 0 && $_REQUEST["letterhead_revert"]){
        $letterhead = (int)$_REQUEST["letterhead_revert"];
        $revert = Revert::generate($collectinv, $positions, new Letterhead($letterhead));
        echo '<script>window.location.href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=docs&ciid='.$collectinv->getId().'"</script>';
    }
}

$orderpositions = Orderposition::getAllOrderposition($collectinv->getId());
?>
<form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="" id="" method="post" class="form-horizontal" role="form">
    <input type="hidden" name="ciid" value="<?php echo $collectinv->getId();?>">
    <input type="hidden" name="exec" value="createNewRevert">
    <input type="hidden" name="subexec" value="save">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Gutschrift erstellen
                <span class="pull-right">
                    <button type="button" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$collectinv->getId()?>';" class="btn btn-sm btn-default">
                        <?=$_LANG->get('Zurück')?>
                    </button>
                    <button class="btn btn-sm btn-success" type="submit">
                        <span class="glyphicons glyphicons-plus"></span>
                        Generieren
                    </button>
                </span>
            </h3>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Positionen
                        <span class="pull-right">
                            <button class="btn btn-sm btn-warning" type="button" onclick="maxAll();">
                                <span class="glyphicons glyphicons-ok"></span>
                                Alle Gutschreiben
                            </button>
                        </span>
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Artikel</th>
                                <th>Menge</th>
                                <th>Nettopreis</th>
                                <th>Bereits Gutgeschrieben</th>
                                <th>Gutschrift Menge</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($orderpositions as $orderposition) {
                                $revertamount = 0;
                                $revertpositions = RevertPosition::getAllForOpos($orderposition);
                                $reverts = '';
                                if (count($revertpositions)>0){
                                    foreach ($revertpositions as $revertposition) {
                                        if ($revertposition->getRevert()->getStatus() != Revert::STATE_STORNO){
                                            $revertamount += $revertposition->getAmount();
                                            $reverts .= $revertposition->getRevert()->getNumber().': '.printPrice($revertposition->getAmount());
                                        } else {
                                            $reverts .= '<s>'.$revertposition->getRevert()->getNumber().': '.printPrice($revertposition->getAmount()).'</s>';
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td><?php echo $orderposition->getId();?></td>
                                    <td><?php echo $orderposition->getTitle();?></td>
                                    <td><?php echo printPrice($orderposition->getAmount());?></td>
                                    <td><?php echo printPrice($orderposition->getPrice());?>€</td>
                                    <td><?php echo $reverts;?></td>
                                    <td>
                                        <?php if (($orderposition->getAmount()-$revertamount)>0){ ?>
                                            <input type="number" class="form-control revamount" min="0" max="<?php echo $orderposition->getAmount()-$revertamount;?>" name="revamount[<?php echo $orderposition->getId();?>]">
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Briefpapier</label>
                <div class="col-sm-10">
                    <?php
                    $letterheads = Letterhead::getAllForType(7);
                    ?>
                    <select name="letterhead_revert" class="form-control">
                        <?php
                        foreach ($letterheads as $item) {
                            if ($item->getStd() == 1)
                                echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
                            else
                                echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(function () {
        $( ".revamount" ).change(function() {
            var max = parseInt($(this).attr('max'));
            var min = parseInt($(this).attr('min'));
            if ($(this).val() > max)
            {
                $(this).val(max);
            }
            else if ($(this).val() < min)
            {
                $(this).val(min);
            }
        });
    });
    function maxAll(){
        $( ".revamount" ).each(function( index ) {
            var max = parseInt($(this).attr('max'));
            $(this).val(max);
        });
    }
</script>