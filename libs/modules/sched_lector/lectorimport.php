<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'lectorimport.class.php';

$import = new LectorImport();

if($_REQUEST["exec"] == "transfer")
    require_once 'lectorimport.transfer.php';

if($_REQUEST["exec"] == "") { ?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Lectorimport')?></td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
<input type="hidden" name="exec" value="transfer">
<table width="500"><tr><td>
<div class="box1">
<table width="100%">
    <tr>
        <td class="content_row_header" style="width:250px"><?=$_LANG->get('Auftragsnummer')?></td>
        <td class="content_row_clear">
            <input name="lector_jobnumber" style="width:200px" class="text">
        </td>
    </tr>
</table>
</div>
<table width="100%">
    <tr>
        <td class="content_row_clear" align="right">
            <input type="submit" value="<?=$_LANG->get('Auftrag suchen')?>">
        </td>
    </tr>
</table>
</td></tr></table>
</form>

<? } ?>