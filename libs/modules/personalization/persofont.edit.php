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

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#font_edit').submit();",'glyphicon-floppy-disk');
if ($persofont->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page".$_REQUEST['page']."&exec=delete&delid".$persofont->getId()."');", 'glyphicon-trash', true);
}

echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Schriftart hinzufügen')?>
				<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Schriftart bearbeiten')?>
				<?//if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Schriftart kopieren')?>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="font_edit" id="font_edit" enctype="multipart/form-data"
				class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.font_title, this.font_file))">
			  <input type="hidden" name="exec" value="edit">
			  <input type="hidden" name="subexec" value="save">
			  <input type="hidden" name="fid" value="<?=$persofont->getId()?>">

			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Titel</label>
				  <div class="col-sm-4">
					  <input id="font_title" name="font_title" type="text" class="form-control"
							 value="<?=$persofont->getTitle()?>" >
				  </div>
			  </div>

			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">TTF-Datei</label>
				  <div class="col-sm-10">
					  <input id="font_file" name="font_file" type="file" style="background-color: none; border: none;">
				  </div>
			  </div>

			  </br>
				  <p>Online Converter zu TTF: <a target="_blank" href="http://www.freefontconverter.com/">freefontconverter.com</a></p>
		  </form>
	  </div>
</div>


