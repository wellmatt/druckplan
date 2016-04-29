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
if ($_REQUEST["subexec"] == "removegroup")
{
    $user->delGroup(new Group($_REQUEST["gid"]));
    $savemsg = getSaveMessage($user->save());
}

if ($_REQUEST["subexec"] == "addgroup")
{
    $user->addGroup(new Group($_REQUEST["gid"]));
    $savemsg = getSaveMessage($user->save());
}

if ($_REQUEST["subexec"] == "deletemail")
{
	$del_mail = new Emailaddress((int)($_REQUEST["mailid"]));
	$savemsg = getSaveMessage($del_mail->delete());
}

if ($_REQUEST["subexec"] == "save")
{
    
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
    	for($i=0; $i < $_REQUEST["email_quantity"]; $i++){
    		//echo " ---- Hallo ";
    		$tmp_mail = new Emailaddress((int)$_REQUEST["mail_id_{$i}"]);
			$tmp_mail->setLogin(trim(addslashes($_REQUEST["mail_login_{$i}"])));
    		$tmp_mail->setAddress(trim(addslashes($_REQUEST["mail_address_{$i}"])));
    		$tmp_mail->setPassword(trim(addslashes($_REQUEST["mail_password_{$i}"])));
    		$tmp_mail->setHost(trim(addslashes($_REQUEST["mail_host_{$i}"])));
    		$tmp_mail->setPort((int)$_REQUEST["mail_port_{$i}"]);
    	    if((int)$_REQUEST["use_imap_{$i}"] == 1) {
    			$tmp_mail->setUseIMAP(1);
    		} else {
    			$tmp_mail->setUseIMAP(0);
    		}
    	    if((int)$_REQUEST["use_ssl_{$i}"] == 1) {
    			$tmp_mail->setUseSSL(1);
    		} else {
    			$tmp_mail->setUseSSL(0);
    		}
    		$tmp_mail->setUserID($user->getId());
    		if ((int)$_REQUEST["mail_read_{$i}"] == 1 && (int)$_REQUEST["mail_write_{$i}"] == 1){
    			$tmp_mail->setType(2);
    		} else {
    			if ((int)$_REQUEST["mail_write_{$i}"] == 1){
    				$tmp_mail->setType(1);
    			} else {
    				$tmp_mail->setType(0);
    			}
    		}
    		if($tmp_mail->getAddress() != NULL && $tmp_mail->getAddress() != ""){
    			$tmp_mail->save();
    			echo $DB->getLastError();
    		}
    	}	
    }
    $savemsg = getSaveMessage($saver);
    $savemsg .= " ".$DB->getLastError();
     
}

$user = new User($_REQUEST["id"]);

$groups = Group::getAllGroups(Group::ORDER_NAME);
$clients = Client::getAllClients(Client::ORDER_NAME);
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
$all_emails = Emailaddress::getAllEmailaddress(Emailaddress::ORDER_ADDRESS, $user->getId());
?>

<script	type="text/javascript" src="jscripts/timepicker/jquery-ui-timepicker-addon.js"></script>
<link href='jscripts/timepicker/jquery-ui-timepicker-addon.css' rel='stylesheet'/>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>
<script src="thirdparty/ckeditor/ckeditor.js"></script>

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

function addEMailRow(){
	var obj = document.getElementById('table_emails');
	var count = parseInt(document.getElementById('email_quantity').value);
	var insert ='<tr><td class="content_row">';
	insert += '<input type="hidden" name="mail_id_'+count+'" value="0" >';
	insert += '<input type="text" class="text" name="mail_address_'+count+'" style="width: 220px">';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input type="text" class="text" name="mail_login_'+count+'" style="width: 120px">';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input type="text" class="text" name="mail_password_'+count+'" style="width: 120px">';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input type="text" class="text" name="mail_host_'+count+'" style="width: 220px">';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input type="text" class="text" name="mail_port_'+count+'" style="width: 50px">';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="use_imap_'+count+'" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
	insert += ' <?=$_LANG->get('IMAP');?>';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="use_ssl_'+count+'" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
	insert += ' <?=$_LANG->get('SSL');?>';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="mail_read_'+count+'" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
	insert += ' <?=$_LANG->get('Lesen');?>';
	insert += '</td>';
	insert += '<td class="content_row">';
	insert += '<input name="mail_write_'+count+'" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
	insert += ' <?=$_LANG->get('Schreiben');?>';
	insert += '</td>';
	insert += '</tr>';

	count += 1;
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('email_quantity').value = count;
}
</script>
<script language="JavaScript">
$(document).ready(function () {
    $('#user_form').validate({});
});
</script>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
		<a href="#top" class="menu_item">Seitenanfang</a>
		<a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
		<a href="#" class="menu_item" onclick="$('#user_form').submit();">Speichern</a>
	</div>
</div>


<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="user_form" name="user_form"
	  class="form-horizontal" role="form"  onsubmit="return checkpass(new Array(this.user_login, this.user_firstname, this.user_lastname, this.user_email))">
		<input type="hidden" name="exec" value="edit">
	    <input type="hidden" name="subexec" value="save">
	    <input type="hidden" name="id" value="<?=$user->getId()?>">
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				<? if ($user->getId()) echo $_LANG->get('Benutzer &auml;ndern'); else echo $_LANG->get('Benutzer hinzuf&uuml;gen');?>
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

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Benutzername</label>
					  <div class="col-sm-10">
						  <input name="user_login" type="text" class="form-control" required
								 value="<?=$user->getLogin()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Passwort</label>
					  <div class="col-sm-10">
						  <input name="user_login" type="password" class="form-control" required
								 value="<?=$user->getLogin()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Passwort wiederholen</label>
					  <div class="col-sm-10">
						  <input name="user_password_repeat" id="user_password_repeat" class="form-control"
								 type="password"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Benutzertyp</label>
					  <div class="col-sm-10">
						  <select name="user_type"  type="text" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							  <option value="normal">
								  <?=$_LANG->get('Benutzer')?>
							  </option>
							  <option value="admin" <? if($user->isAdmin()) echo "selected";?>>
								  <?=$_LANG->get('Administrator')?>
							  </option>
						  </select>
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Mandant</label>
					  <div class="col-sm-10">
						  <select name="user_client" type="text" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							  <?
							  foreach($clients as $c)
							  {?>
								  <option value="<?=$c->getId()?>"
									  <?if ($user->getClient()->getId() == $c->getId()) echo "selected";?>>
									  <?if(!$c->isActive()) echo '<span color="red">';?>
									  <?=$c->getName()?>
									  <?if(!$c->isActive()) echo '</span>';?>
								  </option>
							  <?}

							  ?>
						  </select>
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Sprache</label>
					  <div class="col-sm-10">
						  <select name="user_lang" type="text" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							  <?
							  foreach($languages as $l)
							  {?>
								  <option value="<?=$l->getId()?>"
									  <?if ($user->getLang()->getId() == $l->getId()) echo "selected";?>>
									  <?=$l->getName()?>
								  </option>
							  <?}

							  ?>
						  </select>
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Benutzer aktiv</label>
					  <div class="col-sm-10">
						  <input name="user_active" class="form-control" type="checkbox" value="1"
							  <? if ($user->isActive() || $_REQUEST["id"] == "") echo "checked";?>
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Telefon-IP</label>
					  <div class="col-sm-10">
						  <input name="user_telefonip"  type="text" class="form-control" value="<?=$user->getTelefonIP()?>"
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Vorname</label>
					  <div class="col-sm-10">
						  <input name="user_firstname" type="text" class="form-control" value="<?=$user->getFirstname()?>" required
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Nachname</label>
					  <div class="col-sm-10">
						  <input name="user_lastname" type="text" class="form-control" value="<?=$user->getLastname()?>" required
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Telefon</label>
					  <div class="col-sm-10">
						  <input name="user_phone" type="text" class="form-control" value="<?=$user->getPhone()?>"
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">E-Mail Adresse</label>
					  <div class="col-sm-10">
						  <input name="user_email" type="text" class="form-control" required value="<?=$user->getEmail()?>"
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Mails weiterleiten</label>
					  <div class="col-sm-10">
						  <input name="user_forwardmail" type="checkbox" value="1" class="form-control"
							  <? if ($user->getForwardMail() || $_REQUEST["id"] == "") echo "checked";?>
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Kalender->Geburtstage</label>
					  <div class="col-sm-10">
						  <input name="user_cal_birthday" type="checkbox" value="1" class="form-control"
							  <? if ($user->getCalBirthday()) echo "checked";?>
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">Kalender->Tickets</label>
					  <div class="col-sm-10">
						  <input name="user_cal_tickets" type="checkbox" value="1" class="form-control"
							  <? if ($user->getCalTickets()) echo "checked";?>
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">'Kalender->Aufträge</label>
					  <div class="col-sm-10">
						  <input name="user_cal_orders" type="checkbox" value="1"  class="form-control"
							  <? if ($user->getCalOrders()) echo "checked";?>
								 onfocus="markfield(this,0)" onblur="markfield(this,1)">
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">'Ges. Monat</label>
					  <div class="col-sm-10">
						  <input type="text" class="form-control" id="w_month" name="w_month" value="<?php echo printPrice($user->getW_month(),2);?>"/>
					  </div>
				  </div>

				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">'Signatur</label>
					  <div class="col-sm-10">
						  <textarea name="user_signature" id="user_signature" type="text" class="form-control"><?=$user->getSignature()?>
						  </textarea>
					  </div>
				  </div>


				  <div class="form-group">
					  <label for="" class="col-sm-2 control-label">'Arbeitsstunden</label>
					  <div class="col-sm-10">
						  <?php
						  unset($whours);
						  unset($times);
						  $times = $user->getWorkinghours();
						  $daynames = Array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
						  ?>
						  <table>
							  <?php
							  for($i=0;$i<7;$i++)
							  {
								  ?>
								  <tr>
									  <td class="content_row_header" valign="top"><?=$_LANG->get($daynames[$i]);?></td>
									  <td class="content_row_clear">
										  <?php
										  $count = 0;
										  if (count($times[$i])>0)
										  {
											  foreach($times[$i] as $whours)
											  {
												  ?>
												  <input id="wotime_<?php echo $i;?>_<?php echo $count;?>_start" type="text" value="<?php echo date("H:i",$whours["start"]);?>" name="wotime[<?php echo $i;?>][<?php echo $count;?>][start]"> bis
												  <input id="wotime_<?php echo $i;?>_<?php echo $count;?>_end" type="text" value="<?php echo date("H:i",$whours["end"]);?>" name="wotime[<?php echo $i;?>][<?php echo $count;?>][end]"></br>
												  <script language="JavaScript">
													  $(document).ready(function () {
														  var startTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_start');
														  var endTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_end');

														  $.timepicker.timeRange(
															  startTimeTextBox,
															  endTimeTextBox,
															  {
																  minInterval: (1000*900), // 0,25hr
																  timeFormat: 'HH:mm',
																  start: {}, // start picker options
																  end: {} // end picker options
															  }
														  );
													  });
												  </script>
												  <?php
												  $count++;
											  }
										  }
										  ?>
										  <input id="wotime_<?php echo $i;?>_<?php echo $count;?>_start" type="text" value="" name="wotime[<?php echo $i;?>][<?php echo $count;?>][start]"> bis
										  <input id="wotime_<?php echo $i;?>_<?php echo $count;?>_end" type="text" value="" name="wotime[<?php echo $i;?>][<?php echo $count;?>][end]"></br>
										  <script language="JavaScript">
											  $(document).ready(function () {
												  var startTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_start');
												  var endTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_end');

												  $.timepicker.timeRange(
													  startTimeTextBox,
													  endTimeTextBox,
													  {
														  minInterval: (1000*900), // 0,25hr
														  timeFormat: 'HH:mm',
														  start: {}, // start picker options
														  end: {} // end picker options
													  }
												  );
											  });
										  </script>
									  </td>
								  </tr>
								  <?php
							  }
							  ?>
					  </div>
				  </div>

			  </div>
		  </div>
	  </div>
</div>




