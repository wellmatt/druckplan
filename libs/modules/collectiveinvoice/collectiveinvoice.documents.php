<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/documents/document.class.php');
require_once('libs/modules/organizer/nachricht.class.php');
require_once('libs/modules/warehouse/warehouse.reservation.class.php');

if((int)$_REQUEST["deleteDoc"] > 0){
    $doc = new Document((int)$_REQUEST["deleteDoc"]);
    $doc->delete();
}

if($_REQUEST["createDoc"]){
    $doc = new Document();
    $doc->setRequestId($collectinv->getId());
    $doc->setRequestModule(Document::REQ_MODULE_COLLECTIVEORDER);
    
    if($_REQUEST["createDoc"] == "offer")
        $doc->setType(Document::TYPE_OFFER);
    if($_REQUEST["createDoc"] == "offerconfirm")
    {
        // Reservierung anlegen
        $check = TRUE;
        $opositions = Orderposition::getAllOrderposition($collectinv);
        foreach ($opositions as $op)
        {        
            // Wird überprüft, ob genug Ware verfügbar ist für eine Reservierungs
            if(Warehouse::getTotalStockByArticle($op->getObjectid())>=$op->getQuantity())
            {
                $whouses = Warehouse::getAllStocksByArticle($op->getObjectid());
            	$opamount = $op->getQuantity();
            	// Durchgehen aller Warenhaeuser mit Produkt x und die entsprechenden Mengen reservieren
            	foreach ($whouses as $w)
                {
                   $rsv = new Reservation();
            	   // Es darf aus keinem Warenhouse reserviert werden, welches explizit einem anderen Kunden zugewiesen ist.
            	   if(($w->getCustomer()->getId()==0) || ($w->getCustomer()->getId()==$collectinv->getCustomer()->getId())) 
            	   {
                       $rsum = Reservation::getTotalReservationByWarehouse($w);
                	   $faiwh = $w->getAmount()-$rsum; // $faiwh - free amount in warehouse
    
                	   if($faiwh>=0)
                	   {
                	       $rsv->setArticle(new Article($op->getObjectid()));
                	       $rsv->setOrderposition($op);
                	       $rsv->setWarehouse($w);
                    	   if($faiwh>=$opamount)
                    	   {
                               $rsv->setAmount($opamount);
                    	   }
                           else
                           {
                    	       $rsv->setAmount($faiwh);
                               $opamount = $opamount-$faiwh;
                           }
                           $rsv->save();
                	   }
                       if($opamount==0)
                           break;
            	   }
            	}             
            }
            else
            {
                // Nicht genug Ware vorhanden
                // Meldung?
                $check = FALSE;
                break;
            }
        }
        if($check)
            $doc->setType(Document::TYPE_OFFERCONFIRM);
    }
    if($_REQUEST["createDoc"] == "factory")
        $doc->setType(Document::TYPE_FACTORY);
    if($_REQUEST["createDoc"] == "delivery")
    {
        $opositions = Orderposition::getAllOrderposition($collectinv);
        foreach ($opositions as $op)
        {
            $rvs = Reservation::getAllReservationByOrderposition($op->getObjectid());
            foreach ($rvs as $r)
            {   
                // Ware entnehmen
                $w = $r->getWarehouse();
                $w->setAmount($w->getAmount()-$r->getAmount());
                $w->save();
                // Reservierung loeschen
                $r->delete();
            }
        }
        $doc->setType(Document::TYPE_DELIVERY);
    }
    if($_REQUEST["createDoc"] == "invoice")
    {
        $opositions = Orderposition::getAllOrderposition($collectinv);
        foreach ($opositions as $op)
        {
            $rvs = Reservation::getAllReservationByOrderposition($op->getObjectid());
            foreach ($rvs as $r)
            {   
                // Ware entnehmen
                $w = $r->getWarehouse();
                $w->setAmount($w->getAmount()-$r->getAmount());
                $w->save();
                // Reservierung loeschen
                $r->delete();
            }
        }
        $doc->setType(Document::TYPE_INVOICE);
    }
    if($_REQUEST["createDoc"] == "revert")
    	$doc->setType(Document::TYPE_REVERT);
    
    
    $hash = $doc->createDoc(Document::VERSION_EMAIL);
    $doc->createDoc(Document::VERSION_PRINT, $hash);
    $doc->save();
}

/*
 * Datei per Mail verschicken		//TODO checken !!!
*/
$_REQUEST["mail_subject"] = trim($_REQUEST["mail_subject"]);
$_REQUEST["mail_body"] = trim($_REQUEST["mail_body"]);
$nachricht = new Nachricht();

if ($_REQUEST["subexec"] == "send")
{

    // Nachricht mit werten Füllen
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
    $nachricht->setTo(Array($collectinv->getBusinessContact()))


?>
<link rel="stylesheet" href="css/documents.css" type="text/css">
<div class="box1 menuorder">
    <span class="menu_order" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$collectinv->getId()?>'">
    	<?=$_LANG->get('Zurück')?>
    </span>
</div>
<div class="box1" style="margin-top:50px;">
<table width="100%">
    <colgroup>
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Kundennummer')?>:</td>
        <td class="content_row_clear"><?=$collectinv->getBusinessContact()->getId()?></td>
        <td class="content_row_header"><?=$_LANG->get('Auftrag')?>:</td>
        <td class="content_row_clear"><?=$collectinv->getNumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Telefon')?></td>
        <td class="content_row_clear"><?=$collectinv->getBusinessContact()->getPhone()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Name')?>:</td>
        <td class="content_row_clear" valign="top"><?=nl2br($collectinv->getBusinessContact()->getNameAsLine())?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Adresse')?>:</td>
        <td class="content_row_clear"  valign="top"><?=nl2br($collectinv->getBusinessContact()->getAddressAsLine())?></td>
        <td class="content_row_header"  valign="top"><?=$_LANG->get('E-Mail')?></td>
        <td class="content_row_clear" valign="top"><?=$collectinv->getBusinessContact()->getEmail()?></td>
    </tr>
</table>
</div>
<br>

<h1><?=$_LANG->get('Dokumente')?></h1>
<div class="box2">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<colgroup>
    <col width="160">
    <col>
    <col width="50">
    <col width="150">
    <col width="100">
    <col width="250">
</colgroup>
<tr>
    <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
    <td class="content_row_header"><?=$_LANG->get('Dokumentenname')?></td>
    <td class="content_row_header"><?=$_LANG->get('Versch.')?></td>
    <td class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
    <td class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
    <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
</tr>
<? 
//---------------------------------------------------------------------------
// Angebot
//---------------------------------------------------------------------------?>
<tr class="<?=getRowColor(0)?>">
	<? $docs = Document::getDocuments(Array("type" => Document::TYPE_OFFER, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>
	<td class="content_row_clear"><?=$_LANG->get('Angebot')?></td>
	<td class="content_row_clear">
		<? if(count($docs) == 0)
		    echo '<span class="error">'.$_LANG->get('nicht vorhanden').'</span>';
		else 
		    echo '<span class="ok">'.$docs[0]->getName().'</span>';?>
	</td>
	<td class="content_row_clear">
		<? if(count($docs) > 0){
			if($docs[0]->getSent()){
				echo '<img src="images/status/green_small.gif">';
			} else {
				echo '<img src="images/status/red_small.gif">';
			}
		} else { 
			echo "&nbsp;";
		}?>
	</td>
	<td class="content_row_clear">
		<?if(count($docs) == 0)
		    echo '- - -';
		else
		    echo $docs[0]->getCreateUser()->getFirstName().' '.$docs[0]->getCreateUser()->getLastName();?>
	</td>
	
	<td class="content_row_clear">
		<?if(count($docs) == 0)
		    echo '- - -';
		else
		    echo date('d.m.Y H:m', $docs[0]->getCreateDate());?>
	</td>
	
	<td class="content_row_clear">
		<?if(count($docs) == 0){
		    echo '<ul class="postnav_text_save"><a href="#" onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&createDoc=offer\'">'.$_LANG->get('Generieren').'</a></ul>';
		} else {
		    echo '<table cellpaddin="0" cellspacing="0" width="100%"><tr><td width="30%">';
		    echo '<ul class="postnav_text">';
		    echo '<a href="libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=email">'.$_LANG->get('E-Mail').'</a></ul>';
		    echo '</td><td width="30%">';
		    echo '<ul class="postnav_text">';
		    echo '<a href="libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=print">'.$_LANG->get('Print').'</a></ul>';
		    echo '</td><td width="40%">';
		    echo '<ul class="postnav_text_del"><a href="#" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&deleteDoc='.$docs[0]->getId().'\')">'.$_LANG->get('L&ouml;schen').'</a></ul>';
		    echo '</td></tr></table>';
		}?>
	</td>
</tr>

<?if(count($docs) > 0)
    if($docs[0]->getSent() == 0)
    $senddocs[] = $docs[0];



//---------------------------------------------------------------------------
// Angebotsbetsätigung
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_OFFERCONFIRM, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>
<tr class="<?=getRowColor(0)?>">
	<td class="content_row"><?=$_LANG->get('Angebotsbest&auml;tigung')?></td>
	<td class="content_row">
		<?if(count($docs) == 0)
		    echo '<span class="error">'.$_LANG->get('nicht vorhanden').'</span>';
		else 
		    echo '<span class="ok">'.$docs[0]->getName().'</span>';?>
	</td>
	<td class="content_row">
		<?
		if(count($docs) > 0)
		    if($docs[0]->getSent())
		    echo '<img src="images/status/green_small.gif">';
		else
		    echo '<img src="images/status/red_small.gif">';
		else
		    echo '&nbsp';?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
		    echo '- - -';
		else
		    echo $docs[0]->getCreateUser()->getFirstName().' '.$docs[0]->getCreateUser()->getLastName();?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
		    echo '- - -';
		else
		    echo date('d.m.Y H:m', $docs[0]->getCreateDate());?>
	</td>
	<td class="content_row">
		<? if(count($docs) == 0)
		    echo '<ul class="postnav_text_save"><a href="#" onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&createDoc=offerconfirm\'">'.$_LANG->get('Generieren').'</a></ul>';
		else
		{
		    echo '<table cellpaddin="0" cellspacing="0" width="100%"><tr><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=email\'">'.$_LANG->get('E-Mail').'</a></ul>';
		    echo '</td><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=print\'">'.$_LANG->get('Print').'</a></ul>';
		    echo '</td><td width="40%">';
		    echo '<ul class="postnav_text_del"><a href="#" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&deleteDoc='.$docs[0]->getId().'\')">'.$_LANG->get('L&ouml;schen').'</a></ul>';
		    echo '</td></tr></table>';
		}?>
	</td>
</tr>
<?
if(count($docs) > 0)
    if($docs[0]->getSent() == 0)
    $senddocs[] = $docs[0];
 
//---------------------------------------------------------------------------
// Rechnung
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_INVOICE, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>

<tr class="<?=getRowColor(0)?>">
	<td class="content_row"><?=$_LANG->get('Rechnung')?></td>
	<td class="content_row">
		<?if(count($docs) == 0)
			echo '<span class="error">'.$_LANG->get('nicht vorhanden').'</span>';
		else 
			echo '<span class="ok">'.$docs[0]->getName().'</span>';?>
	</td>
	<td class="content_row">
		<?if(count($docs) > 0){
			if($docs[0]->getSent()){
				echo '<img src="images/status/green_small.gif">';
			} else {
				echo '<img src="images/status/red_small.gif">';
			}
		} else {
			echo '&nbsp;';
		}?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
			echo '- - -';
		else
			echo $docs[0]->getCreateUser()->getFirstName().' '.$docs[0]->getCreateUser()->getLastName();?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
			echo '- - -';
		else
			echo date('d.m.Y H:m', $docs[0]->getCreateDate());?>
	</td>
	<td class="content_row">
		<? if(count($docs) == 0)
		    echo '<ul class="postnav_text_save"><a href="#" onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&createDoc=invoice\'">'.$_LANG->get('Generieren').'</a></ul>';
		else {
		    echo '<table cellpaddin="0" cellspacing="0" width="100%"><tr><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=email\'">'.$_LANG->get('E-Mail').'</a></ul>';
		    echo '</td><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=print\'">'.$_LANG->get('Print').'</a></ul>';
		    echo '</td><td width="40%">';
		    echo '<ul class="postnav_text_del"><a href="#" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&deleteDoc='.$docs[0]->getId().'\')">'.$_LANG->get('L&ouml;schen').'</a></ul>';
		    echo '</td></tr></table>';
		}?>
	</td>
</tr>

<?if(count($docs) > 0)
	if($docs[0]->getSent() == 0)
		$senddocs[] = $docs[0];

 
//---------------------------------------------------------------------------
// Lieferschein
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_DELIVERY, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>

<tr class="<?=getRowColor(0)?>">
	<td class="content_row"><?=$_LANG->get('Lieferschein')?></td>
	<td class="content_row">
		<?if(count($docs) == 0)
			echo '<span class="error">'.$_LANG->get('nicht vorhanden').'</span>';
		else
			echo '<span class="ok">'.$docs[0]->getName().'</span>';?>
	</td>
	<td class="content_row">
		<?if(count($docs) > 0){
			if($docs[0]->getSent()){
				echo '<img src="images/status/green_small.gif">';
			} else {
				echo '<img src="images/status/red_small.gif">';
			}
		} else {
			echo '&nbsp;';
		}?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
			echo '- - -';
		else
			echo $docs[0]->getCreateUser()->getFirstName().' '.$docs[0]->getCreateUser()->getLastName();?>
	</td>
	<td class="content_row">
	<? if(count($docs) == 0)
			echo '- - -';
		else
			echo date('d.m.Y H:m', $docs[0]->getCreateDate());?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
		    echo '<ul class="postnav_text_save"><a href="#" onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&createDoc=delivery\'">'.$_LANG->get('Generieren').'</a></ul>';
		else{
		    echo '<table cellpaddin="0" cellspacing="0" width="100%"><tr><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=email\'">'.$_LANG->get('E-Mail').'</a></ul>';
		    echo '</td><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=print\'">'.$_LANG->get('Print').'</a></ul>';
		    echo '</td><td width="40%">';
		    echo '<ul class="postnav_text_del"><a href="#" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&deleteDoc='.$docs[0]->getId().'\')">'.$_LANG->get('L&ouml;schen').'</a></ul>';
		    echo '</td></tr></table>';
		}?>
	</td>
</tr>

<?if(count($docs) > 0)
	if($docs[0]->getSent() == 0)
		$senddocs[] = $docs[0];

//---------------------------------------------------------------------------
// Gutschriften
//---------------------------------------------------------------------------
$docs = Document::getDocuments(Array("type" => Document::TYPE_REVERT, "requestId" => $collectinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));?>

<tr class="<?=getRowColor(0)?>">
	<td class="content_row"><?=$_LANG->get('Gutschrift')?></td>
	<td class="content_row">
		<?if(count($docs) == 0)
			echo '<span class="error">'.$_LANG->get('nicht vorhanden').'</span>';
		else
			echo '<span class="ok">'.$docs[0]->getName().'</span>';?>
	</td>
	<td class="content_row">
		<?if(count($docs) > 0){
			if($docs[0]->getSent()){
				echo '<img src="images/status/green_small.gif">';
			} else {
				echo '<img src="images/status/red_small.gif">';
			}
		} else {
			echo '&nbsp;';
		}?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
			echo '- - -';
		else
			echo $docs[0]->getCreateUser()->getFirstName().' '.$docs[0]->getCreateUser()->getLastName();?>
	</td>
	<td class="content_row">
	<? if(count($docs) == 0)
			echo '- - -';
		else
			echo date('d.m.Y H:m', $docs[0]->getCreateDate());?>
	</td>
	<td class="content_row">
		<?if(count($docs) == 0)
		    echo '<ul class="postnav_text_save"><a href="#" onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&createDoc=revert\'">'.$_LANG->get('Generieren').'</a></ul>';
		else{ 
		    echo '<table cellpaddin="0" cellspacing="0" width="100%"><tr><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=email\'">'.$_LANG->get('E-Mail').'</a></ul>';
		    echo '</td><td width="30%">';
		    echo '<ul class="postnav_text"><a href="#" onclick="document.getElementById(\'idx_iframe_doc\').src=\'libs/modules/documents/document.get.iframe.php?getDoc='.$docs[0]->getId().'&version=print\'">'.$_LANG->get('Print').'</a></ul>';
		    echo '</td><td width="40%">';
		    echo '<ul class="postnav_text_del"><a href="#" onclick="askDel(\'index.php?page='.$_REQUEST['page'].'&ciid='.$collectinv->getId().'&exec=docs&deleteDoc='.$docs[0]->getId().'\')">'.$_LANG->get('L&ouml;schen').'</a></ul>';
		    echo '</td></tr></table>';
		}?>
	</td>
</tr>

<?if(count($docs) > 0)
	if($docs[0]->getSent() == 0)
		$senddocs[] = $docs[0];

$nachricht->setAttachments($senddocs);?>

</table>
</div>

<table width="100%">
    <tr>
        <td>
        </td>
        <td align="center" width="200">
            <ul class="graphicalButton pointer" onclick="document.getElementById('sendmail').style.display=''">
                <?=$_LANG->get('Mail verschicken')?>
            </ul>
        </td>
    </tr>
</table>
<iframe style="width:1px;height:1px;display:none" id="idx_iframe_doc" src=""></iframe>

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

paste_remove_styles: true, paste_auto_cleanup_on_paste : true, force_br_newlines: true, forced_root_block: '',
});
</script>
<!-- /TinyMCE -->

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
<link rel="stylesheet" type="text/css" href="./css/mail.css" />
<br>
<div class="box2" id="sendmail" style="display:none">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
	<input type="hidden" name="exec" value="docs">
	<input type="hidden" name="subexec" value="send">
	<input type="hidden" name="id" value="<?=$collectinv->getId()?>">
	<div class="newmailHeader">
		<table width="100%">
			<colgroup>
				<col width="150">
				<col>
			</colgroup>
			<tr>
				<td><b><?=$_LANG->get('Empf&auml;nger')?> </b></td>
				<td id="td_mail_to" name="td_mail_to"><?
				foreach($nachricht->getTo() as $to)
				{
				    // Falls Empfänger Benutzer -> anzeigen
				    if (is_a($to, "User"))
				    {
				        $addStr = '<span class="newmailToField" id="touserfield_'.$to->getId().'"><img src="images/icons/user.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'user\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_touser_'.$to->getId().'" id="mail_touser_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfänger Gruppe -> anzeigen
				    if (is_a($to, "Group"))
				    {
				        $addStr = '<span class="newmailToField" id="togroupfield_'.$to->getId().'"><img src="images/icons/users.png" />&nbsp;'.$to->getName().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'group\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_togroup_'.$to->getId().'" id="mail_togroup_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfänger UserContact -> anzeigen
				    if (is_a($to, "UserContact"))
				    {
				        $addStr = '<span class="newmailToField" id="tousercontactfield_'.$to->getId().'"><img src="images/icons/card-address.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'usercontact\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_tousercontact_'.$to->getId().'" id="mail_tousercontact_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfänger BusinessContact -> anzeigen
				    if (is_a($to, "BusinessContact"))
				    {
				        $addStr = '<span class="newmailToField" id="tobusinesscontactfield_'.$to->getId().'"><img src="images/icons/building.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'businesscontact\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_tobusinesscontact_'.$to->getId().'" id="mail_tobusinesscontact_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				    // Falls Empfänger BusinessContact -> anzeigen
				    if (is_a($to, "ContactPerson"))
				    {
				        $addStr = '<span class="newmailToField" id="tocontactpersonfield_'.$to->getId().'"><img src="images/icons/user-business.png" />&nbsp;'.$to->getNameAsLine().'&nbsp;&nbsp;';
				        $addStr .= '<img src="images/icons/cross-white.png" class="pointer" onclick="removeMailto(\'contactperson\', '.$to->getId().')" />';
				        $addStr .= '<input type="hidden" name="mail_tocontactperson_'.$to->getId().'" id="mail_tocontactperson_'.$to->getId().'" value="1"></span>';
				        echo $addStr;
				    }

				}
				?> <a href="libs/modules/organizer/nachrichten.addrcpt.php"
					id="add_to"><img src="images/icons/plus-white.png"
						title="<?=$_LANG->get('Hinzuf&uuml;gen')?>"> </a>
				</td>
			</tr>
			<tr>
				<td><b><?=$_LANG->get('Betreff')?> </b></td>
				<td id="td_mail_subject" name="td_mail_subject"><input
					name="mail_subject" style="width: 635px"
					value="<?=$nachricht->getSubject()?>"></td>
			</tr>
			<tr>
			    <td><b><?=$_LANG->get('Anh&auml;nge')?> </b></td>
			    <td id="td_mail_attach" name="td_mail_attach">
			        <? 
			            foreach($nachricht->getAttachments() as $sd)
			            {
			                echo '<span class="newmailAttachmentField" id="attachfield_'.$sd->getId().'"><img src="images/icons/paper-clip.png">';
			                echo $sd->getName().' <img src="images/icons/cross-white.png" class="pointer" onclick="removeAttach('.$sd->getId().')">';
			                echo '<input type="hidden" name="mail_attach_'.$sd->getId().'" id="mail_attach_'.$sd->getId().'" value="1"></span>';
			            }
			        ?>
			    </td>
			<tr>
			    
			</tr>
		</table>
	</div>
	<div class="newmailBody">
		<textarea name="mail_body" style="height: 200px; width: 790px">
			<?=$nachricht->getText() ?>
		</textarea>
	</div>
	<input type="submit" value="<?=$_LANG->get('Abschicken')?>">
</form>
</div>