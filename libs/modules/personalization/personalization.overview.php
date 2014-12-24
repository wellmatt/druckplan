<? // ---------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------

$search_ver_string = $_REQUEST["search_ver_string"];

$all_perso = Personalization::getAllPersonalizationsSearch("title ASC", $search_ver_string);
?>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"><span style="font-size: 13px"> <?=$_LANG->get('Personalisierungen')?> </span>
		</td>
		<td><?=$savemsg?></td>
		<td>
			<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="perso_ver_search" id="perso_ver_search" >
				<input name="pid" type="hidden" value="<?=$_REQUEST["pid"]?>" />
				<input name="search_ver_string" type="text" value="<?=$search_ver_string?>" style="width:150px;"/>
				<img src="images/icons/magnifier-left.png" alt="<?=$_LANG->get('Suchen');?>" class="pointer"
					 onClick="document.getElementById('perso_ver_search').submit()" />
			</form>
		</td>
		<td width="300" class="content_header" align="right">
			<span style="font-size: 13px">
				<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=new"><img src="images/icons/applications-stack.png"> <?=$_LANG->get('Personalisierung hinzuf&uuml;gen')?></a>
			</span>
		</td>
	</tr>
</table>

<div class="box1">
	<table width="100%" cellpadding="0" cellspacing="0">
		<colgroup>
			<col width="105">
			<col>
			<col width="170">
			<col width="170">
			<col width="100">
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Bild')?></td>
			<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
			<td class="content_row_header"><?=$_LANG->get('Kunde')?></td>
			<td class="content_row_header"><?=$_LANG->get('Artikel')?></td>
			<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
		</tr>
		<? $x = 0;
		foreach($all_perso as $perso){
			?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row icon-link" align="center" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$perso->getId()?>'">
				<?if ($perso->getPicture()!= NULL && $perso->getPicture() !=""){?>
					<img src="images/products/<?=$perso->getPicture()?>" width="100px">&nbsp;
        		<?} else {?>
        			<img src="images/icons/image.png" title="<?=$_LANG->get('Kein Bild hinterlegt'); ?>" alt="Bild">
        		<? } ?>
        		</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$perso->getId()?>'">
					<?=$perso->getTitle()?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$perso->getId()?>'">
					<? // Kunde nur ausgeben, wenn auch gesetzt 
					if ($perso->getCustomer()->getId() > 0){
						echo $perso->getCustomer()->getNameAsLine();
					} else {
						echo "&ensp;";
					}?>
					
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$perso->getId()?>'">
					<?=$perso->getArticle()->getTitle()?>&ensp;
				</td>
				<td class="content_row">
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$perso->getId()?>"><img src="images/icons/pencil.png" title="<?=$_LANG->get('Bearbeiten')?>"></a>
				<!-- a href="index.php?exec=copy&id=<?=$perso->getId()?>"><img src="images/icons/scripts.png" title="<?=$_LANG->get('Kopieren')?>"></a-->
                <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$perso->getId()?>')"><img src="images/icons/cross-script.png" title="<?=$_LANG->get('L&ouml;schen')?>"></a>
            </td>
			</tr>
			<? $x++;
		}// Ende foreach($all_perso)
		?>
	</table>
</div>