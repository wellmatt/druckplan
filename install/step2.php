<div class="row">
    <div class="col-xs-12">
    	<div class="form-header">
    		<div class="form-title form-icon title-icon-user">
    			<b>Datenbank</b> Zugang
    		</div>
    		<div class="steps">
    			Steps 2 - 4
    		</div>
    	</div><!--/ .form-header-->
	</div>
</div><!--/ .row-->

<form action="index.php" method="post" role="form">
    <input type="hidden" name="step" value="3"/>
    <!-- prev Data -->
    <!-- Client -->
    <input type="hidden" name="name" value="<?php echo $_REQUEST["name"];?>"/>
    <input type="hidden" name="adr1" value="<?php echo $_REQUEST["adr1"];?>"/>
    <input type="hidden" name="adr2" value="<?php echo $_REQUEST["adr2"];?>"/>
    <input type="hidden" name="adr3" value="<?php echo $_REQUEST["adr3"];?>"/>
    <input type="hidden" name="plz" value="<?php echo $_REQUEST["plz"];?>"/>
    <input type="hidden" name="city" value="<?php echo $_REQUEST["city"];?>"/>
    <!-- Client end-->
    <!-- prev Data end -->
	<div class="form-wizard">
		<div class="row">
			<div class="col-md-8 col-sm-7">
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="db_host">DB-Host</label>
							<input id="db_host" name="db_host" class="form-icon" value="localhost" placeholder="Server Hostname / IP" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="db_name">DB-Name</label>
							<input id="db_name" name="db_name" class="form-icon" value="contilas" placeholder="DB Name" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="db_user">User</label>
							<input id="db_user" name="db_user" class="form-icon" placeholder="DB User" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="db_pass">Password</label>
							<input id="db_pass" name="db_pass" class="form-icon" placeholder="DB Password" required="" type="text">
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