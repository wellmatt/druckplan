<?php


if (!class_exists('PDO'))
{
	echo json_encode(array('done'=>'error','message'=>"PDO extension is required to use FormCraft with MySQL, which is not enabled on your PHP installation"));
	exit;
}
if(!function_exists('curl_version'))
{
	echo json_encode(array('done'=>'error','message'=>"cURL extension is required to use FormCraft, which is not enabled on your PHP installation"));
	exit;
}

if (isset($_GET['logout']))
{
	session_start();
	session_unset();
	session_destroy();
	session_write_close();
}

if (!isset($_SESSION)) {
	session_start();
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>FormCraft - Log In</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link href='css/style.css' rel='stylesheet' type='text/css'>
	<link href='css/boxes.css' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
	<style>
		@-moz-keyframes appear {
			0% {
				-webkit-transform: scale(0.90) translate3d(0, 0, 0);
				-moz-transform: scale(0.90) translate3d(0, 0, 0);
			}

			100% {
				-webkit-transform: scale(1) translate3d(0, 0, 0);
				-moz-transform: scale(1) translate3d(0, 0, 0);
			}
		}
		@-webkit-keyframes appear {
			0% {
				-webkit-transform: scale(0.90) translate3d(0, 0, 0);
				-moz-transform: scale(0.90) translate3d(0, 0, 0);
			}

			100% {
				-webkit-transform: scale(1) translate3d(0, 0, 0);
				-moz-transform: scale(1) translate3d(0, 0, 0);
			}
		}
		@-o-keyframes appear {
			0% {
				-webkit-transform: scale(0.90) translate3d(0, 0, 0);
				-moz-transform: scale(0.90) translate3d(0, 0, 0);
			}

			100% {
				-webkit-transform: scale(1) translate3d(0, 0, 0);
				-moz-transform: scale(1) translate3d(0, 0, 0);
			}
		}
		@-ms-keyframes appear {
			0% {
				-webkit-transform: scale(0.90) translate3d(0, 0, 0);
				-moz-transform: scale(0.90) translate3d(0, 0, 0);
			}

			100% {
				-webkit-transform: scale(1) translate3d(0, 0, 0);
				-moz-transform: scale(1) translate3d(0, 0, 0);
			}
		}
		@keyframes appear {
			0% {
				-webkit-transform: scale(0.90) translate3d(0, 0, 0);
				-moz-transform: scale(0.90) translate3d(0, 0, 0);
			}

			100% {
				-webkit-transform: scale(1) translate3d(0, 0, 0);
				-moz-transform: scale(1) translate3d(0, 0, 0);
			}
		}

		html
		{
			height: 100%;
		}
		body
		{
			min-height: 100%;
			font-family: 'Open Sans', Arial, sans-serif;
			background: #f7f7f7;

		}
		.login
		{
			margin-top: 50px;
			margin-left: auto;
			margin-right: auto;
			left: 50%;
			right: auto;
			width: 320px;
			height: 300px;
			border: 1px solid #ddd;
			border-bottom: 0px;
			background-color: #fff;
			box-shadow: 0px 2px 2px -1px #777;
			-moz-box-shadow: 0px 2px 2px -1px #777;
			-webkit-box-shadow: 0px 2px 2px -1px #777;
			border-radius: 5px;
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			-webkit-animation-duration: 600ms;
			-webkit-animation-timing-function: ease;
			-webkit-animation-name: appear;
			animation-duration: 600ms;
			animation-timing-function: ease;
			animation-name: appear;
			-moz-animation-duration: 600ms;
			-moz-animation-timing-function: ease;
			-moz-animation-name: appear;
		}
		.login h1
		{

			vertical-align: bottom;
			display: block;
			font-size: 48px;
			letter-spacing: -3px;
			color: #4488ee;
			text-align: left;
			line-height: 120%;
			margin-top: 0px;
			font-weight: 600;
			text-align: center;
			margin-bottom: 0px;
			margin-top: 6%;
		}
		.login input, .login button
		{
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			width: 88%;
			margin: 6% auto 0% auto;
			font-size: 16px;
			color: #777;
			box-shadow: none;
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			padding: 10px 15px;
			-webkit-border-radius: 3px; 
			-moz-border-radius: 3px;
			border-radius: 3px;
			border: 1px solid #ccc;
			line-height: 24px;
			display: block;
		}
		.login input:nth-child(2)
		{
			border-top-left-radius: 0px;
			border-top-right-radius: 0px;
			-moz-border-top-left-radius: 0px;
			-moz-border-top-right-radius: 0px;
			-webkit-border-top-left-radius: 0px;
			-webkit-border-top-right-radius: 0px;
			margin-top: 0px;
			border-top: 0px;
		}
		.login input:nth-child(1)
		{
			border-bottom-left-radius: 0px;
			border-bottom-right-radius: 0px;
			-moz-border-bottom-left-radius: 0px;
			-moz-border-bottom-right-radius: 0px;
			-webkit-border-bottom-left-radius: 0px;
			-webkit-border-bottom-right-radius: 0px;
		}
		.login button
		{
			background-color: #4488ee;
			border: 1px solid #4488ee;
			color: #fff;
			opacity: .85;
			cursor: pointer;

			transition: border-radius 0.50s ease-out, width 0.20s;
			-webkit-transition: border-radius 0.50s ease-out, width 0.20s;
			-moz-transition: border-radius 0.50s ease-out, width 0.20s;
			-ms-transition: border-radius 0.50s ease-out, width 0.20s;

		}
		button.loading_class
		{
			background-image: url('images/loader_4.gif');
			background-size: 22px 22px;
			background-position: center;
			background-repeat: no-repeat;
			padding: 10px 15px;
			opacity: 1;
			-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
			filter: alpha(opacity=100);
			width: 46px;
			border-radius: 30px;
			-moz-border-radius: 30px;
			-webkit-border-radius: 30px;
			min-height: 46px;
		}
		.login input:focus
		{
			border-color: #4488ee;
		}
		.login button
		{
			opacity: 1;
		}
		#response
		{
			width: 88%;
			margin: 0% auto 0% auto;
			font-size: 13px;
			color: #666;
			padding: 10px 15px;
			-webkit-border-radius: 3px; 
			-moz-border-radius: 3px;
			border-radius: 3px;
			line-height: 24px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			text-align: center;
		}

	</style>
</head>

<body>

	<div class='login'>
		<h1>FormCraft</h1>
		<form action='javascript:submit_formcraft_login();' id='login_form'>
			<input type='user' name='user' onblur="if(this.value=='')this.value='username';" onfocus="if(this.value=='username')this.value='';" value='<?php if (isset($_GET['user'])) {echo $_GET['user'];} else {echo 'username';} ?>'>
			<input type='password' name='password' onblur="if(this.value=='')this.value='password';" onfocus="if(this.value=='password')this.value='';" value='<?php if (isset($_GET['pass'])) {echo $_GET['pass'];} else {echo 'password';} ?>'>
			<button type='submit' class='submit'>Log In</button>
			<div id='response'></div>
		</form>
	</div>
	<script src='js/jquery.min.js'></script>
	<script>
		window.ajax = 'function.php';
		function submit_formcraft_login()
		{

			jQuery('#response').hide();

  // animate Submit Button
  var sub = jQuery('.submit').text();
  jQuery('.submit').html('');
  jQuery('.submit').addClass('loading_class');
  jQuery('#response').html('');
  jQuery('.submit').prop("disabled",true);

  var pd = jQuery('.submit').css('height');
  jQuery('.submit').addClass('loading_class');

  jQuery.ajax({
  	dataType: 'json',
  	type: "POST",
  	url: window.ajax,
  	data: 'action=formcraft_login&'+jQuery('#login_form').serialize(),
  	success: function (response)
  	{
  		jQuery('.submit').prop("disabled",false);
  		jQuery('.submit').text(sub);
  		jQuery('.submit').removeClass('loading_class');

  		if (response.done=='login')
  		{
  			window.location.href='index.php';
  			jQuery('#response').html(response.message);
  		}
  		else if (response.done=='error')
  		{
  			jQuery('#response').html(response.message);
  		}
  		else
  		{
  			jQuery('#response').html('Unknown response.');
  		}
  		jQuery('#response').show();
  }, // Success
  error: function (response) 
  {
  	jQuery('.submit').prop("disabled",false);
  	jQuery('.submit').text(sub);
  	jQuery('.submit').removeClass('loading_class');
  	jQuery('#response').html('Unknown response.');
  	jQuery('#response').show();
  }
});

}
</script>
</body>