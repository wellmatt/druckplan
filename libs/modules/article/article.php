<?
//----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			22.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'article.class.php';

switch ($_REQUEST["exec"]) {
	case "delete":
		$del_article = new Article($_REQUEST["did"]);
		$del_article->delete();
		require_once 'article.overview.php';
		break;
	case "edit":
		// Daten setzen und speichern geschieht in der article.edit.php
		require_once 'article.edit.php';
		break;
	case "new":
		require_once 'article.edit.php';
		break;
	case "copy":
		require_once 'article.edit.php';
		break;
	default:
		require_once 'article.overview.php';
		break;
}
?>