<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

$all_tradegroups = Tradegroup::getAllTradegroups(0);
function printSubTradegroupsForSelect($parentId, $depth){
    global $article;
    $all_subgroups = Tradegroup::getAllTradegroups($parentId);
    foreach ($all_subgroups AS $subgroup){
        global $x;
        $x++; ?>
        <option value="<?=$subgroup->getId()?>">
            <?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
            <?= $subgroup->getTitle()?>
        </option>
        <? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
    }
}
$allsupplier = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,' supplier = 1 ');

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Weiter','#',"$('#paperimport').submit();",'glyphicon-floppy-disk');
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Material-Import
            <span class="pull-right">* Nur eine Datei gleichzeitig!</span>
        </h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=libs/modules/paper/material.import.results.php" name="paperimport" id="paperimport" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Achtung</h3>
                </div>
                <div class="panel-body">
                    Der Import der Daten kann je nach Menge der zu verarbeitenden Daten einige Zeit in Anspruch nehmen, brechen Sie den Vorgang auf <b>KEINEN</b> Fall ab!<br>
                    Beim ersten Import ist die benötigte Zeit deutlich größer!<br><br><br>
                    Für jedes importierte Material wird automatisch ein zugehöriger Artikel mit den Lieferanteninformationen und Preisen angelegt.<br>
                    Artikelinformationen und Preise werden bei jedem Import bei vorhanden Materialien aktualisiert.
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Artikel Einstellungen</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Warengruppe</label>
                        <div class="col-sm-10">
                            <select id="article_tradegroup" class="form-control" name="article_tradegroup" required>
                                <?php
                                foreach ($all_tradegroups as $tg){?>
                                    <option value="<?=$tg->getId()?>"><?= $tg->getTitle()?></option>
                                    <?	printSubTradegroupsForSelect($tg->getId(), 0);
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Lieferant</label>
                        <div class="col-sm-10">
                            <select name="article_suppier" id="article_suppier" class="form-control">
                                <?php foreach ($allsupplier as $supplier) {
                                    echo '<option value="' . $supplier->getId() . '">' . $supplier->getNameAsLine() . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Largerartikel</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="article_storage" id="article_storage" value="1">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Dateiauswahl
                    </h3>
                </div>
                <div class="panel-body">
                    * Sollte Ihr Lieferant noch nicht verfügbar sein wenden Sie sich bitte an Ihren Ansprechpartner bei uns
                    <div class="form-group">
                        <label for="paperfile" class="col-sm-2 control-label">Igepa</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" id="paperfile_igepa" name="paperfile_igepa" required>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
