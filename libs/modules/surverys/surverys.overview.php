<table width="100%">
	<tr>
		<td width="200" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST["page"])?>"> <?=$_LANG->get('Frageb&ouml;gen')?></td>
		<td><?=$savemsg?></td>
	</tr>
</table>

<div class="box1" style="height: 100%">
	<iframe id="formcraftframe" style="width: 100%; height: 800px;" scrolling="auto" frameborder="0" src="thirdparty/formcraft/overview.php"></iframe>
</div>