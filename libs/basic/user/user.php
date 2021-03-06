<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       27.02.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

if ($_REQUEST["exec"] == "edit")
{
    require_once('user.add.php');
} else
{

    if ($_REQUEST["exec"] == "delete")
    {
        $user = new User($_REQUEST["id"]);
        $user->delete();
    }
    $users = User::getAllUser(User::ORDER_LOGIN);

	$usercount = count($users);
	if ($usercount >= $_CONFIG->maxusers){
		$newusers = false;
	} else {
		$newusers = true;
	}
    ?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Benutzer
				<span class="pull-right">
					<?php if ($newusers){?>
						<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';" >
							<span class="glyphicons glyphicons-plus"></span>
							<?=$_LANG->get('Benutzer hinzuf&uuml;gen')?>
						</button>
					<?php } else {?>
						Maximale Anzahl an Benutzern erreicht.
					<?php } ?>
				</span>
			</h3>
	  </div>
			  <div class="table-responsive">
				  <table class="table table-hover">
						  <? if($_USER->isAdmin()) echo '<col width="100">'; ?>
					  <tr>
						  <td class="content_row_header"><?=$_LANG->get('ID')?></td>
						  <td class="content_row_header"><?=$_LANG->get('Benutzername')?></td>
						  <td class="content_row_header"><?=$_LANG->get('Voller Name')?></td>
						  <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
						  <td class="content_row_header"><?=$_LANG->get('Gruppen')?></td>
						  <? if($_USER->isAdmin()) echo '<td class="content_row_header">'.$_LANG->get('Mandant').'</td>';?>
						  <td class="content_row_header"><?=$_LANG->get('Aktiv')?></td>
						  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
					  </tr>

					  <?
					  $x = 0;
					  foreach ($users as $u)
					  {

						  ?>
						  <tr class="<?=getRowColor($x)?>">
							  <td class="content_row"><?=$u->getId()?></td>
							  <td class="content_row"><?=$u->getLogin()?></td>
							  <td class="content_row"><?=$u->getFirstname()?> <?=$u->getLastname()?>
							  </td>
							  <td class="content_row"><? if ($u->isAdmin()) echo $_LANG->get('Administrator'); else echo $_LANG->get('Benutzer');?>
							  </td>
							  <td class="content_row"><? $str = "";
								  foreach ($u->getGroups() as $g)
								  {
									  $str .= $g->getName().", ";
								  }
								  echo substr($str, 0, -2);
								  ?> &ensp;
							  </td>
							  <? if($_USER->isAdmin()) echo '<td class="content_row">'.$u->getClient()->getName().'</td>';?>
							  <td class="content_row"><? if($u->isActive()) echo '<img src="images/status/green_small.gif">'; else echo '<img src="images/status/red_small.gif">';?>
							  </td>
							  <td class="content_row">
								  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$u->getId()?>"><span class="glyphicons glyphicons-pencil pointer"></span></a>
								  <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$u->getId()?>')"><span  style="color: red;" class="glyphicons glyphicons-remove pointer"></span></a>
							  </td>
						  </tr>
						  <?
						  $x++;
					  }
					  ?>
				  </table>
			  </div>
		  <?}
		  ?>
	  </div>
</div>
