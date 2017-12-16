<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'shipment.class.php';


if ($_REQUEST["exec"] == "save") {
    $array = [
        'colinv' => $_REQUEST["colinv"],
        'shippingService' => $_REQUEST["shippingService"],
        'labelSize' => $_REQUEST["labelSize"],
        'shipDate' => time(),
        'weight' => tofloat($_REQUEST["weight"]),
        'width' => $_REQUEST["width"],
        'length' => $_REQUEST["length"],
        'height' => $_REQUEST["height"],
        'content' => $DB->escape(trim($_REQUEST["content"])),
        'reference' => $DB->escape(trim($_REQUEST["reference"]))
    ];

    $shipment = new Shipment(0, $array);
    $shipment->save();
    if ($shipment->getId() > 0){
        $shipping = new SaasShipping($shipment);
        $res = $shipping->send();
        $response = json_decode($res);
        if (count($response->error) > 0){
            $errors = $response->error;
            foreach ($errors as $error) {
                echo $error . "</br>";
            }
        } else {
            $shipment->packageLabel = $response->packageLabel;
            $shipment->parcelNumber = $response->parcelNumber;
            $shipment->save();
            $colinv = $shipment->getColinv();
            $colinv->setStatus(6);
            $colinv->save();
            echo '<script>location.href = "index.php?page=libs/modules/shipment/shipment.overview.php";</script>';
        }
    }
}


?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/shipment/shipment.overview.php', null, 'glyphicon-step-backward');
$quickmove->addItem('Speichern', '#', "$('#form').submit();", 'glyphicon-floppy-disk');
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Neue Sendung erfassen</h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post"
              class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Vorgang</label>
                <div class="col-sm-10">
                    <select name="colinv" id="colinv" class="form-control" multiple="multiple"></select>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Dienstleister</label>
                <div class="col-sm-10">
                    <select name="shippingService" id="shippingService" class="form-control">
                        <option value="dpd">DPD</option>
                        <option value="dpd_test">DPD Test</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Label-Größe</label>
                <div class="col-sm-10">
                    <select name="labelSize" id="labelSize" class="form-control">
                        <option value="PDF_A4">A4</option>
                        <option value="PDF_A6">A6</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Gewicht (kg)</label>
                <div class="col-sm-10">
                    <input type="number" step="0.1" class="form-control" name="weight" id="weight">
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Breite (cm)</label>
                <div class="col-sm-10">
                    <input type="number" step="1" class="form-control" name="width" id="width">
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Länge (cm)</label>
                <div class="col-sm-10">
                    <input type="number" step="1" class="form-control" name="length" id="length">
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Höhe (cm)</label>
                <div class="col-sm-10">
                    <input type="number" step="1" class="form-control" name="height" id="height">
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Inhalt</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="content" id="content">
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Referenz</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="reference" id="reference">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function() {
        $("#colinv").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_collectiveinvoice",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            language: "de",
            multiple: false,
            allowClear: false,
            tags: false
        }).trigger('change');
    });
</script>