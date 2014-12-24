<?
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once("libs/modules/calculation/order.class.php");
require_once("libs/modules/businesscontact/businesscontact.class.php");

require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/calculation/calculation.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$all_orders = Order::getAllOrders(Order::ORDER_NUMBER);
foreach ($all_orders as $order){
	if($order->getStatus() >= 3){
	
		$my_numbers = Array();
		foreach(Array("Y-m-d","Y-m","Y") as $date_string){
			if (date($date_string) == date($date_string,$order->getCrtdat())){
		
				$all_calculations = Calculation::getAllCalculations($order);
		
				$material = 0;
				$fertigung = 0;
				$rabattaufab = 0;
				$marge = 0;
				$position_price = 0;
				foreach ($all_calculations as $calculation){
					if($calculation->getState() == 1){
						$machines = Machineentry::getAllMachineentries($calculation->getId());
						foreach ($machines as $m) {
							$fertigung += $m->getPrice(); 				//Fertigkosten
						}
						
						$rabattaufab += ($calculation->getSummaryPrice()*$calculation->getDiscount()/100) + ($calculation->getAddCharge());
						$marge += $calculation->getSummaryPrice()*$calculation->getMargin()/100;
						
						$material += $calculation->getPaperContent()->getSumPrice($calculation->getPaperCount(Calculation::PAPER_CONTENT) + $calculation->getPaperContentGrant()); 
						$material += $calculation->getPaperAddContent()->getSumPrice($calculation->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calculation->getPaperAddContentGrant());
						$material += $calculation->getPaperAddContent2()->getSumPrice($calculation->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calculation->getPaperAddContent2Grant());
						$material += $calculation->getPaperAddContent3()->getSumPrice($calculation->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calculation->getPaperAddContent3Grant());
						$material += $calculation->getPaperEnvelope()->getSumPrice($calculation->getPaperCount(Calculation::PAPER_ENVELOPE) + $calculation->getPaperEnvelopeGrant());
										
						 $all_positions = CalculationPosition::getAllCalculationPositions($calculation->getId());
						 if (count($all_positions) > 0){
								foreach ($all_positions AS $pos){
									$position_price += $pos->getCalculatedPrice();
								}
						}
					}
				}

				$my_numbers[$date_string]['rabauf'] = round($rabattaufab,2);
				$my_numbers[$date_string]['marge'] = round($marge,2);
				$my_numbers[$date_string]['fertigung'] = round($fertigung,2);
				$my_numbers[$date_string]['material'] = round($material,2);
				$my_numbers[$date_string]['position_price'] = round($position_price,2);
				$my_numbers[$date_string]['rabauf_q'] = round($rabattaufab / count($all_orders),2);
				$my_numbers[$date_string]['marge_q'] = round($marge / count($all_orders),2);
				$my_numbers[$date_string]['fertigung_q'] = round($fertigung / count($all_orders),2);
				$my_numbers[$date_string]['material_q'] = round($material / count($all_orders),2);
				$my_numbers[$date_string]['position_price_q'] = round($position_price / count($all_orders),2);
			} else {
				$my_numbers[$date_string]['rabauf'] = 0;
				$my_numbers[$date_string]['marge'] = 0;
				$my_numbers[$date_string]['fertigung'] = 0;
				$my_numbers[$date_string]['material'] = 0;
				$my_numbers[$date_string]['position_price'] = 0;
				$my_numbers[$date_string]['rabauf_q'] = 0;
				$my_numbers[$date_string]['marge_q'] = 0;
				$my_numbers[$date_string]['fertigung_q'] = 0;
				$my_numbers[$date_string]['material_q'] = 0;
				$my_numbers[$date_string]['position_price_q'] = 0;
			}
		}
    }
}

foreach(Array("Y-m-d","Y-m","Y") as $date_string){
	// echo "Array ".$date_string.":";
	// print_r($my_numbers[$date_string]);
	// echo "</br>";
	// echo $my_numbers[$date_string]['marge'];
}
 ?>
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
 <!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery.validate.min.js"></script>
<!-- /jQuery -->
<script type="text/javascript" src="../../../jscripts/jqBarGraph.1.1.min.js"></script>
 
 <h2>Auftragsstatistik</h2>
<div class="box1">
	<table border="0">
		<tr align="center">
			<th>Gesamt</th>
			<th>&nbsp</th>
			<th>Durchschnitt</th>
		</tr>
		<tr align="center">
			<td valign="middle"> <div id="order_g" class="box2"></div></td>
			<td valign="middle">&nbsp</td>
			<td valign="middle"> <div id="order_q" class="box2"></div></td>
		</tr>		
	</table>
</div>

 <script type="text/javascript">
	$(document).ready(function() {
		arrayOfDataMulti_g = new Array(
			[[<? echo $my_numbers["Y-m-d"]['rabauf'].",".$my_numbers["Y-m-d"]['marge'].",".$my_numbers["Y-m-d"]['fertigung'].",".$my_numbers["Y-m-d"]['material'].",".$my_numbers["Y-m-d"]['position_price']; ?>],'Tag'],
			[[<? echo $my_numbers["Y-m"]['rabauf'].",".$my_numbers["Y-m"]['marge'].",".$my_numbers["Y-m"]['fertigung'].",".$my_numbers["Y-m"]['material'].",".$my_numbers["Y-m"]['position_price']; ?>],'Monat'],
			[[<? echo $my_numbers["Y"]['rabauf'].",".$my_numbers["Y"]['marge'].",".$my_numbers["Y"]['fertigung'].",".$my_numbers["Y"]['material'].",".$my_numbers["Y"]['position_price']; ?>],'Jahr']
		);
		

		$('#order_g').jqBarGraph({
		   data: arrayOfDataMulti_g,
		   colors: ['#242424','#437346','#97D95C','#228B22','#FF4040'] ,
		   postfix: '€',
		   legends: ['Rabatt+Auf/Ab','Marge','Fertigung','Materialkosten','Zus.Positionen'],
		   legend: true
		}); 
		
		arrayOfDataMulti_q = new Array(
			[[<? echo $my_numbers["Y-m-d"]['rabauf_q'].",".$my_numbers["Y-m-d"]['marge_q'].",".$my_numbers["Y-m-d"]['fertigung_q'].",".$my_numbers["Y-m-d"]['material_q'].",".$my_numbers["Y-m-d"]['position_price_q']; ?>],'Tag'],
			[[<? echo $my_numbers["Y-m"]['rabauf_q'].",".$my_numbers["Y-m"]['marge_q'].",".$my_numbers["Y-m"]['fertigung_q'].",".$my_numbers["Y-m"]['material_q'].",".$my_numbers["Y-m"]['position_price_q']; ?>],'Monat'],
			[[<? echo $my_numbers["Y"]['rabauf_q'].",".$my_numbers["Y"]['marge_q'].",".$my_numbers["Y"]['fertigung_q'].",".$my_numbers["Y"]['material_q'].",".$my_numbers["Y"]['position_price_q']; ?>],'Jahr']
		);

		$('#order_q').jqBarGraph({
		   data: arrayOfDataMulti_q,
		   colors: ['#242424','#437346','#97D95C','#228B22','#FF4040'] ,
		   postfix: '€',
		   legends: ['Rabatt+Auf/Ab','Marge','Fertigung','Materialkosten','Zus.Positionen'],
		   legend: true
		}); 
				
	});
</script> 	
