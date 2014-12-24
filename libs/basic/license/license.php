<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Lizenzinformationen')?></td>
      <td><?=$savemsg?></td>
   </tr>
</table>

<table width="500"><tr><td>
<div class="box1">
    <table cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td class="content_header" width="140" valign="top"><?=$_LANG->get('Registriert auf')?></td>
            <td class="content_row_clear"><?=$_LICENSE->getRegisteredTo()?></td>
        </tr>
        <tr>
            <td class="content_header" width="140" valign="top"><?=$_LANG->get('L&auml;uft ab am')?></td>
            <td class="content_row_clear"><?=$_LICENSE->getExpires()?></td>
        </tr>
        <tr>
            <td class="content_header" width="140" valign="top"><?=$_LANG->get('Hardware-ID')?></td>
            <td class="content_row_clear"><?=implode("<br>", $_LICENSE->getHardwareID())?></td>
        </tr>
        <tr>
            <td class="content_header" width="140"><?=$_LANG->get('Anzahl Druckmaschinen')?></td>
            <td class="content_row_clear"><?=$_LICENSE->getMaxMachines()?></td>
        </tr>
        <? 
        if($_LICENSE->isTestLicense())
        {?>
            <tr>
                <td class="content_header" width="140" valign="top"><span class="error"><?=$_LANG->get('TESTCASE')?></span></td>
                <td class="content_row_clear">&nbsp;</td>
            </tr>
        <? }
        ?>
    </table>
</div>
</td></tr></table>