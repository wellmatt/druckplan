<?
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

// Vorbereitung der Ausgabe
$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
foreach ($calcs as $calc)
{
    // Produktnamen holen oder ggf. ueberschreiben
    $tmp_productname = $order->getProduct()->getName();
    if ($order->getProductName() != "" && $order->getProductName() != NULL) {
        $tmp_productname = $order->getProductName();
    }
    $order->setProductName($tmp_productname);
}

// Einbindung der generellen Variablen im Templatesystem


require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/offer.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('Calcs',$calcs);

// var_dump ($datei);

$htmldump = $smarty->fetch('string:'.$datei);

// echo '<pre>'.$htmldump.'</pre>';

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);
?>
<?php
//Sicherheitskopie
/*
<p><span style="font-size:14px"><strong>Angebot Nr. {$Id}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></span><span style="font-size:14px"><strong>&nbsp; Kunden-Nr. {$CustomerId}</strong></span><br />
<span style="font-size:12px">Auftrag: {$OrderTitle}</span></p>

<p><span style="font-size:12px"><span style="font-family:arial,helvetica,sans-serif"><!-- {if $Order->getCustContactperson()->getId() > 0} --><!-- {if $Order->getCustContactperson()->getTitle() == 'Herr'} --></span></span></p>

<p><span style="font-size:12px"><span style="font-family:arial,helvetica,sans-serif">Sehr geehrter {$ContactPerson-&gt;getNameAsLine3()}<br />
<!-- {else} --> Sehr geehrte {$ContactPerson-&gt;getNameAsLine3()}<br />
<!-- {/if} --> <!-- {else} --> Sehr geehrte Damen und Herren<br />
<!-- {/if} --> gerne unterbreiten wir Ihnen nachfolgendes Angebot:</span></span></p>

<p>&nbsp;</p>

<p>&nbsp;</p>
<!-- {foreach $Calcs as $Calc} -->

<table border="0" cellpadding="1" cellspacing="1" style="width:450px">
	<tbody>
		<tr>
			<td style="width:130px"><span style="font-size:11px"><strong>Titel</strong></span></td>
			<td><span style="font-size:11px"><strong>{$Order-&gt;getTitle()}</strong></span></td>
		</tr>
		<!-- {if $Calc->getTitle() != ''} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Kalkulationstitel:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getTitle()}</span></td>
		</tr>
		<!-- {/if} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Produkt</span></td>
			<td><span style="font-size:11px">{$Order-&gt;getProductName()}</span></td>
		</tr>
		<!-- {if $Order->getShowProduct() > 0} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Auflage:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getAmount()}</span></td>
		</tr>
		<tr>
			<td style="width:130px"><span style="font-size:11px">Format geschlossen</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getProductFormatWidth()} x {$Calc-&gt;getProductFormatHeight()}</span></td>
		</tr>
		<tr>
			<td style="width:130px"><span style="font-size:11px">Format offen</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getProductFormatWidthOpen()} x {$Calc-&gt;getProductFormatHeightOpen()}</span></td>
		</tr>
		<tr>
			<td style="width:130px"><span style="font-size:11px">Inhalt:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getPaperContent()-&gt;getName()},&nbsp; {$Calc-&gt;getPaperContentWeight()} g<br />
			{$Calc-&gt;getPagesContent()} Seiten, {$Calc-&gt;getChromaticitiesContent()-&gt;getName()}</span></td>
		</tr>
		<!-- {if $Calc->getPaperEnvelope()->getId() > 0} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Umschlag:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getPaperEnvelope()-&gt;getName()}, {$Calc-&gt;getPaperEnvelopeWeight()} g<br />
			{$Calc-&gt;getPagesEnvelope()} Seiten, {$Calc-&gt;getChromaticitiesEnvelope()-&gt;getName()}</span></td>
		</tr>
		<!-- {/if}
{if $Calc->getPaperAddContent()->getId() > 0} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Zus. Inhalt:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getPaperAddContent()-&gt;getName()}, {$Calc-&gt;getPaperAddContentWeight()} g<br />
			{$Calc-&gt;getPagesAddContent()} Seiten, {$Calc-&gt;getChromaticitiesAddContent()-&gt;getName()}</span></td>
		</tr>
		<!-- {/if}
{if $Calc->getPaperAddContent2()->getId() > 0} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Zus. Inhalt 2:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getPaperAddContent2()-&gt;getName()}, {$Calc-&gt;getPaperAddContent2Weight()} g<br />
			{$Calc-&gt;getPagesAddContent2()} Seiten, {$Calc-&gt;getChromaticitiesAddContent2()-&gt;getName()}</span></td>
		</tr>
		<!--
{/if}
{if $Calc->getPaperAddContent3()->getId() > 0} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Zus. Inhalt 3:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getPaperAddContent3()-&gt;getName()}, {$Calc-&gt;getPaperAddContent3Weight()} g<br />
			{$Calc-&gt;getPagesAddContent3()} Seiten, {$Calc-&gt;getChromaticitiesAddContent3()-&gt;getName()}</span></td>
		</tr>
		<!-- {/if} --><!-- {/if} --><!-- {if $Calc->getTextProcessing() != '' && $Calc->getTextProcessing() != 0} -->
		<tr>
			<td style="width:130px"><span style="font-size:11px">Verarbeitung:</span></td>
			<td><span style="font-size:11px">{$Calc-&gt;getTextProcessing()}</span></td>
		</tr>
		<!-- {/if} -->
	</tbody>
</table>

<p>&nbsp;</p>

<table border="0" cellpadding="1" cellspacing="1" style="width:450px">
	<tbody><!--  {foreach $Calc->getPositionsForDocuments() as $Pos} -->
		<tr><!-- {if $Pos->getShowQuantity() > 0} -->
			<td><span style="font-size:11px">{$Pos-&gt;getQuantity()} Stk.</span></td>
			<!-- {/if} -->
			<td style="text-align:center"><span style="font-size:11px">{$Pos-&gt;getComment()}</span></td>
			<!-- {if $Pos->getShowPrice() > 0} -->
			<td style="text-align:right"><span style="font-size:11px">{PrintPrice var=$Pos-&gt;getCalculatedPrice()} {$Currency}</span></td>
			<!-- {/if} -->
		</tr>
		<!--  {/foreach} -->
	</tbody>
</table>

<p>&nbsp;</p>

<table border="0" cellpadding="1" cellspacing="1" style="width:450px">
	<tbody><!-- {if $Order->getShowPricePer1000() > 0} -->
		<tr>
			<td><span style="font-size:11px">Preis pro 1000 Stk.</span></td>
			<!-- {$price_per_thousand = $Calc->getSummaryPrice() / $Calc->getAmount() * 1000} -->
			<td style="text-align:right"><span style="font-size:11px">{PrintPrice var=$price_per_thousand} {$Currency}</span></td>
		</tr>
		<!-- {/if} -->
		<tr>
			<td><span style="font-size:11px"><strong>Preis netto</strong></span></td>
			<td style="text-align:right"><span style="font-size:11px"><strong>{PrintPrice var=$Calc-&gt;getSummaryPrice()} {$Currency}</strong></span></td>
		</tr>
	</tbody>
</table>

<p>&nbsp;</p>

<p><!--  {/foreach} --></p>

<p><span style="font-size:12px">Wir hoffen, dass unser Angebot Ihren Vorstellungen entspricht und w&uuml;rden uns &uuml;ber eine Auftragserteilung freuen.<br />
F&uuml;r R&uuml;ckfragen stehen wir Ihnen selbstverst&auml;ndlich gerne zur Verf&uuml;gung. </span></p>

<p><span style="font-size:12px">Dieses Angebot hat eine G&uuml;ltigkeit von 6 Wochen.<br />
Es gelten die allgemeinen Gesch&auml;ftsbedingungen der {$UserClient}.</span></p>

<p><span style="font-size:12px">Mit freundlichen Gr&uuml;&szlig;en</span></p>

<p>&nbsp;</p>

<p>&nbsp;</p>
*/ 
?>

