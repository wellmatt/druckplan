<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

$all_country = Country::getEveryCountry();
$anz_country = count($all_country);
$savemsg = "";
$save = true;

if ($_REQUEST["exec"] == "save"){
	for ($i=1; $i <= $anz_country; $i++){
		$country = new Country($i);
		$country->setEu((int)$_REQUEST["county_eu_".$i]);
		$country->setVat(tofloat($_REQUEST["county_vat_".$i]));
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
						<td>EU-Mitglied</td>
						<td>USt</td>
						<td><?=$_LANG->get('Aktivierung')?></td>
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
							<td class="content_row pointer">
								<input 	id="county_eu_<?=$country->getId()?>" name="county_eu_<?=$country->getId()?>"
										  class="text" type="checkbox"
										  value="1" <?if ($country->getEu() == 1) echo "checked"; ?>>
							</td>
							<td class="content_row pointer">
								<input id="county_vat_<?=$country->getId()?>" name="county_vat_<?=$country->getId()?>"
										  class="text" type="text"
										  value="<?php echo printPrice($country->getVat());?>">
							</td>
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