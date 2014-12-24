<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			14.11.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/timekeeping/timekeeper.class.php';

$moduleID 	= (int) $_REQUEST["tm_moduleid"];
$objectID 	= (int) $_REQUEST["tm_objectid"];
$timerID	= (int) $_REQUEST["tm_id"]; 

$tmp_timer = new Timekeeper($timerID);
$tmp_timer->setModule($moduleID);
$tmp_timer->setObjectID($objectID);

if ($timerID > 0){
	$_REQUEST["modus"] = "timer";
	// Wenn Artikel angehangen, muss der entspr. Modus aufgerufen werden
	if($tmp_timer->getArticleId() > 0 ){
		$_REQUEST["modus"] = "article";
	}
}

if($_REQUEST["exec"]=="save"){
	$tmp_timer->setUserID($_USER->getId());
	$tmp_timer->setComment(trim(addslashes($_REQUEST["timer_comment"])));
	$tmp_timer->setArticleId((int)$_REQUEST["timer_article_id"]);
	$tmp_timer->setArticleAmount((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["timer_article_amount"]))));
	
	// Startdatum
	$_REQUEST["start_date"] = explode(".", $_REQUEST["start_date"]);
	$time_start = (int)mktime((int)$_REQUEST["start_hour"], (int)$_REQUEST["start_min"], 0, 
										$_REQUEST["start_date"][1], $_REQUEST["start_date"][0], $_REQUEST["start_date"][2]);
	// Enddatum
	$_REQUEST["end_date"] = explode(".", $_REQUEST["end_date"]);
	$time_end = (int)mktime((int)$_REQUEST["end_hour"], (int)$_REQUEST["end_min"], 0, 
										$_REQUEST["end_date"][1], $_REQUEST["end_date"][0], $_REQUEST["end_date"][2]);
	
	// wenn Ende vor Anfang, dann Zeiten tauschen
	if ($time_start > $time_end){
		$t = $time_start;
		$time_start = $time_end;
		$time_end = $t;
	}
	
	$tmp_timer->setStartdate($time_start);
	$tmp_timer->setEnddate($time_end);
	$tmp_saver = $tmp_timer->save();

	if($tmp_saver==true){
		// Wenn gespeichert wurde, muss die Box geschlossen werden ?>
        <script language="javascript">
        	var parent_objid = parent.document.getElementById('timer_objectID').value;
        	var parent_modid = parent.document.getElementById('timer_moduleID').value;
        	parent.updateTimerTable();	<?/* Liste im aufrufenden Fenster aktualisieren */?>
            parent.$.fancybox.close(); 	<?/* FancyBox schliessen */?>
        </script>
        <?
	} else {
		echo $_LANG->get('Messung konnte nicht gespeichert werden. <br>').mysql_error();
	}
	// var_dump($tmp_timer);
} 

$all_article = Article::getAllWorkHourArticle(Article::ORDER_TITLE);
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<script language="javascript" src="../../../jscripts/basic.js"></script>
</head>
<body>
<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<!-- /jQuery -->

<?//------------------ Fuer Datumsfelder -------------------------------------------------------------------?>
<?	if($_REQUEST["modus"] == "timer"){ ?>
	<style type="text/css"><!-- @import url(libs/jscripts/datepicker/datepicker.css); //--></style>
	<script language="JavaScript" >
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
		$('#start_date').datepicker(
				{
					showOtherMonths: true,
					selectOtherMonths: true,
					dateFormat: 'dd.mm.yy',
	                showOn: "button",
	                buttonImage: "../../../images/icons/calendar-blue.png",
	                buttonImageOnly: true
				}
	     );
	});
	
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
		$('#end_date').datepicker(
				{
					showOtherMonths: true,
					selectOtherMonths: true,
					dateFormat: 'dd.mm.yy',
	                showOn: "button",
	                buttonImage: "../../../images/icons/calendar-blue.png",
	                buttonImageOnly: true
				}
	     );
	});
	</script>
<?}?>

<div class="box1">
<h1><?=$_LANG->get('Zeitmessung eintragen')?></h1>
<?	
$feedback = $_LANG->get('Zeitmessung f&uuml;r')." ".$tmp_timer->getModuleName();
$feedback .= " '<i>".$tmp_timer->getObjectName()."</i>' ".$_LANG->get('eintragen.');
echo $feedback;

// Daten zu Ausgabe vorbereiten
$tmp_start= time();
if ($tmp_timer->getStartdate() > 0){
	$tmp_start = $tmp_timer->getStartdate();
}
$tmp_end = time();
if ($tmp_timer->getEnddate() > 0){
	$tmp_end = $tmp_timer->getEnddate();
}

?>
<br /><br />  
<form enctype="multipart/form-data" action="timekeeper.newTimer.iframe.php" method="post"
		onSubmit="return checkform(new Array(this.start_date, this.end_date))">
	<input type="hidden" id="exec" name="exec" value="save" />
	<input type="hidden" id="tm_objectid" name="tm_objectid" value="<?=$objectID?>" />
	<input type="hidden" id="tm_moduleid" name="tm_moduleid" value="<?=$moduleID?>" />
	<input type="hidden" id="tm_id" name="tm_id" value="<?=$timerID?>" />
	
	<?	// Wenn kein Timer hinzugefuegt wird, muss die Zeit bzw. das Datum verdeckt gesetzt werden	
	if($_REQUEST["modus"] != "timer"){ ?>
		<input type="hidden" id="start_date" name="start_date" value="<?=date("d.m.Y", $tmp_start)?>" />
		<input type="hidden" id="start_hour" name="start_hour" value="<?=date("H", $tmp_start)?>" />
		<input type="hidden" id="start_min" name="start_min" value="<?=date("i", $tmp_start)?>" />
		<input type="hidden" id="end_date" name="end_date" value="<?=date("d.m.Y", $tmp_start)?>" />
		<input type="hidden" id="end_hour" name="end_hour" value="<?=date("H", $tmp_start)?>" />
		<input type="hidden" id="end_min" name="end_min" value="<?=date("i", $tmp_start)?>" />
	<? // Ende Zeit-Hidden-Felder
	} ?>
	<table width="100%">
	<?	if($_REQUEST["modus"] == "timer"){ ?>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Von');?>*</td>
				<td class="content_row_clear">
					<input type="text" style="width:80px" id="start_date" name="start_date"
							class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
							onfocus="markfield(this,0)" onblur="markfield(this,1)" 
							value="<?=date("d.m.Y", $tmp_start)?>" /> 
					&emsp;						
					<select id="start_hour" name="start_hour" class="text">
					<?	for ($i=0;$i<25;$i++){ ?>
						<option value="<?=$i?>"
								<?if(date("H", $tmp_start) == $i){echo 'selected="selected"';}?>
								><?=$i?></option>
					<?	} ?>
					</select>
					<b>:</b>
					<select  id="start_min" name="start_min" class="text">
					<?	for ($i=0;$i<60;$i++){ ?>
						<option value="<?=$i?>"
								<?if(date("i", $tmp_start) == $i){echo 'selected="selected"';}?>
								><?=$i?></option>
					<?	} ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Bis');?>*</td>
				<td class="content_row_clear">
					<input type="text" style="width:80px" id="end_date" name="end_date"
							class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
							onfocus="markfield(this,0)" onblur="markfield(this,1)" 
							value="<?=date("d.m.Y", $tmp_end)?>" /> 
					&emsp;
					<select id="end_hour" name="end_hour" class="text">
					<?	for ($i=0;$i<25;$i++){ ?>
						<option value="<?=$i?>"
								<?if(date("H", $tmp_end) == $i){echo 'selected="selected"';}?>
								><?=$i?></option>
					<?	} ?>
					</select>
					<b>:</b>
					<select  id="end_min" name="end_min" class="text">
					<?	for ($i=0;$i<60;$i++){ ?>
						<option value="<?=$i?>"
								<?if(date("i", $tmp_end) == $i){echo 'selected="selected"';}?>
								><?=$i?></option>
					<?	} ?>
					</select>
				</td>
			</tr>
	<?	} ?>
		<tr>
			<td class="content_row_header" valign="top"><?=$_LANG->get('Grund / Bemerkung');?></td>
			<td  class="content_row_clear">
				<textarea id="timer_comment" name="timer_comment" style="width: 350px; height: 70px;"
					><?=$tmp_timer->getComment()?></textarea>
			</td>
		</tr>
		<? // Verknuepfte Artikel nur bei passenden Modus	
		if($_REQUEST["modus"] == "article"){ ?>
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Verkn. Artikel');?></td>
				<td  class="content_row_clear">
					<select class="text" name="timer_article_id" style="width:270px">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($all_article as $ar){?>
								<option value="<?=$ar->getId()?>"
									<?if ($tmp_timer->getArticleId() == $ar->getId()) echo 'selected="selected"'; ?>
									><?= $ar->getTitle()?></option>
						<?	} //Ende ?>
					</select>
					&ensp;
					<input type="text" class="text" name="timer_article_amount" value="<?=printPrice($tmp_timer->getArticleAmount())?>"
							style="width:40px" /> <?=$_LANG->get('Stk.');?>
				</td>
			</tr>
		<? // Ende verkn. Artikel
		}?>
		<tr>
			<td> &emsp;	</td>
			<td> &emsp;	</td>
		</tr>
		<tr>
		<td width="150px">&ensp;</td>
		<td align="right">
			<input type="submit" value="<?=$_LANG->get('Speichern')?>">
		</td>
		</tr>
	
	</table>
</form>
</div>
</body>
</html>