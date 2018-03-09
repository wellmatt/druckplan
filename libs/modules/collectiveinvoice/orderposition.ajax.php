<?
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
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
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once 'libs/modules/partslists/partslist.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));


if ($_REQUEST['exec'] == 'addPosArticle' && $_REQUEST["ciid"] && $_REQUEST["aid"]){
    $newpos = new Orderposition();
    $article = new Article((int)$_REQUEST["aid"]);
    $colinv = new CollectiveInvoice((int)$_REQUEST["ciid"]);

    if (count($article->getOrderamounts())>0){
        $quantity = $article->getOrderamounts()[0];
    } else {
        $quantity = 1;
    }

    $newpos->setQuantity($quantity);
    $newpos->setPrice($article->getPrice(1));
    $newpos->setTaxkey(TaxKey::evaluateTax($colinv, $article));
    $newpos->setComment($article->getDesc());
    $newpos->setCollectiveinvoice($colinv->getId());
    if ($article->getOrderid() > 0)
        $newpos->setType(1);
    else
        $newpos->setType(2);
    $newpos->setObjectid($article->getId());
    $newpos->setSequence(Orderposition::getNextSequence($colinv));
    $newpos->save();

    if ($article->getOrderid()){
        $order = new Order($article->getOrderid());
        $calc = Calculation::getAllCalcWithAmount($order,$quantity);
        $calcarts = CalculationArticle::getAllForCalc($calc);
        foreach ($calcarts as $calcart) {
            $capos = new Orderposition();
            $capos->setQuantity($calcart->getTotalAmount());
            $capos->setPrice(0);
            $capos->setTaxkey(TaxKey::evaluateTax($colinv, $calcart->getArticle()));
            $capos->setComment($calcart->getArticle()->getDesc());
            $capos->setCollectiveinvoice($colinv->getId());
            $capos->setType(2);
            $capos->setObjectid($calcart->getArticle()->getId());
            $capos->setSequence(Orderposition::getNextSequence($colinv));
            $capos->save();
        }
    }

    if ($article->getIsWorkHourArt() || $newpos->getType() == 1){
        $colinv->setNeeds_planning(1);
        $colinv->save();
    }
}

if ($_REQUEST['exec'] == 'addPosPartslist' && $_REQUEST["ciid"] && $_REQUEST["plid"]){
    $partslist = new Partslist((int)$_REQUEST["plid"]);
    $colinv = new CollectiveInvoice((int)$_REQUEST["ciid"]);

    foreach ($partslist->getMyArticles() as $myArticle) {
        $article = $myArticle->getArticle();
        $quantity = $myArticle->getAmount();

        $newpos = new Orderposition();
        $newpos->setQuantity($quantity);
        $newpos->setPrice($article->getPrice(1));
        $newpos->setTaxkey(TaxKey::evaluateTax($colinv, $article));
        $newpos->setComment($article->getDesc());
        $newpos->setCollectiveinvoice($colinv->getId());
        if ($article->getOrderid() > 0)
            $newpos->setType(1);
        else
            $newpos->setType(2);
        $newpos->setObjectid($article->getId());
        $newpos->setSequence(Orderposition::getNextSequence($colinv));
        $newpos->save();

        if ($article->getOrderid()){
            $order = new Order($article->getOrderid());
            $calc = Calculation::getAllCalcWithAmount($order,$quantity);
            $calcarts = CalculationArticle::getAllForCalc($calc);
            foreach ($calcarts as $calcart) {
                $capos = new Orderposition();
                $capos->setQuantity($calcart->getTotalAmount());
                $capos->setPrice(0);
                $capos->setTaxkey(TaxKey::evaluateTax($colinv, $calcart->getArticle()));
                $capos->setComment($calcart->getArticle()->getDesc());
                $capos->setCollectiveinvoice($colinv->getId());
                $capos->setType(2);
                $capos->setObjectid($calcart->getArticle()->getId());
                $capos->setSequence(Orderposition::getNextSequence($colinv));
                $capos->save();
            }
        }

        if ($article->getIsWorkHourArt() || $newpos->getType() == 1){
            $colinv->setNeeds_planning(1);
            $colinv->save();
        }
    }
}

if ($_REQUEST['exec'] == 'addPosManually' && $_REQUEST["ciid"]){
    $newpos = new Orderposition();
    $colinv = new CollectiveInvoice((int)$_REQUEST["ciid"]);

    $newpos->setQuantity(1);
    $newpos->setPrice(1);
    $newpos->setTaxkey(TaxKey::evaluateTax($colinv, new Article()));
    $newpos->setComment("");
    $newpos->setCollectiveinvoice($colinv->getId());
    $newpos->setType(0);
    $newpos->setObjectid(0);
    $newpos->setSequence(Orderposition::getNextSequence($colinv));
    $newpos->save();
}

if ($_REQUEST['exec'] == 'updatePos' && $_REQUEST["oid"]){
    $pos = new Orderposition((int)$_REQUEST["oid"]);
    $pos->setStatus((int)$_REQUEST["opos_status"]);
    $pos->setTaxkey(new TaxKey((int)$_REQUEST["opos_taxkey"]));
    $pos->setPrice(tofloat($_REQUEST["opos_price"]));
    $pos->setQuantity(tofloat($_REQUEST["opos_quantity"]));
    $pos->setComment(trim(addslashes($_REQUEST["opos_comment"])));
    $res = $pos->save();
    $output = ["result"=>$res];
    echo json_encode($output);
}

if ($_REQUEST['exec'] == 'getUpdatedPrice' && $_REQUEST["oid"] && $_REQUEST["quantity"]){
    $pos = new Orderposition((int)$_REQUEST["oid"]);
    $article = new Article((int)$pos->getObjectid());
    $quantity = tofloat($_REQUEST["quantity"]);
    $price = $article->getPrice($quantity);
    $output = ["price"=>$price];
    echo json_encode($output);
}

if ($_REQUEST['exec'] == 'getPosForm' && $_REQUEST["oid"]){
    $taxkeys = TaxKey::getAll();
    $pos = new Orderposition((int)$_REQUEST["oid"]);
    $tmp_art = new Article((int)$pos->getObjectid());
    ?>
    <form name="posform_<?php echo $_REQUEST["oid"];?>" id="posform_<?php echo $_REQUEST["oid"];?>" class="form-horizontal" role="form">
        <input type="hidden" name="oid" value="<?php echo $_REQUEST["oid"];?>">
        <div class="panel panel-default" style="margin-bottom: 0; border-radius: 0;">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo $pos->getTitle();?>
                    <span class="pull-right">
                            <button class="btn btn-xs btn-success" type="button" onclick="submitForm($(this).closest('form'));">
                                <span class="glyphicons glyphicons-plus"></span>
                                Speichern
                            </button>
                            <button class="btn btn-xs btn-warning" type="button" onclick="$(this).closest('tr').remove();">
                                <span class="glyphicons glyphicons-plus"></span>
                                Verwerfen
                            </button>
                        </span>
                    </h3>
              </div>
              <div class="panel-body">
                      <div class="form-group">
                          <label for="" class="col-sm-1 control-label">Status</label>
                          <div class="col-sm-2">
                              <select name="opos_status" id="opos_status_<?php echo $_REQUEST["oid"];?>" class="form-control">
                                  <option value="0" <?php if ($pos->getStatus() == 0) echo ' selected ';?>>gelöscht</option>
                                  <option value="1" <?php if ($pos->getStatus() == 1) echo ' selected ';?>>aktiv</option>
                                  <option value="2" <?php if ($pos->getStatus() == 2) echo ' selected ';?>>deaktiviert</option>
                              </select>
                          </div>
                          <label for="" class="col-sm-1 control-label">Menge</label>
                          <div class="col-sm-2">
                              <?php
                              if ($pos->getType() == 1 || $pos->getType() == 2)
                              {
                                  if (count($tmp_art->getOrderamounts())>0)
                                  {
                                      echo '<select name="opos_quantity" id="opos_quantity_'.$_REQUEST["oid"].'" class="form-control" onchange="getUpdatedPrice('.$_REQUEST["oid"].', this.value)">';
                                      foreach ($tmp_art->getOrderamounts() as $orderamount)
                                      {
                                          echo '<option value="'.$orderamount.'" ';
                                          if ($pos->getQuantity() == $orderamount)
                                              echo ' selected ';
                                          echo ' >'.$orderamount.'</option>';
                                      }
                                      echo '</select>';
                                  } else {
                                      echo '<input name="opos_quantity" id="opos_quantity_'.$_REQUEST["oid"].'" value="'.printPrice($pos->getQuantity(),2).'" class="form-control" onchange="getUpdatedPrice('.$_REQUEST["oid"].', this.value)">';
                                  }
                              } else {
                                  echo '<input name="opos_quantity" id="opos_quantity_'.$_REQUEST["oid"].'" value="'.printPrice($pos->getQuantity(),2).'" class="form-control" onchange="getUpdatedPrice('.$_REQUEST["oid"].', this.value)">';
                              }
                              ?>
                          </div>
                          <label for="" class="col-sm-1 control-label">Preis</label>
                          <div class="col-sm-2">
                              <div class="input-group">
                                  <input 	name="opos_price" id="opos_price_<?php echo $_REQUEST["oid"];?>" class="form-control" value="<?= printPrice($pos->getPrice(),3)?>">
                                  <span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span>
                              </div>
                          </div>
                          <label for="" class="col-sm-1 control-label">Steuer</label>
                          <div class="col-sm-2">
                              <select id="opos_taxkey_<?php echo $_REQUEST["oid"];?>" name="opos_taxkey" class="form-control">
                                  <?php
                                  foreach ($taxkeys as $taxkey) {
                                      if ($pos->getTaxkey()->getId() == $taxkey->getId()){
                                          echo '<option value="'.$taxkey->getId().'" selected>'.$taxkey->getValue().'% ('.$taxkey->getTypeText().')</option>';
                                      } else {
                                          echo '<option value="'.$taxkey->getId().'">'.$taxkey->getValue().'% ('.$taxkey->getTypeText().')</option>';
                                      }
                                  }
                                  ?>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="" class="col-sm-1 control-label">Beschreibung</label>
                          <div class="col-sm-12">
                              <textarea rows="10" name="opos_comment" class="poscomment form-control" id="opos_comment_<?php echo $_REQUEST["oid"];?>" style="width: 100%;"><?php echo $pos->getComment()?></textarea>
                          </div>
                      </div>
              </div>
        </div>
    </form>
    <?php
}