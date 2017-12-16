<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
error_reporting(-1);
ini_set('display_errors', 1);
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/suporder/suporder.class.php';
require_once 'libs/modules/article/article.class.php';

if ($_REQUEST["id"]){
    $suporder = new SupOrder($_REQUEST["id"]);
    $positions = SupOrderPosition::getAllForSupOrder($suporder);
} else {
    die('Keine ID angegeben!');
}
?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="../../../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/ticket.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
    <link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <script type="text/javascript" language="JavaScript">
        function printPage() {
            focus();
            if (window.print)
            {
                jetztdrucken = confirm('Seite drucken ?');
                if (jetztdrucken)
//                    window.print();
            }
        }
    </script>

    <?
    /**************************************************************************
     ******* 				HTML-Bereich								*******
     *************************************************************************/?>
<body OnLoad="printPage()">

<div class="panel panel-default" style="border: none; padding: 0px;">
	  <div class="panel-body" style="border: none; padding: 0px;">
          <br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;
          <p style="font-size: small"><?php echo $_USER->getClient()->getName();?> // <?php echo $_USER->getClient()->getStreet1();?> // <?php echo $_USER->getClient()->getCountry()->getCode();?>-<?php echo $_USER->getClient()->getPostcode();?> <?php echo $_USER->getClient()->getCity();?></p>
          <p style="font-size: smaller">
              <?php echo $suporder->getSupplier()->getNameAsLine(); ?><br>
              <?php echo $suporder->getSupplier()->getStreet(); ?> <?php echo $suporder->getSupplier()->getHouseno(); ?><br>
              <?php if ($suporder->getSupplier()->getAddress2() != "") { ?>
                  <?php echo $suporder->getSupplier()->getAddress2(); ?><br>
              <?php } ?>
              <?php echo $suporder->getSupplier()->getCountry()->getCode();?>-<?php echo $suporder->getSupplier()->getZip(); ?> <?php echo $suporder->getSupplier()->getCity(); ?><br>
          </p>
	  </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Bestellung: <?=$suporder->getNumber()?></h3>
    </div>
    <div class="panel-body">
        <table border="0" cellpadding="3" cellspacing="1" width="100%">
            <colgroup>
                <col width="130">
                <col>
            </colgroup>
            <tr>
                <td class="content_header"><?=$_LANG->get('Titel')?>: </td>
                <td class="content_row_clear"><?=$suporder->getTitle()?></td>
            </tr>
                <tr>
                    <td class="content_header"><?=$_LANG->get('Nummer')?>:</td>
                    <td class="content_row_clear"><?=$suporder->getNumber()?></td>
                </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Zahlungsbedingung')?>: </td>
                <td class="content_row_clear"><?=$suporder->getPaymentterm()->getName()?></td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Lieferant')?>: </td>
                <td class="content_row_clear">
                    <?php echo $suporder->getSupplier()->getNameAsLine()." - ".$suporder->getCpexternal()->getNameAsLine2();?>
                </td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Lieferdatum')?>: </td>
                <td class="content_row_clear"><?=date('d.m.y',$suporder->getEta())?></td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Verant. MA')?>: </td>
                <td class="content_row_clear"><?=$suporder->getCpinternal()->getNameAsLine()?></td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Status')?>: </td>
                <td class="content_row_clear">
                    <?php if ($suporder->getStatus() == 1) echo 'Offen';?>
                    <?php if ($suporder->getStatus() == 2) echo 'Bestellt';?>
                    <?php if ($suporder->getStatus() == 3) echo 'Ware Eingegangen';?>
                    <?php if ($suporder->getStatus() == 4) echo 'Bezahlt';?>
                    <?php if ($suporder->getStatus() == 5) echo 'Erledigt';?>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Positionen</h3>
    </div>
    <table id="storage_pos" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
        <thead>
        <tr>
            <th>#</th>
            <th>Art.Name</th>
            <th>Unsere Art.Nr.</th>
            <th>Ihre Art.Nr.</th>
            <th>Menge</th>
            <th>Preis</th>
        </tr>
        </thead>
        <?php
        foreach ($positions as $position) {
            $pricescale = PriceScale::getPriceScaleForAmount($position->getArticle(),$position->getAmount(),PriceScale::TYPE_BUY);
            $price = $pricescale->getPrice() * $position->getAmount();
            ?>
            <tr>
                <td><?php echo $position->getId();?></td>
                <td><?php echo $position->getArticle()->getTitle();?></td>
                <td><?php echo $position->getArticle()->getNumber();?></td>
                <td><?php echo $pricescale->getArtnum();?></td>
                <td><?php echo $position->getAmount();?></td>
                <td><?php echo $price;?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>