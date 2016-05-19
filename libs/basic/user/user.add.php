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
	  onsubmit="return checkpass(new Array(this.user_login, this.user_firstname, this.user_lastname, this.user_email))">
	  <input type="hidden" name="exec" value="edit">
	  <input type="hidden" name="subexec" value="save">
	  <input type="hidden" name="id" value="<?=$user->getId()?>">

	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
					<? if ($user->getId()) echo $_LANG->get('Benutzer &auml;ndern'); else echo $_LANG->get('Benutzer hinzuf&uuml;gen');?>
					<span class="pull-right"><?=$savemsg?></span>
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
						  <div class="table-responsive">
						  	<table class="table table-hover">
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Benutzername');?> *</td>
											  <td class="content_row_clear">
												  <input name="user_login" style="width: 300px" class="text" required
														 value="<?=$user->getLogin()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Passwort');?> *</td>
											  <td class="content_row_clear">
												  <input name="user_password" id="user_password" style="width: 300px" class="text"
														 type="password" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Passwort wiederholen');?> *</td>
											  <td class="content_row_clear">
												  <input name="user_password_repeat" id="user_password_repeat" style="width: 300px" class="text"
														 type="password"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Benutzertyp');?></td>
											  <td class="content_row_clear"><select name="user_type" style="width: 300px"
																					class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
													  <option value="normal">
														  <?=$_LANG->get('Benutzer')?>
													  </option>
													  <option value="admin" <? if($user->isAdmin()) echo "selected";?>>
														  <?=$_LANG->get('Administrator')?>
													  </option>
												  </select>
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Mandant')?></td>
											  <td class="content_row_clear"><select name="user_client"
																					style="width: 300px" class="text" onfocus="markfield(this,0)"
																					onblur="markfield(this,1)">
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
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Sprache')?></td>
											  <td class="content_row_clear"><select name="user_lang" style="width: 300px"
																					class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Benutzer aktiv')?></td>
											  <td class="content_row_clear"><input name="user_active" type="checkbox"
																				   value="1"
													  <? if ($user->isActive() || $_REQUEST["id"] == "") echo "checked";?>
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header">&nbsp;</td>
											  <td class="content_row_clear">&nbsp;</td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Telefon-IP');?> *</td>
											  <td class="content_row_clear">
												  <input name="user_telefonip" style="width: 300px" class="text" value="<?=$user->getTelefonIP()?>"
														 onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header">&nbsp;</td>
											  <td class="content_row_clear">&nbsp;</td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Vorname');?> *</td>
											  <td class="content_row_clear"><input name="user_firstname"
																				   style="width: 300px" class="text" value="<?=$user->getFirstname()?>" required
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Nachname');?> *</td>
											  <td class="content_row_clear"><input name="user_lastname"
																				   style="width: 300px" class="text" value="<?=$user->getLastname()?>" required
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Telefon');?></td>
											  <td class="content_row_clear"><input name="user_phone" style="width: 300px"
																				   class="text" value="<?=$user->getPhone()?>"
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('E-Mail Adresse');?> *</td>
											  <td class="content_row_clear"><input name="user_email" style="width: 300px" required
																				   class="text" value="<?=$user->getEmail()?>"
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Mails weiterleiten');?></td>
											  <td class="content_row_clear"><input name="user_forwardmail"
																				   type="checkbox" value="1"
													  <? if ($user->getForwardMail() || $_REQUEST["id"] == "") echo "checked";?>
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Kalender->Geburtstage');?></td>
											  <td class="content_row_clear"><input name="user_cal_birthday"
																				   type="checkbox" value="1"
													  <? if ($user->getCalBirthday()) echo "checked";?>
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Kalender->Tickets');?></td>
											  <td class="content_row_clear"><input name="user_cal_tickets"
																				   type="checkbox" value="1"
													  <? if ($user->getCalTickets()) echo "checked";?>
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Kalender->Aufträge');?></td>
											  <td class="content_row_clear"><input name="user_cal_orders"
																				   type="checkbox" value="1"
													  <? if ($user->getCalOrders()) echo "checked";?>
																				   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
										  </tr>
										  <tr>
											  <td class="content_row_header">&nbsp;</td>
											  <td class="content_row_clear">&nbsp;</td>
										  </tr>
									  </table>
								  </td>
								  <td valign="top">
									  <b>Arbeitsstunden:</b>
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
										  <tr>
											  <td class="content_row_header"><?=$_LANG->get('Ges. Monat *');?></td>
											  <td class="content_row_clear"><input style="width: 40px;" type="text" id="w_month" name="w_month" value="<?php echo printPrice($user->getW_month(),2);?>"/> </td>
										  </tr>
									  </table>
								  </td>
							  </tr>

							  <tr>
								  <td class="content_row_header"><?=$_LANG->get('Signatur');?></td>
								  <td class="content_row_clear">&nbsp;</td>
							  </tr>
							  <tr>
								  <td class="content_row_clear" colspan="4">
				<textarea name="user_signature" id="user_signature" style="width: 450px; height: 200px">
					<?=$user->getSignature()?>
				</textarea>
								  </td>
							  </tr>
						  </table>
					  </div>
				</div>
		  </div>
	</div>

	<br/>
	<? if ($user->getId()) { ?>
		<div class="panel panel-default">
			  <div class="panel-heading">
					<h3 class="panel-title">
						'Mitglied von
					</h3>
			  </div>
			  <div class="panel-body">
					  <table width="500px">
						  <colgroup>
							  <col width="150">
							  <col>
							  <col width="60">
						  </colgroup>
						  <tr>
							  <td class="content_row_header"><?=$_LANG->get('Gruppenname')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
						  </tr>
						  <? foreach($groups as $g) {
							  if($user->isInGroup($g)){?>
								  <tr>
									  <td class="content_row_clear"><?=$g->getName()?></td>
									  <td class="content_row_clear"><?=$g->getDescription()?></td>
									  <td class="content_row_clear"><a href="#"  class="icon-link"
																	   onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$user->getId()?>&subexec=removegroup&gid=<?=$g->getId()?>'"><img
												  src="images/icons/minus.png" /> </a></td>
								  </tr>
							  <?	}
						  } ?>
					  </table>
					  <br>
					  <table width="500px">
						  <colgroup>
							  <col width="150">
							  <col>
							  <col width="60">
						  </colgroup>
						  <tr>
							  <td class="content_header" colspan="2"><?=$_LANG->get('Verf&uuml;gbare Gruppen')?>
							  </td>
						  </tr>
						  <tr>
							  <td class="content_row_header"><?=$_LANG->get('Gruppenname')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
						  </tr>
						  <? foreach($groups as $g) {
							  if(!$user->isInGroup($g)){?>
								  <tr>
									  <td class="content_row_clear"><?=$g->getName()?></td>
									  <td class="content_row_clear"><?=$g->getDescription()?></td>
									  <td class="content_row_clear"><a href="#"  class="icon-link"
																	   onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$user->getId()?>&subexec=addgroup&gid=<?=$g->getId()?>'"><img
												  src="images/icons/plus.png" /> </a></td>
								  </tr>
							  <?	}
						  } ?>
					  </table>
			  </div>
	    </div>

		<br>
		<input 	type="hidden" name="email_quantity" id="email_quantity"
				  value="<? if(count($all_emails) > 0) echo count($all_emails); else echo "1";?>">
		<div class="panel panel-default">
			  <div class="panel-heading">
					<h3 class="panel-title">
						IMAP Konten
					</h3>
			  </div>
			  <div class="panel-body">
				  <div class="table-responsive">
					  <table class="table table-hover">
						  <tr>
							  <td class="content_row_header"><?=$_LANG->get('eMail')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Login')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Passwort')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Host/Server')?></td>
							  <td class="content_row_header"><?=$_LANG->get('Port')?></td>
							  <td class="content_row_header">&ensp;</td>
							  <td class="content_row_header">&ensp;</td>
							  <td class="content_row_header"><?=$_LANG->get('Rechte')?></td>
							  <td class="content_row_header">&ensp;</td>
							  <td class="content_row_header"><img src="images/icons/plus.png" class="pointer icon-link" onclick="addEMailRow()"></td>
						  </tr>
						  <? 	$x = 0;
						  if(count($all_emails) > 0){
							  foreach($all_emails as $emailaddress) {?>
								  <tr>
									  <td class="content_row">
										  <input type="hidden" class="text" name="mail_id_<?=$x?>" value="<?=$emailaddress->getId()?>">
										  <input type="text" class="text" name="mail_address_<?=$x?>" value="<?=$emailaddress->getAddress()?>"
												 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 120px">
									  </td>
									  <td class="content_row">
										  <input type="text" class="text" name="mail_login_<?=$x?>" value="<?=$emailaddress->getLogin()?>"
												 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 220px">
									  </td>
									  <td class="content_row">
										  <input type="password" class="text" name="mail_password_<?=$x?>" value="<?=$emailaddress->getPassword()?>"
												 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 120px">
									  </td>
									  <td class="content_row">
										  <input type="text" class="text" name="mail_host_<?=$x?>" value="<?=$emailaddress->getHost()?>"
												 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 220px">
									  </td>
									  <td class="content_row">
										  <input type="text" class="text" name="mail_port_<?=$x?>" value="<?=$emailaddress->getPort()?>"
												 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 50px">
									  </td>
									  <td class="content_row">
										  <input name="use_imap_<?=$x?>" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
											  <? if ($emailaddress->getUseIMAP()) echo 'checked="checked"';?> >
										  <?=$_LANG->get('IMAP');?>
									  </td>
									  <td class="content_row">
										  <input name="use_ssl_<?=$x?>" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
											  <? if ($emailaddress->getUseSSL()) echo 'checked="checked"';?> >
										  <?=$_LANG->get('SSL');?>
									  </td>
									  <td class="content_row">
										  <input name="mail_read_<?=$x?>" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
											  <? if ($emailaddress->readable()) echo 'checked="checked"';?> >
										  <?=$_LANG->get('Lesen');?>
									  </td>
									  <td class="content_row">
										  <input name="mail_write_<?=$x?>" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
											  <? if ($emailaddress->writeable()) echo 'checked="checked"';?> >
										  <?=$_LANG->get('Schreiben');?>
									  </td>
									  <td class="content_row">
										  <a onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&subexec=deletemail&mailid=<?=$emailaddress->getId()?>&id=<?=$user->getId()?>')"  class="icon-link"
											 href="#"><img src="images/icons/cross-script.png" title="<?=$_LANG->get('E-Mail-Adresse l&ouml;schen')?>"></a>
									  </td>
								  </tr>
								  <?			$x++;
							  }
						  } else {?>
							  <tr>
								  <td class="content_row">
									  <input type="hidden" name="mail_ip_0" value="0" >
									  <input type="text" class="text" name="mail_address_0"
											 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 120px">
								  </td>
								  <td class="content_row">
									  <input type="text" class="text" name="mail_login_0"
											 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 220px">
								  </td>
								  <td class="content_row">
									  <input type="text" class="text" name="mail_password_0"
											 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 120px">
								  </td>
								  <td class="content_row">
									  <input type="text" class="text" name="mail_host_0"
											 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 220px">
								  </td>
								  <td class="content_row">
									  <input type="text" class="text" name="mail_port_0"
											 onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 50px">
								  </td>
								  <td class="content_row">
									  <input name="use_imap" type="checkbox" value="1" checked onfocus="markfield(this,0)" onblur="markfield(this,1)">
									  <?=$_LANG->get('IMAP');?>
								  </td>
								  <td class="content_row">
									  <input name="use_ssl" type="checkbox" value="1" checked onfocus="markfield(this,0)" onblur="markfield(this,1)">
									  <?=$_LANG->get('SSL');?>
								  </td>
								  <td class="content_row">
									  <input name="mail_read_<?=$x?>" type="checkbox" checked value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									  <?=$_LANG->get('Lesen');?>
								  </td>
								  <td class="content_row">
									  <input name="mail_write_<?=$x?>" type="checkbox" checked value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									  <?=$_LANG->get('Schreiben');?>
								  </td>
							  </tr>
						  <?	} ?>
					  </table>
				  </div>
				  </br>
				  <? } // Ende if (Benutzer wird neu erstellt)?>
			  </div>
		</div>
</form>