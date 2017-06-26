<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			14.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'emailaddress.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];
$user = new User($_REQUEST["id"]);

if ($_REQUEST["subexec"] == "deletemail")
{
	$del_mail = new Emailaddress((int)($_REQUEST["mailid"]));
	$savemsg = getSaveMessage($del_mail->delete());
}

if ($_REQUEST["subexec"] == "save")
{
	$allusers = User::getAllUser();
	$usercount = count($allusers);
	if ($usercount >= $_CONFIG->maxusers){
		$savemsg = 'Fehler beim Speichern. Sie haben die maximale Anzahl an Benutzern erreicht.';
	} else {
		$user->setLogin(trim(addslashes($_REQUEST["user_login"])));
		$user->setFirstname(trim(addslashes($_REQUEST["user_firstname"])));
		$user->setLastname(trim(addslashes($_REQUEST["user_lastname"])));
		$user->setEmail(trim(addslashes($_REQUEST["user_email"])));
		$user->setPhone(trim(addslashes($_REQUEST["user_phone"])));
		$user->setSignature(trim($_REQUEST["user_signature"]));
		$user->setActive((int)$_REQUEST["user_active"]);
		$user->setForwardMail((int)$_REQUEST["user_forwardmail"]);
		$user->setClient(new Client((int)$_REQUEST["user_client"]));
		$user->setLang(new Translator((int)$_REQUEST["user_lang"]));
		$user->setTelefonIP(trim(addslashes($_REQUEST["user_telefonip"])));

		$user->setCalBirthday((int)$_REQUEST["user_cal_birthday"]);
		$user->setCalTickets((int)$_REQUEST["user_cal_tickets"]);
		$user->setCalOrders((int)$_REQUEST["user_cal_orders"]);
		$user->setBCshowOnlyOverview((int)$_REQUEST["BCshowOnlyOverview"]);

		$user->setHomepage($_REQUEST["menu_path"]);

		if(isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
			$fileName = $_FILES['avatar']['name'];
			$tmpName = $_FILES['avatar']['tmp_name'];
			$fileSize = $_FILES['avatar']['size'];
			$fileType = $_FILES['avatar']['type'];
			$topath = 'docs/avatars/' . $user->getId() . '.png';
			move_uploaded_file($tmpName, $topath);
		}

		if ($_REQUEST["user_type"] == "admin")
			$user->setAdmin(true);
		else
			$user->setAdmin(false);

		if ($user->getId()>0){
			if ($_REQUEST["user_password"] && $_REQUEST["user_password_repeat"] == $_REQUEST["user_password"])
				$user->setPassword(trim(addslashes($_REQUEST["user_password"])));
		}

		$tmp_wtime_arr = Array();
		if ($_REQUEST["wotime"])
		{
			for($i=0;$i<7;$i++)
			{
				$day_total = 0;
				if (count($_REQUEST["wotime"][$i])>0)
				{
					foreach ($_REQUEST["wotime"][$i] as $wtime)
					{
						if ($wtime["start"]>0 && $wtime["end"]>0)
						{
							$tmp_wtime_arr[$i][] = Array("start"=>strtotime($wtime["start"]),"end"=>strtotime($wtime["end"]));
							$day_total += (strtotime($wtime["end"])-strtotime($wtime["start"]));
						}
					}
				}
				if ($day_total>0)
					$day_total = $day_total/60/60;
				switch ($i)
				{
					case 0:
						$user->setW_su(tofloat($day_total));
						break;
					case 1:
						$user->setW_mo(tofloat($day_total));
						break;
					case 2:
						$user->setW_tu(tofloat($day_total));
						break;
					case 3:
						$user->setW_we(tofloat($day_total));
						break;
					case 4:
						$user->setW_th(tofloat($day_total));
						break;
					case 5:
						$user->setW_fr(tofloat($day_total));
						break;
					case 6:
						$user->setW_sa(tofloat($day_total));
						break;
				}
			}
		}
		$user->setWorkinghours($tmp_wtime_arr);
		$user->setW_month(tofloat($_REQUEST["w_month"]));

		$saver = $user->save();

		if($saver){

			GroupUser::wipeForUser($user);
			$newgroups = $_REQUEST["group"];
			foreach ($newgroups as $key => $value) {
				$array = [
					"user" => $user->getId(),
					"group" => $key
				];
				$groupuser = new GroupUser(0, $array);
				$groupuser->save();
			}

			if ($_REQUEST["mailaddress"]){
				$usermailaddresses = Emailaddress::getAllEmailaddressForUser($user);
				$e = 0;
				foreach ($_REQUEST["mailaddress"] as $mailaddres) {
					$hasaddress = false;
					$tmp_mailaddress = new Emailaddress($mailaddres);
					foreach ($usermailaddresses as $usermailaddress) {
						if ($tmp_mailaddress->getId() == $usermailaddress->getId()){
							$hasaddress = true;
						}
					}
					if (!$hasaddress)
						Emailaddress::assignToUser($tmp_mailaddress, $user);
					if ($e == 0 && count($usermailaddresses) == 0){
						Emailaddress::setDefaultForUser(new Emailaddress($mailaddres), $user);
					}
					$e++;
				}
			}
		}

		if ($_REQUEST["dash"]){
			DashBoard::clearForUser($user);
			$r = 1;
			foreach ($_REQUEST["dash"] as $dashrow) {
				if (count($dashrow)>0){
					$c = 1;
					foreach ($dashrow as $dashitem) {
						$d_e = new DashBoard();
						$d_e->setUser($user);
						$d_e->setRow($r);
						$d_e->setColumn($c);
						$d_e->setModule($dashitem);
						$d_e->save();
						$c++;
					}
				}
				$r++;
			}
		}

		$savemsg = getSaveMessage($saver);
		$savemsg .= " ".$DB->getLastError();
	}
}
$dash_widgets = DashBoard::getWidgets();

$user = new User($_REQUEST["id"]);

$groups = Group::getAllGroups(Group::ORDER_NAME);
$clients = Client::getAllClients(Client::ORDER_NAME);
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
?>

<script	type="text/javascript" src="jscripts/timepicker/jquery-ui-timepicker-addon.js"></script>
<link href='jscripts/timepicker/jquery-ui-timepicker-addon.css' rel='stylesheet'/>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>
<script src="thirdparty/ckeditor/ckeditor.js"></script>
<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript">
	$(function() {
		var editor = CKEDITOR.replace( 'user_signature', {
			// Define the toolbar groups as it is a more accessible solution.
			toolbarGroups: [
				{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
				{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
				{ name: 'links' },
				{ name: 'insert' },
				{ name: 'tools' },
				{ name: 'others' },
				'/',
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
				{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
				{ name: 'styles' },
				{ name: 'colors' }
			]
			// Remove the redundant buttons from toolbar groups defined above.
			//removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
		} );
	} );
</script>

<script language="javascript">
function checkpass(obj){
	var pass1 = document.getElementById('user_password').value;
	var pass2 = document.getElementById('user_password_repeat').value;
	if (pass1 != pass2){
		alert('<?=$_LANG->get('Passw&ouml;rter stimmen nicht &uuml;berein')?>');
		document.getElementById('user_password').focus();
		return false;
	}
	return checkform(obj);
}

function addDashRow(){
	var count = parseInt($('#dash_row_count').val())+1;
	var insert = '<tr>';
	insert += '<td>'+count+'</td>';
	insert += '<td><select name="dash['+count+'][1]" class="form-control">';
	<?php
	foreach ($dash_widgets as $dash_widget) {
		?>
		insert += '<option value="<?=$dash_widget;?>"><?=$dash_widget;?></option>';
		<?php
	}
	?>
	insert += '</select></td>';
	insert += '<td><select name="dash['+count+'][2]" class="form-control">';
	<?php
	foreach ($dash_widgets as $dash_widget) {
	?>
	insert += '<option value="<?=$dash_widget;?>"><?=$dash_widget;?></option>';
	<?php
	}
	?>
	insert += '</select></td>';
	insert += '<td><select name="dash['+count+'][3]" class="form-control">';
	<?php
	foreach ($dash_widgets as $dash_widget) {
	?>
	insert += '<option value="<?=$dash_widget;?>"><?=$dash_widget;?></option>';
	<?php
	}
	?>
	insert += '</select></td>';
	insert += '</tr>';

	$('#dash_table').append(insert);
	$('#dash_row_count').val(count);
}

function addEMailRow(){
	var address = $("#mail_select option:selected").text();
	var addressid = $("#mail_select option:selected").val();
	var insert = '<p>'+address;
	insert += '<input type="hidden" name="mailaddress[]" value="'+addressid+'">';
	insert += '<span style="color: red;" class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().remove();" title="E-Mail-Adresse l&ouml;schen"></span>';
	insert += '</p>';
	$('#mailaddresses').append(insert);
}
function starEmail(ele,mail,user){
	$.ajax({
		type: "POST",
		url: "libs/basic/user/user.ajax.php",
		data: { ajax_action: "star_email", user: user, mailaddress: mail },
		success: function(data)
		{
			$('.glyphicons .glyphicons-star').each(function(){
				$(this).removeClass('glyphicons-star');
				$(this).addClass('glyphicons-star-empty');
			});
			$(ele).removeClass('glyphicons-star-empty');
			$(ele).addClass('glyphicons-star');
		}
	});
}
function removeEmail(ele,mail,user){
	$.ajax({
		type: "POST",
		url: "libs/basic/user/user.ajax.php",
		data: { ajax_action: "remove_email", user: user, mailaddress: mail },
		success: function(data)
		{
			$(ele).parent().remove();
		}
	});
}
</script>
<script language="JavaScript">
$(document).ready(function () {
    $('#user_form').validate({});
});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a#menu_path").fancybox({
			'type'    : 'iframe'
		});
	});
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#user_form').submit();",'glyphicon-floppy-disk');
if ($user->getId()>0){
	$quickmove->addItem('Löschen', '#', "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$user->getId()."');", 'glyphicon-trash', true);

}
echo $quickmove->generate();
// end of Quickmove generation ?>


<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="user_form" name="user_form" enctype="multipart/form-data"
	 class="form-horizontal" onsubmit="return checkpass(new Array(this.user_login, this.user_firstname, this.user_lastname, this.user_email)) ">
	  <input type="hidden" name="exec" value="edit">
	  <input type="hidden" name="subexec" value="save">
	  <input type="hidden" name="id" value="<?=$user->getId()?>">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<? if ($user->getId()) echo $_LANG->get('Benutzer &auml;ndern'); else echo $_LANG->get('Benutzer hinzuf&uuml;gen'); ?>
				<span class="pull-right"><?= $savemsg ?></span>
			</h3>
		</div>
		<div class="panel-body">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Nutzerdaten
					</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Benutzername</label>
								<div class="col-sm-9">
									<input name="user_login" class="form-control" required
										   value="<?= $user->getLogin() ?>" onfocus="markfield(this,0)"
										   onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Passwort</label>
								<div class="col-sm-9">
									<input name="user_password" id="user_password" class="form-control"
										   type="password" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Passwort wdh.</label>
								<div class="col-sm-9">
									<input name="user_password_repeat" id="user_password_repeat"
										   class="form-control"
										   type="password" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Benutzertyp</label>
								<div class="col-sm-9">
									<select name="user_type" class="form-control" onfocus="markfield(this,0)"
											onblur="markfield(this,1)">
										<option value="normal">
											<?= $_LANG->get('Benutzer') ?>
										</option>
										<option value="admin" <? if ($user->isAdmin()) echo "selected"; ?>>
											<?= $_LANG->get('Administrator') ?>
										</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Mandant</label>
								<div class="col-sm-9">
									<select name="user_client" class="form-control" onfocus="markfield(this,0)"
											onblur="markfield(this,1)">
										<?
										foreach ($clients as $c) {
											?>
											<option value="<?= $c->getId() ?>"
												<? if ($user->getClient()->getId() == $c->getId()) echo "selected"; ?>>
												<? if (!$c->isActive()) echo '<span color="red">'; ?>
												<?= $c->getName() ?>
												<? if (!$c->isActive()) echo '</span>'; ?>
											</option>
											<?
										}

										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Sprache</label>
								<div class="col-sm-9">
									<select name="user_lang" class="form-control" onfocus="markfield(this,0)"
											onblur="markfield(this,1)">
										<?
										foreach ($languages as $l) {
											?>
											<option value="<?= $l->getId() ?>"
												<? if ($user->getLang()->getId() == $l->getId()) echo "selected"; ?>>
												<?= $l->getName() ?>
											</option>
											<?
										}

										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Benutzer aktiv</label>
								<div class="col-sm-1">
									<input name="user_active" type="checkbox" class="form-control" value="1"
										<? if ($user->isActive() || $_REQUEST["id"] == "") echo "checked"; ?>
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Telefon-IP</label>
								<div class="col-sm-9">
									<input name="user_telefonip" class="form-control"
										   value="<?= $user->getTelefonIP() ?>"
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Startseite</label>
								<div class="col-sm-9 form-text">
									<input type="hidden" name="menu_path" id="menu_path" value="<?=$user->getHomepage();?>">
									<div>
										<span id="span_menu_path"><?=$user->getHomepage();?></span>
										<a href="libs/basic/menu/modulepath.php" id="menu_path">
											<span class="button"><?=$_LANG->get('Ausw&auml;hlen')?></span>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Vorname</label>
								<div class="col-sm-9">
									<input name="user_firstname" class="form-control"
										   value="<?= $user->getFirstname() ?>" required
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Nachname</label>
								<div class="col-sm-9">
									<input name="user_lastname" class="form-control"
										   value="<?= $user->getLastname() ?>" required
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Telefon</label>
								<div class="col-sm-9">
									<input name="user_phone" class="form-control" value="<?= $user->getPhone() ?>"
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">E-Mail Adresse</label>
								<div class="col-sm-9">
									<input name="user_email" required
										   class="form-control" value="<?= $user->getEmail() ?>"
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Mails weiterleiten</label>
								<div class="col-sm-1">
									<input name="user_forwardmail" class="form-control"
										   type="checkbox" value="1"
										<? if ($user->getForwardMail() || $_REQUEST["id"] == "") echo "checked"; ?>
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kal.->Geburtstage</label>
								<div class="col-sm-1">
									<input name="user_cal_birthday" class="form-control" type="checkbox" value="1"
										<? if ($user->getCalBirthday()) echo "checked"; ?>
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kal.->Tickets</label>
								<div class="col-sm-1">
									<input name="user_cal_tickets" type="checkbox" value="1" class="form-control"
										<? if ($user->getCalTickets()) echo "checked"; ?>
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kal.->Aufträge</label>
								<div class="col-sm-1">
									<input name="user_cal_orders" type="checkbox" value="1" class="form-control"
										<? if ($user->getCalOrders()) echo "checked"; ?>
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">GK nur Übersicht</label>
								<div class="col-sm-1">
									<input name="BCshowOnlyOverview" type="checkbox" value="1" class="form-control"
										<? if ($user->getBCshowOnlyOverview()) echo "checked"; ?>
										   onfocus="markfield(this,0)" onblur="markfield(this,1)">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Arbeitsstunden</h3>
						</div>
						<div class="panel-body">
							<?php
							unset($whours);
							unset($times);
							$times = $user->getWorkinghours();
							$daynames = Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");

							for ($i = 0; $i < 7; $i++) {
								if ($i>0)
									echo '<hr>';
								?>
								<div class="row">
									<div class="col-md-3"><?= $_LANG->get($daynames[$i]); ?></div>
									<div class="col-md-9">
										<?php
										$count = 0;
										if (count($times[$i]) > 0) {
											foreach ($times[$i] as $whours) {
												?>
												<div class="row">
													<div class="col-md-5">
														<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_start"
															   type="text" class="form-control"
															   value="<?php echo date("H:i", $whours["start"]); ?>"
															   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][start]">
													</div>
													<div class="col-md-2">
														bis
													</div>
													<div class="col-md-5">
														<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_end"
															   type="text" class="form-control"
															   value="<?php echo date("H:i", $whours["end"]); ?>"
															   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][end]">
													</div>
													<script language="JavaScript">
														$(document).ready(function () {
															var startTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_start');
															var endTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_end');

															$.timepicker.timeRange(
																startTimeTextBox,
																endTimeTextBox,
																{
																	minInterval: (1000 * 900), // 0,25hr
																	timeFormat: 'HH:mm',
																	start: {}, // start picker options
																	end: {} // end picker options
																}
															);
														});
													</script>
												</div>
												<?php
												$count++;
											}
										}
										?>
										<div class="row">
											<div class="col-md-5">
												<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_start"
													   class="form-control" type="text" value=""
													   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][start]">
											</div>
											<div class="col-md-2">
												bis
											</div>
											<div class="col-md-5">
												<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_end"
													   class="form-control" type="text" value=""
													   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][end]">
											</div>
											<script language="JavaScript">
												$(document).ready(function () {
													var startTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_start');
													var endTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_end');

													$.timepicker.timeRange(
														startTimeTextBox,
														endTimeTextBox,
														{
															minInterval: (1000 * 900), // 0,25hr
															timeFormat: 'HH:mm',
															start: {}, // start picker options
															end: {} // end picker options
														}
													);
												});
											</script>
										</div>
									</div>
								</div>
								<?php
							}
							?>
							<hr>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Ges. Monat *</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="w_month" id="w_month"
										   value="<?php echo printPrice($user->getW_month(), 2); ?>">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Avatar</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Datei</label>
								<div class="col-sm-10">
									<input type="file" id="avatar" name="avatar">
									<span><u>Max 160x160px und 100KB!</u></span>
								</div>
							</div>
							&nbsp;<br>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Aktuell</label>
								<div class="col-sm-10">
									<img src="libs/basic/user/user.avatar.get.php?uid=<?php echo $user->getId(); ?>"
										 width="160" height="160"/>
								</div>
							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Signatur</h3>
						</div>
						<div class="panel-body">
					<textarea name="user_signature" id="user_signature"
							  class="form-control"><?= $user->getSignature() ?></textarea>
						</div>
					</div>

				</div>
			</div>

			<? if ($user->getId()) {

				$groups = Group::getAllGroups();
				$gids = GroupUser::getGroupIdsForUser($user);
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Gruppen</h3>
					</div>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
							<tr>
								<th>Name</th>
								<th>Mitglied</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($groups as $group) {?>
								<tr>
									<td><?php echo $group->getName();?></td>
									<td>
										<div class="form-group">
											<label for="" class="col-sm-2 control-label"></label>
											<div class="col-sm-10">
												<div class="checkbox">
													<label>
														<input type="checkbox" name="group[<?php echo $group->getId();?>]" id="group_<?php echo $group->getId();?>" value="1"
															<?php if (in_array($group->getId(),$gids)){ echo ' checked ';}?>>
													</label>
												</div>
											</div>
										</div>
									</td>
								</tr>
							<?php }?>
							</tbody>
						</table>
					</div>
				</div>

				<?php
				$dashboard_rows = DashBoard::countRowsForUser($user);
				?>
				<input type="hidden" id="dash_row_count" value="<?php echo $dashboard_rows;?>">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
							DashBoard Konfig
							<span class="pull-right" onclick="addDashRow();"><span
									class="glyphicons glyphicons-plus pointer"></span> Reihe</span>
						</h3>
					</div>
					<div class="table-responsive">
						<table class="table table-hover" id="dash_table">
							<thead>
								<tr>
									<th>Reihe</th>
									<th>Spalte 1</th>
									<th>Spalte 2</th>
									<th>Spalte 3</th>
								</tr>
							</thead>
							<tbody>
							<?php
							for ($r = 1; $r <= $dashboard_rows; $r++)
							{
								echo '<tr>';
								echo '<td>'.$r.'</td>';
								for ($c = 1; $c <= 3; $c++)
								{
									$dash_entry = DashBoard::getForUserAndPosition($user,$r,$c);
									if ($dash_entry != null && $dash_entry->getId()>0){
										echo '<td><select name="dash['.$r.']['.$c.']" class="form-control">';
										foreach ($dash_widgets as $dash_widget) {
											if ($dash_widget == $dash_entry->getModule()){
												echo '<option selected value="' . $dash_widget . '">' . $dash_widget . '</option>';
											} else {
												echo '<option value="' . $dash_widget . '">' . $dash_widget . '</option>';
											}
										}
										echo '</select></td>';
									}
								}
								echo '</tr>';
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
							Zugeordnete eMail-Adressen
						</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-2"></div>
							<div class="col-sm-10">
								<div id="mailaddresses">
									<?php
									$usermailaddresses = Emailaddress::getAllEmailaddressForUser($user);
									foreach ($usermailaddresses as $usermailaddress) {
										echo '<p>'.$usermailaddress->getAddress();
										echo '<input type="hidden" name="mailaddress[]" value="'.$usermailaddress->getId().'">';
										echo '<span style="color: red;" class="glyphicons glyphicons-remove pointer" onclick="removeEmail(this,'.$usermailaddress->getId().','.$user->getId().');" title="E-Mail-Adresse l&ouml;schen"></span>';
										if (Emailaddress::isDefault($usermailaddress, $user)){
											echo '<span class="glyphicons glyphicons-star"></span>';
										} else {
											echo '<span class="glyphicons glyphicons-star-empty pointer" title="als Standard setzen" onclick="starEmail(this,'.$usermailaddress->getId().','.$user->getId().');"></span>';
										}
										echo '</p>';
									}
									?>
								</div>
							</div>
						</div>

						<?php
						$mailaddresses = Emailaddress::getAllEmailaddress();
						?>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Hinzufügen</label>
							<div class="col-sm-9">
								<select name="mail_select" id="mail_select" class="form-control">
									<?php
									foreach ($mailaddresses as $item) {
										echo '<option value="' . $item->getId() . '">' . $item->getAddress() . '</option>';
									}
									?>
								</select>
							</div>
							<div class="col-sm-1">
								<span class="glyphicons glyphicons-plus pointer" onclick="addEMailRow()"></span>
							</div>
						</div>
					</div>
				</div>
			<? } // Ende if (Benutzer wird neu erstellt)?>
		</div>
	</div>
</form>