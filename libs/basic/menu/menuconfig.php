<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       25.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
if($_REQUEST["exec"] == "delete")
{
    $menuentry = new Menuentry((int)$_REQUEST["id"]);
    $savemsg = getSaveMessage($menuentry->delete());

    echo '<script language="javascript">location.href=\'index.php\'</script>';
}

function printSubTreeEdit($tree, $i = 1)
{
    global $_LANG;
    foreach($tree as $t)
    {
        $ausgabe = true;
        echo '<p class="menu_level'.$i.'">';
        if ($t->getIcon() != "")
            echo '<img src="'.$t->getIcon().'" />';
        echo ' <a href="index.php?page='.$_REQUEST['page'].'&exec=edit&editpid='.$t->getPid().'">'.$t->getName().'</a></p>';

        if ($ausgabe)
        {
            echo '<p class="menu_level'.($i+1).'"><span class="glyphicons glyphicons-plus">&nbsp;</span>';
            echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&parent='.$t->getPid().'">';
            echo $_LANG->get('Neuer Eintrag').'</a></p>';
        }


        foreach ($t->getChilds() as $c)
        {
            printSubTreeEdit(Array($c), $i+1);
            $hasChilds = true;
        }
    }
}

if($_REQUEST["retval"] == 1)
    $savemsg = getSaveMessage(true);
else if($_REQUEST["retval"] == 2)
    $savemsg = getSaveMessage(false);
?>

<script type="text/javascript">
<!--
function hideModuleOpts(val)
{
	if(val == 1)
	{
		document.getElementById('tr_menu_path').style.display = '';
		document.getElementById('tr_menu_icon').style.display = '';
	} else
	{
		document.getElementById('tr_menu_path').style.display = 'none';
		document.getElementById('tr_menu_icon').style.display = 'none';
	}

}
//-->
</script>

<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		$("a#menu_path").fancybox({
		    'type'    : 'iframe'
		}),
		$("a#menu_icon").fancybox({
		    'type'    : 'iframe'
		})
	});
</script>
<!-- /FancyBox -->

<?
if($_REQUEST["exec"] == "edit")
{
    $_REQUEST["editpid"] = (int)$_REQUEST["editpid"];
    if ($_REQUEST["editpid"] != 0)
    {
        $menuentry = new Menuentry($_REQUEST["editpid"]);
    } elseif ($_REQUEST["parent"] != "")
    {
        $menuentry = new Menuentry();
        $menuentry->setParentid((int)$_REQUEST["parent"]);
    }

    if ($_REQUEST["subexec"] == "save")
    {
        $menuentry->setName(trim($_REQUEST["menu_name"]));
        $menuentry->setOrder((int)$_REQUEST["menu_order"]);
        $menuentry->setType((int)$_REQUEST["menu_type"]);
        $menuentry->setPublic((int)$_REQUEST["menu_public"]);
        $menuentry->setPath(trim($_REQUEST["menu_path"]));
        $menuentry->setIcon(trim($_REQUEST["menu_icon"]));
        $menuentry->setAllowedUsers($_REQUEST["menu_allowed_users"]);
        $menuentry->setAllowedGroups($_REQUEST["menu_allowed_groups"]);
        $res = getSaveMessage($menuentry->save());

    }
} else {
	$menuentry = new Menuentry();
}
$users = User::getAllUser(User::ORDER_LOGIN);
$groups = Group::getAllGroups(Group::ORDER_NAME);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Men&uuml;konfiguration
                <span class="pull-right">
                    <?=$savemsg?>
                </span>
        </h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <div class="menuedit">
                    <?=printSubTreeEdit($_MENU->getElements())?>
                    <p class="menu_level1"><span class="glyphicons glyphicons-plus pointer"></span> <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&parent=0"><?=$_LANG->get('Neuer Eintrag')?></p></a>
                </div>
            </div>
            <? if($_REQUEST["exec"] == "edit"){?>
                <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" name="menu_form">
                    <input type="hidden" name="exec" value="edit">
                    <input type="hidden" name="subexec" value="save">
                    <input type="hidden" name="editpid" value="<?=$_REQUEST["editpid"]?>">
                    <input type="hidden" name="parent" value="<?=$menuentry->getParentid()?>">
                    <input type="hidden" name="menu_path" id="menu_path" value="<?=$menuentry->getPath()?>">
                    <input type="hidden" name="menu_icon" id="menu_icon" value="<?=$menuentry->getIcon()?>">

                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><? if ($_REQUEST["editpid"] != "") echo $_LANG->get('Eintrag editieren'); else echo $_LANG->get('Eintrag anlegen');?></label>
                            <div class="col-sm-10">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Bezeichnung</label>
                            <div class="col-sm-5">
                                <input name="menu_name" id="" value="<?=$menuentry->getName()?>" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Sortierung</label>
                            <div class="col-sm-5">
                                <input name="menu_order" class="form-control" value="<?=$menuentry->getOrder()?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">&Ouml;ffentlich</label>
                            <div class="col-sm-2">
                                <input type="radio" name="menu_public" value="0" <?if($menuentry->getPublic() == 0) echo "checked";?>><?=$_LANG->get('Nein')?>
                            </div>
                            <div class="col-sm-2">
                                <input type="radio" name="menu_public" value="1" <?if($menuentry->getPublic() == 1) echo "checked";?>><?=$_LANG->get('Ja')?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Typ</label>
                            <div class="col-sm-2">
                                <input type="radio" name="menu_type" id="menu_type" value="0" <?if($menuentry->getType() == Menuentry::TYPE_FOLDER) echo "checked";?> onchange="hideModuleOpts(0)"><?=$_LANG->get('Ordner')?>
                            </div>
                            <div class="col-sm-2">
                                <input type="radio" name="menu_type" id="menu_type" value="1" <?if($menuentry->getType() == Menuentry::TYPE_MODULE) echo "checked";?> onchange="hideModuleOpts(1)"><?=$_LANG->get('Modul')?>
                            </div>
                        </div>
                        <div class="form-group" id="tr_menu_path" <?if($menuentry->getType() != Menuentry::TYPE_MODULE) echo 'style="display:none"'?>>
                            <label for="" class="col-sm-2 control-label">Modulpfad</label>
                            <div class="col-sm-10">
                                <span id="span_menu_path"><?=$menuentry->getPath()?> </span><a href="libs/basic/menu/modulepath.php" id="menu_path"><span class="button">&nbsp;<?=$_LANG->get('Ausw&auml;hlen')?></span></a>
                            </div>
                        </div>
                        <div class="form-group" id="tr_menu_icon" <?if($menuentry->getType() != Menuentry::TYPE_MODULE) echo 'style="display:none"'?>>
                            <label for="" class="col-sm-2 control-label">Men&uuml;icon</label>
                            <div class="col-sm-10">
                                <span id="span_menu_icon"><img src="<?=$menuentry->getIcon()?>" id="img_menu_icon"> </span><a href="libs/basic/menu/moduleicon.php" id="menu_icon"><span class="button">&nbsp;<?=$_LANG->get('Ausw&auml;hlen')?></span></a>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                        	<table class="table table-hover">
                                <tr>
                                    <td class="content_header" colspan="3"><?=$_LANG->get('Benutzer')?></td>
                                </tr>
                                <tr>
                                    <td class="content_row_header">&nbsp;</td>
                                    <td class="content_row_header"><?=$_LANG->get('Login')?></td>
                                    <td class="content_row_header"><?=$_LANG->get('Name')?></td>
                                </tr>
                                <? $x=0; foreach ($users as $u) { ?>
                                    <tr class="<?=getRowColor($x)?>">
                                        <td class="content_row"><input type="checkbox" name="menu_allowed_users[]" value="<?=$u->getId()?>" <?if(count($menuentry->getAllowedUsers()) > 0 && array_search($u->getId(), $menuentry->getAllowedUsers()) !== false) echo "checked";?>></td>
                                        <td class="content_row"><?=$u->getLogin()?></td>
                                        <td class="content_row"><?=$u->getNameAsLine()?></td>
                                    </tr>
                                    <? $x++;} ?>
                                <tr>
                                    <td class="content_header" colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="content_header" colspan="3"><?=$_LANG->get('Gruppen')?></td>
                                </tr>
                                <tr>
                                    <td class="content_row_header">&nbsp;</td>
                                    <td class="content_row_header"><?=$_LANG->get('Gruppe')?></td>
                                    <td class="content_row_header"><?=$_LANG->get('Mitglieder')?></td>
                                </tr>
                                <? $x = 0; foreach ($groups as $g) { ?>
                                    <tr class="<?=getRowColor($x)?>">
                                        <td class="content_row"><input type="checkbox" name="menu_allowed_groups[]" value="<?=$g->getId()?>" <?if(count($menuentry->getAllowedGroups()) > 0 && array_search($g->getId(), $menuentry->getAllowedGroups()) !== false) echo "checked";?>></td>
                                        <td class="content_row"><?=$g->getName()?>&nbsp;</td>
                                        <td class="content_row"><?foreach($g->getMembers() as $m) { echo $m->getLogin().", "; } ?>&nbsp;</td>
                                    </tr>
                                    <? $x++;} ?>
                            </table>
                            <table>
                                <tr>
                                    <td width="250" align="left">
                                        &ensp;
                                    </td>
                                    <td width="300" align="left">
                                        <input type="button" class="buttonRed" value="<?=$_LANG->get('L&ouml;schen')?>" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$menuentry->getPid()?>')">
                                    </td>
                                    <td width="450" align="right">
                                        <input type="submit" value="<?=$_LANG->get('Speichern')?>">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            <?} //ENDE if($_REQUEST["exec"] == "edit") ?>
        </div>
        
    </div>
</div>
