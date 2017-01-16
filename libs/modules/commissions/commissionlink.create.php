<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

if (!$_REQUEST["bcid"]){
    die('Kein Geschäftskontakt übergeben!');
}

$comlink = new CommissionLink((int)$_REQUEST["comlink"]);
$businesscontact = new BusinessContact((int)$_REQUEST["bcid"]);

if ($_REQUEST["exec"] == "save"){
    $percentage = tofloat($_REQUEST["percentage"]);
    if ($_REQUEST["partner"] > 0 && $percentage > 0){
        $array = [
            "partner" => (int)$_REQUEST["partner"],
            "businesscontact" => (int)$_REQUEST["bcid"],
            "percentage" => $percentage,
        ];
        $comlink = new CommissionLink(0, $array);
        $ret = $comlink->save();
        $savemsg = getSaveMessage($ret);
    }
}
if ($_REQUEST["del"] > 0){
    $dellink = new CommissionLink((int)$_REQUEST["del"]);
    $dellink->delete();
    echo '<script>window.location.href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id='.$businesscontact->getId().'"</script>';
}


?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id='.$businesscontact->getId(),null,'glyphicon-step-backward');
$quickmove->addItem('Speichern', '#', "$('#comlinkform').submit();", 'glyphicon-floppy-disk');
if ($comlink->getId() > 0) {
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/commissions/commissionlink.create.php&bcid=" . $businesscontact->getId() . "&del=".$comlink->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<link href="jscripts/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="jscripts/select2/dist/js/select2.min.js"></script>
<script src="jscripts/select2/dist/js/i18n/de.js"></script>

<?php if (isset($savemsg)) { ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Hinweis!</strong> <?= $savemsg ?>
    </div>
<?php } ?>


<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Provision - <?php echo $businesscontact->getNameAsLine();?></h3>
            </div>
            <div class="panel-body">
                <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="comlinkform" id="comlinkform" method="post" class="form-horizontal"
                      role="form">
                    <input type="hidden" name="bcid" value="<?php echo $businesscontact->getId();?>">
                    <input type="hidden" name="exec" value="save">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Provisionspartner</label>
                        <div class="col-sm-10">
                            <select name="partner" id="partner" class="form-control">
                                <?php if ($comlink->getId() > 0){?>
                                    <option value="<?php echo $comlink->getPartner()->getId();?>" selected><?php echo $comlink->getPartner()->getNameAsLine();?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Prozent</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" name="percentage" value="<?php echo printPrice($comlink->getPercentage());?>">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#partner").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_commissionpartner",
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
            minimumInputLength: 1,
            language: "de",
            multiple: false,
            allowClear: true,
            tags: false
        }).trigger('change');
    });
</script>