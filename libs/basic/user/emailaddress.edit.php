<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *  
 */


$mailaddress = new Emailaddress($_REQUEST["id"]);
if($_REQUEST["exec"] == "copy")
{
	$mailaddress->clearID();
}
if($_REQUEST["subexec"] == "save")
{
	$mailaddress->setLogin($_REQUEST["login"]);
	$mailaddress->setAddress($_REQUEST["address"]);
	$mailaddress->setPassword($_REQUEST["password"]);
	$mailaddress->setHost($_REQUEST["host"]);
	$mailaddress->setPort($_REQUEST["port"]);
	$mailaddress->setSmtpHost($_REQUEST["smtp_host"]);
	$mailaddress->setSmtpPort($_REQUEST["smtp_port"]);
	$mailaddress->setSmtpUser($_REQUEST["smtp_user"]);
	$mailaddress->setSmtpPassword($_REQUEST["smtp_password"]);
	if ($_REQUEST["ssl"] == 1){
		$mailaddress->setSsl(1);
	} else {
		$mailaddress->setSsl(0);
	}
	if ($_REQUEST["tls"] == 1){
		$mailaddress->setTls(1);
	} else {
		$mailaddress->setTls(0);
	}
	if ($_REQUEST["smtp_ssl"] == 1){
		$mailaddress->setSmtpSsl(1);
	} else {
		$mailaddress->setSmtpSsl(0);
	}
	if ($_REQUEST["smtp_tls"] == 1){
		$mailaddress->setSmtpTls(1);
	} else {
		$mailaddress->setSmtpTls(0);
	}
	$savemsg = getSaveMessage($mailaddress->save()).$DB->getLastError();
}
?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#email_form').submit();",'glyphicon-floppy-disk');

if ($mailaddress->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$mailaddress->getId()."');", 'glyphicon-trash', true);
}

echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('eMail-Adresse hinzuf&uuml;gen')?>
				<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('eMail-Adresse &auml;ndern')?>
				<?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('eMail-Adresse kopieren')?>
				<span class="pull-right"><?=$savemsg?></span>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="email_form" name="email_form" class="form-horizontal" role="form">
			  <input name="exec" value="edit" type="hidden">
			  <input type="hidden" name="subexec" value="save">
			  <input name="id" value="<?=$mailaddress->getId()?>" type="hidden">

			  <p>* Hinweis: Aktuell werden nur SSL Verbindungen unterstützt</p>
			  <div class="panel panel-default">
			  	  <div class="panel-heading">
			  			<h3 class="panel-title">IMAP Konto</h3>
			  	  </div>
			  	  <div class="panel-body">

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Adresse</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="address" name="address" value="<?=$mailaddress->getAddress()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">User</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="login" name="login" value="<?=$mailaddress->getLogin()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Passwort</label>
						  <div class="col-sm-4">
							  <input type="password" class="form-control" id="password" name="password" value="<?=$mailaddress->getPassword()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Server</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="host" name="host" value="<?=$mailaddress->getHost()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Port</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="port" name="port" value="<?=$mailaddress->getPort()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">SSL</label>
						  <div class="col-sm-10">
							  <div class="checkbox">
								  <label>
									  <input type="checkbox" name="ssl" id="ssl" value="1" <?php if ($mailaddress->getSsl()) echo ' checked ';?>>
								  </label>
							  </div>
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">TLS</label>
						  <div class="col-sm-10">
							  <div class="checkbox">
								  <label>
									  <input type="checkbox" name="tls" id="tls" value="1" <?php if ($mailaddress->getTls()) echo ' checked ';?>>
								  </label>
							  </div>
						  </div>
					  </div>
			  	  </div>
			  </div>
			  <div class="panel panel-default">
			  	  <div class="panel-heading">
			  			<h3 class="panel-title">SMTP Konto</h3>
			  	  </div>
			  	  <div class="panel-body">

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">User</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="smtp_user" name="smtp_user" value="<?=$mailaddress->getSmtpUser()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Passwort</label>
						  <div class="col-sm-4">
							  <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?=$mailaddress->getSmtpPassword()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Server</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?=$mailaddress->getSmtpHost()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Port</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="smtp_port" name="smtp_port" value="<?=$mailaddress->getSmtpPort()?>">
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">SSL</label>
						  <div class="col-sm-10">
							  <div class="checkbox">
								  <label>
									  <input type="checkbox" name="smtp_ssl" id="smtp_ssl" value="1" <?php if ($mailaddress->getSmtpSsl()) echo ' checked ';?>>
								  </label>
							  </div>
						  </div>
					  </div>

					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">TLS</label>
						  <div class="col-sm-10">
							  <div class="checkbox">
								  <label>
									  <input type="checkbox" name="smtp_tls" id="smtp_tls" value="1" <?php if ($mailaddress->getSmtpTls()) echo ' checked ';?>>
								  </label>
							  </div>
						  </div>
					  </div>
			  	  </div>
			  </div>
		  </form>
	  </div>
</div>