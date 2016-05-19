<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       11.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';

if ($_REQUEST["doupload"] == 1){
	require_once 'publish.php';
} else {

if($_REQUEST["delete"] != "")
{
    $_REQUEST["delete"] = trim(addslashes($_REQUEST["delete"]));
    
    $sql = "SELECT * FROM ftpcustuploads WHERE ftp_hash = '{$_REQUEST["delete"]}'";
    $download = $DB->select($sql);
    $download = $download[0];
    $fileext = explode(".", $download["ftp_orgname"]);
    $fileext = $fileext[count($fileext) - 1];
    $filename = "ftp/cust_uploads/".$download["ftp_hash"].".".$fileext;

    if(unlink($filename))
    {
        $sql = "DELETE FROM ftpcustuploads WHERE ftp_hash = '{$_REQUEST["delete"]}'";
        $savemsg = getSaveMessage($res = $DB->no_result($sql));
    }   
}

if($_REQUEST["filter"] == "on" || $_REQUEST["filter"] == ""){		
	$filter = "on";					// aktueller Zustand der Filterfunktion
	$filter2 = "off";				// invertierter Zustand der Filterfunktion
	$filter_msg = $_LANG->get("Alle Kunden einblenden");
} else {
	$filter = "off";
	$filter2 = "on";
	$filter_msg = $_LANG->get("Inaktive ausblenden");
}

$customers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME);
?>
<script language="javascript">
function askDel(myurl)
{
   if(confirm("Sind Sie sicher?"))
   {
      if(myurl != '')
         location.href = myurl;
      else
         return true;
   }
   return false;
}
</script>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Kundenuploads
			<span class= "pull-center">
				<img src="images/icons/navigation.png" alt="Upload">
				<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&doupload=1)?>';" >
					<?=$_LANG->get('Upload');?>
				</button>
			</span>
			<span class= "pull-right">
				<img src="images/icons/funnel.png"alt="Download">
				<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&filter=<?=$filter2?>';" >
					<?=$filter_msg?>
				</button>
			</span>
		</h3>
	</div>
	<div class="panel-body">
		<?
		foreach($customers as $cust){
		$sql = "SELECT * FROM ftpcustuploads
	            WHERE ftp_cust_id = {$cust->getId()}
	            ORDER BY ftp_crtdat DESC";
		$files = $DB->select($sql);
		if(count($files) > 0 && $files != false || $filter == "off" ){
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<b><?=$cust->getNameAsLine()?></b>
				</h3>
			</div>
				<div class="table-responsive">
					<table class="table table-hover">
						<colgroup>
							<col>
							<col width="120">
							<col width="120">
							<col width="80">
						</colgroup>
						<tr>
							<td class="content_row_header">Dateiname</td>
							<td class="content_row_header">Dateigr&ouml;&szlig;e</td>
							<td class="content_row_header">Hochgeladen am</td>
							<td class="content_row_header" colspan="2">Optionen</td>
						</tr>
						<?
						$y=0;
						foreach($files as $file){
							if($file["ftp_filesize"] > 1024*1024){
								$size = $file["ftp_filesize"] / 1024 / 1024;
								$unit = "MB";
							} elseif ($file["ftp_filesize"] > 1024) {
								$size = $file["ftp_filesize"] / 1024;
								$unit = "KB";
							} else {
								$size = $file["ftp_filesize"];
								$unit = "B";
							}
							?>
							<tr class="<?=getRowColor($y)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
								<td class="content_row"><?=$file["ftp_orgname"]?></td>
								<td class="content_row"><?=printPrice(round($size, 2))?> <?=$unit?></td>
								<td class="content_row"><?=date('d.m.Y H:i', $file["ftp_crtdat"])?></td>
								<td class="content_row">
									<a class="icon-link" href="get.php?type=2&hash=<?=$file["ftp_hash"]?>"><img src="images/icons/navigation-270-frame.png" title="Download" alt="Download"></a>
									<!-- a href="#" onclick="askDel('index.php?mid=<?=$_REQUEST["mid"]?>&delete=<?=$file["ftp_hash"]?>')">L&ouml;schen</a-->
									&ensp;
									<a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delete=<?=$file["ftp_hash"]?>')"><img src="images/icons/cross-script.png"> </a>
								</td>
							</tr>
							<? $y++; } ?>
					</table>
				</div>
		</div>
					<?
				}
			}
		}?>
	</div>
</div>
