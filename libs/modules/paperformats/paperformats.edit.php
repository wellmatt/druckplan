<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.07.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$format = new Paperformat($_REQUEST["id"]);
if($_REQUEST["exec"] == "copy")
{
    $format->clearID();
}
if($_REQUEST["subexec"] == "save")
{
    $format->setName(trim(addslashes($_REQUEST["format_name"])));
    $format->setWidth((int)$_REQUEST["format_width"]);
    $format->setHeight((int)$_REQUEST["format_height"]);
    $savemsg = getSaveMessage($format->save()).$DB->getLastError();
}
?>
<table width="100%">
    <tr>
        <td width="200" class="content_header">
            <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
            <?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Produktformat hinzuf&uuml;gen')?>
            <?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Produktformat &auml;ndern')?>
            <?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Produktformat kopieren')?>
        </td>
        <td align="right"><?=$savemsg?></td>
    </tr>
</table>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#paper_form').submit();">Speichern</a>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="paper_form" name="paper_form" onSubmit="return checkform(new Array(this.format_name))">
	<div class="box1">
        <input name="exec" value="edit" type="hidden">
        <input type="hidden" name="subexec" value="save">
        <input name="id" value="<?=$format->getId()?>" type="hidden">
        <table width="100%">
            <colgroup>
                <col width="200">
                <col>
            </colgroup>
            <tr>
                <td class="content_row_header"><?=$_LANG->get('Name')?> *</td>
                <td class="content_row_clear">
                    <input id="format_name" name="format_name" value="<?=$format->getName()?>" class="text" style="width:300px">
                </td>
            </tr>
            <tr>
                <td class="content_row_header"><?=$_LANG->get('Breite')?> *</td>
                <td class="content_row_clear">
                    <input id="format_width" name="format_width" value="<?=$format->getWidth()?>" class="text" style="width:40px"> mm
                </td>
            </tr>
            <tr>
                <td class="content_row_header"><?=$_LANG->get('H&ouml;he')?> *</td>
                <td class="content_row_clear">
                    <input id="format_height" name="format_height" value="<?=$format->getHeight()?>" class="text" style="width:40px"> mm
                </td>
            </tr>
        </table>
	</div>
	<br/>
</form>