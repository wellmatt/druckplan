<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.10.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$all_country = Country::getEveryCountry();
$anz_country = count($all_country);
$savemsg = "";
$save = true;

if ($_REQUEST["exec"] == "save"){
	for ($i=1; $i <= $anz_country; $i++){
		$country = new Country($i);
		$country->setActive((int)$_REQUEST["county_active_".$i]);
		$ret = $country->save();
		if(!$ret){
			$save = false;
		}
	}
	$savemsg = getSaveMessage($save);
}
$all_country = Country::getEveryCountry();
?>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Speichern','#',"$('#countries_form').submit();",'glyphicon-floppy-disk');

echo $quickmove->generate();
// end of Quickmove generation ?>



<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Länderverwaltung
		</h3>
	</div>
		<form action="index.php?page=<?=$_REQUEST['page']?>" id="countries_form" method="post">
			<input type="hidden" name="exec" value="save">
			<div class="table-responsive">
				<table class="table table-hover">
					<tr>
						<td width="20%"><?=$_LANG->get('Name')?></td>
						<td width="20%"><?=$_LANG->get('L&auml;nderk&uuml;rzel')?></td>
						<td>&ensp;</td>
						<td><?=$_LANG->get('Aktivierung')?></td>
						<td>&ensp;</td>
					</tr>
					<? $x = 0;
					foreach($all_country as $country){
						?>
						<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
							<td class="content_row pointer">
								<?=$country->getName()?>&ensp;
							</td>
							<td class="content_row pointer">
								<?=$country->getCode()?>&ensp;
							</td>
							<td class="content_row pointer" >&ensp;</td>
							<td class="content_row pointer">
								<input 	id="county_active_<?=$country->getId()?>" name="county_active_<?=$country->getId()?>"
										  class="text" type="checkbox"
										  value="1" <?if ($country->getActive() == 1) echo "checked"; ?>>
							</td>
							<td class="content_row pointer" >&ensp;</td>
						</tr>
						<? $x++;
					}// Ende foreach($all_article)
					?>
				</table>
			</div>
		</form>
</div>

<br/>
<?// Speicher & Navigations-Button ?>
<div class="table-responsive">
	<table class="table table-hover">
		<tr>
			<td class="content_row_clear" align="right">
				<input type="submit" value="<?=$_LANG->get('Speichern')?>">
			</td>
		</tr>
	</table>
</div>
