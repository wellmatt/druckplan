<?php
$order = new Order((int)$_REQUEST["id"]);
$contents = Calculation::contentArray();
error_reporting(0);
ini_set('display_errors', 0);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Auftragsdaten: <b><?= $order->getNumber() ?></b></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-1">
                <b><?= $_LANG->get('Produkt') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= $order->getProduct()->getName() ?>
            </div>
            <div class="col-md-2">
                <b><?= $_LANG->get('Beschreibung') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= $order->getProduct()->getDescription() ?>
            </div>
        </div>
    </div>
</div>


<? $i = 1;
foreach (Calculation::getAllCalculations($order,Calculation::ORDER_AMOUNT) as $calc) {
    $allmes = Machineentry::getAllMachineentries($calc->getId());
    $calc_sorts = $calc->getSorts();
    if ($calc_sorts == 0)
        $calc_sorts = 1;
    ?>
    
    <div class="panel panel-default">
    	  <div class="panel-heading">
    			<h3 class="panel-title">
                    Auflage: <?= printBigInt($calc->getAmount()) ?>
                    <span class="pull-right clickable pointer panel-collapsed"><i class="glyphicon glyphicon-chevron-down"></i></span>
                    <span class="pull-right">
                        <?= $calc_sorts ?> Sorte(n) x <?= $calc->getAmount() / $calc_sorts ?> Auflage &nbsp;&nbsp;
                    </span>
                </h3>
    	  </div>
    	  <div class="panel-body slidepanel" style="display: none;">
              <div class="panel panel-success">
              	  <div class="panel-heading">
              			<h2 class="panel-title">Fertigungsprozess</h2>
              	  </div>
              	  <div class="panel-body" style="padding: 3px;">
                      <? foreach (MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION) as $mg) {
                          $machentries = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID, $mg->getId());
                          if (count($machentries) > 0){?>
                              <div class="panel panel-info">
                                  <div class="panel-heading">
                                      <h3 class="panel-title"><?php echo $mg->getName();?></h3>
                                  </div>
                                  <div class="panel-body">
                                      <div class="row">
                                          <? foreach($machentries as $me) {?>
                                              <div class="col-md-4">
                                                  <div class="panel panel-default">
                                                      <div class="panel-heading">
                                                          <h3 class="panel-title">
                                                              <?php echo $me->getMachine()->getName(); ?>
                                                              <? if ($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                                                                  $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET ||
                                                                  $me->getMachine()->getType() == Machine::TYPE_FOLDER
                                                              ) {
                                                                  switch ($me->getPart()) {
                                                                      case Calculation::PAPER_CONTENT:
                                                                          echo " - " . $_LANG->get('Inhalt 1');
                                                                          break;
                                                                      case Calculation::PAPER_ADDCONTENT:
                                                                          echo " - " . $_LANG->get('Inhalt 2');
                                                                          break;
                                                                      case Calculation::PAPER_ENVELOPE:
                                                                          echo " - " . $_LANG->get('Umschlag');
                                                                          break;
                                                                      case Calculation::PAPER_ADDCONTENT2:
                                                                          echo " - " . $_LANG->get('Inhalt 3');
                                                                          break;
                                                                      case Calculation::PAPER_ADDCONTENT3:
                                                                          echo " - " . $_LANG->get('Inhalt 4');
                                                                          break;
                                                                  }
                                                              } ?>
                                                          </h3>
                                                      </div>
                                                      <ul class="list-group">
                                                          <?php if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL && $me->getLabelcount()) {?>
                                                              <li class="list-group-item">
                                                                  <span class="badge"><?php echo ceil($calc->getAmount() / $me->getLabelcount());?></span>
                                                                  Anzahl Rollen Etikettenangabe
                                                              </li>
                                                          <?php } ?>
                                                          <?php if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL && $me->getRollcount()) {?>
                                                              <li class="list-group-item">
                                                                  <span class="badge"><?php echo ceil($calc->getAmount() / $me->getRollcount());?></span>
                                                                  Anzahl Rollen Laufmeterangabe
                                                              </li>
                                                          <?php } ?>
                                                          <?php if($me->getMachine()->getType() == Machine::TYPE_CTP) {
                                                              foreach($allmes as $me2) {
                                                                  if($me2->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                                                                      switch($me2->getPart())
                                                                      {
                                                                          case Calculation::PAPER_CONTENT:
                                                                              ?>
                                                                              <li class="list-group-item">
                                                                                  <span class="badge"><?php echo $calc->getPlateCount($me2);?></span>
                                                                                  Druckplatten Inhalt 1
                                                                              </li>
                                                                              <?php
                                                                              break;
                                                                          case Calculation::PAPER_ADDCONTENT:
                                                                              ?>
                                                                              <li class="list-group-item">
                                                                                  <span class="badge"><?php echo $calc->getPlateCount($me2);?></span>
                                                                                  Druckplatten Inhalt 2
                                                                              </li>
                                                                              <?php
                                                                              break;
                                                                          case Calculation::PAPER_ADDCONTENT2:
                                                                              ?>
                                                                              <li class="list-group-item">
                                                                                  <span class="badge"><?php echo $calc->getPlateCount($me2);?></span>
                                                                                  Druckplatten Inhalt 3
                                                                              </li>
                                                                              <?php
                                                                              break;
                                                                          case Calculation::PAPER_ADDCONTENT3:
                                                                              ?>
                                                                              <li class="list-group-item">
                                                                                  <span class="badge"><?php echo $calc->getPlateCount($me2);?></span>
                                                                                  Druckplatten Inhalt 4
                                                                              </li>
                                                                              <?php
                                                                              break;
                                                                          case Calculation::PAPER_ENVELOPE:
                                                                              ?>
                                                                              <li class="list-group-item">
                                                                                  <span class="badge"><?php echo $calc->getPlateCount($me2);?></span>
                                                                                  Druckplatten Umschlag
                                                                              </li>
                                                                              <?php
                                                                              break;
                                                                      }
                                                                  }
                                                              }
                                                              ?>
                                                              <li class="list-group-item">
                                                                  <span class="badge"><?php echo $calc->getPlateCount();?></span>
                                                                  Druckplatten Gesamt
                                                              </li>
                                                          <?php } ?>
                                                          <?php if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                                                              switch($me->getPart())
                                                              {
                                                                  case Calculation::PAPER_CONTENT:
                                                                      ?>
                                                                      <li class="list-group-item">
                                                                          <span class="badge"><?php echo $calc->getChromaticitiesContent()->getName();?></span>
                                                                          Farbigkeit
                                                                      </li>
                                                                      <?php
                                                                      break;
                                                                  case Calculation::PAPER_ADDCONTENT:
                                                                      ?>
                                                                      <li class="list-group-item">
                                                                          <span class="badge"><?php echo $calc->getChromaticitiesAddContent()->getName();?></span>
                                                                          Farbigkeit
                                                                      </li>
                                                                      <?php
                                                                      break;
                                                                  case Calculation::PAPER_ADDCONTENT2:
                                                                      ?>
                                                                      <li class="list-group-item">
                                                                          <span class="badge"><?php echo $calc->getChromaticitiesAddContent2()->getName();?></span>
                                                                          Farbigkeit
                                                                      </li>
                                                                      <?php
                                                                      break;
                                                                  case Calculation::PAPER_ADDCONTENT3:
                                                                      ?>
                                                                      <li class="list-group-item">
                                                                          <span class="badge"><?php echo $calc->getChromaticitiesAddContent3()->getName();?></span>
                                                                          Farbigkeit
                                                                      </li>
                                                                      <?php
                                                                      break;
                                                                  case Calculation::PAPER_ENVELOPE:
                                                                      ?>
                                                                      <li class="list-group-item">
                                                                          <span class="badge"><?php echo $calc->getChromaticitiesEnvelope()->getName();?></span>
                                                                          Farbigkeit
                                                                      </li>
                                                                      <?php
                                                                      break;
                                                              }?>
                                                              <li class="list-group-item">
                                                                  <span class="badge">
                                                                      <?php
                                                                      if ((int)$me->getUmschl() == 1)
                                                                          echo 'Umschlagen';
                                                                      elseif ((int)$me->getUmst() == 1)
                                                                          echo 'Umscht&uuml;lpen';
                                                                      else
                                                                          echo 'Sch&ouml;n & Wider';
                                                                      ?>
                                                                  </span>
                                                                  Druckart
                                                              </li>
                                                          <?php } ?>

                                                          <li class="list-group-item">
                                                              <span class="badge"><?=printPrice($me->getMachine()->getTimeBase())?> min.</span>
                                                              Grundzeit
                                                          </li>
                                                          <?php if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {?>
                                                              <li class="list-group-item">
                                                                  <span class="badge"><?php echo $calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange();?> min.</span>
                                                                  Einrichtzeit Druckplatten
                                                              </li>
                                                          <?php } ?>
                                                          <li class="list-group-item">
                                                              <span class="badge"><?=printPrice($me->getTime())?> min.</span>
                                                              Gesamtzeit
                                                          </li>

                                                          <li class="list-group-item">
                                                              <span class="badge"><?=printPrice($me->getPrice())?>€</span>
                                                              Preis
                                                          </li>
                                                      </ul>
                                                  </div>
                                              </div>
                                          <?php } ?>
                                      </div>
                                  </div>
                              </div>
                          <?php } ?>
                      <?php } ?>
              	  </div>
              </div>

              <div class="panel panel-success">
                  <div class="panel-heading">
                      <h3 class="panel-title">Rohbogen</h3>
                  </div>
                  <table class="table table-striped table-hover">
                      <thead>
                      <tr>
                          <th></th>
                          <th>Format</th>
                          <th>Anzahl</th>
                          <th>Nutzen</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php
                      foreach (Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID) as $me) {
                          if ($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                              $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET
                          ) {
                              switch ($me->getPart()) {
                                  case Calculation::PAPER_CONTENT:
                                      if ($calc->getFormat_in_content() != "") {
                                          $format_in = explode("x", $calc->getFormat_in_content());
                                          $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                                          $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth()));
                                          $roh2 = ceil(($calc->getPaperCount($me->getPart())+$calc->getPaperContentGrant()) / $roh);
                                          ?>
                                          <tr>
                                              <td>Inhalt 1</td>
                                              <td><?php echo $calc->getFormat_in_content(); ?> mm</td>
                                              <td><?php echo $roh2; ?> Bogen</td>
                                              <td><?php echo (int)$roh_schnitte; ?></td>
                                          </tr>
                                          <?php
                                      } else {
                                          ?>
                                          <tr>
                                              <td>Inhalt 1</td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                          </tr>
                                          <?php
                                      }
                                      break;
                                  case Calculation::PAPER_ADDCONTENT:
                                      if ($calc->getFormat_in_addcontent() != "") {
                                          $format_in = explode("x", $calc->getFormat_in_addcontent());
                                          $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth());
                                          $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth()));
                                          $roh2 = ceil(($calc->getPaperCount($me->getPart())+$calc->getPaperAddContentGrant()) / $roh);
                                          ?>
                                          <tr>
                                              <td>Inhalt 2</td>
                                              <td><?php echo $calc->getFormat_in_addcontent(); ?> mm</td>
                                              <td><?php echo $roh2; ?> Bogen</td>
                                              <td><?php echo (int)$roh_schnitte; ?></td>
                                          </tr>
                                          <?php
                                      } else {
                                          ?>
                                          <tr>
                                              <td>Inhalt 2</td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                          </tr>
                                          <?php
                                      }
                                      break;
                                  case Calculation::PAPER_ADDCONTENT2:
                                      if ($calc->getFormat_in_addcontent2() != "") {
                                          $format_in = explode("x", $calc->getFormat_in_addcontent2());
                                          $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width());
                                          $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width()));
                                          $roh2 = ceil(($calc->getPaperCount($me->getPart())+$calc->getPaperAddContent2Grant()) / $roh);
                                          ?>
                                          <tr>
                                              <td>Inhalt 3</td>
                                              <td><?php echo $calc->getFormat_in_addcontent2(); ?> mm</td>
                                              <td><?php echo $roh2; ?> Bogen</td>
                                              <td><?php echo (int)$roh_schnitte; ?></td>
                                          </tr>
                                          <?php
                                      } else {
                                          ?>
                                          <tr>
                                              <td>Inhalt 3</td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                          </tr>
                                          <?php
                                      }
                                      break;
                                  case Calculation::PAPER_ADDCONTENT3:
                                      if ($calc->getFormat_in_addcontent3() != "") {
                                          $format_in = explode("x", $calc->getFormat_in_addcontent3());
                                          $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width());
                                          $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width()));
                                          $roh2 = ceil(($calc->getPaperCount($me->getPart())+$calc->getPaperAddContent3Grant()) / $roh);
                                          ?>
                                          <tr>
                                              <td>Inhalt 4</td>
                                              <td><?php echo $calc->getFormat_in_addcontent3(); ?> mm</td>
                                              <td><?php echo $roh2; ?> Bogen</td>
                                              <td><?php echo (int)$roh_schnitte; ?></td>
                                          </tr>
                                          <?php
                                      } else {
                                          ?>
                                          <tr>
                                              <td>Inhalt 4</td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                          </tr>
                                          <?php
                                      }
                                      break;
                                  case Calculation::PAPER_ENVELOPE:
                                      if ($calc->getFormat_in_envelope() != "") {
                                          $format_in = explode("x", $calc->getFormat_in_envelope());
                                          $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth());
                                          $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth()));
                                          $roh2 = ceil(($calc->getPaperCount($me->getPart())+$calc->getPaperEnvelopeGrant()) / $roh);
                                          ?>
                                          <tr>
                                              <td>Umschlag</td>
                                              <td><?php echo $calc->getFormat_in_envelope(); ?> mm</td>
                                              <td><?php echo $roh2; ?> Bogen</td>
                                              <td><?php echo (int)$roh_schnitte; ?></td>
                                          </tr>
                                          <?php
                                      } else {
                                          ?>
                                          <tr>
                                              <td>Umschlag</td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                          </tr>
                                          <?php
                                      }
                                      break;
                              }
                          }
                      }
                      ?>
                      </tbody>
                  </table>
              </div>

              <div class="panel panel-success">
                  <div class="panel-heading">
                      <h3 class="panel-title">Druckbogen / Detaillierte Informationen</h3>
                  </div>
                  <div class="panel-body">
                      <div class="row">
                          <?php
                          foreach ($contents as $content) {
                              if ($calc->$content['id']()->getId()>0) {
                                  ?>
                                  <div class="col-md-3">
                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                              <h3 class="panel-title"><?php echo $content['name'];?></h3>
                                          </div>
                                          <ul class="list-group">
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->$content['id']()->getName();?></span>
                                                  Material
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->$content['weight']();?> g</span>
                                                  Grammatur
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->$content['chr']()->getName();?></span>
                                                  Farbigkeit
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?= $calc->$content['width']() ?> mm x <?= $calc->$content['height']() ?> mm</span>
                                                  Bogenformat
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?= $calc->getProductFormatWidth() ?> mm x <?= $calc->getProductFormatHeight() ?> mm</span>
                                                  Produktformat
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?= $calc->getProductFormatWidthOpen() ?> mm x <?= $calc->getProductFormatHeightOpen() ?> mm</span>
                                                  Produktformat offen
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?= $calc->getProductsPerPaper($content['const']) ?></span>
                                                  Nutzen pro Bogen
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge">
                                                      <?php
                                                      if ($calc->$content['chr']()->getReversePrinting() == 0 AND $calc->$content['chr']()->getColorsBack() == 0) {
                                                          echo $calc->getPaperCount($content['const']) + $calc->$content['grant']();
                                                      } else {
                                                          echo 2 * ($calc->getPaperCount($content['const']) + $calc->$content['grant']());
                                                      } ?>
                                                  </span>
                                                  Druckleistung insgesamt
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printBigInt($calc->$content['grant']());?></span>
                                                  Zuschuss
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printBigInt($calc->getPaperCount($content['const']) + $calc->$content['grant']());?></span>
                                                  Druckbogen insgesamt
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printPrice(($calc->$content['width']() * $calc->$content['height']() * $calc->$content['weight']() / 10000 / 100) * $calc->getAmount() / 10000);?>kg</span>
                                                  Papiergewicht
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printPrice((($calc->getProductFormatWidth() * $calc->getProductFormatHeight()) * $calc->$content['weight']() / 100000) / 1000);?>kg</span>
                                                  Papiergewicht pro Stück
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printPrice(($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getPaperCount($content['const'])) * (1.4 * 0.5 / 1000));?></span>
                                                  kg pro Farbton
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printPrice((($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getPaperCount($content['const'])) * (1.4 * 0.5 / 1000) * ($calc->$content['chr']()->getColorsBack() + $calc->$content['chr']()->getColorsFront())));?>kg</span>
                                                  Farbe Gesamt
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printPrice(($calc->$content['chr']()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getPaperCount($content['const'])) * (1.4 * 0.5 / 1000))));?>€</span>
                                                  Farbe Kosten pro Farbe
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo printPrice(($calc->$content['chr']()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getPaperCount($content['const'])) * (1.4 * 0.5 / 1000) * ($calc->$content['chr']()->getColorsBack() + $calc->$content['chr']()->getColorsFront()))));?>€</span>
                                                  Farbe Kosten Gesamt
                                              </li>
                                          </ul>
                                      </div>
                                  </div>
                                  <?php
                              }
                          }
                          ?>
                      </div>
                  </div>
              </div>

              <div class="panel panel-success">
                  <div class="panel-heading">
                      <h3 class="panel-title">Papierpreise</h3>
                  </div>
                  <div class="panel-body">
                      <div class="row">
                          <?php
                          foreach ($contents as $content) {
                              if ($calc->$content['id']()->getId()>0) {
                                  ?>
                                  <div class="col-md-3">
                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                              <h3 class="panel-title"><?php echo $content['name'];?></h3>
                                          </div>
                                          <ul class="list-group">
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->$content['id']()->getName();?></span>
                                                  Material
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->$content['weight']();?>g</span>
                                                  Grammatur
                                              </li>
                                              <li class="list-group-item">
                                      <span class="badge">
                                          <?php
                                          if ($calc->getPaperContent()->getRolle() != 1){
                                              ?>

                                              <?=printPrice($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()))?>
                                              <?=$_USER->getClient()->getCurrency()?>
                                              <?php
                                          } else {
                                              ?>

                                              <?=printPrice($calc->getPaperContent()->getSumPrice(($calc->getPaperCount(Calculation::PAPER_CONTENT) * $calc->getPaperContentHeight())/1000))?>
                                              <?=$_USER->getClient()->getCurrency()?>
                                              <?php
                                          }
                                          ?></span>
                                                  Preis
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->$content['pages']();?></span>
                                                  Umfang
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->getProductFormat()->getName();?></span>
                                                  Format
                                              </li>
                                              <li class="list-group-item">
                                                  <span class="badge"><?php echo $calc->$content['chr']()->getName();?></span>
                                                  Farbigkeit
                                              </li>
                                              <li class="list-group-item">
                                      <span class="badge">
                                          <?
                                          if ($calc->$content['id']()->getPriceBase() != Paper::PRICE_PER_THOUSAND)
                                              echo $_LANG->get('Preis pro Rolle');
                                          else
                                              echo $_LANG->get('Preis pro 1000 Bogen');
                                          ?>
                                      </span>
                                                  Preisbasis
                                              </li>
                                          </ul>
                                      </div>
                                  </div>
                                  <?php
                              }
                          }
                          ?>
                      </div>
                  </div>
              </div>
              <?php
              if ($calc->getPaperContent()->getId()>0)
                  $sheets_color1 = ($calc->getChromaticitiesContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesContent()->getColorsBack() + $calc->getChromaticitiesContent()->getColorsFront())));
              if ($calc->getPaperAddContent()->getId()>0)
                  $sheets_color2 = ($calc->getChromaticitiesAddContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent()->getColorsBack() + $calc->getChromaticitiesAddContent()->getColorsFront())));
              if ($calc->getPaperAddContent2()->getId()>0)
                  $sheets_color3 = ($calc->getChromaticitiesAddContent2()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent2()->getColorsBack() + $calc->getChromaticitiesAddContent2()->getColorsFront())));
              if ($calc->getPaperAddContent3()->getId()>0)
                  $sheets_color4 = ($calc->getChromaticitiesAddContent3()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesAddContent3()->getColorsBack() + $calc->getChromaticitiesAddContent3()->getColorsFront())));
              if ($calc->getPaperEnvelope()->getId()>0)
                  $sheets_envelope = ($calc->getChromaticitiesEnvelope()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight() / 1000000) * ($calc->getAmount()) * (1.4 * 0.5 / 1000) * ($calc->getChromaticitiesEnvelope()->getColorsBack() + $calc->getChromaticitiesEnvelope()->getColorsFront())));
              ?>
              <div class="panel panel-success">
                  <div class="panel-heading">
                      <h3 class="panel-title">Kosten / Ertragsaufstellung</h3>
                  </div>
                  <ul class="list-group">

                      <li class="list-group-item">
                          <span class="badge"><?php echo printPrice(($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()) + $calc->getPaperAddContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant()) + $calc->getPaperAddContent2()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant()) + $calc->getPaperAddContent3()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant()) + $calc->getPaperEnvelope()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant()))  + (($sheets_color1) + ($sheets_color2) + ($sheets_color3) + ($sheets_color4) + ($sheets_envelope))) ; ?>€</span>
                          Materialkosten
                      </li>
                      <li class="list-group-item">
                          <span class="badge"><?php echo printPrice($calc->getPricesub() - ($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()) + $calc->getPaperAddContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant()) + $calc->getPaperAddContent2()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant()) + $calc->getPaperAddContent3()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant()) + $calc->getPaperEnvelope()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant()))); ?>€</span>
                          Fertigungskosten
                      </li>


                      <li class="list-group-item">
                          <span class="badge"><?php echo printPrice($calc->getPricesub() + ($sheets_color1) + ($sheets_color2) + ($sheets_color3) + ($sheets_color4) + ($sheets_envelope)); ?>€</span>
                          Produktionskosten insgesamt
                      </li>
                      <li class="list-group-item">
                          Vertriebskonditionen
                      </li>
                      <li class="list-group-item">
                          <span class="badge"><?php echo printPrice($calc->getMargin()); ?>%</span>
                          Marge
                      </li>
                      <li class="list-group-item">
                          <span class="badge"><?php echo printPrice($calc->getDiscount()); ?>€</span>
                          Rabatt
                      </li>
                      <li class="list-group-item">
                          <span class="badge"><?php echo printPrice($calc->getAddCharge()); ?>€</span>
                          Manueller Aufschlag
                      </li>
                      <li class="list-group-item">
                          <span class="badge"><?php echo printPrice($calc->getPricetotal() + ($sheets_color1) + ($sheets_color2) + ($sheets_color3) + ($sheets_color4) + ($sheets_envelope)); ?>€</span>
                          Verkaufspreispreis
                      </li>
                  </ul>
              </div>

    	  </div>
    </div>
<?php } ?>


<script>
    $(document).on('click', '.panel-heading span.clickable', function(){
        var $this = $(this);
        if(!$this.hasClass('panel-collapsed')) {
            $this.parents('.panel').find('.slidepanel').slideUp();
            $this.addClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            $this.parents('.panel').find('.slidepanel').slideDown();
            $this.removeClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    })
</script>
