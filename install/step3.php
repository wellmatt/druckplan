<div class="row">
    <div class="col-xs-12">
    	<div class="form-header">
    		<div class="form-title form-icon title-icon-user">
    			<b>User</b> Data
    		</div>
    		<div class="steps">
    			Steps 3 - 4
    		</div>
    	</div><!--/ .form-header-->
	</div>
</div><!--/ .row-->

<form action="index.php" method="post" role="form">
    <input type="hidden" name="step" value="4"/>
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
    <!-- prev Data end -->
	<div class="form-wizard">
		<div class="row">
			<div class="col-md-8 col-sm-7">
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="usr_username">Username</label>
							<input id="usr_username" name="usr_username" class="form-icon" placeholder="Username" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="usr_fname">Vorname</label>
							<input id="usr_fname" name="usr_fname" class="form-icon" placeholder="Vorname" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="usr_lname">Scherer</label>
							<input id="usr_lname" name="usr_lname" class="form-icon" placeholder="Nachname" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="usr_pass">Password</label>
							<input id="usr_pass" name="usr_pass" class="form-icon" placeholder="Password" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="usr_mail">E-Mail</label>
							<input id="usr_mail" name="usr_mail" class="form-icon" placeholder="eMail" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
			</div>
		</div><!--/ .row-->
	</div><!--/ .form-wizard-->
	<div class="next">
		<button class="button button-control" type="submit"><span>weiter</span></button>
	</div>

</form><!--/ form-->