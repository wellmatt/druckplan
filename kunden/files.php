<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------

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
        $res = $DB->no_result($sql);
    }   
}

$sql = "SELECT * FROM ftpcustuploads
        WHERE ftp_cust_id = {$_SESSION["cust_id"]}
        ORDER BY ftp_crtdat DESC";
$files = $DB->select($sql);
        
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
 
<div class="box2" style="min-height:180px;">
<table cellpadding="2" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col>
        <col width="120">
        <col width="120">
        <!-- <col width="120"> -->
        <col width="160">
    </colgroup>
    <tr>
        <td class="filerow_header">Dateiname</td>
        <td class="filerow_header">Dateigr&ouml;&szlig;e</td>
        <td class="filerow_header">Hochgeladen am</td>
        <!-- <td class="filerow_header">Vertaulichkeit</td>-->
        <td class="filerow_header">Optionen</td>
    </tr>
    
    <? $x = 0; foreach($files as $file) 
    {
        if($file["ftp_filesize"] > 1024*1024)
        {
            $size = $file["ftp_filesize"] / 1024 / 1024;
            $unit = "MB";
        }
        else if($file["ftp_filesize"] > 1024)
        {
            $size = $file["ftp_filesize"] / 1024;
            $unit = "KB";
        }
        else
        { 
            $size = $file["ftp_filesize"];
            $unit = "B";
        }
            
    ?> 
    <tr class="filerow">
        <td class="filerow"><?=$file["ftp_orgname"]?></td>
        <td class="filerow"><?=round($size, 2)?> <?=$unit?></td>
        <td class="filerow"><?=date('d.m.Y H:i', $file["ftp_crtdat"])?></td>
        <!-- <td class="filerow">
             <?
             if($file["ftp_conf_step"] == 1) echo '<b style="color:#FF0000">Geheim</b>';
             if($file["ftp_conf_step"] == 2) echo "Vertraulich";
             if($file["ftp_conf_step"] == 3) echo "Intern";
             if($file["ftp_conf_step"] == 4) echo "&Ouml;ffentlich";
             ?>
        </td>-->
        <td class="filerow">
            <a href="../get.php?type=2&hash=<?=$file["ftp_hash"]?>" class="button">Download</a>
            <a href="#" onclick="askDel('index.php?pid=<?=$_REQUEST["pid"]?>&delete=<?=$file["ftp_hash"]?>')" class="button_del">L&ouml;schen</a>
        </td>
    </tr>
    <? $x++; } ?>
</table>
</div>

