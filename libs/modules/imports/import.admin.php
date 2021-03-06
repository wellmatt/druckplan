<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once('import.class.php');

$perf = new Perferences();

if ($_REQUEST["import"] && $_FILES['csv']){
    if ($_FILES['csv']['type'] == 'text/comma-separated-values') {
        $csv = Import::getCSV($_FILES['csv']['tmp_name']);
        switch ($_REQUEST["import"]){
            case 'pricescale':
                $alert = Import::ImportPriceScales($csv);
                break;
            default:
                break;
        }
    }
}

?>
<?php if (is_string($alert)){?>
<div class="row">
	<div class="col-md-12">
        <?php echo $alert; ?>
	</div>
</div>
<?php }?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Import</h3>
            </div>
            <div class="panel-body">
                <!-- TAB NAVIGATION -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#tab1" role="tab" data-toggle="tab">Preisstaffeln</a></li>
                </ul>
                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <div class="active tab-pane fade in" id="tab1">
                        <h2>Preisstaffeln</h2>
                        <p>Hinweis: Import der Preisstaffeln per CSV (Komma getrennt)</p>
                        <p>Spalten die vorhanden sein <u>müssen</u>:</p>
                        <ul>
                            <li>article (Artikel-ID im Contilas)</li>
                            <li>type (1 = VK / 2 = EK)</li>
                            <li>min (Menge von)</li>
                            <li>max (Menge bis)</li>
                            <li>price (Preis ohne Währung)</li>
                            <li>supplier (bei EK Lieferant, ID des Geschäftskontakts, sonst 0)</li>
                        </ul>
                        <p>Beispiel:</p>
                        <pre>article,type,min,max,price,supplier,test1,test2<br>1,1,1,1000,10,1,5,7<br>1,2,1,1000,5,1,9,acht</pre>
                        <br>
                        <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="import" value="pricescale">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <label class="input-group-btn">
                                            <span class="btn btn-primary">
                                               Durchsuchen <input style="display:none;"  multiple="" type="file" class="form-cntrol" name="csv" required>
                                            </span>
                                        </label>
                                        <input class="form-control" readonly="" type="text">
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-warning" type="submit">Import starten</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $(document).on('change', ':file', function () {
            var input = $(this), numFiles = input.get(0).files ? input.get(0).files.length : 1, label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [
                numFiles,
                label
            ]);
        });
        $(document).ready(function () {
            $(':file').on('fileselect', function (event, numFiles, label) {
                var input = $(this).parents('.input-group').find(':text'), log = numFiles > 1 ? numFiles + ' files selected' : label;
                if (input.length) {
                    input.val(log);
                } else {
                    if (log)
                        alert(log);
                }
            });
        });
    });
</script>