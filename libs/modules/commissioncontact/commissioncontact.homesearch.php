<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			07.08.2013
// Copyright:		2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
//		Diese Datei behandelt die globale Suchfunktion auf der Startseite
//
// ----------------------------------------------------------------------------------
require_once 'commissioncontact.class.php';

if ($main_searchstring != "" && $main_searchstring!=NULL){
	// $main_searchstring siehe /libs/basic/home.php
	$businesscontacts = BusinessContact::getAllBusinessContactsForHome(BusinessContact::ORDER_HOMENAME, $main_searchstring);
} else {
	$businesscontacts = FALSE;
}

?>
<h1><?=$_LANG->get('Suchergebnisse Kunden');?></h1>
<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="30">
		<col>
		<col width="200">
		<col width="200">
		<col width="150">
	</colgroup>
	<? if(count($businesscontacts) > 0 && $businesscontacts != FALSE){?>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('ID')?></td>
			<td class="content_row_header"><?=$_LANG->get('Firma')?></td>
			<td class="content_row_header"><?=$_LANG->get('Ort')?></td>
			<td class="content_row_header"><?=$_LANG->get('Stra&szlig;e')?></td>
			<td class="content_row_header"><?=$_LANG->get('Typ')?></td>
		</tr>
		<?
		$x = 0;
		foreach ($businesscontacts as $bc){
		?>
		<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
				<?=$bc->getId()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
				<img src="./images/icons/user-business.png" title="Kunde">
				<?=$bc->getNameAsLine()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
				<?=$bc->getCity()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
				<?=$bc->getAddress1()?>&nbsp;
			</td>
			<td class="content_row">
			<? 
			if($bc->isExistingCustomer()){
				echo $_LANG->get('Best-Kunde');
			}
			if($bc->isPotentialCustomer()){
				echo $_LANG->get('Soll-Kunde');
			}
			if($bc->isSupplier()){
				echo ", ".$_LANG->get('Lieferant');
			}
			?>
			</td>
		</tr>
		<?
			$contacts = ContactPerson::searchContactPersonsByBussinessContact(ContactPerson::ORDER_NAME, $bc->getId(),$main_searchstring);
			foreach ($contacts as $con){
				?>
					<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
						<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
							&ensp;
						</td>
						<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
							 &emsp;
							 <?if($con->getTitle()=="Frau"){ // Unterschiedliche Icons fuer die Anrede?>
							 	<img src="./images/icons/user-green-female.png" title="Kunde"> &ensp; 
							 <? } else {?>
							 	<img src="./images/icons/user-green.png" title="Kunde"> &ensp;
							 <? } ?> 
							<?=$con->getNameAsLine()?>&nbsp;
						</td>
						<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
							<?=$bc->getCity()?>&nbsp;
						</td>
						<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
							<?=$bc->getAddress1()?>&nbsp;
						</td>
						<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$bc->getId()?>'">
							&ensp;
						</td>
					</tr>		
			<? 
			}
		$x++;
		}
	} else {
		echo '<tr class="'.getRowColor(0) .'"> <td colspan="8" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Keine Kunden gefunden.').'</span>';
		echo '</td></tr>';
	}
	?>
</table>