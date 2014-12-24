<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       08.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$_REQUEST["mail_subject"] = trim($_REQUEST["mail_subject"]);
$_REQUEST["mail_body"] = trim($_REQUEST["mail_body"]);
$nachricht = new Nachricht();

if($_REQUEST["contact_type"] == "systemuser")
{
    $to[] = new User((int)$_REQUEST["contact_id"]);
    $nachricht->setTo($to);
} else if($_REQUEST["contact_type"] == "usercontact")
{
    $to[] = new UserContact((int)$_REQUEST["contact_id"]);
    $nachricht->setTo($to);
} else if($_REQUEST["contact_type"] == "businesscontact")
{
    $to[] = new BusinessContact((int)$_REQUEST["contact_id"]);
    $nachricht->setTo($to);
} else if($_REQUEST["contact_type"] == "contactperson")
{
    $to[] = new ContactPerson((int)$_REQUEST["contact_id"]);
    $nachricht->setTo($to);    
}

if ($_REQUEST["subexec"] == "send")
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

    if ($nachricht->send())
    {
        ?>
        <script language="javascript">document.location='index.php?page=<?=$_REQUEST['page']?>&retval=1';</script>
        <? 
    } else
        $savemsg = getSaveMessage(false);
} else if ($_REQUEST["subexec"] == "answer")
{
    
    $oldmsg = new Nachricht((int)$_REQUEST["answer_id"]);
    $nachricht->setSubject("AW: ".$oldmsg->getSubject());
    $nachricht->setTo(Array($oldmsg->getFrom()));
    $newtext = "<br><br>
Am ".date('d.m.Y', $oldmsg->getCreated())." um ".date('H:m:s', $oldmsg->getCreated())." Uhr schrieb ".$oldmsg->getFrom()->getNameAsLine().":<br>
<br>
".$oldmsg->getText();
    $nachricht->setText($newtext);
    $nachricht->setFrom($_USER);
    $oldmsg->setAnswered(true);
    
    unset($oldmsg);
} else if ($_REQUEST["subexec"] == "forward")
{
    
    $oldmsg = new Nachricht((int)$_REQUEST["forward_id"]);
    $nachricht->setSubject("WG: ".$oldmsg->getSubject());
    $newtext = "<br><br>
Weitergeleitete Nachricht:
<br>
<table>
    <tr>
        <td><b>Von:</b></td><td>{$oldmsg->getFrom()->getNameAsLine()}</td>
    </tr>
    <tr>
        <td><b>An:</b></td>
        <td>
            "; foreach($oldmsg->getTo() as $to) { $newtext .= $to->getNameAsLine().", "; }; $newtext .= "
        </td>
    </tr>
    <tr>
        <td><b>Datum:</b></td><td>".date('d.m.Y - H:m:s', $oldmsg->getCreated())."
    </tr>
</table>
".$oldmsg->getText();
    $nachricht->setText($newtext);
    $nachricht->setFrom($_USER);
    
    unset($oldmsg);
} 
    

//theme : "advanced" oder "simple"
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
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,outdent,indent,blockquote,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "undo,redo,|,link,unlink,anchor,cleanup,code,|,forecolor,backcolor,|,sub,sup,|,tablecontrols",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/main.css",

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
      
      paste_remove_styles: true, paste_auto_cleanup_on_paste : true, force_br_newlines: true, forced_root_block: '',
	});
</script>
<!-- /TinyMCE -->

<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
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
</script>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
<input type="hidden" name="exec" value="newmail">
<input type="hidden" name="subexec" value="send">
<div class="newmailHeader">
    <table width="100%">
        <colgroup>
            <col width="150">
            <col>
        </colgroup>
        <tr>
            <td><b><?=$_LANG->get('Empf&auml;nger')?></b></td>
            <td id="td_mail_to" name="td_mail_to">
            <? 
                foreach($nachricht->getTo() as $to)
                {
                    // Falls Empf�nger Benutzer -> anzeigen
                    if (is_a($to, "User"))
                    {
                        $addStr = '<span class="newmailToField" id="touserfield_'.$to->getId().'"><img src="images/icons/user.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
                        $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\'user\', '.$to->getId().')" />';
                        $addStr .= '<input type="hidden" name="mail_touser_'.$to->getId().'" id="mail_touser_'.$to->getId().'" value="1"></span>';
                        echo $addStr;
                    }
                    
                    // Falls Empf�nger Gruppe -> anzeigen
                    if (is_a($to, "Group"))
                    {
                        $addStr = '<span class="newmailToField" id="togroupfield_'.$to->getId().'"><img src="images/icons/users.png" />&nbsp;'.$to->getName().'&nbsp;&nbsp;';
                        $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\'group\', '.$to->getId().')" />';
                        $addStr .= '<input type="hidden" name="mail_togroup_'.$to->getId().'" id="mail_togroup_'.$to->getId().'" value="1"></span>';
                        echo $addStr;
                    }
                    
                    // Falls Empf�nger UserContact -> anzeigen
                    if (is_a($to, "UserContact"))
                    {
                        $addStr = '<span class="newmailToField" id="tousercontactfield_'.$to->getId().'"><img src="images/icons/card-address.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
                        $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\'usercontact\', '.$to->getId().')" />';
                        $addStr .= '<input type="hidden" name="mail_tousercontact_'.$to->getId().'" id="mail_tousercontact_'.$to->getId().'" value="1"></span>';
                        echo $addStr;
                    }
                    
                    // Falls Empf�nger BusinessContact -> anzeigen
                    if (is_a($to, "BusinessContact"))
                    {
                        $addStr = '<span class="newmailToField" id="tobusinesscontactfield_'.$to->getId().'"><img src="images/icons/building.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
                        $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\'businesscontact\', '.$to->getId().')" />';
                        $addStr .= '<input type="hidden" name="mail_tobusinesscontact_'.$to->getId().'" id="mail_tobusinesscontact_'.$to->getId().'" value="1"></span>';
                        echo $addStr;
                    }
                    
                    // Falls Empf�nger BusinessContact -> anzeigen
                    if (is_a($to, "ContactPerson"))
                    {
                        $addStr = '<span class="newmailToField" id="tocontactpersonfield_'.$to->getId().'"><img src="images/icons/user-business.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
                        $addStr .= '<img src="images/icons/cross-white.png" class="pointer icon-link" onclick="removeMailto(\'contactperson\', '.$to->getId().')" />';
                        $addStr .= '<input type="hidden" name="mail_tocontactperson_'.$to->getId().'" id="mail_tocontactperson_'.$to->getId().'" value="1"></span>';
                        echo $addStr;
                    }
                    
                }
            ?>
                <a class="icon-link" href="libs/modules/organizer/nachrichten.addrcpt.php" id="add_to"><img src="images/icons/plus-white.png" title="<?=$_LANG->get('Hinzuf&uuml;gen')?>"></a>
            </td>
        </tr>
        <tr>
            <td><b><?=$_LANG->get('Betreff')?></b></td>
            <td id="td_mail_subject" name="td_mail_subject"><input name="mail_subject" style="width:635px" value="<?=$nachricht->getSubject()?>"></td>
        </tr>        
    </table>
</div>
<div class="newmailBody">
    <textarea name="mail_body" style="height:350px;width:790px"><?=$nachricht->getText() ?></textarea>
</div>
<input type="submit" value="<?=$_LANG->get('Abschicken')?>">
</form>