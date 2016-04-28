<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			03.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				Zahlungsbedingungen
				<span class="pull-right">
					 <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';">
						 <img src="images/icons/user--plus.png">
						 <?=$_LANG->get('Zahlungsbedingungen hinzuf&uuml;gen')?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">

		  <div class="table-responsive">
			  <table class="table table-hover">
				  <colgroup>
					  <col width="100">
					  <col width="190">
					  <col>
					  <?/**if($_CONFIG->shopActivation){?><col width="100" ><?}***/?>
					  <col width="100" >
				  </colgroup>
				  <tr>
					  <td class="content_row_header"><?=$_LANG->get('ID')?></td>
					  <td class="content_row_header"><?=$_LANG->get('Name');?></td>
					  <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
					  <?/**if($_CONFIG->shopActivation){?><td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td><?}*/?>
					  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
				  </tr>
				  <? $x=0;
				  foreach ($all_payments as $pt){ ?>
					  <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
						  <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&pay_id=<?=$pt->getId()?>'">
							  <?=$pt->getId()?>
						  </td>
						  <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&pay_id=<?=$pt->getId()?>'">
							  <?=$pt->getName()?>
						  </td>
						  <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&pay_id=<?=$pt->getId()?>'">
							  <?=$pt->getComment()?>
						  </td>
						  <?/***if($_CONFIG->shopActivation){?>
						  <td class="content_row pointer" align="center"
						  onclick="document.location='index.php?exec=edit&pay_id=<?=$pt->getId()?>'">
						  <img src="images/status/
						  <? if ($pt->getShoprel() == 0){
						  echo "red_small.gif";
						  } else {
						  echo "green_small.gif";
						  }
						  ?> ">
						  </td>
						  <?}***/ ?>
						  <td class="content_row">
							  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&pay_id=<?=$pt->getId();?>"><img src="images/icons/pencil.png"></a>
							  &ensp;
							  <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&pay_id=<?=$pt->getId();?>')"><img src="images/icons/cross-script.png"></a>
						  </td>
					  </tr>
					  <? $x++;
				  } ?>
			  </table>
		  </div>
	  </div>
</div>