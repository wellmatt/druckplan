<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/bulkLetter/bulkLetter.class.php';

if($_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "edit"){
	// Mahnstufe bearbeiten
	require_once 'libs/modules/bulkLetter/bulkLetter.edit.php';
} else {
	
	// Uebersicht ausgeben
	
	if ($_REQUEST["exec"] == "delete") {
		$del_bulkletter = new Bulkletter($_REQUEST["delid"]);
		$del_bulkletter->delete();
	}
	
	$all_bulkletter = Bulkletter::getAllBulkletter(Bulkletter::ORDER_CREATE_DESC);
	?>
<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					Serienbrief
					<span class="pull-right">
					  <button class="btn btn-xs btn-success"onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=new';">
						  <span class="glyphicons glyphicons-plus"></span>
						  <?=$_LANG->get('Serienbrief erstellen')?>
					  </button>
			  		</span>
				</h3>

		  </div>
	<div class="table-responsive">
		<table class="table table-hover">
			<tr>
				<td class="content_row_header"><?=$_LANG->get('ID')?></td>
				<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
				<td class="content_row_header"><?=$_LANG->get('Text')?></td>
                <td class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
                <td class="content_row_header" ><?=$_LANG->get('Ansprechpartner')?></td>
                <td class="content_row_header" ><?=$_LANG->get('Gesch&auml;ftskontakte')?></td>
                <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
				<td class="content_row_header"><?=$_LANG->get('Download')?></td>

			</tr>
			<? $x = 0;
			foreach($all_bulkletter AS $bulk){ ?>
				<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$bulk->getId()?>'">
						<?=$bulk->getId()?>&ensp;
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$bulk->getId()?>'">
						<?=$bulk->getTitle()?>
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$bulk->getId()?>'">
						<?=substr($bulk->getText(), 0, 250)?> <?if(strlen($bulk->getText()) > 250 ) echo "...";?>
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$bulk->getId()?>'">
						<?=date('d.m.Y',$bulk->getCrt_date())?>
					</td>
                    <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$bulk->getId()?>'">
                        <?=$bulk->getTitle()?>
                    </td>
                    <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$bulk->getId()?>'">
                        <?=$bulk->getTitle()?>
                    </td>
					<td class="content_row">
		                <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$bulk->getId()?>"><span class="glyphicons glyphicons-pencil" title="<?=$_LANG->get('Bearbeiten')?>"></span></a>
						&ensp;
		                <a href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delid=<?=$bulk->getId()?>')"
		                	><span style="color: red;" class="glyphicons glyphicons-remove" title="<?=$_LANG->get('Entfernen')?>"></span></a>
	            	</td>
	            	<td class="content_row" >
	            	
						<ul class="postnav_save_small" style="padding:0px">
							<a href="<?=$bulk->getPdfLink(Document::VERSION_EMAIL)?>"
								title="PDF mit Hintergrund"><?=$_LANG->get('E-Mail')?></a>
						</ul>
					
						<ul class="postnav_save_small" style="padding:0px">
							<a href="<?=$bulk->getPdfLink(Document::VERSION_PRINT)?>"
								title="PDF ohne Hintergrund"><?=$_LANG->get('Print')?></a>
						</ul>
					</td>
					
				</tr>
				<? $x++;
			}// Ende foreach($all_article)
			?>
		</table>
	</div>
</div>
<?} ?>