<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       18.12.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/timekeeping/timekeeper.class.php';

$current_timer = new Timekeeper($_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]);

if($_REQUEST["exec"]=="save"){
	$current_timer->setComment(trim(addslashes($_REQUEST["timer_comment"])));
	$current_timer->setEnddate(time());
	$tmp_saver = $current_timer->save();
	
    if($tmp_saver==true){
    	$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"] = 0;
    	
        ?>
        <script language="javascript">
        	<?/*
        	var parent_objid = parent.document.getElementById('timer_objectID').value;
        	var parent_modid = parent.document.getElementById('timer_moduleID').value;
			*/?>
			// alert('<?=$_LANG->get('Zeit gespeichert');?>');

        	// Falls man im Entsprechenden Objekt (und Modul) ist muss die Tabelle aktualisiert werden
        	
        	<? /*** ?>
        	if(parent_objid == <?=$current_timer->getObjectID()?> && parent_modid == <?=$current_timer->getModule()?>){
				var insert_data = '<tr>';
				insert_data += '<td><?=$current_timer->getUser()->getNameAsLine()?></td>';
				insert_data += '<td><?=date('d.m.Y', $current_timer->getStartdate())?></td>';
				insert_data += '<td><?=printPrice($current_timer->getDurationInHour())?> <?=$_LANG->get('Std.');?></td>';
				insert_data += '</tr>';
				insert_data += '<tr>';
				insert_data += '<td class="content_row" colspan="3"><?=$current_timer->getComment()?></td></tr>';
				insert_data += '<tr><td>&ensp;</td></tr>'; 
				parent.document.getElementById('table_alltimer').insertAdjacentHTML('BeforeEnd', insert_data);
        	} ***/?>

        	// wenns nur eins gibt
        	// parent.document.getElementById('img_timer_start').style.display='';
			// parent.document.getElementById('a_timer_stop').style.display='none';
			// parent.document.getElementById('img_timer_loading').style.display='none';
			// parent.document.getElementById('a_timer_othertimer').style.display='none';

			for(var i=0;i<parent.document.getElementsByName('img_timer_start').length;i++){
				parent.document.getElementsByName('img_timer_start')[i].style.display='';
				parent.document.getElementsByName('a_timer_stop')[i].style.display='none';
				parent.document.getElementsByName('img_timer_loading')[i].style.display='none';
				parent.document.getElementsByName('a_timer_othertimer')[i].style.display='none';
	        }
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
</head>
<body>
<h1><?=$_LANG->get('Zeitmessung stoppen')?></h1>

<?	
$feedback = $_LANG->get('Die Zeitmessung wird f&uuml;r')." ".$current_timer->getModuleName();
$feedback .= " '".$current_timer->getObjectName()."' ".$_LANG->get('beendet.');
echo $feedback;?>
<br/><br/>  
<form enctype="multipart/form-data" action="timekeeper.stopPlaning.iframe.php" method="post"
		onSubmit="return checkform(new Array(this.timer_comment))">
	<input type="hidden" id="exec" name="exec" value="save" />
	<table width="100%">
		<tr>
			<td class="content_row_header" valign="top"><?=$_LANG->get('Grund / Bemerkung');?></td>
			<td align="right">
				<textarea id="timer_comment" name="timer_comment" style="width: 350px; height: 70px;"></textarea>
			</td>
		</tr>
		<tr>
		<td width="150px">&ensp;</td>
		<td align="right">
			<input type="submit" value="<?=$_LANG->get('Speichern')?>">
		</td>
		</tr>
	
	</table>
</form>
</body>
</html>