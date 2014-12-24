<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       25.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
?>
<script language="javascript">
    function show_res_order(orderId){
        location.href="page=libs/modules/calculation/order.php&exec=edit&id="+orderId+"&step=4";
    }
    
    function show_res_schedule(orderId){
        location.href="index.php?page=libs/modules/schedule/schedule.php&exec=parts&id="+orderId;
    }
</script>
<?php
require_once ('libs/modules/calculation/order.class.php');
require_once ('libs/modules/schedule/schedule.class.php');


if ($_REQUEST["search"]) {
	$res_order = Order::searchOrderByTitleCustomer($_REQUEST["search"]);
	$res_schedules = Schedule::searchScheduleByTitleCustomer($_REQUEST["search"]);
	if (!empty($res_order) || !empty($res_schedules)) {
		
		if(!empty($res_order)){
			$search_result='
			<div class="box1">
        <table width="100%" cellpadding="0" cellspacing="0">
    <colgroup>
        <col width="130">
        <col width="200">
        <col>
        <col width="150">
    </colgroup>
    <tr>
        <td class="content_row_header">' . $_LANG->get('Nummer') . '</td>
        <td class="content_row_header">' . $_LANG->get('Kunde') . '</td>
        <td class="content_row_header">' . $_LANG->get('Titel') . '</td>
        <td class="content_row_header">' . $_LANG->get('Status') . '</td>
    </tr>
    ';
			$x = 0;
			foreach ($res_order as $order){
				if(!empty($res_schedules)){
					foreach($res_schedules as $schedule){
						if($order->getNumber() == $schedule->getNumber()){
							$duplicate_record[$schedule->getId()] = $schedule->getNumber();
						}
					}
				}
				$search_result.='
                <tr class="' . getRowColor($x) . '">
                  <td class="content_row">' . $order->getNumber() . '</td>
                  <td class="content_row">' . $order->getCustomer()->getNameAsLine() . '</td>
                  <td class="content_row">' . $order->getTitle() . '</td>
                  <td class="content_row">   
                    <table border="0" cellpadding="1" cellspacing="0">
                      <tr>
                        <td width="25"><img class="select" src="./images/status/';
				if($order->getStatus() == 1)
				$search_result.= 'red.gif';
				else
				$search_result.= 'black.gif';
				$search_result.= '">
                    </td>
                    <td width="25">
                           <img class="select" src="./images/status/';
				if($order->getStatus() == 2)
				$search_result.= 'orange.gif';
				else
				$search_result.= 'black.gif';
				$search_result.= '">
                    </td>
                    <td width="25">
                            <img class="select" src="./images/status/';
				if($order->getStatus() == 3)
				$search_result.= 'yellow.gif';
				else
				$search_result.= 'black.gif';
				$search_result.= '">
                    </td>
                    <td width="25">
                           <img class="select" src="./images/status/';
				if($order->getStatus() == 4)
				$search_result.= 'lila.gif';
				else
				$search_result.= 'black.gif';
				$search_result.= '">
                    </td>
                    <td width="25">
                           <img class="select" src="./images/status/';
				if($order->getStatus() == 5)
				$search_result.= 'green.gif';
				else
				$search_result.= 'black.gif';
				$search_result.= '">
                    </td>
                    <td><input type="button" name="btn_order" class="search_order" onclick="show_res_order('.$order->getId().')" value="' . $_LANG->get('Kalkulation').'"></td>';
				if($id_s=array_search($order->getNumber(), $duplicate_record)){
					$search_result.='
                	<td><input type="button" name="btn_schedule" class="search_order" onclick="show_res_schedule('.$id_s.')" value="' . $_LANG->get('Planung').'"></td>';
				}
				else {
					$search_result.='
					 <td><input type="button" class="search_order" style="visibility:hidden"></td>';
				}
				$search_result.='
                    </tr>
                </table> </td>
                                </tr>';
				$x++;
			}
			$search_result.=' </table>';
		}

		if(!empty($res_schedules)){
			$search_result2='
        <table width="100%" cellpadding="0" cellspacing="0">
    <colgroup>
        <col width="130">
        <col>
        <col width="150">
    </colgroup>';
			if(empty($res_order)){
				$search_result2.='
				 <tr>
        <td class="content_row_header">' . $_LANG->get('Nummer') . '</td>
        <td class="content_row_header">' . $_LANG->get('Titel') . '</td>
        <td class="content_row_header">' . $_LANG->get('Status') . '</td>
    </tr>';
			}
			$x = 0;
			foreach ($res_schedules as $val) {
				if(!in_array($val->getNumber(), $duplicate_record)){
					$search_result2.='
                    <tr class="' . getRowColor($x) . '">
                    <td class="content_row">
                    ' . $val->getNumber() . '</td>
                        <td class="content_row">' . $val->getObject() . '</td>
                         <td class="content_row">
                           <table border="0" cellpadding="1" cellspacing="0">
                <tr>
                    <td width="25">
                            <img class="select" src="./images/status/';
					if($val->getStatus() == 1)
					$search_result2.= 'red.gif';
					else
					$search_result2.= 'black.gif';
					$search_result2.= '">
                    </td>
                    <td width="25">
                           <img class="select" src="./images/status/';
					if($val->getStatus() == 2)
					$search_result2.= 'orange.gif';
					else
					$search_result2.= 'black.gif';
					$search_result2.= '">
                    </td>
                    <td width="25">
                            <img class="select" src="./images/status/';
					if($val->getStatus() == 3)
					$search_result2.= 'yellow.gif';
					else
					$search_result2.= 'black.gif';
					$search_result2.= '">
                    </td>
                    <td width="25">
                           <img class="select" src="./images/status/';
					if($val->getStatus() == 4)
					$search_result2.= 'lila.gif';
					else
					$search_result2.= 'black.gif';
					$search_result2.= '">
                    </td>
                    <td width="25">
                           <img class="select" src="./images/status/';
					if($val->getStatus() == 5)
					$search_result2.= 'green.gif';
					else
					$search_result2.= 'black.gif';
					$search_result2.= '">
                    </td>
                    <td><input type="button" class="search_order" style="visibility:hidden"></td>
                    <td><input type="button" name="btn_schedule" class="search_order" onclick="show_res_schedule('.$val->getId().')" value="' . $_LANG->get('Planung').'"></td>
                   
                </tr>
                </table> </td>
                                </tr>';
					$x++;
				}
			}
			$search_result2.=' </table></div>';
		}
	} else {
		$search_result = "" . $_LANG->get('Keine Ergebnisse')."!";
	}
}
echo $search_result;
echo $search_result2;

?>









