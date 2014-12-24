<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.03.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
?>
<link rel="stylesheet" type="text/css" href="./css/mail.css" />
<table width="100%">
<tr>
	<td width="20%" class="content_header">
		<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Nachrichten')?>
	</td>
	<td>
	<? 	if(count($emailIds) > 1) { ?>
			<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="account" name="account">
				<b><?=$_LANG->get('E-Mail Konto')?> : </b>
				<select name="emailId" style="width:180px" onchange="document.account.submit();">
				<?
				foreach($emailIds as $key => $value) {
					if($emailId == $value) {
						echo '<option value="'.$value.'" selected>'.$key.'</option>';
					} else {
						echo '<option value="'.$value.'">'.$key.'</option>';
					}
				}
				?>
				</select>
			</form>
	<? 	}?>
      </td>
      <td width="20%" class="content_header" align="right">
          <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=newmail&folder=<?=urlencode($currentFolder)?>&emailId=<?=$emailId?>"><img src="images/icons/mail--plus.png" /> <?=$_LANG->get('Neue Nachricht')?></a>
      </td>
   </tr>
</table>

<div class="box1">
	<table width="100%">
		<tr>
			<td width="30%" valign="top">
			<?
			require_once 'folder.template.php';
			?>
			</td>
			<td width="80%" valign="top">
			<?
			if($error) {
				echo '<font color="#FF0000">'.$error.'</font><br/><br/>';
			} else if($ok) {
				echo '<font color="#00CC00">'.$ok.'</font><br/><br/>';
			}
			
			if($contentTemplate != "") {
				require_once $contentTemplate;
			}
			?>
			</td>
		</tr>
	</table>
</div>