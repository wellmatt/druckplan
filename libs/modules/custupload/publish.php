<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			15.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once('libs/modules/organizer/nachricht.class.php');

// chdir("ftp/".$_CONFIG[$_CONFIG["_MODUS"]]["FTP"]["UPLOAD_DIR"]);

$_REQUEST["file"]    =  trim(addslashes($_REQUEST["file"]));
$cust_id   =  (int)$_REQUEST["customer"];

if($_FILES){
	$orig_name = $_FILES["datei"]["name"];				// Original Name holen 
	$filename = md5(time().$_FILES["datei"]["name"]);	// Hash erzeugen
	$fileext = explode(".", $orig_name);				// Dateiendung holen
	$fileext = $fileext[count($fileext) - 1];
	if(move_uploaded_file($_FILES["datei"]["tmp_name"], "./ftp/cust_uploads/".$filename.".".$fileext)){
		$sql = "INSERT INTO ftpcustuploads
				(ftp_cust_id, ftp_orgname, ftp_hash, ftp_status, 
				 ftp_crtdat, ftp_filesize)
				VALUES
				({$cust_id}, '{$orig_name}', '{$filename}', 0,
				UNIX_TIMESTAMP(), {$_FILES["datei"]["size"]})";
		if ($DB->no_result($sql)){
			$upload_ok = true;
		}
    }
}

/* if ($_FILES["datei"]["error"] > 0){
	echo "Error: " . $_FILES["datei"]["error"] . "<br>";
} else {
	echo "Upload: " . $_FILES["datei"]["name"] . "<br>";
	echo "Type: " . $_FILES["datei"]["type"] . "<br>";
	echo "Size: " . ($_FILES["datei"]["size"] / 1024) . " kB<br>";
	echo "Stored in: " . $_FILES["datei"]["tmp_name"];
}*/


/**

if ($_REQUEST["exec"] == "publish" && $_FILES['datei']['error'] == 0){
	chdir ("../..");
	foreach ($_REQUEST["mails"] as $mailaddr){
		if ($mailaddr != NULL && $mailaddr != ""){
			$text = '<html>
					<head><style type="text/css">'.$_SESSION["_PAGE"]->returnStyle().'</style></head>
							<body class="page">
							Sehr geehrte Damen und Herren,<br><br>
							es wurde eine neue Datei f�r Sie hinterlegt. �ber den folgenden Link gelangen Sie direkt zum Download:<br><br>
							<a href="'.$_CONFIG[$_CONFIG["_MODUS"]]["FTP"]["DOWNLOADURL"].$filehash.'">'.$_CONFIG[$_CONFIG["_MODUS"]]["FTP"]["DOWNLOADURL"].$filehash.'</a><br>';
			if ($_REQUEST["mail_comment"] != NULL && $_REQUEST["mail_comment"] != ""){
				$text .=   "<br>".$_REQUEST["mail_comment"]."<br> <br>";
			}
			$text .=  ' Mit freundlichen Gr��en<br><br>
					'.$_SESSION["user_firstname"].' '.$_SESSION["user_lastname"].'
							</body>
							</html>';
			$sentmails = sendExternalMail("Eine neue Datei wurde f�r Sie bereitgestellt",
					$text,
					$mailaddr,
					"");
			if ($sentmails <= 0)
				$mailerr++;
		}
	}
}
***/

/*
 * Datei per Mail verschicken
*/
$_REQUEST["mail_subject"] = trim($_REQUEST["mail_subject"]);
$_REQUEST["mail_body"] = trim($_REQUEST["mail_body"]);
$nachricht = new Nachricht();

if ($_REQUEST["subexec"] == "send" && $upload_ok)
{

	// Nachricht mit werten F�llen
	$nachricht->setFrom($_USER);
	$nachricht->setSubject($_REQUEST["mail_subject"]);
	$nachricht->setText($_REQUEST["mail_body"]);

	$to = Array();
	foreach(array_keys($_REQUEST) as $key)
	{
		if (preg_match("/mail_touser_(?P<id>\d+)/", $key, $match))
		{
			$to[] = new User($match["id"]);
		} else if (preg_match("/mail_togroup_(?P<id>\d+)/", $key, $match))
		{
			$to[] = new Group($match["id"]);
		} else if (preg_match("/mail_tousercontact_(?P<id>\d+)/", $key, $match))
		{
			$to[] = new UserContact($match["id"]);
		} else if (preg_match("/mail_tobusinesscontact_(?P<id>\d+)/", $key, $match))
		{
			$to[] = new BusinessContact($match["id"]);
		} else if (preg_match("/mail_tocontactperson_(?P<id>\d+)/", $key, $match))
		{
			$to[] = new ContactPerson($match["id"]);
		}

	}
	$nachricht->setTo($to);

	$attach = Array();
	foreach(array_keys($_REQUEST) as $key)
	{
		if (preg_match("/mail_attach_(?P<id>\d+)/", $key, $match))
		{
			$attach[] = new Document($match["id"]);
		}
	}
	$nachricht->setAttachments($attach);

	if ($nachricht->send())
	{
		foreach($attach as $file)
			$file->setSent(1);
		$savemsg = getSaveMessage(true);
	} else
		$savemsg = getSaveMessage(false);
} else
	$nachricht->setTo();
   
// Umleiten auf �bersichtsseite
if ($upload_ok && $mailerr == 0){ ?>
	<script language="JavaScript">
		location.href='index.php?page=<?=$_REQUEST['page']?>&saveok=1&hash=<?=$filehash?>';
	</script>
<? }  ?>

<script language="JavaScript" src="./thirdparty/jquery-1.5.2.js"></script>
<script language="JavaScript">
   function updateMailList() {
      var custid = document.all.customer.value;
      if (custid != -1)
      {
         document.getElementById('sendto').innerHTML = 'Bitte warten, E-Mailadressen werden geladen...';
         $.post( "libs/modules/businesscontact/customer.ajax.php", {giveme: "emaillist", cid: custid},
            function(data) {
             	document.getElementById('sendto').style.display='';
               document.getElementById('sendto').innerHTML = data;
            }
         
         )
      }
   }


   function updateConfidence() {
	      var custid = document.all.customer.value;
	      if (custid != -1)
	      {
	         
	         $.post( "libs/modules/businesscontact/customer.ajax.php", {giveme: "confidence", cid: custid},
	            function(data) {
	               document.getElementById('confstep').value = data;
	            }
	         
	         )
	      }
	   }
   
   function sendFile(form) {
      document.getElementById('loading_message').style.display = 'block';
      submitForm(form);
   }
</script>

<!-- FancyBox -->
<script
	type="text/javascript"
	src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script
	type="text/javascript"
	src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link
	rel="stylesheet" type="text/css"
	href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
$(document).ready(function() {
/*
*   Examples - images
*/

$("a#add_to").fancybox({
				'type'    : 'iframe'
				})
	});
</script>
<!-- /FancyBox -->

<script language="javascript">
function removeMailto(what, id)
{
    if (what == 'user')
    {
        document.getElementById('mail_touser_'+id).disabled = true;
        document.getElementById('touserfield_'+id).style.display = 'none';
    } else if (what == 'group')
    {
        document.getElementById('mail_togroup_'+id).disabled = true;
        document.getElementById('togroupfield_'+id).style.display = 'none';
    } else if (what == 'usercontact')
    {
        document.getElementById('mail_tousercontact_'+id).disabled = true;
        document.getElementById('tousercontactfield_'+id).style.display = 'none';
    } else if (what == 'businesscontact')
    {
        document.getElementById('mail_tobusinesscontact_'+id).disabled = true;
        document.getElementById('tobusinesscontactfield_'+id).style.display = 'none';
    } else if (what == 'contactperson')
    {
        document.getElementById('mail_tocontactperson_'+id).disabled = true;
        document.getElementById('tocontactpersonfield_'+id).style.display = 'none';
    }
}

function removeAttach(id)
{
    document.getElementById('mail_attach_'+id).disabled = true;
    document.getElementById('attachfield_'+id).style.display = 'none';
}
</script>
 <script>
$(function() {
   $( "#customer_search" ).autocomplete({
        delay: 0,
        source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer',
		minLength: 2,
		dataType: "json",
        select: function(event, ui) {
			$('#customer').val(ui.item.value);
			$('#customer_search').val(ui.item.label);
			return false;
        }
    });
});
</script>
<link rel="stylesheet" type="text/css" href="./css/mail.css" />
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" role="form" name="xform_ftppublish" enctype="multipart/form-data">
	<input name="exec" value="publish" type="hidden">
	<input name="doupload" value="1" type="hidden">
	<input name="subexec" value="send" type="hidden">

	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					Neue Datei für Kunden veröffentlichen
				</h3>
		  </div>
		  <div class="panel-body">
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Datei</label>
				  <div class="col-sm-10">
					  <input name="datei" type="file" style="background-color: none; border: none;"">
				  </div>
			  </div>
			  <? $customers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME); ?>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Kunde</label>
				  <div class="col-sm-10">
					  <input name="customer_search" id="customer_search" type="text" class="form-control" onChange="updateMailList();updateConfidence()"/>
					  <input name="customer" id="customer" type="hidden"/>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Empfänger</label>
				  <div class="col-sm-10">
					  <a class="btn btn-xs btn-success" href="libs/modules/organizer/nachrichten.addrcpt.php" class="icon-link"
						 id="add_to">
						  <?=$_LANG->get('hinzufügen')?>
					  </a>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Betreff</label>
				  <div class="col-sm-10">
					  <input type="text" name="mail_subject" class="form-control">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Nachricht</label>
				  <div class="col-sm-10">
					  <textarea rows="5" name="mail_body" id="mail_body" class="form-control"></textarea>
				  </div>
			  </div>
			  <? /*** ?>
			  <tr>
			  <td class="content_row" valign="top">Vertraulichkeit:</td>
			  <td class="content_row">
			  <select class="text" name="confstep" id="confstep" style="width:150px"
			  onfocus="markfield(this,0)" onblur="markfield(this,1)">
			  <option value="4" <? if($_REQUEST["conf_step"] == "4") echo "selected='selected'"?>>&Ouml;ffentlich</option>
			  <option value="3" <? if($_REQUEST["conf_step"] == "3") echo "selected='selected'"?>>Intern</option>
			  <option value="2" <? if($_REQUEST["conf_step"] == "2") echo "selected='selected'"?>>Vertraulich</option>
			  <option value="1" <? if($_REQUEST["conf_step"] == "1") echo "selected='selected'"?>>Geheim</option>
			  </select>
			  </td>
			  </tr>
			  <? ***/ ?>
			  <br>
			  <table border="0" class="content_table" cellpadding="3" cellspacing="0" width="100%">
				  <tr>
					  <td class="content_row_clear">
						  <input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
									onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
					  </td>
					  <td align="right" width="150">
						  <input type="submit" value="<?=$_LANG->get('Upload')?>">
					  </td>
				  </tr>
			  </table>
				<div style="top:0px; left:0px;z-index:100;background-color:#bbbbbb; height:100%; width:100%; position:absolute;opacity:0.8;display:none;" id="loading_message">
					<div style="position:absolute;top:300px;left:400px;height:150px;width:200px;z-index:101;background-color:#ffffff;border:1px solid #000000;opacity:1.0">
						<img src="images/content/loading2.gif"><br><br>
						Daten werden &uuml;bertragen. Dieser Vorgang kann einige Minuten dauern.<br><br>
						Bitte warten ....
					</div>
				</div>
		  </div>
	</div>
</form>














