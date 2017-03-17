<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

$revenueaccount = new RevenueAccount((int)$_REQUEST["id"]);

if ($_REQUEST["subexec"] == "save"){
    $array = [
        "title" => $_REQUEST["title"],
        "number" => intval($_REQUEST["number"]),
        "taxkey" => $_REQUEST["taxkey"],
        "revenueaccountcategory" => $_REQUEST["revenueaccountcategory"],
        "postage" => ($_REQUEST["postage"] == 1) ? 1 : 0,
        "affiliatedcompany" => ($_REQUEST["affiliatedcompany"] == 1) ? 1 : 0
    ];
    $revenueaccount = new RevenueAccount((int)$_REQUEST["id"], $array);
    $ret = $revenueaccount->save();
    $_REQUEST["id"] = $revenueaccount->getId();
}

?>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/revenueaccounts/revenueaccount.overview.php', null, 'glyphicon-step-backward');
$quickmove->addItem('Speichern', '#', "$('#edit_form').submit();", 'glyphicon-floppy-disk');
if ($_USER->isAdmin() && $revenueaccount->getId() > 0) {
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/revenueaccounts/revenueaccount.overview.php&remove=" .$revenueaccount->getId() . "');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Erlöskonto bearbeiten</h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="edit_form" id="edit_form" method="post"
              class="form-horizontal" role="form">
            <input type="hidden" name="subexec" value="save">
            <input type="hidden" name="id" value="<?php echo $revenueaccount->getId();?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Titel</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" id="title" placeholder="Erlöse Digital" value="<?php echo $revenueaccount->getTitle();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Nummer</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="number" id="number" placeholder="1020301" value="<?php echo $revenueaccount->getNumber();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Kategorie</label>
                <div class="col-sm-10">
                    <select name="revenueaccountcategory" id="revenueaccountcategory" class="form-control">
                        <?php
                        foreach (RevenueaccountCategory::getAll() as $rac) {
                            if ($rac->getId() == $revenueaccount->getRevenueaccountcategory()->getId())
                                echo '<option selected value="' . $rac->getId() . '">' . $rac->getTitle() . '</option>';
                            else if ($revenueaccount->getId() == 0 && $_REQUEST["rac"] == $rac->getId())
                                echo '<option selected value="' . $rac->getId() . '">' . $rac->getTitle() . '</option>';
                            else
                                echo '<option value="' . $rac->getId() . '">' . $rac->getTitle() . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Umsatzsteuer</label>
                <div class="col-sm-10">
                    <select id="taxkey" name="taxkey" class="form-control">
                        <?php if ($revenueaccount->getTaxkey()->getId() > 0) echo '<option value="'.$revenueaccount->getTaxkey()->getId().'">'.$revenueaccount->getTaxkey()->getValue().'% ('.$revenueaccount->getTaxkey()->getTypeText().')</option>';?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Porto Konto</label>
                <div class="col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="postage" id="postage" value="1" <?php if ($revenueaccount->getPostage() == 1) echo ' checked ';?>>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">VU Konto</label>
                <div class="col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="affiliatedcompany" id="affiliatedcompany" value="1" <?php if ($revenueaccount->getAffiliatedcompany() == 1) echo ' checked ';?>>
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $("#taxkey").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_taxkey",
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
        }).val(<?php echo $revenueaccount->getTaxkey()->getId();?>).trigger('change');
    });
</script>