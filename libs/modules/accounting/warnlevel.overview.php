<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/accounting/warnlevel.class.php';

if($_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "edit"){
	// Mahnstufe bearbeiten
	require_once 'libs/modules/accounting/warnlevel.edit.php';
} else {
	
	// Uebersicht ausgeben
	
	if ($_REQUEST["exec"] == "delete") {
		$del_warnlevel = new Warnlevel($_REQUEST["delid"]);
		$del_warnlevel->delete();
	}
	
	$all_warnlevel = Warnlevel::getAllWarnlevel();
	?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Mahnstufen
				<span class="pull-right">
				<button class="btn btn-xs btn-success"
						onclick="document.location. href='index.php?page=<?= $_REQUEST['page'] ?>&exec=new';">
					<span class="glyphicons glyphicons-plus"></span>
					<?= $_LANG->get('Mahnstufe hinzuf&uuml;gen') ?>
				</button>
			</span>
		</h3>
	</div>
	<div class="table-responsive">
		<table class="table table-hover">
			<tr>
				<td class="content_row_header"><?= $_LANG->get('ID') ?></td>
				<td class="content_row_header"><?= $_LANG->get('Titel') ?></td>
				<td class="content_row_header"><?= $_LANG->get('Text') ?></td>
				<td class="content_row_header"><?= $_LANG->get('Optionen') ?></td>
			</tr>
			<? $x = 0;
			foreach ($all_warnlevel as $warn) { ?>
				<tr class="<?= getRowColor($x) ?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
					<td class="content_row pointer"
						onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&wid=<?= $warn->getId() ?>'">
						<?= $warn->getId() ?>&ensp;
					</td>
					<td class="content_row pointer"
						onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&wid=<?= $warn->getId() ?>'">
						<?= $warn->getTitle() ?>
					</td>
					<td class="content_row pointer"
						onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&wid=<?= $warn->getId() ?>'">
						<?= substr($warn->getText(), 0, 250) ?><? if (strlen($warn->getText()) > 250) echo "..."; ?>
					</td>
					<td class="content_row">
						<a class="icon-link"
						   href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&wid=<?= $warn->getId() ?>"><span
								class="glyphicons glyphicons-pencil pointer"
								title="<?= $_LANG->get('Bearbeiten') ?>"></span> </a>
						&ensp;
						<a class="icon-link" href="#"
						   onclick="askDel('index.php?page=<?= $_REQUEST['page'] ?>&exec=delete&delid=<?= $warn->getId() ?>')">
							<span style="color: red;" class="glyphicons glyphicons-remove pointer " title="<?= $_LANG->get('L&ouml;schen') ?>"></span> </a>
					</td>
				</tr>
				<? $x++;
			}// Ende foreach($all_article)
			?>
		</table>
	</div>
</div>
<? } ?>