<link rel="stylesheet" type="text/css" href="./css/mail.css" />

<table width="100%">
<tr>
<td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_SESSION["pid"])?>"> <?=$_LANG->get('Nachrichten')?></td>
      <td></td>
      <td width="200" class="content_header" align="right">
          <a href="index.php?exec=newmail"><img src="images/icons/mail--plus.png" /> <?=$_LANG->get('Neue Nachricht')?></a>
      </td>
   </tr>
</table>

<div class="box1">
	<table width="100%">
		<tr>
			<td width="20%" valign="top">
			<?
			require_once 'folder.template.php';
			?>
			</td>
			<td width="80%" valign="top">
        	<?
       		require_once $contentTemplate;
	        ?>
			</td>
		</tr>
	</table>
</div>