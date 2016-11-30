<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Matthias Welland <mwelland@ipactor.de>, 2016
 *
 */

require_once 'libs/modules/statistics/statistics.class.php';
require_once 'libs/modules/article/article.class.php';

if ($_REQUEST["stat_from"]) {
    $start = strtotime($_REQUEST["stat_from"]);
}
if ($_REQUEST["stat_to"]) {
    $end = strtotime($_REQUEST["stat_to"]);
}
if ($_REQUEST["stat_customer"]) {
    $stat_customer = $_REQUEST["stat_customer"];
}
if ($_REQUEST["stat_status"]) {
    $stat_status = $_REQUEST["stat_status"];
}
if ($_REQUEST["stat_article"]) {
    $stat_article = $_REQUEST["stat_article"];
}

?>


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Artikelumsatzstatistik</h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter</h3>
            </div>
            <div class="panel-body">

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Datum vom</label>
                        <div class="col-sm-4">
                            <input type="text" id="stat_from" name="stat_from"
                                   class="form-control text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                                   value="<? echo date('d.m.Y', $start); ?>"/>
                        </div>
                    <label for="" class="col-sm-2 control-label">bis</label>
                    <div class="col-sm-4">
                        <input type="text" id="stat_to" name="stat_to"
                               class="form-control text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                               value="<? echo date('d.m.Y', $end); ?>"/>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Kunde</label>
                    <div class="col-sm-10">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Auftragsstatus</label>
                    <div class="col-sm-3">
                        <select  name="stat_status" id=""  class="form-control">
                            <option <?php if ((int)$_REQUEST["stat_status"]==0) echo ' selected ';?> value="0">- Alle -</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==1) echo ' selected ';?> value="1">Angelegt</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==2) echo ' selected ';?> value="2">Gesendet u. Bestellt</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==3) echo ' selected ';?> value="3">angenommen</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==4) echo ' selected ';?> value="4">In Produktion</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==5) echo ' selected ';?> value="5">Erledigt</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Jahr</label>
                    <div class="col-sm-10">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Monat</label>
                    <div class="col-sm-10">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Suche</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="" id="" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="art_table" class="table table-hover">
                <thead>
                <tr>

                    <th>Artikel-ID</th>
                    <th>Artikelname</th>
                    <th>Beschreibung</th>
                    <th>Einkaufspreis</th>
                    <th>Verkaufspreis</th>
                    <th>Marge in %</th>
                    <th>Umsatz netto</th>
                    <th>Umsatz brutto</th>
                    <th>Ertrag in € (netto)</th>
                    <th>Ertrag in %</th>
                </tr>
                </thead>
                <?php
                $articel = Article::getAllArticle(0);
                foreach ($articel as $ar) {

                    echo '<tr>';
                    echo "<td>{$ar->getID()}</td>";
                    echo "<td>{$ar->getDescription()}</td>";
                    echo "<td>{$ar->getId()}</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td>}</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo '</tr>';

                } ?>
            </table>
        </div>
    </div>
</div>
