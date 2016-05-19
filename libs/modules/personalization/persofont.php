<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			26.02.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/personalization/persofont.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

if($_REQUEST["exec"] == "delete"){
	$del_font = new PersoFont((int)$_REQUEST["delid"]);
	$del_filename = PersoFont::FILE_DESTINATION.$del_font->getFilename();
	unlink($del_filename."ctg.z");
	unlink($del_filename.".z");
	unlink($del_filename.".php");
	$del_font->delete();
}

if($_REQUEST["exec"] == "edit"){
	require_once 'persofont.edit.php';	
} else {
	
$all_fonts = PersoFont::getAllPersoFonts(PersoFont::ORDER_TITLE);
?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				Schriftarten
				<span class="pull-right">
					<img src="images/icons/edit.png">
					<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';" >
						<?=$_LANG->get('Schrfitart hinzuf&uuml;gen')?>
					</button>
				</span>
			</h3>
	  </div>
		  <div class="table-responsive">
			  <table class="table table-hover">
				  <tr>
					  <td class="content_row_header" align="center"><?=$_LANG->get('ID')?></td>
					  <td class="content_row_header"><?=$_LANG->get('Titel')?></td>
					  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
				  </tr>
				  <?	$x = 0;
				  foreach($all_fonts as $font){?>
					  <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
						  <td class="content_row" align="center" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&fid=<?=$font->getId()?>'">
							  <?=$font->getId()?>
						  </td>
						  <td class="content_row" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&fid=<?=$font->getId()?>'">
							  <?=$font->getTitle();?>
						  </td>
						  <td class="content_row">
							  <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&fid=<?=$font->getId()?>" class="icon-link"
							  ><img src="images/icons/pencil.png" title="<?=$_LANG->get('Bearbeiten')?>"></a>
							  &ensp;
							  <a href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delid=<?=$font->getId()?>')" class="icon-link"
							  ><img src="images/icons/cross-script.png" title="<?=$_LANG->get('L&ouml;schen')?>"></a>
						  </td>
					  </tr>
				  <?	} ?>
			  </table>
		  </div>
</div>
<?
} 
?>