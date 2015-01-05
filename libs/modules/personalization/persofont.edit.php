<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			26.02.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

$persofont = new PersoFont($_REQUEST["fid"]);

if($_REQUEST["subexec"] == "save"){
	
	$persofont->setTitle(trim(addslashes($_REQUEST["font_title"])));
	if (isset($_FILES["font_file"])) {
		if ($_FILES["font_file"]["name"] != "" && $_FILES["font_file"]["name"] != NULL){
			$destination = PersoFont::FILE_DESTINATION;
			$filename = trim(addslashes($_REQUEST["font_title"]));
			$filename = preg_replace("/[^A-Za-z0-9]/", '', $filename);
			$filename = strtolower($filename);
			$new_filename = $destination.$filename;
			$tmp_outer = move_uploaded_file($_FILES["font_file"]["tmp_name"], $new_filename);
			$persofont->setFileName($filename);

			$tc_fontname = TCPDF::addTTFfont($new_filename);
			if ($tc_fontname){
			    $savemsg = getSaveMessage($persofont->save());
			    if($DB->getLastError()!=NULL && $DB->getLastError()!=""){
			        $savemsg .= $DB->getLastError();
			    }
			}
		}
	}
} 
?>
<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Artikel hinzuf�gen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Artikel bearbeiten')?>
			<?//if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Artikel kopieren')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>
<form 	action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="font_edit" id="font_edit" enctype="multipart/form-data"
		onSubmit="return checkform(new Array(this.font_title, this.font_file))">
	<div class="box1">
		<input type="hidden" name="exec" value="edit"> 
		<input type="hidden" name="subexec" value="save"> 
		<input type="hidden" name="fid" value="<?=$persofont->getId()?>">
		
		<table width="100%">
			<colgroup>
				<col width="180">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
				<td class="content_row_clear">
				<input id="font_title" name="font_title" type="text" class="text" 
						value="<?=$persofont->getTitle()?>" style="width: 370px">
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('TTF-Datei')?> *</td>
				<td class="content_row_clear">
				<input id="font_file" name="font_file" type="file" class="text" 
						style="width: 370px"> <br/>
				</td>
			</tr>
		</table>
		</br>
		<p>Online Converter zu TTF: <a target="_blank" href="http://www.freefontconverter.com/">freefontconverter.com</a></p>
	</div>
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
</form>