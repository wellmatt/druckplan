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
    $user->setSignature(trim(addslashes($_REQUEST["user_signature"])));
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

    if ($_REQUEST["user_password"] && $_REQUEST["user_password_repeat"] == $_REQUEST["user_password"])
        $user->setPassword(trim(addslashes($_REQUEST["user_password"])));
    
    $saver = $user->save();
    
    if($saver){
    	for($i=0; $i < $_REQUEST["email_quantity"]; $i++){
    		//echo " ---- Hallo ";
    		$tmp_mail = new Emailaddress((int)$_REQUEST["mail_id_{$i}"]);
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
$groups = Group::getAllGroups(Group::ORDER_NAME);
$clients = Client::getAllClients(Client::ORDER_NAME);
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
$all_emails = Emailaddress::getAllEmailaddress(Emailaddress::ORDER_ADDRESS, $user->getId());
?>

<!-- TinyMCE -->
<script
	type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "undo,redo,|,link,unlink,anchor,cleanup,code,|,forecolor,backcolor,|,sub,sup",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		formats : {
			alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
			aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
			alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
			alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
			bold : {inline : 'span', 'classes' : 'bold'},
			italic : {inline : 'span', 'classes' : 'italic'},
			underline : {inline : 'span', 'classes' : 'underline', exact : true},
			strikethrough : {inline : 'del'}
		},
      
      width: "690px", height: "150px", paste_remove_styles: true, paste_auto_cleanup_on_paste : true, force_br_newlines: true, forced_root_block: '',
	});
</script>
<!-- /TinyMCE -->

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

<table width="100%">
	<tr>
		<td width="200" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <? if ($user->getId()) echo $_LANG->get('Benutzer &auml;ndern'); else echo $_LANG->get('Benutzer hinzuf&uuml;gen');?>
		</td>
		<td></td>
		<td width="200" class="content_header" align="right"><?=$savemsg?></td>
	</tr>
</table>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form"
	onsubmit="return checkpass(new Array(this.user_login, this.user_firstname, this.user_lastname, this.user_email))">
<div class="box1">
	<?// Objekte werden an die checkform() durchgereicht?>
	<input type="hidden" name="exec" value="edit"> <input type="hidden"
		name="subexec" value="save"> <input type="hidden" name="id"
		value="<?=$user->getId()?>">
	<table width="500px" border="0" cellpadding="0" cellspacing="0">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Benutzername');?> *</td>
			<td class="content_row_clear">
				<input name="user_login" style="width: 300px" class="text" 
				value="<?=$user->getLogin()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Passwort');?></td>
			<td class="content_row_clear">
				<input name="user_password" id="user_password" style="width: 300px" class="text" 
				type="password" onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Passwort wiederholen');?></td>
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
				style="width: 300px" class="text" value="<?=$user->getFirstname()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Nachname');?> *</td>
			<td class="content_row_clear"><input name="user_lastname"
				style="width: 300px" class="text" value="<?=$user->getLastname()?>"
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
			<td class="content_row_clear"><input name="user_email" style="width: 300px"
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
			<td class="content_row_header"><?=$_LANG->get('Signatur');?></td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
		<tr>
			<td class="content_row_clear" colspan="2"><textarea name="user_signature"
					style="width: 450px; height: 200px">
					<?=$user->getSignature()?>
				</textarea>
			</td>
		</tr>
		<tr>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
	</table>
</div>
<br/>
<?// Speicher & Navigations-Button ?>
<table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup> 
    <tr>
        <td class="content_row_header">
        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
        </td>
        <td class="content_row_clear" align="right">
        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
        </td>
    </tr>
</table>
<br>
<? if ($user->getId()) { ?>
<div class="box2">
<table width="500px">
	<colgroup>
		<col width="150">
		<col>
		<col width="60">
	</colgroup>
	<tr>
		<td class="content_header" colspan="2"><h1><?=$_LANG->get('Mitglied von')?></h1></td>
	</tr>
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
<br>
<div class="box2">
<input 	type="hidden" name="email_quantity" id="email_quantity" 
			value="<? if(count($all_emails) > 0) echo count($all_emails); else echo "1";?>">
<table width="100%" id="table_emails">
	<colgroup>
		<col width="150">
		<col width="150">
		<col width="150">
		<col width="70">
		<col width="70">
		<col width="150">
		<col>
	</colgroup>
	<tr>
		<td class="content_header" colspan="2"><h1><?=$_LANG->get('E-Mail-Adressen')?></h1></td>
	</tr>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('Adresse')?></td>
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
						onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 220px">
				</td>
				<td class="content_row">
					<input type="text" class="text" name="mail_password_<?=$x?>" value="<?=$emailaddress->getPassword()?>"
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
				<input name="use_imap" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?=$_LANG->get('IMAP');?>
			</td>
			<td class="content_row">	
				<input name="use_ssl" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?=$_LANG->get('SSL');?>
			</td>
			<td class="content_row">				
				<input name="mail_read_<?=$x?>" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?=$_LANG->get('Lesen');?>
			</td>
			<td class="content_row">	
				<input name="mail_write_<?=$x?>" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?=$_LANG->get('Schreiben');?>
			</td>
		</tr>
<?	} ?>
</table>
</div>
</br>

<div class="box2">
<table width="100%" id="table_user_times">
	<colgroup>
		<col width="150">
		<col width="150">
		<col width="150">
		<col width="70">
		<col>
	</colgroup>
	<tr>
		<td class="content_header" colspan="2"><h1><?=$_LANG->get('Arbeitszeiten')?></h1></td>
	</tr>


	<tr>
		<td class="content_row_header"><?=$_LANG->get('Checkin')?></td>
		<td class="content_row_header"><?=$_LANG->get('Pause (H:M:S)')?></td>
		<td class="content_row_header"><?=$_LANG->get('Checkout')?></td>
		<td class="content_row_header"><?=$_LANG->get('Gesamt (H:M:S)')?></td>
	</tr>
<? 	
	
	$user_id = $user->getId();

	$user_time = array();
	$sql = "SELECT * FROM user_times WHERE user_id = {$user_id}";
	$res = $DB->select($sql);
	
	foreach ($res as $r){
		$tmp_pause = 0;
		$sql2 = "SELECT * FROM user_times_pause WHERE user_times_id = {$r["id"]}";
		$res2 = $DB->select($sql2);
		foreach ($res2 as $r2){
			if ((!empty($r2["end"])) && (!empty($r2["start"])))
			{			
				$tmp_pause = $tmp_pause + ($r2["end"] - $r2["start"]);
			}
		}
		$user_time[] = array($r["id"],$r["checkin"],$r["checkout"],$tmp_pause);
	}
	
	// print_r($user_time);
	
	$x = 0;
		foreach($user_time as $ut) {?>
			<tr>
				<td class="content_row"><? echo date("d.m.Y H:i", $ut[1]); ?></td>
				<?
					$hours = floor($ut[3] / 3600);
					$mins = floor(($ut[3] - ($hours*3600)) / 60);
					$secs = floor($ut[3] % 60);
				?>
				<td class="content_row"><? echo $hours.":".$mins.":".$secs ; ?></td>
				<td class="content_row"><? echo date("d.m.Y H:i", $ut[2]); ?></td>
				<?
					$hours = floor((($ut[2] - $ut[1]) - $ut[3]) / 3600);
					$mins = floor(((($ut[2] - $ut[1]) - $ut[3]) - ($hours*3600)) / 60);
					$secs = floor((($ut[2] - $ut[1]) - $ut[3]) % 60);
				?>
				<td class="content_row"><? echo $hours.":".$mins.":".$secs ; ?></td>
			</tr>
		<? } ?>
</table>
</div>


<br/>
<?// Speicher & Navigations-Button ?>
<table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup> 
    <tr>
        <td class="content_row_header">
        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
        </td>
        <td class="content_row_clear" align="right">
        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
        </td>
    </tr>
</table>
<? } // Ende if (Benutzer wird neu erstellt)?>
</form>