<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('deliveryterms.class.php');

global $_USER;
global $_LANG;
global $_MENU;
global $savemsg;

$delterm = new DeliveryTerms(trim(addslashes($_REQUEST['id'])));

switch($_REQUEST['exec']){
case 'edit':
	require_once('deliveryterms.edit.php');
	break;
case 'delete':
	$delterm->delete();
	require_once('deliveryterms.overview.php');
	break;
default:
	require_once('deliveryterms.overview.php');
}
?>