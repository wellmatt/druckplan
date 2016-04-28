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
<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
		<a href="#top" class="menu_item">Seitenanfang</a>
		<a href="#" class="menu_item" onclick="$('#countries_form').submit();">Speichern</a>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
			Länderverwaltung
		</h3>
	</div>
	<div class="panel-body">

		<form action="index.php?page=<?=$_REQUEST['page']?>" id="countries_form" method="post">

			<input type="hidden" name="exec" value="save">
			<div class="table-responsive">
				<table class="table table-hover">
					<colgroup>
						<col width="180">
						<col width="80">
						<col width="20">
						<col width="80">
						<col>
					</colgroup>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Name')?></td>
						<td class="content_row_header"><?=$_LANG->get('L&auml;nderk&uuml;rzel')?></td>
						<td class="content_row_header">&ensp;</td>
						<td class="content_row_header"><?=$_LANG->get('Aktivierung')?></td>
						<td class="content_row_header">&ensp;</td>
					</tr>
					<? $x = 0;
					foreach($all_country as $country){
						?>
						<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
							<td class="content_row pointer">
								<?=$country->getName()?>&ensp;
							</td>
							<td class="content_row pointer" align="center">
								<?=$country->getCode()?>&ensp;
							</td>
							<td class="content_row pointer" >&ensp;</td>
							<td class="content_row pointer" align="center">
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
</div>

<br/>
<?// Speicher & Navigations-Button ?>
<div class="table-responsive">
	<table class="table table-hover">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
		<tr>
			<td class="content_row_clear" align="right">
				<input type="submit" value="<?=$_LANG->get('Speichern')?>">
			</td>
		</tr>
	</table>
</div>
