<?php

// Get all submitted data
// Client -->
$cl_name = $_REQUEST["name"];
$cl_adr1 = $_REQUEST["adr1"];
$cl_adr2 = $_REQUEST["adr2"];
$cl_adr3 = $_REQUEST["adr3"];
$cl_plz = $_REQUEST["plz"];
$cl_city = $_REQUEST["city"];
// Client end-->
// DB -->
$db_host = $_REQUEST["db_host"];
$db_name = $_REQUEST["db_name"];
$db_user = $_REQUEST["db_user"];
$db_pass = $_REQUEST["db_pass"];
// DB end-->
// User -->
$usr_username = $_REQUEST["usr_username"];
$usr_fname = $_REQUEST["usr_fname"];
$usr_lname = $_REQUEST["usr_lname"];
$usr_pass = $_REQUEST["usr_pass"];
$usr_mail = $_REQUEST["usr_mail"];
// User end-->

?>

<div class="row">
    <div class="col-xs-12">
    	<div class="form-header">
    		<div class="form-title form-icon title-icon-user">
    			<b>Zusammenfassung</b>
    		</div>
    		<div class="steps">
    			Steps 4 - 4
    		</div>
    	</div><!--/ .form-header-->
	</div>
</div><!--/ .row-->

<form action="install.php" method="post" role="form">
    <input type="hidden" name="step" value="install"/>
    <!-- prev Data -->
    <!-- Client -->
    <input type="hidden" name="name" value="<?php echo $_REQUEST["name"];?>"/>
    <input type="hidden" name="adr1" value="<?php echo $_REQUEST["adr1"];?>"/>
    <input type="hidden" name="adr2" value="<?php echo $_REQUEST["adr2"];?>"/>
    <input type="hidden" name="adr3" value="<?php echo $_REQUEST["adr3"];?>"/>
    <input type="hidden" name="plz" value="<?php echo $_REQUEST["plz"];?>"/>
    <input type="hidden" name="city" value="<?php echo $_REQUEST["city"];?>"/>
    <!-- Client end-->
    <!-- DB -->
    <input type="hidden" name="db_host" value="<?php echo $_REQUEST["db_host"];?>"/>
    <input type="hidden" name="db_name" value="<?php echo $_REQUEST["db_name"];?>"/>
    <input type="hidden" name="db_user" value="<?php echo $_REQUEST["db_user"];?>"/>
    <input type="hidden" name="db_pass" value="<?php echo $_REQUEST["db_pass"];?>"/>
    <!-- DB end-->
    <!-- User -->
    <input type="hidden" name="usr_username" value="<?php echo $_REQUEST["usr_username"];?>"/>
    <input type="hidden" name="usr_fname" value="<?php echo $_REQUEST["usr_fname"];?>"/>
    <input type="hidden" name="usr_lname" value="<?php echo $_REQUEST["usr_lname"];?>"/>
    <input type="hidden" name="usr_pass" value="<?php echo $_REQUEST["usr_pass"];?>"/>
    <input type="hidden" name="usr_mail" value="<?php echo $_REQUEST["usr_mail"];?>"/>
    <!-- User end-->
    <!-- prev Data end -->
    <div class="form-wizard">
    	<div class="row">
    		<div class="col-md-8 col-sm-7">
    			<div class="row">
    				<div class="col-md-12 col-sm-12">
    				    <b>Client:</b></br>
    				    Firma: '<?php echo $cl_name;?>'</br>
    				    Adr. 1: '<?php echo $cl_adr1;?>'</br>
    				    Adr. 2: '<?php echo $cl_adr2;?>'</br>
    				    Adr. 3: '<?php echo $cl_adr3;?>'</br>
    				    PLZ: '<?php echo $cl_plz;?>'</br>
    				    Ort: '<?php echo $cl_city;?>'</br>
    				</div>
    			</div><!--/ .row-->
    			<div class="row">
    				<div class="col-md-12 col-sm-12">
    				    <b>DB:</b></br>
    				    Host: '<?php echo $db_host;?>'</br>
    				    Name: '<?php echo $db_name;?>'</br>
    				    User: '<?php echo $db_user;?>'</br>
    				    Pass: '<?php echo $db_pass;?>'</br>
    				</div>
    			</div><!--/ .row-->
    			<div class="row">
    				<div class="col-md-12 col-sm-12">
    				    <b>DB:</b></br>
    				    Host: '<?php echo $db_host;?>'</br>
    				    Name: '<?php echo $db_name;?>'</br>
    				    User: '<?php echo $db_user;?>'</br>
    				    Pass: '<?php echo $db_pass;?>'</br>
    				</div>
    			</div><!--/ .row-->
    			<div class="row">
    				<div class="col-md-12 col-sm-12">
    				    <b>User:</b></br>
    				    Username: '<?php echo $usr_username;?>'</br>
    				    Vorname: '<?php echo $usr_fname;?>'</br>
    				    Nachname: '<?php echo $usr_lname;?>'</br>
    				    Pass: '<?php echo $usr_pass;?>'</br>
    				    eMail: '<?php echo $usr_mail;?>'</br>
    				</div>
    			</div><!--/ .row-->
    		</div>
    	</div><!--/ .row-->
    </div><!--/ .form-wizard-->
	<div class="next">
		<button class="button button-control" type="submit"><span>installieren</span></button>
	</div>

</form><!--/ form-->