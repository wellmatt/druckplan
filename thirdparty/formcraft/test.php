<?php 

require_once('config.fc.php');
require_once('function.php');

if (!isset($_SESSION)) {
	session_start();
}


$row = ORM::for_table('builder')->find_one($_GET['id']);

$con = json_decode(stripslashes($row['con']), 1);


if (  $_GET['preview']!=true )
{

	if (!$con[0]['formpage']=='1')
	{
		exit;
	}
}
else
{
	if (!isset($_SESSION['username']))
	{
		header( 'Location: login.php' );
	}
}

?>
<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />	
	<title><?php echo $row['name']; ?></title>
</head>

<body>

	<?php 


	$image = $con[0]['formpage_image'];

	if ($image)
	{
		echo "<img class='logo_form' src='".$image."' style='margin: auto auto; display: block'/><br><br>";
	}
	else
	{
		echo '<br>';
	}

	if ($_GET['preview']==true)
	{
		formcraft($_GET['id'],'','','','','','',true);
	}
	else
	{
		formcraft($_GET['id'], 'popup', '', 'Submit Your Music', '','blue', 'white' );
	}

	?>
	<br><br>
	<style>
		@media screen and (min-width: 960px) {
			body {
				background-color: #fff;
				font-family: Arial;
			}
		}
		.nform
		{
			margin-right: auto;
			margin-left: auto;
		}
	</style>
</body>
</html>
