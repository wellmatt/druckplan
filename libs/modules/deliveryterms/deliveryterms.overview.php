<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$delivterms = DeliveryTerms::getAllDeliveryConditions();
?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				Lieferbedingungen
				<span class="pull-right">
					 <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';">
						 <img src="images/icons/user--plus.png">
						 <?=$_LANG->get('Lieferbedingungen hinzuf&uuml;gen')?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">

		  <div class="table-responsive">
			  <table class="table table-hover">
				  <colgroup>
					  <col with="20">
					  <col with="190">
					  <col>
					  <col with="80">
					  <?/**if($_CONFIG->shopActivation){?><td class="content_row_header"><col with="80"></td><?}**/?>
					  <col with="100" >
				  </colgroup>
				  <tr>
					  <td class="content_row_header"><?=$_LANG->get('ID')?></td>
					  <td class="content_row_header"><?=$_LANG->get('Name')?></td>
					  <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
					  <td class="content_row_header"><?=$_LANG->get('Kosten')?></td>
					  <?/**if($_CONFIG->shopActivation){?><td class="content_row_header" align="center"><?=$_LANG->get('Shop-Freigabe')?></td><?}**/?>
					  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
				  </tr>
				  <?$x = 0;
				  foreach($delivterms as $dt){?>
					  <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
						  <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
							  <?=$dt->getId()?>
						  </td>
						  <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
							  <?=$dt->getName1()?>
						  </td>
						  <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
							  <?=$dt->getComment()?>
						  </td>
						  <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
							  <?=printPrice($dt->getCharges())?> <?=$_USER->getClient()->getCurrency()?>
						  </td>
						  <?/**if($_CONFIG->shopActivation){?>
						  <td class="content_row pointer" align="center"
						  onclick="document.location='index.php?exec=edit&did=<?=$dt->getId()?>'">
						  <img src="images/status/
						  <? if ($dt->getShoprel() == 0){
						  echo "red_small.gif";
						  } else {
						  echo "green_small.gif";
						  }
						  ?> ">
						  </td>
						  <?}**/?>
						  <td class="content_row">
							  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>"><img src="images/icons/pencil.png"></a>
							  &ensp;
							  <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$dt->getId()?>')"><img src="images/icons/cross-script.png"></a>
						  </td>
					  </tr>
					  <?$x++;
				  }?>
			  </table>
		  </div>
	  </div>
</div>

