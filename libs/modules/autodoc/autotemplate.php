<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/autodoc/tmplDefault.class.php';
require_once 'libs/modules/autodoc/tmplAgent.class.php';

$tmplfolder = "./docs/tmpl_files/";
$tmp = 'libs/modules/autodoc/default.js';

$editors = array(
    new TmplDefault("Angebot", "coloffer"),
    new TmplDefault("Angebotsbestätigung", "colofferconfirm"),
    new TmplDefault("Rechnung", "colinvoice"),
    new TmplDefault("Lieferschein", "coldelivery"),
    new TmplDefault("Gutschrift", "revert"),
    new TmplDefault("Kalk_AN", "offer"),
    new TmplDefault("Kalk_AB", "offerconfirm"),
    new TmplDefault("Kalk_DR", "factory"),
    new TmplDefault("Kalk_RE", "invoice"),
    new TmplDefault("Kalk_LS", "delivery"),
    new TmplDefault("Kalk_PO", "paperorder"),
    new TmplDefault("Kalk_ETI", "label", 106, 60),
    new TmplDefault("Mahnung", "invoicewarning"),
    new TmplDefault("Serienbrief", "bulkletter")
);
$agent = new TmplAgent($tmp);

// Speichern und Speichern von Vorlagen
$datei = NULL;
$exec = $_REQUEST["exec"];
$index = -1;
$i = 0;
foreach ($editors as $editor) {
    if ($exec == $_LANG->get($editor->getName())) {
        $index = $i; // Sieht Unwichtig aus, wird aber für die html-tab benötigt.
        $tmplname = $_REQUEST["templatename_" . $_LANG->get($editor->getName())];
        
        $datei[$_LANG->get($editor->getName())] = $_POST[$_LANG->get($editor->getName())];
        file_put_contents($tmplfolder . $editor->getFile() . ".tmpl", $datei[$_LANG->get($editor->getName())]);
        
        // Überprüft vorher, dass Default-Templates nicht überschrieben werden
        if ($tmplname != "" && $tmplname != "default_" . $editor->getName()) {
            // var_dump($exec);
            // var_dump($datei[$_LANG->get($editor->getName())]);
            $agent->Add($tmplname, "", $exec, $datei[$_LANG->get($editor->getName())]);
        }
        
    } else {
        $output = implode("", file($tmplfolder . $editor->getFile() . ".tmpl"));
        if(empty(trim($output)))
            $output = $agent->Get( "default_" . $editor->getName());
        $datei[$_LANG->get($editor->getName())] = $output;
    }
    $i++;
}


// Löschen von Vorlagen
$del = $_REQUEST["del"];
$del_text = $_REQUEST["del_text"];
if($del && $del_text)
{
    $index = count($editors);
    // Überprüft vorher, dass Default-Templates nicht überschrieben werden
    $check = TRUE;    
    foreach ($editors as $editor)
        if ("default_" . $editor->getName() == $del_text)
            $check=FALSE;
    if($check)
        $agent->Delete($del_text);
}
?>

<script type="text/javascript" language="javascript"
	src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<!-- Make sure the path to CKEditor is correct. -->
<script src="./thirdparty/ckeditor/ckeditor.js"></script>

<script type="text/javascript">
    $(function() {
    	var index = $('#tabs a[href= "#_<?php $_LANG->get($_POST["exec"]);?>"]').parent().index();
    	$( "#tabs" ).tabs({ selected: <?php echo $index ?> });
    });
</script>

<script type="text/javascript">
function tmplFunction(obj) {
	var cdate = new Date();
	var month = cdate.getMonth();
	var day = cdate.getDate();
	var year = cdate.getFullYear();

	
    var tmplname = prompt("Bitte geben sie die Bezeichnung der Vorlage an", obj.name + '_' + month + '-' + day + '-' + year);
    if (tmplname != null) {
    	document.getElementById("templatename_"+obj.name).value = tmplname;
    	document.getElementById("form_"+obj.name).submit();
    }
    else 
    	document.getElementById("templatename").value = "";
}
</script>

<div class="box1">
	<div id="tabs">
		<ul>
			<? foreach ($editors as $editor){?>
				<li><a href="#_<? echo $_LANG->get($editor->getName());?>"><? echo $_LANG->get($editor->getName());?></a></li>
			<?}?>
			    <li><a href="#overview">&Uuml;bersicht</a></li>
		</ul>
            <? foreach ($editors as $editor){?>
            <form action="index.php?page=<?=$_REQUEST['page']?>"
			method="POST" enctype="multipart/form-data"
			name="form_<? echo $_LANG->get($editor->getName());?>"
			id="form_<? echo $_LANG->get($editor->getName());?>">
			<div id="_<? echo $_LANG->get($editor->getName());?>">
				<div style="width:<?echo $_LANG->get($editor->getWidth());?>mm; margin:auto;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<colgroup>
							<col width="180">
							<col>
						</colgroup>
						<textarea align="center"
							name="<? echo $_LANG->get($editor->getName());?>"
							id="<? echo $_LANG->get($editor->getName());?>" rows="100"
							cols="80">
					       <?php echo $datei[$_LANG->get($editor->getName())]; ?>
                        </textarea>
						<script>                     
						   var editor = CKEDITOR.replace(<?php echo $_LANG->get($editor->getName());?>);
						   editor.config.templates_files = ['<?php echo $tmp?>'];
                           editor.config.height = <?php echo ($editor->getHeight()*3);?>;
                        </script>
						<table border="0" class="content_table" cellpadding="3"
							cellspacing="0" width="100%">
							<tr>
								<td align="left" class="content_row_clear" width="33%"><input
									type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>"
									class="button"
									onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
								</td>
								<td align="center" width="33%"><input
									id="templatename_<?php echo $_LANG->get($editor->getName());?>"
									name="templatename_<?php echo $_LANG->get($editor->getName());?>"
									type="hidden" value=""> <input type="button"
									value="Speichern als Vorlage"
									onclick="tmplFunction(<?php echo $_LANG->get($editor->getName());?>);"
									class="button"></td>
								<td align="right" width="33%"><input type="hidden" id="exec"
									name="exec"
									value="<?php echo $_LANG->get($editor->getName());?>"> <input
									type="submit" value="Speichern"></td>
							</tr>
						</table>
					</table>
				</div>
			</div>
		</form>
        <?}?>
        
<div id="overview">
			<div style="width: 100%"; margin:auto;">
				<table id="table_overview" class="display" cellspacing="10" cellpadding="10"
					width="100%"
					border="2">
					<thead>
						<tr>
							<th style="font-size:1.2em">Nr</th>
							<th style="font-size:1.2em">Titel</th>
							<!-- 							<th style="font-size:1.2em">Bild</th> -->
							<th style="font-size:1.2em">Zugeh&ouml;rigkeit</th>
						</tr>
					</thead>
					<tbody>
						<?
    $tmpl_js = $agent->Load();
    $i = 1;
    foreach ($tmpl_js->{"templates"} as $tmpl) {
        ?>
						<tr>
							<th><? echo $i++;?></th>
							<th><? echo $tmpl->{"title"};?></th>
							<!-- 							<th><?// echo $tmpl->{"image"};?></th> -->
							<th><? echo $tmpl->{"description"};?></th>
						</tr>
						<?}?>
					</tbody>
				</table>
				<form action="index.php?page=<?=$_REQUEST['page']?>" method="POST"
					enctype="multipart/form-data" name="form_delete" id="form_delete">
					<table border="0" class="content_table" cellpadding="3"
						cellspacing="0" width="100%">
						<tr>
							<td align="left" class="content_row_clear" width=""><input
								type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>"
								class="button"
								onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
							</td>
							<td align="center" class="content_row_clear" width="">
								<h4>Titel der Vorlage:</h4>
								<input id="del_text" name="del_text" type="text" value="">
							</td>
							<td align="right" width=""><input type="hidden" id="exec"
								name="del" value="del"> <input type="submit"
								value="<?php echo $_LANG->get("Löschen")?>"></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>
