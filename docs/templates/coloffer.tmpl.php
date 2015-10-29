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

$orderpos = $order->getPositions();

//Gesamtpreis
$sum = 0;
$gesnetto = 0;
$taxeskey = Array();
$taxes = Array();
foreach ($orderpos as $op)
{
    $gesnetto += $op->getNetto();
    if(!in_array($op->getTax(), $taxeskey))
        $taxeskey[] = $op->getTax();

    $taxes[$op->getTax()] += $op->getNetto() / 100 * $op->getTax();
}

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/coloffer.tmpl';
$datei = ckeditor_to_smarty($tmp);


// Table

$smarty->assign('OrderPos',$orderpos);


$smarty->assign('DeliveryCosts',$order->getDeliveryCosts());
if ($order->getDeliveryCosts()) {
    // Versandkosten werden auch besteuert (hier 19%)
    if(!in_array("19", $taxeskey))
        $taxeskey[] = "19";
    $taxes["19"] += $order->getDeliveryCosts() / 100 * 19;
    $gesnetto += $order->getDeliveryCosts();
}

$sum = $gesnetto;
$smarty->assign('SumNetto',$gesnetto);


// Steuern
$smarty->assign('Taxes',$taxes);
$smarty->assign('TaxesKey',$taxeskey);

// Gesamtsumme
$sum = $gesnetto + array_sum ($taxes);
$smarty->assign('SumBrutto',$sum);

// Footer

$smarty->assign('UserClient',$_USER->getClient()->getName());

$htmldump = $smarty->fetch('string:'.$datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);

?>
<?php /*
<p><span style="font-size:14px"><span style="font-family:arial,helvetica,sans-serif"><strong>Angebot Nr. {$Id}</strong></span><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></span><span style="font-size:14px"><strong>&nbsp; Kunden-Nr. {$CustomerId}</strong></span><br />
<span style="font-size:12px">Auftrag: {$OrderTitle}</span></p>

<p>&nbsp;</p>

<p><br />
<span style="font-family:arial,helvetica,sans-serif"><span style="font-size:12px">Sehr geehrte Damen und Herren<br />
vielen Dank f&uuml;r Ihre Anfrage.</span></span></p>

<p><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:12px">Nach Ihren Angaben unterbreiten wir Ihnen nachfolgendes Angebot:</span></span></p>

<table border="0" cellpadding="1" cellspacing="1" style="width:450px">
	<thead>
		<tr>
			<th scope="col" style="border-color:rgb(0, 0, 0); text-align:center; width:25px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">Pos.</span></span></th>
			<th scope="col" style="border-color:rgb(0, 0, 0); text-align:left; width:225px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">Beschreibung</span></span></th>
			<th scope="col" style="border-color:rgb(0, 0, 0); text-align:left; width:40px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">Menge</span></span></th>
			<th scope="col" style="border-color:rgb(0, 0, 0); text-align:left; width:40px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">USt.</span></span></th>
			<th scope="col" style="border-color:rgb(0, 0, 0); text-align:right; width:60px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">EP (Netto)</span></span></th>
			<th scope="col" style="border-color:rgb(0, 0, 0); text-align:right; width:60px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">GP (Netto)</span></span></th>
		</tr>
	</thead>
	<tbody><!-- {foreach $OrderPos as $Pos} -->
		<tr>
			<td style="text-align:center; width:25px">
			<p><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">{$Pos@iteration}</span></span></p>
			</td>
			<td style="text-align:left; width:225px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">{$Pos-&gt;getComment()}</span></span></td>
			<td style="text-align:left; width:40px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">{$Pos-&gt;getQuantity()}</span></span></td>
			<td style="text-align:left; width:40px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">{$Pos-&gt;getTax()} %</span></span></td>
			<td style="text-align:right; width:60px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">{$Pos-&gt;getPrice()} {$Currency}</span></span></td>
			<td style="text-align:right; width:60px"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px">{$Pos-&gt;getNetto()} {$Currency}</span></span></td>
		</tr>
		<!-- {/foreach} -->
	</tbody>
</table>

<p>&nbsp;</p>

<table border="0" cellpadding="1" cellspacing="1" style="height:106px; width:455px">
	<tbody><!-- {if $DeliveryCosts gt 0} -->
		<tr>
			<td style="text-align:right; width:300px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif">Porto und Verpackung</span></span></td>
			<td style="text-align:right; width:150px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif">{$DeliveryCosts} {$Currency}</span></span></td>
		</tr>
		<!-- {/if} -->
		<tr>
			<td style="text-align:right; width:300px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif"><strong>Gesamtsumme (Netto)</strong></span></span></td>
			<td style="text-align:right; width:150px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif"><strong>{$SumNetto} {$Currency}</strong></span></span></td>
		</tr>
		<!-- {foreach $TaxesKey as $Key} -->
		<tr>
			<td style="text-align:right; width:300px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif">USt. ({$Key} %)</span></span></td>
			<td style="text-align:right; width:150px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif">&nbsp;{$Taxes.$Key} {$Currency}</span></span></td>
		</tr>
		<!-- {/foreach} -->
		<tr>
			<td style="text-align:right; width:300px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif"><strong>Gesamtsumme (Brutto)</strong></span></span></td>
			<td style="text-align:right; width:150px"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif"><strong>{$SumBrutto} {$Currency}</strong></span></span></td>
		</tr>
	</tbody>
</table>

<p><span style="font-size:12px"><span style="font-family:arial,helvetica,sans-serif">Wir hoffen, dass unser Angebot Ihren Vorstellungen entspricht und w&uuml;rden uns &uuml;ber eine Auftragserteilung freuen.<br />
F&uuml;r R&uuml;ckfragen stehen wir Ihnen selbstverst&auml;ndlich gerne zur Verf&uuml;gung. </span></span></p>

<p><span style="font-size:12px"><span style="font-family:arial,helvetica,sans-serif">Dieses Angebot hat eine G&uuml;ltigkeit von 6 Wochen.<br />
Es gelten die allgemeinen Gesch&auml;ftsbedingungen der {$UserClient}.</span></span></p>

<p><span style="font-size:12px"><span style="font-family:arial,helvetica,sans-serif">Mit freundlichen Gr&uuml;&szlig;en<br />
{$UserFirstname} {$UserLastname}<br />
<br />
{$UserClient}</span></span></p>

*/ ?>