<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

// error_reporting(-1);
// ini_set('display_errors', 1);

chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/businesscontact/attribute.class.php';
require_once 'libs/modules/organizer/event.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/abonnements/abonnement.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false)
    die("Login failed");

$colinv = new CollectiveInvoice($_REQUEST["ciid"]);
$all_attributes = Attribute::getAllAttributesForCollectiveinvoice();

if($_REQUEST["subexec"] == "save")
{
    // Merkmale speichern
    $colinv->clearAttributes();	// Erstmal alle loeschen und dann nur aktive neu setzen
    $save_attributes = Array();
    $i=1;
    foreach ($all_attributes AS $attribute){
		$allitems = $attribute->getItems();
		foreach ($allitems AS $item){
			if((int)$_REQUEST["attribute_item_check_{$attribute->getId()}_{$item["id"]}"] == 1){
			    if($item["input"] == 1 && $_REQUEST["attribute_item_input_{$attribute->getId()}_{$item["id"]}"] != "" || $item["input"] == 0)
			    {
    				$tmp_attribute["id"] = 0;
    				$tmp_attribute["value"] = 1;
    				$tmp_attribute["attribute_id"] = $attribute->getId();
    				$tmp_attribute["item_id"] = $item["id"];
    				$tmp_attribute["inputvalue"] = $_REQUEST["attribute_item_input_{$attribute->getId()}_{$item["id"]}"];
    				$save_attributes[] = $tmp_attribute;
    				$i++;
			    }
			}
		}
	}
	$colinv->saveActiveAttributes($save_attributes);
    echo '<script language="JavaScript">parent.$.fancybox.close();</script>'; // parent.location.href=parent.location.href;
}
$all_active_attributes = $colinv->getActiveAttributeItemsInput();

?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />


<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />

<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>

<!-- MegaNavbar -->
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- /MegaNavbar -->


<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<!-- /jQuery -->

<script language="javascript" src="../../../jscripts/basic.js"></script>
<!-- FancyBox -->
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Merkmale
			</h3>
		</div>
		<div class="panel-body">
			<form action="collectiveinvoice.attribute.frame.php" method="post" name="abo_form" class="form-horizontal">
				<input type="hidden" name="subexec" value="save">
				<input type="hidden" name="ciid" value="<?= $_REQUEST["ciid"] ?>">

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
						</thead>
						<? foreach ($all_attributes AS $attribute) { ?>
							<tbody>
							<tr>
								<td><?= $attribute->getTitle() ?></td>
								<td>
									<? $allitems = $attribute->getItems(); ?>
										<? $x = 0;
										foreach ($allitems AS $item) {
											if ($x % 5 == 0) echo "<tr>";
											echo '<td width="200px">';
											echo '<input name="attribute_item_check_' . $attribute->getId() . '_' . $item["id"] . '" ';
											echo ' value="1" type="checkbox" onfocus="markfield(this,0)" onblur="markfield(this,1)"';
											if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1) echo "checked";
											echo ">";
											echo "  ";
											echo $item["title"];
											if ($item["input"] == 1) {
												echo ' <input name="attribute_item_input_' . $attribute->getId() . '_' . $item["id"] . '" ';
												echo ' value="';
												echo $all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"];
												echo '" class="form-control" type="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
											}
											echo "</td>";
											if ($x % 5 == 4) echo "</tr>";
											$x++;
										} ?>
								</td>
							</tr>
							</tbody>
						<? } ?>
					</table>
				</div>
				<span class="pull-right">
					<?= $savemsg ?>
					<button class="btn btn-origin btn-success" type="submit">
						<?=$_LANG->get('Speichern')?>
					</button>
				</span>
			</form>
		</div>
	</div>
