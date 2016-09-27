<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Matthias Welland <mwelland@ipactor.de>, 2016
 *
 */
?>


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Maschinenumsatzstatistik</h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter</h3>
            </div>
            <div class="panel-body">

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Datum vom</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="" id="" placeholder="">
                    </div>
                    <label for="" class="col-sm-2 control-label">bis</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="" id="" placeholder="">
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Warengruppe</label>
                    <div class="col-sm-10">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Maschinengruppe</label>
                    <div class="col-sm-10">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
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
            <table class="table table-hover">
                <thead>
                <tr>

                    <th>Maschinen-ID</th>
                    <th>Maschinenname</th>
                    <th>Beschreibung</th>
                    <th>Soll-Zeit</th>
                    <th>Ist-Zeit</th>
                    <th>Abweichung in %</th>
                    <th>Umsatz</th>
                    <th>Anzahl Aufträge</th>
                    <th>Ertrag in € (netto)</th>
                    <th>Ertrag in %</th>


                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Summe Soll-Zeit</th>
                    <th>Summe Ist-Zeit</th>
                    <th>Summe Abweichung in %</th>
                    <th>Summe Umsatz (netto):</th>
                    <th>Summe Anzahl Aufträge</th>
                    <th>Summe Ertrag in € (netto)</th>
                    <th>Summe Ertrag in %</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
