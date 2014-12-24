<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			24.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

/* TODO sehr gut ueberlegen, wie der Warenkorb abgebildet wird !
 * 		- erstmal in der Session speichern
 * IDEE Zus. in einem Cookie speichern, dann kann man auch darauf zurueckgreifen,
 * 		wenn ein unerwarteter Fehler auftrat (PC-Absturz, Verbindung weg, ...)
 * 		und man kann sich Sachen zusammenklicken und spaeter bestellen.
 */

$shopping_basket = new Shoppingbasket();
$shopping_basket_entrys = Array ();

if ($_SESSION["shopping_basket"]){
	$shopping_basket = $_SESSION["shopping_basket"];
	$shopping_basket_entrys = $shopping_basket->getEntrys();
}
?>

<?//---------------------------- WARENKORB ------------------------------------------------- ?>
<div class="shopping_menu"><b>Warenkorb</b></div>

<? $x=0; 
if (count($shopping_basket_entrys)>0){ //Artikel auflisten?>
	<form method="post" action="index.php" name="form_additems">
		<input type="hidden" name="pid" value="<?=(int)$_REQUEST["pid"]?>">
		<input type="hidden" name="tgid" value="<?=(int)$tg_id?>">
		<input type="hidden" name="exec" value="add_item">
		<table>
			<colgroup>
				<col>
				<col width="15">
				<col width="5">
				<col width="25">
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
				<td class="content_row_header" align="right"><?=$_LANG->get('Stk.')?></td>
				<td class="content_row_header">&ensp;</td>
				<td class="content_row_header"><?=$_LANG->get('Preis')?></td>
			</tr>
			<?foreach ($shopping_basket_entrys as $entry){?>
				<tr>
					<td class="content_row">
					<?	if($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){
							$tmp_pid = 60;	
							$tmp_obj = "articleid=".$entry->getId();
							$tmp_exec= "exec=showArticleDetails";
						} else if($entry->getType() == Shoppingbasketitem::TYPE_PRODUCTS){
							 $tmp_pid = 100;
							 $tmp_obj = "productid=".$entry->getId();
							 $tmp_exec= "exec=edit";
						}else if($entry->getType() == Shoppingbasketitem::TYPE_PERSONALIZATION){
							 $tmp_pid = 40;
							 $tmp_obj = "persoorderid=".$entry->getId();
							 $tmp_exec= "exec=edit";
						}?>
						<a href="index.php?pid=<?=$tmp_pid?>&<?=$tmp_obj?>&<?=$tmp_exec?>">
								<?=substr($entry->getTitle(),0,21)?></a>
					</td> 
					<td class="content_row" align="right"><?=$entry->getAmount()?> </td>
					<td class="content_row">&ensp;</td>
					<td class="content_row" align="right"><?=printPrice($entry->getPrice())?>&euro; </td>  
				</tr>
			<?$x++;
			}?>
		</table>
		<p  align="right">
			<a href="index.php?pid=80">Warenkorb ansehen</a>
		</p>
	</form>
<? } else {?>
<div class="menu_lvl2"> <br/> <?=$_LANG->get("Der Warenkorb ist leer");?> <br/><br/><br/><br/></div>
<?} //ENDE alse(count(Eintraege))?>


