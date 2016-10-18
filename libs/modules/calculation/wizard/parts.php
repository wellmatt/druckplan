<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


?>

<input type="hidden" name="part_<?php echo $content_part;?>_product_format" id="part_<?php echo $content_part;?>_product_format">
<input type="hidden" name="part_<?php echo $content_part;?>_product_paper" id="part_<?php echo $content_part;?>_product_paper">
<input type="hidden" name="part_<?php echo $content_part;?>_product_paperweight" id="part_<?php echo $content_part;?>_product_paperweight">
<input type="hidden" name="part_<?php echo $content_part;?>_product_pages" id="part_<?php echo $content_part;?>_product_pages">
<input type="hidden" name="part_<?php echo $content_part;?>_product_chromaticity" id="part_<?php echo $content_part;?>_product_chromaticity">


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Inhalt <?php echo $content_part;?>
        </h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
        	  <div class="panel-heading">
        			<h3 class="panel-title">Endformat</h3>
        	  </div>
        	  <div class="panel-body">
                  <div id="part_<?php echo $content_part;?>_formats" class="formats"></div>
                  <hr>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="" class="col-sm-4 control-label">Breite</label>
                              <div class="col-sm-6">
                                  <div class="input-group">
                                      <input name="part_<?php echo $content_part;?>_product_width" class="form-control" id="part_<?php echo $content_part;?>_product_width" value="">
                                      <span class="input-group-addon">mm</span>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="" class="col-sm-4 control-label">Breite (offenes Format)</label>
                              <div class="col-sm-6">
                                  <div class="input-group">
                                      <input name="part_<?php echo $content_part;?>_product_width_open" class="form-control" id="part_<?php echo $content_part;?>_product_width_open" value="">
                                      <span class="input-group-addon">mm</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="" class="col-sm-4 control-label">Höhe</label>
                              <div class="col-sm-6">
                                  <div class="input-group">
                                      <input name="part_<?php echo $content_part;?>_product_height" class="form-control" id="part_<?php echo $content_part;?>_product_height" value="">
                                      <span class="input-group-addon">mm</span>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="" class="col-sm-4 control-label">Höhe (offenes Format)</label>
                              <div class="col-sm-6">
                                  <div class="input-group">
                                      <input name="part_<?php echo $content_part;?>_product_height_open" class="form-control" id="part_<?php echo $content_part;?>_product_height_open" value="">
                                      <span class="input-group-addon">mm</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-12 form-text" style="text-align: center">
                          <div onclick="SwapFormat(<?php echo $content_part;?>);" class="pointer">
                              <span title="Werte tauschen" class="glyphicons glyphicons-embed"></span>&nbsp;Werte tauschen
                          </div>
                      </div>
                  </div>
        	  </div>
        </div>

        <div class="panel panel-default">
        	  <div class="panel-heading">
        			<h3 class="panel-title">Material</h3>
        	  </div>
        	  <div class="panel-body">
                  <div id="part_<?php echo $content_part;?>_papers" class="papers"></div>
        	  </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Gewicht</h3>
            </div>
            <div class="panel-body">
                <div id="part_<?php echo $content_part;?>_weights" class="weights"></div>
            </div>
        </div>

        <div class="panel panel-default">
        	  <div class="panel-heading">
        			<h3 class="panel-title">Seiten</h3>
        	  </div>
        	  <div class="panel-body">
                  <div id="part_<?php echo $content_part;?>_pages" class="pages"></div>
                  <hr>
                  <div class="row" id="folddiv_<?php echo $content_part;?>" style="display: none">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="" class="col-sm-4 control-label">Falzung</label>
                              <div class="col-sm-6">
                                  <select name="part_<?php echo $content_part;?>_product_foldtype" id="part_<?php echo $content_part;?>_product_foldtype" class="form-control" onchange="select_foldtype(this,<?php echo $content_part;?>);">
                                      <option value="0" data-width="1" data-height="1" selected>Keine</option>
                                      <?php
                                      foreach ($foldtypes as $foldtype) {
                                          echo '<option style="display:none;" value="' . $foldtype->getId() . '" data-width="'.$foldtype->getHorizontal().'" data-height="'.$foldtype->getVertical().'">' . $foldtype->getName() . '</option>';
                                      }
                                      ?>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
        	  </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Farbigkeit</h3>
            </div>
            <div class="panel-body">
                <div id="part_<?php echo $content_part;?>_chromas" class="chromas"></div>
            </div>
        </div>
    </div>
</div>