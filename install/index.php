<?php 

$step = $_REQUEST["step"];
if ($step == null)
    $step = 1;

?>
<!DOCTYPE html>
<!--[if lte IE 8]>              <html class="ie8 no-js" lang="en">     <![endif]-->
<!--[if IE 9]>					<html class="ie9 no-js" lang="en-US">  <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="not-ie no-js" lang="en"><!--<![endif]--><head>
		<!-- Google Web Fonts
		================================================== -->
		<link href="data/css.css" rel="stylesheet" type="text/css">
		<!-- Basic Page Needs
		================================================== -->
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>contilas - Install</title>	
		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<!-- CSS
		================================================== -->
		<link rel="stylesheet" href="data/tmm_form_wizard_style_demo.css">
		<link rel="stylesheet" href="data/grid.css">
		<link rel="stylesheet" href="data/tmm_form_wizard_layout.css">
		<link rel="stylesheet" href="data/fontello.css">
	</head>
	<body>
		<!-- - - - - - - - - - - - - Content - - - - - - - - - - - - -  -->
		<div id="content">
			<div class="form-container">
				<div id="tmm-form-wizard" class="container substrate">
					<div class="row">
						<div class="col-xs-12">
							<h2 class="form-login-heading">Contilas <span>Install Wizard</span></h2>
						</div>
					</div><!--/ .row-->

					<div class="row stage-container">

						<div class="stage <?php if ($step > 1) echo ' tmm-success '; elseif ($step == 1) echo ' tmm-current ';?> col-md-3 col-sm-3">
							<div class="stage-header head-icon head-icon-payment"></div>
							<div class="stage-content">
								<h3 class="stage-title">Client Data</h3>
								<div class="stage-info">
									Mandanten Daten
								</div>
							</div>
						</div><!--/ .stage-->
						
						<div class="stage <?php if ($step > 2) echo ' tmm-success '; elseif ($step == 2) echo ' tmm-current ';?> col-md-3 col-sm-3">
							<div class="stage-header head-icon head-icon-lock"></div>
							<div class="stage-content">
								<h3 class="stage-title">Datenbank Zugang</h3>
								<div class="stage-info">
									MySQL Datenbank Server
								</div>
							</div>
						</div><!--/ .stage-->
						
						<div class="stage <?php if ($step > 3) echo ' tmm-success '; elseif ($step == 3) echo ' tmm-current ';?> col-md-3 col-sm-3">
							<div class="stage-header head-icon head-icon-user"></div>
							<div class="stage-content">
								<h3 class="stage-title">User Information</h3>
								<div class="stage-info">
									Admin Account anlegen
								</div>
							</div>
						</div><!--/ .stage-->
						
						<div class="stage <?php if ($step > 4) echo ' tmm-success '; elseif ($step == 4) echo ' tmm-current ';?> col-md-3 col-sm-3">
							<div class="stage-header head-icon head-icon-details"></div>
							<div class="stage-content">
								<h3 class="stage-title">Zusammenfassung</h3>
								<div class="stage-info">
									Kontrolle der Daten / Start Installation
								</div>
							</div>
						</div><!--/ .stage-->

					</div><!--/ .row-->

					<?php 
					require_once 'step'.$step.'.php';
					?>
					
				</div><!--/ .container-->
			</div><!--/ .form-container-->
		</div><!--/ #content-->


		<script src="data/jquery.js"></script>
		<!--[if lt IE 9]>
				<script src="js/respond.min.js"></script>
		<![endif]-->
		<script src="data/tmm_form_wizard_custom.js"></script>
</body></html>