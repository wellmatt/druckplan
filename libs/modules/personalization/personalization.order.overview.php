<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       11.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/documents/document.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once 'libs/modules/personalization/personalization.class.php';
require_once 'libs/modules/warehouse/warehouse.class.php';

// error_reporting(-1);
// ini_set('display_errors', 1);

if((int)$_REQUEST["setStatus"] != 0){
	$perso_order = new Personalizationorder($_REQUEST["poid"]);
	$perso_order->setStatus((int)$_REQUEST["setStatus"]);
	$perso_order->save();
}

if($_REQUEST["exec"] == "delete"){
	
	$del_perso_order = new Personalizationorder((int)$_REQUEST["delid"]);
	$tmp_docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER, 
										"requestId" => $del_perso_order->getId(), 
										 "module" => Document::REQ_MODULE_PERSONALIZATION));
	$hash = $tmp_docs[0]->getHash();
	$tmp_del = $del_perso_order->delete();
	// Wenn Datenbank angepasst ist, muss die Datei auch geloescht werden
	if($tmp_del){
		$tmp_filename1 = Personalizationorder::FILE_PATH.$_USER->getClient()->getId().".per_".$hash."_e.pdf";
		$tmp_filename2 = Personalizationorder::FILE_PATH.$_USER->getClient()->getId().".per_".$hash."_p.pdf";
		unlink($tmp_filename1);
		unlink($tmp_filename2);
	}
    
	/*
	$_REQUEST["del"] = trim(addslashes($_REQUEST["delete"]));
    
    $sql = "SELECT * FROM ftpcustuploads WHERE ftp_hash = '{$_REQUEST["delete"]}'";
    $download = $DB->select($sql);
    $download = $download[0];
    $fileext = explode(".", $download["ftp_orgname"]);
    $fileext = $fileext[count($fileext) - 1];
    $filename = "ftp/cust_uploads/".$download["ftp_hash"].".".$fileext;

    if(unlink($filename))
    {
        $sql = "DELETE FROM ftpcustuploads WHERE ftp_hash = '{$_REQUEST["delete"]}'";
        $savemsg = getSaveMessage($res = $DB->no_result($sql));
    }   */
}

if($_REQUEST["filter"] == "on" || $_REQUEST["filter"] == ""){		
	$filter = "on";					// aktueller Zustand der Filterfunktion
	$filter2 = "off";				// invertierter Zustand der Filterfunktion
	$filter_msg = $_LANG->get("Alle Kunden einblenden");
} else {
	$filter = "off";
	$filter2 = "on";
	$filter_msg = $_LANG->get("Inaktive ausblenden");
}

$customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME);
?>
<script language="javascript">
function askDel(myurl)
{
   if(confirm("Sind Sie sicher?"))
   {
      if(myurl != '')
         location.href = myurl;
      else
         return true;
   }
   return false;
}
</script>

<table border="0" cellpadding="0" cellspacing="0" width="1000">
<tr>
	<td height="30" width="300" class="content_header">
		<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
		<span style="font-size: 13px">Bestellungen aus Web-to-Print</span>
	</td>
	<td align="center"><?=$savemsg?></td>
	<td class="content_header" width="160">
		&ensp;
	</td>
</tr>
</table>
<? 
foreach($customers as $cust){

	$all_perso_orders = Personalizationorder::getAllPersonalizationorders($cust->getID(), Personalizationorder::ORDER_CRTDATE, true); 
    
	// print_r($all_perso_orders);
	
    if((count($all_perso_orders) > 0 && $all_perso_orders != false) || $filter == "off" ){  ?>
        
        <span style="font-size: 12px"><b><?=$cust->getNameAsLine()?></b></span>
        <div class="box1">
        <table cellpadding="2" cellspacing="0" border="0" width="100%">
            <colgroup>
                <col>
                <col width="220">
                <col width="180">
                <col width="120">
                <col width="110">
                <col width="130">
                <col width="40">
                <col width="80">
            </colgroup>
            <tr>
                <td class="content_row_header"><?=$_LANG->get('Titel');?></td>
                <td class="content_row_header"><?=$_LANG->get('Verkn. Artikel');?></td>
                <td class="content_row_header"><?=$_LANG->get('Lagerplatz (Menge)');?></td>
                <td class="content_row_header"><?=$_LANG->get('Bestelldatum');?></td>
                <td class="content_row_header" align="right"><?=$_LANG->get('Bestellmenge');?></td>
                <td class="content_row_header" align="center"><?=$_LANG->get('Status');?></td>
                <td class="content_row_header">&ensp;</td>
                <td class="content_row_header"><?=$_LANG->get('Optionen');?></td>
            </tr>
            <? 
            $y=0;
            foreach($all_perso_orders as $perso_order){
				$perso = new Personalization($perso_order->getPersoID());
				// echo "Perso Order: " . $perso_order->getId() . "</br>";
				$docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER, 
											 "requestId" => $perso_order->getId(), 
											 "module" => Document::REQ_MODULE_PERSONALIZATION));
				if (count($docs) > 0)
				    $hash = $docs[0]->getHash();
				if (count($docs) > 1)
				    $hash2 = $docs[1]->getHash();
				$tmp_id =$cust->getClient()->getId();
				
				// echo "hash: " . $hash . "</br>";
				?>
				 
	            <tr class="<?=getRowColor($y)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
	                <td class="content_row"><?=$perso_order->getTitle()?></td>
	                <td class="content_row">
	                	<?=$perso->getArticle()->getTitle();?> &ensp;
	                </td>
	                <td class="content_row">
	                <? 	if ($perso->getArticle()->getId() > 0){
		                	$all_stocks = Warehouse::getAllStocksByArticle($perso->getArticle()->getId());
		                	foreach ($all_stocks AS $stock){
								echo $stock->getName()." (".$stock->getAmount().") ";
							}
	                	} ?> &ensp;
	                </td>
		        	<td class="content_row"><?if ($perso_order->getOrderdate() > 0) echo date("d.m.Y - H:i",$perso_order->getOrderdate())?> &ensp;</td>
		        	<td class="content_row"  align="right"><?=$perso_order->getAmount()?> <?=$_LANG->get('Stk.');?></td>
		        	<td class="content_row"  align="center">
		        		<table border="0" cellpadding="1" cellspacing="0">
                		<tr>
		                    <td width="25">
		                        <a href="index.php?page=<?=$_REQUEST['page']?>&poid=<?=$perso_order->getId()?>&setStatus=2">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($perso_order->getStatus() == 2)
		                                echo 'orange.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.$perso_order->getStatusDescription(2).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?page=<?=$_REQUEST['page']?>&poid=<?=$perso_order->getId()?>&setStatus=3">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($perso_order->getStatus() == 3)
		                                echo 'yellow.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.$perso_order->getStatusDescription(3).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?page=<?=$_REQUEST['page']?>&poid=<?=$perso_order->getId()?>&setStatus=4">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($perso_order->getStatus() == 4)
		                                echo 'lila.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.$perso_order->getStatusDescription(4).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?page=<?=$_REQUEST['page']?>&poid=<?=$perso_order->getId()?>&setStatus=5">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($perso_order->getStatus() == 5)
		                                echo 'green.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.$perso_order->getStatusDescription(5).'">';
		                            ?>
		                        </a>
		                    </td>
		                </tr>
		                </table>
			        	<!-- img src="./images/status/<?=$perso_order->getStatusImage()?>" 
			        		 title="<?=$perso_order->getStatusDescription()?>" 
			        		 alt="<?=$perso_order->getStatusDescription()?>"-->
			        </td>
			        <td class="content_row">
			       	<?	if($perso_order->getComment() != NULL && $perso_order->getComment() != ""){?>
							<img src="./images/icons/balloon-ellipsis.png" alt="Kommentar" 
						 		 title="<?=$perso_order->getComment()?>" />	 
					<?	} else {
							echo "&emsp;";
						} ?>
			        </td>
	                <td class="content_row">
	                	<a class="icon-link" target="_blank" href="./docs/personalization/<?=$tmp_id?>.per_<?=$hash?>_e.pdf"
	                		><img src="images/icons/application-browser.png" title="Download Vorderseite mit Hintergrund" alt="Download"></a> 
	                	<a href="./docs/personalization/<?=$tmp_id?>.per_<?=$hash?>_p.pdf" class="icon-link" target="_blank" 
	                		><img src="images/icons/application.png" title="Download Vorderseite ohne Hintergrund" alt="Download"></a></br>
	                	<? if (count($docs) > 1){?>
	                	<a class="icon-link" target="_blank" href="./docs/personalization/<?=$tmp_id?>.per_<?=$hash2?>_e.pdf"
	                		><img src="images/icons/application-browser.png" title="Download R&uuml;ckseite mit Hintergrund" alt="Download"></a> 
	                	<a href="./docs/personalization/<?=$tmp_id?>.per_<?=$hash2?>_p.pdf" class="icon-link" target="_blank" 
	                		><img src="images/icons/application.png" title="Download R&uuml;ckseite ohne Hintergrund" alt="Download"></a>
	                	<? }?>
						&ensp;
						<a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delid=<?=$perso_order->getId()?>')"><img src="images/icons/cross-script.png"> </a>
	                </td>
	            </tr>
            <? $y++; } ?>
        </table>
        </div>
        <br>
        <? 
    } 
}?>