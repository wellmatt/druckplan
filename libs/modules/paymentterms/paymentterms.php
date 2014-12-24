<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			03.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('paymentterms.class.php');

global $_USER;

$payment = new PaymentTerms((int)$_REQUEST['pay_id']);

switch($_REQUEST['exec']){
case 'edit':
	require_once 'paymentterm.edit.php';
	break;
case 'delete':
	$payment->delete();
	$all_payments = PaymentTerms::getAllPaymentConditions();
	require_once 'paymentterm.overview.php';	
	break;
case 'save':
	$payment->setComment(trim(addslashes($_REQUEST["pt_comment"])));
	$payment->setName(trim(addslashes($_REQUEST["pt_name"])));
	$payment->setNettodays((int)$_REQUEST["pt_nettodays"]);
	$payment->setSkonto1((int)$_REQUEST["pt_skonto1"]);
	$payment->setSkonto2((int)$_REQUEST["pt_skonto2"]);
	$payment->setSkontodays1((int)$_REQUEST["pt_skonto_days1"]);
	$payment->setSkontodays2((int)$_REQUEST["pt_skonto_days2"]);
	if ($_REQUEST["pt_shoprel"]==1){
		$payment->setShoprel(1);	
	} else {
		$payment->setShoprel(0);
	}
	$payment->setActive(1);
	$savemsg = getSaveMessage($payment->save());
	require_once 'paymentterm.edit.php';
	break;
default:
	$all_payments = PaymentTerms::getAllPaymentConditions();
	require_once 'paymentterm.overview.php';
}
?>