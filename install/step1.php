<div class="row">
    <div class="col-xs-12">
    	<div class="form-header">
    		<div class="form-title form-icon title-icon-user">
    			<b>Client</b> Data
    		</div>
    		<div class="steps">
    			Steps 1 - 4
    		</div>
    	</div><!--/ .form-header-->
	</div>
</div><!--/ .row-->

<form action="index.php" method="post" role="form">
    <input type="hidden" name="step" value="2"/>
	<div class="form-wizard">
		<div class="row">
			<div class="col-md-8 col-sm-7">
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="name">Name</label>
							<input id="name" name="name" class="form-icon" placeholder="Bitte Firmennamen inkl. Zusätze eingeben" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="adr1">Adresse 1</label>
							<input id="adr1" name="adr1" class="form-icon" placeholder="Adresszeile 1" required="" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="adr2">Adresse 2</label>
							<input id="adr2" name="adr2" class="form-icon" placeholder="Adresszeile 2" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<fieldset class="input-block">
							<label for="adr3">Adresse 3</label>
							<input id="adr3" name="adr3" class="form-icon" placeholder="Adresszeile 3" type="text">
						</fieldset><!--/ .input-->
					</div>
				</div><!--/ .row-->
				<div class="row">
					<div class="col-md-4 col-sm-4">
						<fieldset class="input-block">
							<label for="plz">PLZ</label>
							<input id="plz" name="plz" placeholder="PLZ" required="" type="text">
						</fieldset><!--/ .code-->
					</div>
					
					<div class="col-md-8 col-sm-8">
						<fieldset class="input-block">
							<label for="city">Ort</label>
							<input id="city" name="city" placeholder="Ort" required="" type="text">
						</fieldset><!--/ .code-->
					</div>
				</div><!--/ .row-->
				<span>**weitere Details können später ergänzt werden!</span>
			</div>
		</div><!--/ .row-->
	</div><!--/ .form-wizard-->
	
	<div class="next">
		<button class="button button-control" type="submit"><span>weiter</span></button>
		<div class="button-divider"></div>
	</div>

</form><!--/ form-->