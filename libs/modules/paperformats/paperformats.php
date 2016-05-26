<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.07.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'paperformat.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];
if($_REQUEST["exec"] == "delete")
{
    $format = new Paperformat($_REQUEST["id"]);
    $savemsg = getSaveMessage($format->delete());
}

if($_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "copy")
{
    require_once 'paperformats.edit.php';
} else
{
    $formats = Paperformat::getAllPaperFormats(Paperformat::ORDER_NAME);
    ?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				Produktformate
				<span class="pull-right">
					<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';" >
						<span class="glyphicons glyphicons-file-lock"></span>
							<?=$_LANG->get('Format hinzuf&uuml;gen')?>
					</button>
				</span>
			</h3>
	  </div>
	  <div class="table-responsive">
		  <table class="table table-hover">
			  <tr>
				  <td class="content_row_header">&nbsp;</td>
				  <td class="content_row_header"><?=$_LANG->get('Name')?></td>
				  <td class="content_row_header"><?=$_LANG->get('Breite')?></td>
				  <td class="content_row_header"><?=$_LANG->get('H&ouml;he')?></td>
				  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
			  </tr>

			  <? $x = 0;
			  foreach($formats as $f)
			  {?>
				  <tr class="<?=getRowColor($x)?>">
					  <td class="content_row">&nbsp;</td>
					  <td class="content_row"><?=$f->getName()?>&nbsp;</td>
					  <td class="content_row"><?=$f->getWidth()?> mm</td>
					  <td class="content_row"><?=$f->getHeight()?> mm</td>
					  <td class="content_row">
						  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$f->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
						  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$f->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
						  <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$f->getId()?>')"><span class="glyphicons glyphicons-remove"></span></a>
					  </td>
				  </tr>

				  <? $x++; }
			  ?>
		  </table>
	  </div>
</div>

<? }?>