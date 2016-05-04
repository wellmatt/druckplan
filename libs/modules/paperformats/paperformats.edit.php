<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.07.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$format = new Paperformat($_REQUEST["id"]);
if($_REQUEST["exec"] == "copy")
{
    $format->clearID();
}
if($_REQUEST["subexec"] == "save")
{
    $format->setName(trim(addslashes($_REQUEST["format_name"])));
    $format->setWidth((int)$_REQUEST["format_width"]);
    $format->setHeight((int)$_REQUEST["format_height"]);
    $savemsg = getSaveMessage($format->save()).$DB->getLastError();
}
?>

<div id="fl_menu">
    <div class="label">Quick Move</div>
    <div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#paper_form').submit();">Speichern</a>
    </div>
</div>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                <img  src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
                <?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Produktformat hinzuf&uuml;gen')?>
                <?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Produktformat &auml;ndern')?>
                <?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Produktformat kopieren')?>
                <span class="pull-right"><?=$savemsg?></span>
                </td>
            </h3>
	  </div>
	  <div class="panel-body">
          <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="paper_form" name="paper_form"
                class="form-horizontal" role="form"onSubmit="return checkform(new Array(this.format_name))">
              <input name="exec" value="edit" type="hidden">
              <input type="hidden" name="subexec" value="save">
              <input name="id" value="<?=$format->getId()?>" type="hidden">

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Name</label>
                  <div class="col-sm-10">
                      <input id="format_name" name="format_name" type="text" class="form-control" value="<?=$format->getName()?>">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Breite</label>
                  <div class="col-sm-10">
                      <div class="input-group">
                          <input type="text" class="form-control" id="format_width" name="format_width" value="<?=$format->getWidth()?>">
                          <span class="input-group-addon">mm</span>
                      </div>
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Höhe</label>
                  <div class="col-sm-10">
                      <div class="input-group">
                          <input type="text" class="form-control" id="format_height" name="format_height" value="<?=$format->getHeight()?>">
                          <span class="input-group-addon">mm</span>
                      </div>
                  </div>
              </div>
          </form>
	  </div>
</div>



