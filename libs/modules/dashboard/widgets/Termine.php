<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/organizer/event.class.php';


$events = Event::getMyUpcomingEvents(10);

?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Bevorstehende Termine</h3>
	  </div>
	  <div class="panel-body">
          <ul class="products-list product-list-in-box">
              <?php foreach($events as $event){?>
                  <li class="item">
                      <div class="pointer product-info" style="margin-left: 5px;" onclick="callBoxFancyHomeEvent('libs/modules/organizer/calendar.newevent.php?eventid=<?php echo $event->getId();?>&returnhome=true');">
                          <div class="product-title">
                              <?php echo $event->getTitle(); ?>
                              <span class="label label-info pull-right" style="font-size: 10px; line-height: 1.42857;"><?php echo date('d.m.y H:i', $event->getBegin()); ?> - <?php echo date('d.m.y H:i', $event->getEnd()); ?></span>
                          </div>
                          <?php if (count($event->getParticipantsExt()>0)){?>
                          <div class="product-title">
                              <?php
                              foreach ($event->getParticipantsExt() as $item) {
                                  $cp = new ContactPerson($item);
                                  ?>
                                  <span class="badge label-success"><?php echo $cp->getBusinessContact()->getNameAsLine().' - '.$cp->getNameAsLine();?></span>
                                  <?php
                              }
                              ?>
                          </div>
                          <?php } ?>
                          <?php if (count($event->getParticipantsInt()>0)){?>
                              <div class="product-title">
                                  <?php
                                  foreach ($event->getParticipantsInt() as $item) {
                                      if ($item != $_USER->getId()) {
                                          $user = new User($item);
                                          ?>
                                          <span class="badge label-info"><?php echo $user->getNameAsLine(); ?></span>
                                          <?php
                                      }
                                  }
                                  ?>
                              </div>
                          <?php } ?>
                          <span class="product-description">
                              <?php echo $event->getDesc(); ?>
                          </span>
                      </div>
                  </li>
                  <!-- /.item -->
              <?php } ?>
          </ul>
	  </div>
</div>



<div id="hidden_clicker" style="display:none">
    <a id="hiddenclicker_home_event" href="http://www.google.com" >Hidden Clicker</a>
</div>

<script>
    function callBoxFancyHomeEvent(my_href) {
        var j1 = document.getElementById("hiddenclicker_home_event");
        j1.href = my_href;
        $('#hiddenclicker_home_event').trigger('click');
    }
    $(document).ready(function() {
        $("a#hiddenclicker_home_event").fancybox({
            'type': 'iframe',
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'speedIn': 600,
            'speedOut': 200,
            'height': 1000,
            'width': 1000,
            'overlayShow': true,
            'helpers': {overlay: null, closeClick: true}
        });
    });
</script>