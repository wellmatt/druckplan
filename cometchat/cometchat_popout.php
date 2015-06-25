<?php
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
if(!empty($_REQUEST['basedata'])) {
	setcookie($cookiePrefix."data", $_REQUEST['basedata'], 0, "/");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<link rel="shortcut icon" type="image/png" href="favicon32.ico">
	<title>CometChat</title>
	<link type="text/css" href="./cometchatcss.php?cc_theme=synergy" rel="stylesheet" charset="utf-8">
	<script type="text/javascript" src="./cometchatjs.php?cc_theme=synergy" charset="utf-8"></script>
</head>
<body>
</body>
</html>