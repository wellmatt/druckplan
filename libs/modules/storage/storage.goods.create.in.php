<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

$tmp_positions = SupOrderPosition::getAllForSupOrder($origin);
$positions = [];
foreach ($tmp_positions as $position) {
    if ($position->getArticle()->getUsesstorage())
        $positions[] = $position;
}
?>

<?php foreach ($positions as $position) {
    $storages = StorageArea::getStoragesPrioArticle($position->getArticle());
    ?>

    <div class="panel panel-default">
    	  <div class="panel-heading" id="heading_<?php echo $position->getId();?>" style="background-color: #f2dede;">
    			<h3 class="panel-title">
                    <?php echo $position->getArticle()->getTitle();?>
                    <span class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
                </h3>
    	  </div>
    	  <div class="panel-body slidepanel">
              <div class="row">
                  <div class="col-md-6">
                      <b>Artikelname:</b> <?php echo $position->getArticle()->getTitle();?>
                  </div>
                  <div class="col-md-6">
                      <b>Artikelnummer:</b> <?php echo $position->getArticle()->getNumber();?>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-6">
                      <b>Eingegangen:</b> <span id="book_amount_<?php echo $position->getId();?>"><?php echo $position->getAmount();?></span>
                  </div>
                  <div class="col-md-6">
                      <b>zu Buchen:</b> <span class="" id="tobook_<?php echo $position->getId();?>"><?php echo $position->getAmount();?></span>
                  </div>
              </div>
              <br>
              <?php if (count($storages['prio'])>0){?>
              <div class="row">
                  <div class="col-md-12">
                      <div class="panel panel-default">
                          <div class="panel-heading">
                              <h3 class="panel-title">Artikellager mit freier Belegung</h3>
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
                                              <?php echo $this_position->getAllocation();?>%
                                          </div>
                                          <div class="col-md-3">
                                              <input type="text" name="" id="" data-bookid="<?php echo $position->getId();?>" class="form-control bookinput tobook_<?php echo $position->getId();?>" placeholder="X auf dieses Lager buchen">
                                          </div>
                                          <div class="col-md-3">
                                              <div class="input-group">
                                                  <input type="number" min="1" max="<?php echo 100-$storage['alloc'];?>" class="form-control numberBox">
                                                  <span class="input-group-addon">%</span>
                                              </div>
                                          </div>
                                      </div>
                                      <?php
                                  }?>
                          </div>
                      </div>
                  </div>
              </div>
              <?php }
              if (count($storages['other'])>0){?>
              <div class="row">
                  <div class="col-md-12">
                      <div class="panel panel-default">
                          <div class="panel-heading">
                              <h3 class="panel-title">Lager mit freier Belegung</h3>
                          </div>
                          <div class="panel-body">
                                  <div class="row">
                                      <div class="col-md-2"><b>Lager-Name</b></div>
                                      <div class="col-md-2"><b>Ges.-Belegung</b></div>
                                      <div class="col-md-2"><b>&nbsp;</b></div>
                                      <div class="col-md-3"><b>Buchung</b></div>
                                      <div class="col-md-3"><b>neue Belegung</b></div>
                                  </div>
                                  <?php foreach ($storages['other'] as $storage) {
                                      $this_storage = new StorageArea($storage['id']);?>
                                      <div class="row">
                                          <div class="col-md-2">
                                              <?php echo $this_storage->getName();?>
                                          </div>
                                          <div class="col-md-2">
                                              <?php echo $storage['alloc'];?>%
                                          </div>
                                          <div class="col-md-2">
                                              &nbsp;
                                          </div>
                                          <div class="col-md-3">
                                              <input type="text" name="" id="" data-bookid="<?php echo $position->getId();?>" class="form-control bookinput tobook_<?php echo $position->getId();?>" placeholder="X auf dieses Lager buchen">
                                          </div>
                                          <div class="col-md-3">
                                              <div class="input-group">
                                                  <input type="number" min="1" max="<?php echo 100-$storage['alloc'];?>" class="form-control numberBox">
                                                  <span class="input-group-addon">%</span>
                                              </div>
                                          </div>
                                      </div>
                                      <?php
                                  }?>
                          </div>
                      </div>
                  </div>
              </div>
              <?php } ?>
    	  </div>
    </div>
<?php } ?>

<script>
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
        var amount = $('#book_amount_'+id).text();
        var now = 0;
        var inputs = $('.tobook_'+id);

        if (amount-$(this).val()<0){
            $(this).val(0);
            alert('Anzahl Buchungen > Eingegangene Anzahl!');
        }

        inputs.each(function( index ) {
            now = now + $(this).val();
        });
        var result = amount - now;
        $('#tobook_'+id).text(result);
        if (result == 0)
            $('#heading_'+id).css('background-color','#dff0d8');
        else
            $('#heading_'+id).css('background-color','#f2dede');
    })
    $(document).on('click', '.panel-heading span.clickable', function(e){
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
