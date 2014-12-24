<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.07.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/timekeeping/timekeeper.class.php';

$moduleID 	= (int)$_REQUEST["tm_moduleid"];
$objectID 	= (int)$_REQUEST["tm_objectid"];

$current_timer = new Timekeeper($_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]);

if($_REQUEST["exec"]=="save"){
	if ($_REQUEST["reason"] == "5")
	{
		$comment = $_REQUEST["timer_comment"];
	}
	else
	{
		if ($_REQUEST["reason"] == "1") { $comment = "in Korrektur"; }
		if ($_REQUEST["reason"] == "2") 
		{ 
			$comment = "Platten fertig";
			$ticket_id = $current_timer->getObjectID();
			$obj = new Ticket($ticket_id);
			$planning = $obj->getPlanning();
			if ($planning->getId() != 0)
			{
				$planning->setStatusDtp(3);
				$planning->save();
			}
		}
		if ($_REQUEST["reason"] == "3") { $comment = "Rückfrage intern"; }
		if ($_REQUEST["reason"] == "4") { $comment = "Rückfrage extern"; }
		if ($_REQUEST["reason"] == "6") { $comment = ""; }
	}
	
	$current_timer->setComment(trim(addslashes($comment)));
	$current_timer->setEnddate(time());
	$tmp_saver = $current_timer->save();
	
    if($tmp_saver==true){
    	$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"] = 0;
    	
        ?>
        <script language="javascript">
			alert('<?=$_LANG->get('Zeit gespeichert');?>');
        	parent.document.getElementById('img_timer_start').style.display='';
			parent.document.getElementById('a_timer_stop').style.display='none';
			parent.document.getElementById('img_timer_loading').style.display='none';
			parent.document.getElementById('a_timer_othertimer').style.display='none';
			parent.postMessage("Update", "*");
		</script>
		
		<? 
		if ($current_timer->getModule() != (int)$_REQUEST["moduleID"] || $current_timer->getObjectID() != (int)$_REQUEST["objectID"]){
			// echo $current_timer->getModule() . " - " . $_REQUEST["moduleID"] . "</br>";
			// echo $current_timer->getObjectID() . " - " . $_REQUEST["objectID"] . "</br>";
			echo '<script language="javascript">parent.postMessage("OtherTimer", "*");</script>';
		} 
		?>
		
		<script language="javascript">
            parent.$.fancybox.close();
        </script>
		
        <?
    } else {
		echo $_LANG->get('Timer konnte nicht gestopt werden.');
	}
} ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<script language="javascript" src="../../../jscripts/basic.js"></script>
<script type="text/javascript">
function checkcomment(){
	var tmp_type= document.getElementById('reason').value;

	if(tmp_type == 5){
		document.getElementById('timer_comment').style.display= '';
	} else {
		document.getElementById('timer_comment').style.display= 'none';
	}
}
</script>
</head>
<body>
<h1><?=$_LANG->get('Zeitmessung stoppen')?></h1>

<?	
$feedback = $_LANG->get('Die Zeitmessung wird f&uuml;r')." ".$current_timer->getModuleName();
$feedback .= " '".$current_timer->getObjectName()."' ".$_LANG->get('beendet.');
echo $feedback;?>
<br/><br/>  
<form enctype="multipart/form-data" action="timekeeper.iframe.php" method="post"
		onSubmit="return checkform(new Array(this.reason))">
	<input type="hidden" id="exec" name="exec" value="save" />
	<input type="hidden" id="moduleID" name="moduleID" value="<?=(int)$_REQUEST["tm_moduleid"]?>" />
	<input type="hidden" id="objectID" name="objectID" value="<?=(int)$_REQUEST["tm_objectid"]?>" />
	<table width="100%">
		<tr>
			<td class="content_row_header" valign="top"><?=$_LANG->get('Grund');?></td>
		</tr>
		<tr>
			<td align="right">
				<select name="reason" id="reason" style="width: 340px" class="text" onchange="checkcomment()">
						<option value=""> &lt; <?=$_LANG->get('Bitte w&auml;hlen') ?> 	&gt;</option>
							<option value="1">in Korrektur</option>
							<option value="2">Platten fertig</option>
							<option value="3">Rückfrage intern</option>
							<option value="4">Rückfrage extern</option>
							<option value="5">Anderen</option>
							<option value="6">Kein</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">
				<textarea id="timer_comment" name="timer_comment" style="width: 340px; height: 70px; display: none;"></textarea>
			</td>
		</tr>
		<tr>
		<td width="150px">&ensp;</td>
		<td align="left">
			<input type="submit" value="<?=$_LANG->get('Speichern')?>">
		</td>
		</tr>
	
	</table>
</form>
</body>
</html>