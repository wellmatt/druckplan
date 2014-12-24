<?
//----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			22.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once ('tradegroup.class.php');

switch ($_REQUEST["exec"]) {
	case "delete":
		$del_tradegroup = new Tradegroup($_REQUEST["id"]);
		$del_tradegroup->delete();
		require_once 'tradegroup.overview.php';
		break;
	case "edit":
		// Daten setzen und speichern geschieht in der article.edit.php
		require_once 'tradegroup.edit.php';
		break;
	case "new":
		require_once 'tradegroup.edit.php';
		break;
	default:
		require_once 'tradegroup.overview.php';
		break;
}

?>