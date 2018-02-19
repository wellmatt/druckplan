<?php
/**
 *  Copyright (c) 2018 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2018
 *
 */
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/planning/planning.job.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$date = strtotime($_REQUEST["date"]);
$day_start = mktime(0, 0, 0, date('m', $date), date('d', $date), date('Y', $date));
$day_end = mktime(23,59,59,date('m',$date),date('d',$date),date('Y',$date));

$artmach = $_REQUEST["artmach"];

$jobs = PlanningJob::getAllJobs(" AND type = 2 AND artmach = {$artmach} AND start >= {$day_start} AND start <= {$day_end}");
?>

<div class="form-group">
    <div class="col-sm-12">
        <ul id="sortable" style="width: auto;">
            <?php
            if (count($jobs)>0){
                foreach ($jobs as $pj){
                    $contents = Calculation::contentArray();
                    $me = $pj->getMe();
                    $calc = new Calculation($me->getCalcId());
                    ?>
                    <li class="ui-state-default" style="height: auto;">
                        <input type="hidden" class="id" value="<?php echo $pj->getId() ?>">
                        <input type="hidden" class="sort" name="column[<?php echo $pj->getId() ?>][sort]" value="<?php echo '0' ?>">
                        <div class="table-responsive">
                            <table class="table table-hover" id="art_table">
                                <thead style="font-weight: bold;">
                                    <tr>
                                        <td width="20">Prio</td>
                                        <td width="400">Objekt</td>
                                        <td width="150">MA</td>
                                        <td width="50">Vorgang</td>
                                        <td width="50">Ticket</td>
                                        <td width="90">Prod. Beginn</td>
                                        <td width="80">Lief.-Datum</td>
                                        <td width="50">S-Zeit</td>
                                        <td width="50">I-Zeit</td>
                                        <td width="80">Status</td>
                                        <td width="80">Material</td>
                                        <td width="80">Grammatur</td>
                                        <td width="80">Farbigkeit</td>
                                        <td width="80">Bogenformat</td>
                                        <td width="80">Produktformat</td>
                                    </tr>
                                </thead>
                                <tr>
                                    <td><?php echo '0';?></td>
                                    <td><?php echo $pj->getArtmach()->getGroup()->getName() . ' - ' . $pj->getArtmach()->getName();?></td>
                                    <?php if ($pj->getAssigned_user()->getId()>0){ ?>
                                        <td><?php echo $pj->getAssigned_user()->getNameAsLine();?></td>
                                    <?php } else {?>
                                        <td><?php echo $pj->getAssigned_group()->getName();?></td>
                                    <?php } ?>
                                    <td><a target="_blank" href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?php echo $pj->getObject()->getId();?>">#<?php echo $pj->getObject()->getNumber();?></a></td>
                                    <td><a target="_blank" href="index.php?page=libs/modules/tickets/ticket.php&exec=edit&returnhome=1&tktid=<?php echo $pj->getTicket()->getId();?>">#<?php echo $pj->getTicket()->getNumber();?></a></td>
                                    <td><?php echo date("d.m.Y",$pj->getTicket()->getDuedate());?></td>
                                    <td><?php echo date("d.m.Y",$pj->getObject()->getDeliverydate());?></td>
                                    <td><?php echo number_format($pj->getTplanned(), 2, ",", "");?></td>
                                    <td><?php echo number_format($pj->getTactual(), 2, ",", "");?></td>
                                    <td><?php echo $pj->getTicket()->getState()->getTitle();?></td>
                                    <?php foreach ($contents as $content) {
                                        if ($content['const'] == $me->getPart()) {?>
                                            <td><?php echo $calc->{$content['id']}()->getName();?></td>
                                            <td><?php echo $calc->{$content['weight']}();?>g</td>
                                            <td><?php echo $calc->{$content['chr']}()->getName();?></td>
                                            <td><?php echo $calc->{$content['width']}().'mm x '.$calc->{$content['height']}().'mm';?></td>
                                            <td><?php echo $calc->getProductFormatWidth().'mm x '.$calc->getProductFormatHeight().'mm';?></td>
                                    <?php } } ?>
                                </tr>
                            </table>
                        </div>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
</div>

<script>
    $(function () {
        $("#sortable").sortable({
            placeholder: "ui-state-highlight",
            stop: function (event, ui) {
                $('#sortable > li > input.sort').each(function (index) {
                    $(this).val(index);
                });
            }
        });
        $("#sortable").disableSelection();
    });
</script>