<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			29.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/timekeeping/timekeeper.class.php';

$timer_objectID = (int)$_REQUEST["objectID"];
$timer_moduleID = (int)$_REQUEST["moduleID"];
$timer_subObjectID = (int)$_REQUEST["subObjectID"];

/*
 * Zeitmessung starten und ID der Messung in die Session ablegen
 */
if ($_REQUEST["ajax_action"] == "startTimer"){	
	$timer = new Timekeeper();
	$timer->setModule($timer_moduleID);
	$timer->setObjectID($timer_objectID);
	$timer->setUserID($_USER->getId());
	$timer->setStartdate(time());
	$timer->setSubObjectID($timer_subObjectID);
	$tm_save = $timer->save();
	$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"] = $timer->getId();
	echo "Timer_ON";
}

/*
 * Zetimessung ohne angabe eines Grundes beenden
 */
if ($_REQUEST["ajax_action"] == "stopTimer"){
	$timer = new Timekeeper($_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]);
	$timer->setEnddate(time());
	echo $timer->save();
}

/*
 * Loeschend eines Timers
 */
if ($_REQUEST["ajax_action"] == "deleteTimer"){
	$timer = new Timekeeper((int)$_REQUEST["timerID"]);
	echo $timer->delete();
}

/*
 * Aktulaisieren der Timer-Liste // NUR SUMME
 */
if ($_REQUEST["ajax_action"] == "updateTimerTableSum"){
	global $_USER;
	$all_timer = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_START_DESC, 0, $timer_objectID, $timer_moduleID);
	$sum_hours = 0;
	$sum_min = 0;
	$article_counter = array();
	if(count($all_timer) > 0){
		foreach ($all_timer AS $timer){
			$style = "";
			$sum_seconds += $timer->getDurationInSecond();
			$sum_hours += $timer->getDurationHour();
			$sum_min += $timer->getDurationMinutes();
		}
		echo "<b>".$_LANG->get('Summe').": &emsp; </b> ".Timekeeper::printCalculateDuration($sum_min, $sum_hours);
	} else {
		echo "";
	}
}


/*
 * Aktulaisieren der Timer-Liste
 */
if ($_REQUEST["ajax_action"] == "updateTimerTable"){
	global $_USER;
	$all_timer = Timekeeper::getAllTimekeeper(Timekeeper::ORDER_START_DESC, 0, $timer_objectID, $timer_moduleID);
	$sum_hours = 0;
	$sum_min = 0;
	$article_counter = array();
	if(count($all_timer) > 0){
		foreach ($all_timer AS $timer){
			$style = "";
			$sum_seconds += $timer->getDurationInSecond();
			$sum_hours += $timer->getDurationHour();
				$sum_min += $timer->getDurationMinutes();?>
				<colgroup>
					<col width="50%">
					<col width="35%">
					<col width="15%">
				</colgroup>
				<tr>
					<td>
						<?=$timer->getUser()->getNameAsLine()?>
					</td>
					<td>
						<?=date('d.m.Y', $timer->getStartdate())?> <? // date("H:i", $start)?>
						&emsp;&emsp;&emsp;
						<?if ($_USER->isAdmin() || $_USER->getId() == $timer->getUser()->getId()){?>
						<a href="libs/modules/timekeeping/timekeeper.newTimer.iframe.php?tm_id=<?=$timer->getId()?>&tm_objectid=<?=$timer_objectID?>&tm_moduleid=<?=$timer_moduleID?>" 
							class="edittimer icon-link" ><img src="images/icons/pencil.png" title="<?=$_LANG->get('Zeit editieren');?>"></a>
						<?}?>
						&emsp;
						<?if ($_USER->isAdmin()){?>
							<a class="icon-link" href="#"	onclick="deleteTimer(<?=$timer->getId()?>)"
                				><img src="images/icons/cross-script.png"title="<?=$_LANG->get('L&ouml;schen');?>"></a>
                		<?}?>
					</td>
					<td align="right">
						<?/*** Beides ausgeben, da eh nur eins gesetzt sein wird ***/?>
						<?=$timer->printDurationInHour()?>
						<?if($timer->getArticleId() > 0){echo printPrice($timer->getArticleAmount())." ".$_LANG->get('Stk.');}?>
					</td>
				</tr>
			<?	if($timer->getComment() != "" && $timer->getComment() != NULL){ ?>
					<tr>
						<td class="content_row" colspan="3">
						<?=$timer->getComment()?>
						</td>
					</tr>
			<?		$style = "_clear"; // Damit die gepunktete Linie zwischen den Zeilen nicht zu oft ausgegeben wird
				}	
				if($timer->getArticleId() > 0){ 
					$article_counter[$timer->getArticleId()] += $timer->getArticleAmount();
				?>
					<tr>
						<td class="content_row<?=$style?>" colspan="2">
						<i><?=$timer->getArticle()->getTitle()?></i>
						</td>
						<td class="content_row<?=$style?>" align="right"> &emsp;
						<? // =printPrice($timer->getArticleAmount())." ".$_LANG->get('Stk.');?>
						</td>
					</tr>
			<?	}
				if($timer->getArticleId() == 0 && ($timer->getComment() == "" || $timer->getComment() == NULL) ) { ?>
					 <tr><td class="content_row" colspan="3">&ensp;</td></tr>
			<? 	} ?>
				<tr><td>&ensp;</td></tr>
<?		}	?>	
		<tr>
			<td colspan="3" align="right">
				<b><?=$_LANG->get('Summe')?>: &emsp; </b> <?=Timekeeper::printCalculateDuration($sum_min, $sum_hours)?> 
				
			<?	if(count($article_counter) > 0){
					echo "<br>"; 	
					foreach ($article_counter AS $key => $counter){ 
						$tmp_art = new Article($key);?>
						<i><?=$tmp_art->getTitle()?></i>: <?=printPrice($counter)?> <?=$_LANG->get('Stk.');?> <br/>
			<?		}
				} ?> 
			</td>
		</tr>
		<tr><td>&ensp; </td></tr>
<?	} else { ?>
		<tr>
			<td colspan="3" align="left">
				<?=$_LANG->get('Keine Zeiten eingegeben')?>
			</td>
		</tr>
<?	}
}
?>