<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

$positions = Orderposition::getAllOrderposition($origin->getId());
?>

<?php foreach ($positions as $position) {
    if ($position->getType() == 1 || $position->getType() == 2){
        $article = new Article($position->getObjectid());
        if ($article->getUsesstorage()){
            $storages = StorageArea::getStoragesPrioArticle($article);
            $bookamount = StorageBookEnrty::calcutateToBookAmount($position);
            if ($bookamount>0){?>

                <div class="panel artpanel panel-default">
                      <div class="panel-heading" id="heading_<?php echo $position->getId();?>" style="background-color: #f2dede;">
                            <h3 class="panel-title">
                                <?php echo $article->getTitle();?>
                                <span class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
                            </h3>
                      </div>
                      <div class="panel-body slidepanel">
                          <div class="row">
                              <div class="col-md-6">
                                  <b>Artikelname:</b> <?php echo $article->getTitle();?>
                              </div>
                              <div class="col-md-6">
                                  <b>Artikelnummer:</b> <?php echo $article->getNumber();?>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6">
                                  <b>Ausgehend:</b> <span id="book_amount_<?php echo $position->getId();?>"><?php echo $position->getAmount();?></span>
                              </div>
                              <div class="col-md-6">
                                  <b>zu Buchen:</b> <span class="" id="tobook_<?php echo $position->getId();?>"><?php echo StorageBookEnrty::calcutateToBookAmount($position);?></span>
                              </div>
                          </div>
                          <br>
                          <?php if (count($storages['prio'])>0){?>
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="panel panel-default">
                                      <div class="panel-heading">
                                          <h3 class="panel-title">Bestandslager mit Belegung</h3>
                                      </div>
                                      <div class="panel-body">
                                              <div class="row">
                                                  <div class="col-md-2"><b>Lager-Name</b></div>
                                                  <div class="col-md-2"><b>Ges.-Belegung</b></div>
                                                  <div class="col-md-2"><b>Belegung d. Art.</b></div>
                                                  <div class="col-md-3"><b>Buchung</b></div>
                                                  <div class="col-md-3"><b>neue Belegung</b></div>
                                              </div>
                                              <?php foreach ($storages['prio'] as $storage) {
                                                  $this_storage = new StorageArea($storage['id']);
                                                  $this_position = new StoragePosition($storage['posid']);?>
                                                  <div class="row">
                                                      <div class="col-md-2">
                                                          <?php echo $this_storage->getName();?>
                                                      </div>
                                                      <div class="col-md-2">
                                                          <?php echo $storage['alloc'];?>%
                                                      </div>
                                                      <div class="col-md-2">
                                                          <?php echo $this_position->getAmount() . ' Stk. ( ' . $this_position->getAllocation();?>% )
                                                      </div>
                                                      <div class="col-md-3">
                                                          <input type="text" name="book[<? echo $position->getId();?>][<?php echo $this_storage->getId();?>][amount]" data-bookid="<?php echo $position->getId();?>" class="form-control bookinput tobook_<?php echo $position->getId();?>" placeholder="X auf dieses Lager buchen">
                                                      </div>
                                                      <div class="col-md-3">
                                                          <div class="input-group">
                                                              <div class="input-group-btn">
                                                                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&nbsp;<span class="caret"></span></button>
                                                                  <ul class="dropdown-menu">
                                                                      <li><a href="#" onclick="setAlloc(<?php echo $position->getId();?>,<?php echo $this_storage->getId();?>,0);">0%</a></li>
                                                                      <li><a href="#" onclick="setAlloc(<?php echo $position->getId();?>,<?php echo $this_storage->getId();?>,25);">25%</a></li>
                                                                      <li><a href="#" onclick="setAlloc(<?php echo $position->getId();?>,<?php echo $this_storage->getId();?>,50);">50%</a></li>
                                                                      <li><a href="#" onclick="setAlloc(<?php echo $position->getId();?>,<?php echo $this_storage->getId();?>,75);">75%</a></li>
                                                                      <li><a href="#" onclick="setAlloc(<?php echo $position->getId();?>,<?php echo $this_storage->getId();?>,100);">100%</a></li>
                                                                  </ul>
                                                              </div>
                                                              <input type="number" min="0" max="<?php echo 100-$storage['alloc'];?>" name="book[<? echo $position->getId();?>][<?php echo $this_storage->getId();?>][alloc]" id="book_alloc_<?php echo $position->getId();?>_<?php echo $this_storage->getId();?>" class="form-control numberBox">
                                                              <span class="input-group-addon">%</span>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <br>
                                                  <div class="row">
                                                      <div class="col-md-2"><span style="font-weight: bold;">Ort: </span> <?=$this_storage->getLocation()?></div>
                                                      <div class="col-md-2"><span style="font-weight: bold;">Gang: </span> <?=$this_storage->getCorridor()?></div>
                                                      <div class="col-md-2"><span style="font-weight: bold;">Regal: </span> <?=$this_storage->getShelf()?></div>
                                                      <div class="col-md-2"><span style="font-weight: bold;">Reihe: </span> <?=$this_storage->getLine()?></div>
                                                      <div class="col-md-2"><span style="font-weight: bold;">Ebene: </span> <?=$this_storage->getLayer()?></div>
                                                      <div class="col-md-2">
                                                          <span style="font-weight: bold;">Priorit&auml;t</span>
                                                          <?php if ($this_storage->getPrio() == 0) echo 'Niedrig';?>
                                                          <?php if ($this_storage->getPrio() == 1) echo 'Mittel';?>
                                                          <?php if ($this_storage->getPrio() == 2) echo 'Hoch';?>
                                                      </div>
                                                  </div>
                                                  <hr>
                                                  <?php
                                              }?>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <?php } else {?>
                              <div class="panel panel-danger">
                                  <div class="panel-heading">
                                        <h3 class="panel-title">Artikel nicht auf Lager</h3>
                                  </div>
                              </div>
                          <?php } ?>
                      </div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
<?php } ?>

<script>
    function setAlloc(posid,stid,perc){
        var input = $('#book_alloc_'+posid+'_'+stid);
        if (perc <= parseInt(input.attr('max'))){
            input.val(perc);
        } else {
            input.val(input.attr('max'));
        }
    }
    $(function () {
        $( ".numberBox" ).change(function() {
            var max = parseInt($(this).attr('max'));
            var min = parseInt($(this).attr('min'));
            if ($(this).val() > max)
            {
                $(this).val(max);
            }
            else if ($(this).val() < min)
            {
                $(this).val(min);
            }
        });
    });
    $(document).on('change', '.bookinput', function(e){
        var id = $(this).attr('data-bookid');
        console.log('ID: '+id);
        var amount = $('#book_amount_'+id).text();
        console.log('amount: '+amount);
        var now = 0;
        var inputs = $('.tobook_'+id);

        if (amount - parseInt($(this).val()) < 0){
            $(this).val(0);
            alert('Anzahl Buchungen > Eingegangene Anzahl!');
        }

        inputs.each(function( index, ele ) {
            if (isNaN(parseInt($(ele).val())) == false){
                now = now + parseInt($(ele).val());
                console.log('now+ : '+ parseInt($(ele).val()));
            }
        });
        var result = amount - now;
        console.log('result: '+ result);
        $('#tobook_'+id).text(result);
        if (result == 0)
            $('#heading_'+id).css('background-color','#dff0d8');
        else
            $('#heading_'+id).css('background-color','#f2dede');
    })
    $(document).on('click', '.panel-heading span.clickable', function(e){
        var $this = $(this);
        if(!$this.hasClass('panel-collapsed')) {
            $this.parents('.artpanel').find('.slidepanel').slideUp();
            $this.addClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            $this.parents('.artpanel').find('.slidepanel').slideDown();
            $this.removeClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    })
</script>
