<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
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
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/calculation/calculation.position.class.php';
require_once 'libs/basic/cleanjsonserializer.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_REQUEST["exec"] == 'getAvailableParts' && $_REQUEST["product"]){
    $part = (int)$_REQUEST["part"];
    $product = new Product((int)$_REQUEST["product"]);
    $parts = [$product->getHasContent(),$product->getHasAddContent(),$product->getHasAddContent2(),$product->getHasAddContent3(),$product->getHasEnvelope()];
    echo json_encode($parts);
}

if ($_REQUEST["exec"] == 'getAvailablePaperFormats' && $_REQUEST["product"] && $_REQUEST["part"]){
    $part = (int)$_REQUEST["part"];
    $product = new Product((int)$_REQUEST["product"]);
    $formats = $product->getAvailablePaperFormats();
    foreach ($formats as $format) {
        ?>
        <input type="button" class="btn btn-small btn-info" onclick="select_format(this);"
               value="<?php echo $format->getName() . "\n" . '(' . $format->getWidth() . ' x ' . $format->getHeight() . ' '.$_LANG->get('mm').')';?>" style="margin-left: 3px;"
               data-part="<?php echo $part;?>"
               data-format="<?php echo $format->getId();?>"
               data-width="<?php echo $format->getWidth();?>"
               data-height="<?php echo $format->getHeight();?>"
        >
        <?php
    }
}

if ($_REQUEST["exec"] == 'getSelectedPapersIds' && $_REQUEST["product"] && $_REQUEST["part"]){
    $part = (int)$_REQUEST["part"];
    $product = new Product((int)$_REQUEST["product"]);
    $papers = $product->getSelectedPapersIds($part);
    foreach ($papers as $paperId) {
        $paper = new Paper($paperId["id"]);
        ?>
        <input type="button" class="btn btn-small btn-info" onclick="select_paper(this);"
               value="<?php echo $paper->getName();?>" style="margin-left: 3px;"
               data-part="<?php echo $part;?>"
               data-paper="<?php echo $paper->getId();?>"
        >
        <?php
    }
}

if ($_REQUEST["exec"] == 'getAvailablePaperWeights' && $_REQUEST["product"] && $_REQUEST["part"] && $_REQUEST["paper"]){
    $part = (int)$_REQUEST["part"];
    $product = new Product((int)$_REQUEST["product"]);
    $papers = $product->getSelectedPapersIds($part);
    $paper = $papers[$_REQUEST["paper"]];
    foreach (array_keys($paper) as $weight) {
        if ($weight != "id") {
            ?>
            <input type="button" class="btn btn-small btn-info" onclick="select_weight(this);"
                   value="<?php echo $weight;?>g" style="margin-left: 3px;"
                   data-part="<?php echo $part;?>"
                   data-paper="<?php echo $_REQUEST["paper"];?>"
                   data-weight="<?php echo $weight;?>"
            >
            <?php
        }
    }
}

if ($_REQUEST["exec"] == 'getAvailablePages' && $_REQUEST["product"] && $_REQUEST["part"]){
    $part = (int)$_REQUEST["part"];
    $product = new Product((int)$_REQUEST["product"]);

    if ($part < 5){
        if($product->getType() == Product::TYPE_NORMAL){
            foreach ($product->getAvailablePageCounts() as $pc) {
                ?>
                <input type="button" class="btn btn-small btn-info" onclick="select_pages(this);"
                       value="<?php echo $pc;?> Seiten" style="margin-left: 3px;"
                       data-part="<?php echo $part;?>"
                       data-pages="<?php echo $pc;?>"
                >
                <?php
            }
        } else {
            echo '<div class="form-group">
                  <label for="" class="col-sm-4 control-label">Seitenzahl</label>
                  <div class="col-sm-6">
                      <input name="part_'.$part.'_numberpages" class="form-control" id="part_'.$part.'_numberpages" value="">
                  </div>
              </div>';
        }
    } else {
        $pages = array("2", "4", "6", "8");
        foreach ($pages as $pc) {
            ?>
            <input type="button" class="btn btn-small btn-info" onclick="select_pages(this);"
                   value="<?php echo $pc;?> Seiten" style="margin-left: 3px;"
                   data-part="<?php echo $part;?>"
                   data-pages="<?php echo $pc;?>"
            >
            <?php
        }
    }
}

if ($_REQUEST["exec"] == 'getAvailablePaperChromas' && $_REQUEST["product"] && $_REQUEST["part"] && $_REQUEST["pages"]){
    $part = (int)$_REQUEST["part"];
    $product = new Product((int)$_REQUEST["product"]);
    $pages = $_REQUEST["pages"];

    if (count($product->getAvailableChromaticities())>0){
        $chromas = $product->getAvailableChromaticities();
    } else {
        $chromas = Chromaticity::getAllChromaticities();
    }

    foreach ($chromas as $pc) {
        if($pages > 1 || ($pages == 1 && !$pc->getReversePrinting()))
        {
            ?>
            <input type="button" class="btn btn-small btn-info" onclick="select_chroma(this);"
                   value="<?php echo $pc->getName();?>" style="margin-left: 3px;"
                   data-part="<?php echo $part;?>"
                   data-chroma="<?php echo $pc->getId();?>"
            >
            <?php
        }
    }
}




?>