<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/links/link.class.php';

$link = new Link((int)$_REQUEST['id']);

if ($_REQUEST["exec"] == "save"){
    $array = [
        'title' => $_REQUEST["title"],
        'url' => $_REQUEST["url"],
        'private' => (int)$_REQUEST["private"],
        'username' => $_REQUEST["username"],
        'password' => $_REQUEST["password"],
        'user' => (int)$_USER->getId(),
    ];
    $link = new Link((int)$_REQUEST["id"], $array);
    $link->save();
    $_REQUEST["id"] = $link->getId();
} elseif ($_REQUEST["exec"] == "delete"){
    $link->delete();
    header("location: index.php");
}

?>


<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Speichern','#',"$('#linkform').submit();",'glyphicon-floppy-disk');
if ($_USER->isAdmin() && $link->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$link->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Link</h3>
	  </div>
	  <div class="panel-body">
          <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="linkform" id="linkform" method="post"
                class="form-horizontal" role="form">
              <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
              <input type="hidden" name="exec" value="save">
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Titel</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="title" id="title" value="<?php echo $link->getTitle();?>" placeholder="Titel für Link eingeben...">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">URL</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="url" id="url" value="<?php echo $link->getUrl();?>" placeholder="http://...">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Benutzer</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="username" id="username" value="<?php echo $link->getUsername();?>">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Kennwort</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="password" id="password" value="<?php echo $link->getPassword();?>">
                  </div>
              </div>
              <?php if ($_USER->isAdmin()){?>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Privat</label>
                  <div class="col-sm-10">
                      <div class="checkbox">
                          <label>
                              <input type="checkbox" name="private" id="private" value="1" <?php if ($link->getPrivate() == 1) echo ' checked ';?>>
                          </label>
                      </div>
                  </div>
              </div>
              <?php } else { ?>
                  <input type="hidden" name="private" value="1">
              <?php } ?>
              ** Bitte beachten: Manche Webseiten blockieren das Einbinden via Frame und können nur in neuem Fenster geöffnet werden (z.B. google.de)!
          </form>
	  </div>
</div>
