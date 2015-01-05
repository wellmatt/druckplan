﻿<?php
require_once('perferences.class.php');

$perf = new Perferences();

if ($_REQUEST["exec"] == "save")
{
	if($_FILES){
		if(move_uploaded_file($_FILES["pdf_back"]["tmp_name"], "./docs/templates/briefbogen.jpg")){
// 			$savemsg = getSaveMessage(true);
		} else {
// 			$savemsg = getSaveMessage(false);
		}
	}
	
	$perf->setZuschussProDP(str_replace(",",".",str_replace(".","",$_REQUEST["zuschussprodp"])));
	$perf->setCalc_detailed_printpreview($_REQUEST["calc_detailed_printpreview"]);
	$perf->setPdf_margin_top(str_replace(",",".",str_replace(".","",$_REQUEST["pdf_margin_top"])));
	$perf->setPdf_margin_left(str_replace(",",".",str_replace(".","",$_REQUEST["pdf_margin_left"])));
	$perf->setPdf_margin_right(str_replace(",",".",str_replace(".","",$_REQUEST["pdf_margin_right"])));
	$perf->setPdf_margin_bottom(str_replace(",",".",str_replace(".","",$_REQUEST["pdf_margin_bottom"])));
	$savemsg = getSaveMessage($perf->save());
}

?>
<script>
	$(function() {
		$( "#tabs" ).tabs({ selected: 0 });
	});
</script>
<script type="text/javascript">
function load_image(id,ext)
{
   if(validateExtension(ext) == false)
   {
      alert("Es wird nur das JPG Format unterstützt");
      document.getElementById("pdf_back").innerHTML = "";
      document.getElementById("pdf_back").value = "";
      document.getElementById("pdf_back").focus();
      return;
   }
}

function validateExtension(v)
{
      var allowedExtensions = new Array("jpg","JPG");
      for(var ct=0;ct<allowedExtensions.length;ct++)
      {
          sample = v.lastIndexOf(allowedExtensions[ct]);
          if(sample != -1){return true;}
      }
      return false;
}
</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="images/icons/gear.png"> <?=$_LANG->get('Einstellungen')?></td>
	  <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" enctype="multipart/form-data" name="perf_form" id="perf_form">
<input type="hidden" name="exec" value="save">
<div class="box1">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-0"><? echo $_LANG->get('Allgemein');?></a></li>
			<li><a href="#tabs-1"><? echo $_LANG->get('Kalkulation');?></a></li>
			<li><a href="#tabs-2"><? echo $_LANG->get('PDF');?></a></li>
		</ul>

		<div id="tabs-0">
                
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
               <colgroup>
                  <col width="180">
                  <col>
               </colgroup>
               <tr>
                  <td class="content_row_header" valign="top">Briefbogen:</td>
                  <td class="content_row_clear">
                     <input type="file" name="pdf_back" onChange="load_image(this.id,this.value)" id="pdf_back" /></br>
            		 vorhandenen in neuem Fenster <a target="_blank" href="docs/templates/briefbogen.jpg"><b>öffnen</b></a></br></br>
            		 Möglichst hohe Auflösung bei geringer Dateigröße </br>
            		 (300dpi / 100-300KB) ist empfolen!
                  </td>
               </tr>
            </table>
       </div>
       <div id="tabs-1">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
               <colgroup>
                  <col width="180">
                  <col>
               </colgroup>
               <tr>
                  <td class="content_row_header" valign="top">Zuschuss pro DP:</td>
                  <td class="content_row_clear">
                     <input type="text" name="zuschussprodp" id="zuschussprodp" value="<?=str_replace(".",",",$perf->getZuschussProDP());?>" />
                  </td>
               </tr>
               <tr>
                  <td class="content_row_header" valign="top">Detailierte Druckbogenvorschau:</td>
                  <td class="content_row_clear">
                     <input type="checkbox" name="calc_detailed_printpreview" id="calc_detailed_printpreview" value="1" <? if ($perf->getCalc_detailed_printpreview()) echo "checked"; ?>/>
                  </td>
               </tr>
            </table>
       </div>
       <div id="tabs-2">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
               <colgroup>
                  <col width="180">
                  <col>
               </colgroup>
               <tr>
                  <td class="content_row_header" valign="top">Margin Top:</td>
                  <td class="content_row_clear">
                     <input type="text" name="pdf_margin_top" id="pdf_margin_top" value="<?=str_replace(".",",",$perf->getPdf_margin_top());?>" />
                  </td>
               </tr>
               <tr>
                  <td class="content_row_header" valign="top">Margin Left:</td>
                  <td class="content_row_clear">
                     <input type="text" name="pdf_margin_left" id="pdf_margin_left" value="<?=str_replace(".",",",$perf->getPdf_margin_left());?>" />
                  </td>
               </tr>
               <tr>
                  <td class="content_row_header" valign="top">Margin Right:</td>
                  <td class="content_row_clear">
                     <input type="text" name="pdf_margin_right" id="pdf_margin_right" value="<?=str_replace(".",",",$perf->getPdf_margin_right());?>" />
                  </td>
               </tr>
               <tr>
                  <td class="content_row_header" valign="top">Margin Bottom:</td>
                  <td class="content_row_clear">
                     <input type="text" name="pdf_margin_bottom" id="pdf_margin_bottom" value="<?=str_replace(".",",",$perf->getPdf_margin_bottom());?>" />
                  </td>
               </tr>
            </table>
       </div>

<table border="0" class="content_table" cellpadding="3" cellspacing="0" width="100%">
<tr>
   <td class="content_row_clear">
   		<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	    		onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
   </td>
   <td align="right" width="150">
      	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
   </td>
</tr>
</table>

</form>
</div>