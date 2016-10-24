<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

if ($_REQUEST["exec"] == "ticket_db_cleanup" && $_REQUEST["ticket_db_cleanup_category"]){
    $ticketcategory = new TicketCategory((int)$_REQUEST["ticket_db_cleanup_category"]);
    $sql = ' WHERE `state` = 1 AND category = '.$ticketcategory->getId();
    $tickets = Ticket::getAllTickets($sql);
    foreach ($tickets as $ticket) {
        $ticket->deleteForced();
    }
}

?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">System Wartung</h3>
	  </div>
	  <div class="panel-body">
          Hinweis: Bitte beachten Sie das Sie vor dem Ausführen von Wartungsvorgängen ein <b><a href="index.php?page=libs/modules/backup/backup.admin.php">Backup</a></b> erstellen sollten!<br>&nbsp;
          <!-- TAB NAVIGATION -->
          <ul class="nav nav-tabs" role="tablist">
              <li class="active"><a href="#tickets" role="tab" data-toggle="tab">Tickets</a></li>
          </ul>
          <!-- TAB CONTENT -->
          <div class="tab-content">
              <div class="active tab-pane fade in" id="tickets">
                  <div class="panel panel-default">
                  	  <div class="panel-heading">
                  			<h3 class="panel-title">Tickets - Datenbank aufräumen</h3>
                  	  </div>
                  	  <div class="panel-body">
                          Hinweis: Mit dieser Funktion können Sie geschlöschte Tickets endgültig aus der Datenbank entfernen um die Leistung zu steigern<br>&nbsp;
                          <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="ticket_db_cleanup" id="ticket_db_cleanup" method="post"
                                class="form-horizontal" role="form">
                              <input type="hidden" name="exec" value="ticket_db_cleanup">
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Kategorie</label>
                                  <div class="col-sm-3">
                                      <select name="ticket_db_cleanup_category" id="ticket_db_cleanup_category" class="form-control">
                                          <?php
                                          $tkt_categories = TicketCategory::getAllCategories();
                                          foreach ($tkt_categories as $item) {
                                                  echo '<option value="' . $item->getId() . '">' . $item->getTitle() . '</option>';
                                          }
                                          ?>
                                      </select>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="col-sm-offset-2 col-sm-10">
                                      <button type="submit" class="btn btn-danger">Endgültig Löschen</button>
                                  </div>
                              </div>

                          </form>
                  	  </div>
                  </div>
              </div>
          </div>

      </div>
</div>
