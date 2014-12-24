<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$collectiveinvoices= CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_CRTDATE_DESC);

?>
<table width="100%">
	<tr>
		<td width="180" class="content_header">
			<img src="<?= $_MENU->getIcon($_SESSION['pid'])?>"> <?= $_LANG->get('Sammelrechnungen')?>
		</td>
		<td align="center">
			<? echo $savemsg; ?>
		</td>
		<td width="300" class="content_header" align="right">
			<a	href="index.php?page=<?=$_REQUEST['page']?>&exec=select_user" title="<?= $_LANG->get('Sammelrechnung erstellen')?>"> 
				<img src="images/icons/folder--plus.png"> <?= $_LANG->get('Sammelrechnung erstellen')?>
			</a>
		</td>
	</tr>
</table>
<div class="box1">
	<table width="100%" cellspacing="0" cellpadding="0">
		<colgroup>
			<col width="140">
			<col>
			<col width="150">
			<col width="200">
			<col width="130">
			<col width="100">
		</colgroup>
		<tr>
			<td class="content_header"><?=$_LANG->get('Nummer')?>
			</td>
			<td class="content_header"><?=$_LANG->get('Titel')?>
			</td>
			<td class="content_header"><?=$_LANG->get('Kunde')?>
			</td>
			<td class="content_header"><?=$_LANG->get('Status')?>
			</td>
			<td class="content_header" align="center"><?=$_LANG->get('Optionen')?>
			</td>
		</tr>
<?		$collectiveinvoices= CollectiveInvoice::getAllCollectiveInvoice();
		if(count($collectiveinvoices)>0){
			$i = 0;
			foreach($collectiveinvoices as $ci){?>
		<tr class="<?=getRowColor($i)?> pointer" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
			<td class="content_row" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$ci->getId()?>'">
				<?=$ci->getNumber()?>&nbsp;
			</td>
			<td class="content_row" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$ci->getId()?>'">
				<?=$ci->getTitle()?>&nbsp;
			</td>
			<td class="content_row" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$ci->getId()?>'">
			<?=date("d.m.Y",$ci->getcrtdate())?>&nbsp;
			</td>
			<td class="content_row" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$ci->getId()?>'">
				<?=$ci->getBusinessContact()->getNameAsLine()?>&nbsp;
			</td>
			<td class="content_row">
				<table>
					<tr>
						<td>
						<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?= $ci->getId() ?>&exec=setState&state=1">
								<img class="select" src="./images/status/<?
	            				if($ci->getStatus() == 1)
	            	    				echo 'red.gif';
					            	else
										echo 'black.gif'; ?>" title="<?= $_LANG->get('Status ändern')?>">
						</a>
						</td>
						<td><a
							href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$ci->getId()?>&exec=setState&state=2">
								<img class="select" src="./images/status/<?
	            				if($ci->getStatus() == 2)
					                	echo 'orange.gif';
					                else
					                	echo 'black.gif';?>" title="<?= $_LANG->get('Status ändern')?>">
						</a>
						</td>
						<td><a
							href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$ci->getId()?>&exec=setState&state=3">
								<img class="select" src="./images/status/<?
									if($ci->getStatus() == 3)
					                	echo 'yellow.gif';
					                else
					                	echo 'black.gif'; ?>" title="<?= $_LANG->get('Status ändern')?>">
						</a>
						</td>
						<td><a
							href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?= $ci->getId()?>&exec=setState&state=4">
								<img class="select" src="./images/status/<?
	            					if($ci->getStatus() == 4)
	            						echo 'lila.gif';
									else
										echo 'black.gif';?>" title="<?= $_LANG->get('Status ändern')?>">
						</a>
						</td>
					</tr>
				</table>
			</td>
			<td class="content_row" align="center">
				<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$ci->getId()?>">
					<img src="images/icons/pencil.png" title="<?= $_LANG->get('Bearbeiten')?>"></a>
				&ensp;
				<a href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&del_id=<?=$ci->getId()?>')">
					<img src="images/icons/cross-script.png" title ="<?= $_LANG->get('Löschen')?>"></a>
			</td>
		</tr>
		<?$i++;
		}
	}?>
	</table>
</div>