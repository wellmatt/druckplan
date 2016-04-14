<?
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

$calcs = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
$perf = new Perferences();
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Auftragsdaten: <b><?= $order->getNumber() ?></b></h3>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-1">
				<b><?= $_LANG->get('Produkt') ?>:</b>
			</div>
			<div class="col-md-2">
				<?= $order->getProduct()->getName() ?>
			</div>
			<div class="col-md-2">
				<b><?= $_LANG->get('Beschreibung') ?>:</b>
			</div>
			<div class="col-md-2">
				<?= $order->getProduct()->getDescription() ?>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {
		$( "#tabs" ).tabs({ selected: 0 });
	});
</script>
<script>
	$(function() {
		$( ".innertabs" ).tabs({ selected: 0 });
	});
</script>

<div id="tabs">
	<ul>
	<?php $f = 0; foreach ($calcs as $c) { ?>
		<li><a href="#tabs-<?php echo $f;?>"><?=$_LANG->get('Teilauftrag')?> # <?=$f?> - <?=$_LANG->get('Auflage')?> <?=printBigInt($c->getAmount())?></a></li>
		<?php $f++; } ?>
	</ul>
    <? $x = 0;
    foreach ($calcs as $c) {
    	$part_count = 0;?>
	<div id="tabs-<?php echo $x;?>">
    	<div class="innertabs" id="innertabs<?=$x?>">
        	<ul>
            	<?php
            	if($c->getPaperContent()->getId())
            	    echo '<li><a href="#innertabs'.$x.'-0">Inhalt 1</a></li>';
            	if($c->getPaperAddContent()->getId())
            	    echo '<li><a href="#innertabs'.$x.'-1">Inhalt 2</a></li>';
            	if($c->getPaperAddContent2()->getId())
            	    echo '<li><a href="#innertabs'.$x.'-2">Inhalt 2</a></li>';
            	if($c->getPaperAddContent3()->getId())
            	    echo '<li><a href="#innertabs'.$x.'-3">Inhalt 4</a></li>';
        	    if($c->getPaperEnvelope()->getId())
            	    echo '<li><a href="#innertabs'.$x.'-4">Umschlag</a></li>';
            	?>
        	</ul>
        	<div id="innertabs<?=$x?>-0">
                <? if($c->getPaperContent()->getId()) {
                	$schemes = array();
                	$part = Calculation::PAPER_CONTENT;
                	$calc = $c;
                	$mach_entry = Machineentry::getMachineForPapertype($part, $c->getId());
                	foreach ($mach_entry as $me)
                	{
                	    if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                	        $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                	    {
                	        $mach = $me->getMachine();
                	        $machentry = $me;
                	    }
                	}
                	$product_max = 0;
                	$product_counted = false;

                	include('scheme.php');
                	?>
                	<table>
                	<?
                	for ($i = 0; $i < count($schemes); $i++)
                	{ // vorschau pro anweichender Produkzahl pro Bogen
                		?>
                		<tr><td>
                			<? /*<u><?=$_LANG->get('Inhalt')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
                			<u><?=$_LANG->get('Inhalt')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
                			<?=$c->getPaperContent()->getName()?>, <?=$c->getPaperContentWeight()?> g,<br>
                			<?=$c->getPaperContentWidth()?> x <?=$c->getPaperContentHeight()?> <?=$_LANG->get('mm')?>,
                			<?=printPrice($c->getCutContent())?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?> <br>
                			<?
                			    if ($machentry->getRoll_dir() == 0)
                			    {
                    			    if($c->getPaperContent()->getPaperDirection($c, Calculation::PAPER_CONTENT) == Paper::PAPER_DIRECTION_SMALL){
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                    				} else {
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                    				}
                			    } elseif ($machentry->getRoll_dir() == 1)
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                			    } else
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                			    }
                				?>
                			</br>
                			<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_CONTENT?>&max=<?=$schemes[$i]['nutzen']?>&counted=true"
                						width="700" height="700" ></object>
                		</td></tr></br>
                		<?
                		if ($perf->getCalc_detailed_printpreview() == 0){
                		  break;
                		}
                	}
                	?>
                	</table>
                	<?
                	$part_count++;
            	} ?>
        	</div>
        	<div id="innertabs<?=$x?>-1">
                <? if($c->getPaperAddContent()->getId()) {
                	$schemes = array();
                	$part = Calculation::PAPER_ADDCONTENT;
                	$calc = $c;
                	$mach_entry = Machineentry::getMachineForPapertype($part, $c->getId());
                	foreach ($mach_entry as $me)
                	{
                	    if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                	        $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                	    {
                	        $mach = $me->getMachine();
                	        $machentry = $me;
                	    }
                	}
                	$product_max = 0;
                	$product_counted = false;

                	include('scheme.php');
                	?>
                	<table>
                	<?
                	for ($i = 0; $i < count($schemes); $i++)
                	{ // vorschau pro anweichender Produkzahl pro Bogen
                		?>
                		<tr><td>
                			<? /*<u><?=$_LANG->get('zus. Inhalt')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
                			<u><?=$_LANG->get('zus. Inhalt')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
                			<?=$c->getPaperAddContent()->getName()?>, <?=$c->getPaperAddContentWeight()?> <?=$_LANG->get('g')?>,<br>
                			<?=$c->getPaperAddContentWidth()?> x <?=$c->getPaperAddContentHeight()?> <?=$_LANG->get('mm')?>,
                			<?=printPrice($c->getCutAddContent())?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
                			<?
                			    if ($machentry->getRoll_dir() == 0)
                			    {
                    			    if($c->getPaperContent()->getPaperDirection($c, Calculation::PAPER_CONTENT) == Paper::PAPER_DIRECTION_SMALL){
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                    				} else {
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                    				}
                			    } elseif ($machentry->getRoll_dir() == 1)
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                			    } else
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                			    }
                				?>
                			</br>
                			<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT?>&max=<?=$schemes[$i]['nutzen']?>&counted=true"
                					width="700" height="700" ></object>
                		</td></tr></br>
                		<?
                		if ($perf->getCalc_detailed_printpreview() == 0){
                		  break;
                		}
                	}
                	?>
                	</table>
                	<?
                	$part_count++;
            	} ?>
        	</div>
        	<div id="innertabs<?=$x?>-4">
                <? if($c->getPaperEnvelope()->getId()) {
                	$schemes = array();
                	$part = Calculation::PAPER_ENVELOPE;
                	$calc = $c;
                	$mach_entry = Machineentry::getMachineForPapertype($part, $c->getId());
                	foreach ($mach_entry as $me)
                	{
                	    if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                	        $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                	    {
                	        $mach = $me->getMachine();
                	        $machentry = $me;
                	    }
                	}
                	$product_max = 0;
                	$product_counted = false;

                	include('scheme.php');
                	?>
                	<table>
                	<?

                	for ($i = 0; $i < count($schemes); $i++)
                	{ // vorschau pro anweichender Produkzahl pro Bogen
                	?>
                		<tr><td>
                			<? /*<u><?=$_LANG->get('Umschlag')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
                			<u><?=$_LANG->get('Umschlag')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
                			<?=$c->getPaperEnvelope()->getName()?>, <?=$c->getPaperEnvelopeWeight()?> <?=$_LANG->get('g')?>,<br>
                			<?=$c->getPaperEnvelopeWidth()?> x <?=$c->getPaperEnvelopeHeight()?> <?=$_LANG->get('mm')?>,
                			<?=printPrice($c->getCutEnvelope())?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
                			<?
                			    if ($machentry->getRoll_dir() == 0)
                			    {
                    			    if($c->getPaperContent()->getPaperDirection($c, Calculation::PAPER_CONTENT) == Paper::PAPER_DIRECTION_SMALL){
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                    				} else {
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                    				}
                			    } elseif ($machentry->getRoll_dir() == 1)
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                			    } else
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                			    }
                				?>
                			</br>
            				<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ENVELOPE?>&max=<?=$schemes[$i]['nutzen']?>&counted=true"
                					width="700" height="700" ></object>
                		</td></tr></br>
                		<?
                		if ($perf->getCalc_detailed_printpreview() == 0){
                		  break;
                		}
                	}
                	?>
                	</table>
                	<?
                	$part_count++;
            	} ?>
        	</div>
        	<div id="innertabs<?=$x?>-2">
                <? if($c->getPaperAddContent2()->getId()) {
            		$schemes = array();
            		$part = Calculation::PAPER_ADDCONTENT2;
            		$calc = $c;
                	$mach_entry = Machineentry::getMachineForPapertype($part, $c->getId());
                	foreach ($mach_entry as $me)
                	{
                	    if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                	        $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                	    {
                	        $mach = $me->getMachine();
                	        $machentry = $me;
                	    }
                	}
            		$product_max = 0;
            		$product_counted = false;

            		include('scheme.php');
            		?>
            		<table>
            		<?

            		for ($i = 0; $i < count($schemes); $i++)
            		{ // vorschau pro anweichender Produkzahl pro Bogen
            		?>
                		<tr><td>
            				<? /*<u><?=$_LANG->get('zus. Inhalt 2')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
            				<u><?=$_LANG->get('zus. Inhalt 2')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
            				<?=$c->getPaperAddContent2()->getName()?>, <?=$c->getPaperAddContent2Weight()?> <?=$_LANG->get('g')?>,<br>
            				<?=$c->getPaperAddContent2Width()?> x <?=$c->getPaperAddContent2Height()?> <?=$_LANG->get('mm')?>,
            				<?=$_CONFIG->anschnitt?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
                			<?
                			    if ($machentry->getRoll_dir() == 0)
                			    {
                    			    if($c->getPaperContent()->getPaperDirection($c, Calculation::PAPER_CONTENT) == Paper::PAPER_DIRECTION_SMALL){
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                    				} else {
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                    				}
                			    } elseif ($machentry->getRoll_dir() == 1)
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                			    } else
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                			    }
                				?>
                			</br>
        					<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT2?>"
            						width="700" height="700" ></object>
                		</td></tr></br>
            			<?
                		if ($perf->getCalc_detailed_printpreview() == 0){
                		  break;
                		}
            		}
            		?>
            		</table>
            		<?
            	   $part_count++;
            	} ?>
        	</div>
        	<div id="innertabs<?=$x?>-3">
                <? if($c->getPaperAddContent3()->getId()) {
            		$schemes = array();
            		$part = Calculation::PAPER_ADDCONTENT3;
            		$calc = $c;
                	$mach_entry = Machineentry::getMachineForPapertype($part, $c->getId());
                	foreach ($mach_entry as $me)
                	{
                	    if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                	        $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                	    {
                	        $mach = $me->getMachine();
                	        $machentry = $me;
                	    }
                	}
            		$product_max = 0;
            		$product_counted = false;

            		include('scheme.php');
            		?>
            		<table>
            		<?

            		for ($i = 0; $i < count($schemes); $i++)
            		{ // vorschau pro anweichender Produkzahl pro Bogen
            		?>
                		<tr><td>
            				<? /*<u><?=$_LANG->get('zus. Inhalt 3')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br> */?>
            				<u><?=$_LANG->get('zus. Inhalt 3')?> (<?=$schemes[$i]['count']?> Seite(n) mit <?=$schemes[$i]['nutzen']?> Nutzen):</u><br>
            				<?=$c->getPaperAddContent3()->getName()?>, <?=$c->getPaperAddContent3Weight()?> <?=$_LANG->get('g')?>,<br>
            				<?=$c->getPaperAddContent3Width()?> x <?=$c->getPaperAddContent3Height()?> <?=$_LANG->get('mm')?>,
            				<?=$_CONFIG->anschnitt?> <?=$_LANG->get('mm')?> <?=$_LANG->get('Anschitt')?><br>
                			<?
                			    if ($machentry->getRoll_dir() == 0)
                			    {
                    			    if($c->getPaperContent()->getPaperDirection($c, Calculation::PAPER_CONTENT) == Paper::PAPER_DIRECTION_SMALL){
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                    				} else {
                    					echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                    				}
                			    } elseif ($machentry->getRoll_dir() == 1)
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('breite Bahn');
                			    } else
                			    {
                			        echo $_LANG->get('Laufrichtung').":".$_LANG->get('schmale Bahn');
                			    }
                				?>
                			</br>
        					<object data="libs/modules/calculation/order.printpreview.pdf.php?calc_id=<?=$c->getId()?>&part=<?=Calculation::PAPER_ADDCONTENT3?>"
            						width="700" height="700" ></object>
                		</td></tr></br>
            			<?
                		if ($perf->getCalc_detailed_printpreview() == 0){
                		  break;
                		}
            		}
            		?>
            		</table>
            		<?
            	$part_count++;
            	} ?>
        	</div>
       </div>
    </div>
    <? $x++;
    } ?>
</div>