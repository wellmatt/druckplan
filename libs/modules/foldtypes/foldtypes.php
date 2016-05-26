<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'foldtype.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $ft = new Foldtype($_REQUEST["id"]);
    $savemsg = getSaveMessage($ft->delete());
} 

if($_REQUEST["exec"] == "copy" || $_REQUEST["exec"] == "edit")
{
    require_once 'foldtypes.edit.php';
} else
{
    $foldtypes = Foldtype::getAllFoldTypes(Foldtype::ORDER_NAME);
    
?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
                Falzarten
                <span class="pull-right">
                    <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';">
                        <span class="glyphicons glyphicons-sort"></span>
                            <?=$_LANG->get('Falzart hinzuf&uuml;gen')?>
                    </button>
                </span>
            </h3>
	  </div>
      <div class="table-responsive">
          <table class="table table-hover">
              <tr>
                  <td class="content_row_header"><?=$_LANG->get('ID')?></td>
                  <td class="content_row_header"><?=$_LANG->get('Bezeichnung')?></td>
                  <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
                  <td class="content_row_header"><?=$_LANG->get('Anz. vert.')?></td>
                  <td class="content_row_header"><?=$_LANG->get('Anz. hor.')?></td>
                  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
              </tr>
              <? $x = 0;
              foreach($foldtypes as $f)
              {?>
                  <tr class="<?=getRowColor($x)?>">
                      <td class="content_row"><?=$f->getId()?></td>
                      <td class="content_row"><?=$f->getName()?></td>
                      <td class="content_row"><?=$f->getDescription()?></td>
                      <td class="content_row" align="center"><?=$f->getVertical()?></td>
                      <td class="content_row" align="center"><?=$f->getHorizontal()?></td>
                      <td class="content_row">
                          <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$f->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
                          <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$f->getId()?>"><span class="glyphicons glyphicons-file"></span></a>
                          <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST["page"]?>&exec=delete&id=<?=$f->getId()?>')"><span class="glyphicons glyphicons-remove"></span></a>
                      </td>
                  </tr>

                  <? $x++; }
              ?>
          </table>
      </div>
</div>
<? } ?>