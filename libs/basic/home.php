<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       17.07.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------

//echo " ### ".$_REQUEST["mainsearch_string"]." ###";
if ($_REQUEST["mainsearch_string"] != "" && $_REQUEST["mainsearch_string"] != NULL ){
	$_REQUEST["search_orderTitle"] = "";
	$_REQUEST["search_orderId"] = "";
	$_REQUEST["search_invoiceId"] = "";
	$main_searchstring = $_REQUEST["mainsearch_string"];
}

require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';

//$selects = BusinessContact::getAllBusinessContactsByName1(BusinessContact::LOADER_BASICS);
$businesscontact = new BusinessContact((int)$_REQUEST["search_orderCustomer"]); 
?>


<link rel="stylesheet" type="text/css" href="./css/home.css" />

<?
if (isset($_REQUEST["submit_search"])) {
	$str= "<h1>".$_LANG->get('Suchergebnisse Vorg&auml;nge')."</h1>";

	/*if (!empty($_REQUEST["search_orderId"])) {
		$str.= 'Auftrags-Nr. <b>' . $_REQUEST["search_orderId"] . '</b> <br/>';
	}

	if (!empty($_REQUEST["search_orderTitle"])) {
	$str.= 'Auftragstitel <b>' . $_REQUEST["search_orderTitle"] . '</b><br/>';
	}

	if (!empty($_REQUEST["search_invoiceId"])) {
	$str.= 'Rechnungs-Nr. <b>' . $_REQUEST["search_invoiceId"] . '</b> <br/>';
	}

	if ($_REQUEST["search_orderCustomer"] > 0) {
	$str.= ' Kunde <b>'.$businesscontact->getName1().'</b>';
	}
	else {
	//Suche nach Kundennummer ueberfluessig, KDN-Nr. gibt es in dem Sinne nicht
	if (!empty($_REQUEST["search_customerId"])) {
	if (!empty($_REQUEST["search_orderTitle"])) $str.= ', ';
	$str.= 'Kunden-Nr. <b>' . $_REQUEST["search_customerId"] . '</b>';
	}
	}*/

	if (!empty($_REQUEST["mainsearch_string"])){
		// $str ueberschreiben, wenn Suche im Header aktiv war
		// $str = 'Die Suche nach <b> '.$_REQUEST["mainsearch_string"].' </b> ergab:  <br/> ';
	}

	//!empty($_REQUEST["search_customerId"])
	if (!empty( $_REQUEST["search_orderId"]) || !empty($_REQUEST["search_orderTitle"]) ||
			$_REQUEST["search_orderCustomer"] != 0 || !empty($_REQUEST["search_invoiceId"]) ||
			$_REQUEST["mainsearch_string"] ) {

		if ($_REQUEST["search_orderCustomer"] != 0)
			$_REQUEST["search_customerId"] = $_REQUEST["search_orderCustomer"];

		if ($_REQUEST["header_search"]){
			// Wenn die Suche im Header gestartet wurde muss anders gesucht werden
			$res = Order::searchOrderByTitleCustomer($_REQUEST["mainsearch_string"]);
		} else {
			//Hier werden die Werte "AND" verknuepft
			// $res = Order::searchOrderByCustomeridNumberTitle($_REQUEST["search_orderCustomer"], $_REQUEST["search_orderId"], $_REQUEST["search_orderTitle"], $_REQUEST["search_invoiceId"]);
		}

		if (!empty($res)) {
			$str.=' <table width="100%" cellpadding="0" cellspacing="0">
					<colgroup>
					<col width="130">
					<col width="180">
					<col>
					<col width="150">
					</colgroup>
					<tr>
						<td class="content_row_header">' . $_LANG->get('Nr.') . '</td>
						<td class="content_row_header">' . $_LANG->get('Kunde') . '</td>
						<td class="content_row_header">' . $_LANG->get('Titel') . '</td>
						<td class="content_row_header">' . $_LANG->get('Status') . '</td>
				 	</tr> ';
			$x = 0;
			foreach ($res as $val) {
				$str.='
						<tr class="' . getRowColor($x) . '" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
							<td class="content_row pointer" onclick="document.location='."'".'index.php?page=libs/modules/calculation/order.php&exec=edit&id='.$val->getId()."&step=4'".'">
								<a href="index.php?page=libs/modules/calculation/order.php&exec=edit&id=' . $val->getId() . '&step=4">' . $val->getNumber() . '</a>
							</td>
							<td class="content_row pointer" onclick="document.location='."'".'index.php?page=libs/modules/calculation/order.php&exec=edit&id='.$val->getId()."&step=4'".'">
								'.$val->getCustomer()->getNameAsLine().'&nbsp;
							</td>
							<td class="content_row pointer" onclick="document.location='."'".'index.php?page=libs/modules/calculation/order.php&exec=edit&id='.$val->getId()."&step=4'".'">
								' . $val->getTitle() . '
							</td>
							<td  class="content_row pointer" onclick="document.location='."'".'index.php?page=libs/modules/calculation/order.php&exec=edit&id='.$val->getId()."&step=4'".'">
								<table border="0" cellpadding="1" cellspacing="0">
									<tr>
										<td width="25">
											<img class="select" src="./images/status/';
												if ($val->getStatus() == 1)
													$str.= 'red.gif';
												else
													$str.= 'black.gif';
												$str.= '">
										</td>
										<td width="25">
											<img class="select" src="./images/status/';
												if ($val->getStatus() == 2)
													$str.= 'orange.gif';
												else
													$str.= 'black.gif';
												$str.= '">
										</td>
										<td width="25">
											<img class="select" src="./images/status/';
												if ($val->getStatus() == 3)
													$str.= 'yellow.gif';
												else
													$str.= 'black.gif';
												$str.= '">
										</td>
										<td width="25">
											<img class="select" src="./images/status/';
												if ($val->getStatus() == 4)
													$str.= 'lila.gif';
												else
													$str.= 'black.gif';
												$str.= '">
										</td>
										<td width="25">
											<img class="select" src="./images/status/';
												if ($val->getStatus() == 5)
													$str.= 'green.gif';
												else
													$str.= 'black.gif';
												$str.= '">
										</td>
									</tr>
								</table> 
							</td>
						</tr>';
				$x++;
			}
			$str.= "</table>";
		} else {
			$str.= '<table width="100%">
						<tr class="'.getRowColor(0) .'">
							<td class="content_row" align="center">
								<span class="error">'.$_LANG->get('Keine Vorg&auml;nge gefunden').'</span>
							</td>
						</tr>
					</table>';
		}
		/*unset($_REQUEST["search_orderId"]);
		 unset($_REQUEST["search_orderTitle"]);
		unset($_REQUEST["search_orderCustomer"]);
		unset($_REQUEST["search_customerId"]);*/
	} else{
		$str = " ";//.$_LANG->get('Bitte geben Sie eine Auftrags-, Rechnungs- oder Kunden-Nr. ein oder w&auml;hlen Sie einen Kunden aus') . "!";
	}
}

?>
<!-- div class="box1">
	<div id="search">
		<div class="content_row_header">
			<?= $_LANG->get('Suche') ?>
		</div>
		<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="home_search">
			<div class="home_search_label">
				<?=$_LANG->get('Auftrags-Nr.')?>:
			</div>
			<div class="home_search_input">
				<input name="search_orderId" style="width: 200px" value="<?=$_REQUEST["search_orderId"]?>" />
			</div>
			<div class="home_search_label">
				<?=$_LANG->get('Auftragstitel')?>:
			</div>
			<div class="home_search_input">
				<input name="search_orderTitle" style="width: 200px" value="<?=$_REQUEST["search_orderTitle"]?>" />
			</div>
			<div class="home_search_label">
				<?=$_LANG->get('Rechnungs-Nr.')?>:
			</div>
			<div class="home_search_input">
				<input name="search_invoiceId" style="width: 200px"  value="<?=$_REQUEST["search_invoiceId"]?>" />
			</div>
			<div class="home_search_label">
				<?=$_LANG->get('Kundenauswahl')?>:
			</div>
			<div class="home_search_input" name="div_customerId">
				<select style="width: 200px" name="search_orderCustomer"
					onchange="this.options[this.selectedIndex].value">
					<option value="0">
						<?=$_LANG->get('Bitte w&auml;hlen')?>
					</option>
					<?
					foreach ($selects as $select) {
						echo "<option value='" . $select->getId() . "' ";
						if ($businesscontact->getId() == $select->getId()) echo "selected";
						echo ">" . $select->getName1() . "" . ($select->getName2() ? ', ' : '') . "" . $select->getName2() . "</option>";
					}?>
				</select>
			</div>
			
			<?/********
			<div class="home_search_label">
				<?=$_LANG->get('Kunden-Nr.')?>
				:
			</div>
			<div class="home_search_input" name="search_orderId">
				<input name="search_customerId" style="width: 200px" />
			</div>******/?>
			
			<div class="home_search_label"></div>
			<div class="home_search_input" style="height: 50px" name="search_orderId">
				<input type="submit" class="button" name="submit_search"
					value="<?=$_LANG->get('Suche starten')?>" />
			</div>
			<p></p>
			<div id="search_res" style="margin-bottom: 10px;">	
				<p><?=$str?></p>
			</div>
		</form>
	</div>
</div-->

<? /* HomeBildschirm auf 2 Spalten einstellen 
		-> wobei dei Erste Spalte wieder in 2 Spalten geteilt wird 
	*/ ?>

	
    <table width="100%" style="table-layout: fixed">
    	<? // ---------------------------- Suchergebnisse (global) ------------------------------------------------------------ 
    	if (isset($_REQUEST["submit_search"])) { ?>
    		<tr>
    			<td>
    				<!-- h1>Suchergebnisse</h1 -->
    				<div class="box1_home" style="min-height:150px;">
    				
    					<div id="search_res" style="margin-bottom: 10px;">
    						<?require_once("./libs/modules/businesscontact/businesscontact.homesearch.php");		// Geschaeftkontakte	?>
    					</div>
    					<br/>
    					<!--  div id="search_res" style="margin-bottom: 10px;">
    					</div -->
    					<br/>
    					<div id="search_res" style="margin-bottom: 10px;">
    						<?require_once("./libs/modules/tickets/ticket.homesearch.php");							// Tickets ?>
    					</div>
    					<br/>
    					<div id="search_res" style="margin-bottom: 10px;">	
    						<?=$str 																				// Auftraege  ?>
    					</div>
    					<br/>
    					<div id="search_res" style="margin-bottom: 10px;">
    						<?require_once("./libs/modules/schedule/schedule.homesearch.php");						// Planung?>
    					</div>
    					<br/>
    					<div id="search_res" style="margin-bottom: 10px;">
    						<?require_once("./libs/modules/warehouse/warehouse.homesearch.php");					// Lager ?>
    					</div>
    				</div>
    			</td>
    		</tr>
    	<? } ?>
    	<tr>
    		<? // ---------------------------- Tickets -------------------------------------------------------------?>
    		<td valign="top">
    				<?require_once("./libs/modules/tickets/ticket.forme.php");?>
    		</td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    	</tr>
    	<!--<tr>
    		<td valign="top">
    				<?//require_once("./libs/modules/tickets/ticket.due.php");?>
    		</td>
    	</tr> -->	
    	<!-- <tr>
    		<td>&nbsp;</td>
    	</tr>
    	<tr>
    		<td>
        		<? // ---------------------------- Grafiken Auftraege (Uebersichten) -------------------------------------------------- 
        		if($_USER->isAdmin()){?>
        			<? //require_once("./libs/modules/home/overview.php");?>
        			
                <p></p>
                <?}?>
    		</td>
    	</tr> -->
    </table>	
	
	





<? /* ---------------------------- Lager -------------------------------------------------------------*/?>
<?/* 28.01.2014,gln: Anzeige Home ohne Lagerbestaende */?>
<? /* ------------------------------------------------------------------------------------------------*/?>
<?/*<div class="box1" style="min-height:150px;">
<table>
	<colgroup>
		<col width="650">
		<col width="650">
	</colgroup>
	<tr>
		<td valign="top">
			<div class="box1">
				<?require_once("./libs/modules/warehouse/warehouse.recall.php");?>
			</div>
		</td>
		<td valign="top">
			<div class="box1">
				<?require_once("./libs/modules/warehouse/warehouse.lowamount.php");?>
			</div>
		</td>
	</tr>
</table>
</div>
*/?>