<?php
/*
 * ds / 09.05.2014
 * GET param 'cloneProduct' is called by paper.add.php to notify this order to
 * clone the assigned order with the previous created paper. 
 */ 
if(isset($_GET['cloneProduct']) && isset($_GET['paperId'])) {
	
	$paperId = (int) $_GET['paperId'];
	
	require_once $_BASEDIR . 'libs/modules/paper/paper.class.php';
	$selectedPaper = new Paper($paperId);
	
	// Assign the cloned product
	$product = $order->getProduct();
	$newProduct = clone $product;
	$newProduct->clearId();
	$newProduct->setIsIndividual(true);
	$newProduct->setName($_GET['newProductName']); 
	
	
	/*
	 * Update Paper assignments
	 */
	$paperweights = $newProduct->getSelectedPapersIds(0);
	
	$paperweights['content'][$paperId] = array(); // create new paper assignment for this product
	$paperweights['content'][$paperId]['id'] = $paperId;
	foreach($selectedPaper->getWeights() as $w) {
		$paperweights['content'][$paperId][$w] = 1;
	}
	$newProduct->setSelectedPapersIds($paperweights);
	$newProduct->save();
	
	$order->setProduct($newProduct);
	$order->save();
	
	// Remove the session helper array.
	$_SESSION['_alternativePaperMode'] = null;
	unset($_SESSION['_alternativePaperMode']);
	
	// Force user to proceed next step insteadof just save this form.
	$hideFormSaveButton = true;
}
?>

<script language="javascript">
    function clickProductFormat(type, id)
    {
        $("#"+id).toggleClass( "btn-info").addClass("btn-success");

        id=id.substr(3);
        document.getElementById('h_product_format').value = id;

        if(id != 0)
        {

            document.getElementById('paper_free').style.display = 'none';
            updateFormat(id);
        } else
        {
            //document.getElementById('paper_free').style.display = '';
            document.getElementById('order_product_width').value = 0;
            document.getElementById('order_product_height').value = 0;
            document.getElementById('order_product_width_open').value = 0;
            document.getElementById('order_product_height_open').value = 0;
        }
        
        if(document.getElementById('h_content_paper').value == 0 || document.getElementById('h_content_paper').value == '')
        { 
            $.post("libs/modules/calculation/order.ajax.php", 
            {exec: 'updatePaperprops', product: <?= $order->getProduct()->getId() ?>, id: id}, 
            function(data) {
                // Work on returned data
                
                document.getElementById('paper').innerHTML = data;
            });
        }
        
    }
    
    function updateFormat(id)
    {
        $.post("libs/modules/calculation/order.ajax.php", {exec: 'updateFormats', id: id}, function(data) {
            // Work on returned data
            var sizes = data.split('_');
            document.getElementById('order_product_width').value = sizes[0];
            document.getElementById('order_product_height').value = sizes[1];
            calcOpenFormat();
        });
    }
    
    function clickPaperContent(id)
    {
        $("#"+id).toggleClass( "btn-info").addClass("btn-success");
        id=id.substr(3);
        
        document.getElementById('h_content_paper').value = id;
        oldval = document.getElementById('h_content_paper_weight').value;
        
        $.post("libs/modules/calculation/order.ajax.php", 
        {exec: 'updatePaperWeight', product: <?= $order->getProduct()->getId() ?>, id: id, oldval: oldval}, 
        function(data) {
            // Work on returned data
            document.getElementById('paper_weight').innerHTML = data;
        });         
    }
    
    
    function clickContentWeight(id)
    {
        $("#"+id).toggleClass( "btn-info").addClass("btn-success");
        id=id.substr(3);
        
        document.getElementById('h_content_paper_weight').value = id;        
        
        if(document.getElementById('h_content_pages').value == 0 || document.getElementById('h_content_pages').value == '')
        {   
            $.post("libs/modules/calculation/order.ajax.php", 
            {exec: 'numberPages', product: <?= $order->getProduct()->getId() ?>, id: id, orderId: <?= $order->getId() ?>}, function(data) {
                // Work on returned data 
                document.getElementById('number_pages').innerHTML = data;
            });
        }
    }
    
    function clickContentPages(id)
    {
        $("#"+id).toggleClass( "btn-info").addClass("btn-success");
        id=id.substr(3);
        if (id != document.getElementById('h_content_pages').value)
            oldPages = document.getElementById('h_content_pages').value;
        
        document.getElementById('h_content_pages').value = id;

        if(document.getElementById('h_content_chromaticity').value == 0 || document.getElementById('h_content_chromaticity').value == '' || oldPages == 1 || id == 1)
        {
            $.post("libs/modules/calculation/order.ajax.php", 
            {exec: 'printChrom', product: <?= $order->getProduct()->getId() ?>, id: id, orderId: <?= $order->getId() ?>}, function(data) {
                // Work on returned data 
                document.getElementById('print_chrom').innerHTML = data;
            });

            document.getElementById('h_content_chromaticity').value = 0;
        }
    }


    // Falls Eingabe per Textfeld erfolgt
    function focusContentPages()
    {
        $("#"+id).toggleClass( "btn-info").addClass("btn-success");
        id=0;
        if(document.getElementById('h_content_chromaticity').value == 0 || document.getElementById('h_content_chromaticity').value == '' || oldPages == 1 || id == 1)
        {
            $.post("libs/modules/calculation/order.ajax.php", 
            {exec: 'printChrom', product: <?= $order->getProduct()->getId() ?>, id: id, orderId: <?= $order->getId() ?>}, function(data) {
                // Work on returned data 
                document.getElementById('print_chrom').innerHTML = data;
            });


            document.getElementById('h_content_chromaticity').value = 0;
        }
    }

    function setContentPages(val)
    {
        if (val < <?=$order->getProduct()->getPagesFrom()?> || val > <?=$order->getProduct()->getPagesTo()?>)
        {
            alert('Seitenzahl muss im Bereich von <?=$order->getProduct()->getPagesFrom()?> bis <?=$order->getProduct()->getPagesTo()?> liegen');
            document.getElementById('numberpages_content').focus();
        }
        if (val != document.getElementById('h_content_pages').value)
            oldPages = document.getElementById('h_content_pages').value;
        
        document.getElementById('h_content_pages').value = val;

    }
    
    
function clickContentChromaticity(id)
            {
                $("#"+id).toggleClass( "btn-info").addClass("btn-success");

                id=id.substr(3);
                
                document.getElementById('h_content_chromaticity').value = id;
                
			<? 	if ($order->getProduct()->getHasAddContent()) { ?>
                    if(document.getElementById('h_addcontent_paper').value == 0 || document.getElementById('h_addcontent_paper').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateAddedPaper', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id, hiddenAddPaper : document.getElementById('h_addcontent_paper').value },
                        function(data) {
                            // Work on returned data        
                            document.getElementById('additional_paper').innerHTML = data;
                        });
                    }
                    if(document.getElementById('h_envelope_paper').value == 0)
                    {
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateEnvPaper', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id, hiddenEnvPaper : document.getElementById('h_envelope_paper').value},
                        function(data) {
                            // Work on returned data
                            document.getElementById('envelope_paperprops').innerHTML = data;
                        });
                        document.getElementById('tr_order_amount').style.display = '';
                        document.getElementById('tr_order_sorts').style.display = '';
                    }
                    if(document.getElementById('h_addcontent2_paper').value == 0 || document.getElementById('h_addcontent2_paper').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateAdded2Paper', 
                            product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id, hiddenAddPaper : document.getElementById('h_addcontent2_paper').value },
                        function(data) {
                            // Work on returned data        
                            document.getElementById('additional2_paper').innerHTML = data;
                        });
                    }
                    if(document.getElementById('h_addcontent_paper').value == 0 || document.getElementById('h_addcontent_paper').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateAdded3Paper', 
                            product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id, hiddenAddPaper : document.getElementById('h_addcontent_paper').value },
                        function(data) {
                            // Work on returned data        
                            document.getElementById('additional3_paper').innerHTML = data;
                        });
                    }
			<? 	} else if ($order->getProduct()->getHasEnvelope()) { ?>
                    if(document.getElementById('h_envelope_paper').value == 0 || document.getElementById('h_envelope_paper').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateEnvPaper', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id},
                        function(data) {
                            // Work on returned data
                            document.getElementById('envelope_paperprops').innerHTML = data;
                        });
                    }
			<? } else if ($order->getProduct()->hasMachineOfType(Machine::TYPE_FOLDER)) { ?>
                   // if(document.getElementById('h_folding').value == 0 || document.getElementById('h_folding').value == '')
                   // {
                        if(document.getElementById('h_content_pages').value >= 10 && document.getElementById('tr_order_amount').style.display == 'none'){
                        	 document.getElementById('tr_order_amount').style.display = '';
                             document.getElementById('tr_order_sorts').style.display = '';
                        }
                        $.post("libs/modules/calculation/order.ajax.php", 
                                {exec: 'updateFoldtypes', pages: document.getElementById('h_content_pages').value, id: id},
                                function(data) {
                            // Work on returned data 
                            document.getElementById('order_folding').innerHTML = data;
                            //		calcOpenFormat();
                        });
                   // }
			<? } ?>
			document.getElementById('tr_order_amount').style.display = '';
            document.getElementById('tr_order_sorts').style.display = '';
            }

            
<? // ------------------------------ JavaScript fuer den zus. Inhalt --------------------------------------------------------- ?>
            
            function clickAddPaper(id)
            {
                $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                id=id.substr(3);
                
                document.getElementById('h_addcontent_paper').value = id;
                oldval = document.getElementById('h_addcontent_paper_weight').value;
                
                if (id == 0)
                {
                    document.getElementById('tr_addcontent_weight').style.display = 'none';
                    document.getElementById('tr_addcontent_chromaticity').style.display = 'none';
                    document.getElementById('tr_addcontent_pages').style.display = 'none';
                    if(<?
						if ($calc->getId())
						    echo $calc->getId(); else
						    echo "0";
						?> == 0)
                        {
                            $.post("libs/modules/calculation/order.ajax.php", 
                            {exec: 'updateEnvPaper', product: <?= $order->getProduct()->getId() ?>, id: id}, 
                            function(data) {
                                // Work on returned data
                            
                                document.getElementById('envelope_paperprops').innerHTML = data;
                            });
                        }
                    } else
                    {
                        document.getElementById('tr_addcontent_weight').style.display = '';
                        document.getElementById('tr_addcontent_chromaticity').style.display = '';
                        document.getElementById('tr_addcontent_pages').style.display = '';
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateAddPaperWeight', product: <?= $order->getProduct()->getId() ?>, id: id, oldval: oldval}, 
                        function(data) {
                            // Work on returned data
                        
                            document.getElementById('additional_paperweight').innerHTML = data;
                        });
                    }
                }
            
            
                function clickAddPaperWeight(id)
                {
                    $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                    id=id.substr(3);
                
                    document.getElementById('h_addcontent_paper_weight').value = id;

                    if(document.getElementById('h_addcontent_pages').value == 0 || document.getElementById('h_addcontent_pages').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php",
                            {exec: 'updateAddPaperPages', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id},
                            function(data) {
                                // Work on returned data
                                document.getElementById('additional_paperpages').innerHTML = data;
                            });
                    }
                }
            
            
                function clickAddPaperChromaticity(id)
                {
                    $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                    id=id.substr(3);
                    document.getElementById('h_addcontent_chromaticity').value = id;

                    <? if ($order->getProduct()->getHasEnvelope()) { ?>
                        if(document.getElementById('h_envelope_paper').value == 0 || document.getElementById('h_envelope_paper').value == '')
                        {
                            $.post("libs/modules/calculation/order.ajax.php",
                                {exec: 'updateEnvPaper', product: <?= $order->getProduct()->getId() ?>, id: id},
                                function(data) {
                                    // Work on returned data

                                    document.getElementById('envelope_paperprops').innerHTML = data;
                                });
                        }
                    <? } else if ($order->getProduct()->hasMachineOfType(Machine::TYPE_FOLDER)) { ?>
                        $.post("libs/modules/calculation/order.ajax.php", {exec: 'updateFoldtypes', pages: document.getElementsByName('h_content_pages')[0].value, id: id}, function(data) {
                            // Work on returned data
                            document.getElementById('order_folding').innerHTML = data;
                            //		calcOpenFormat();
                        });
                    <? } else { ?>
                        document.getElementById('tr_order_amount').style.display = '';
                        document.getElementById('tr_order_sorts').style.display = '';
                    <? } ?>
                }



            function clickAddPaperPages(id)
            {
                $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                id=id.substr(3);
                document.getElementById('h_addcontent_pages').value = id;

                if(document.getElementById('h_addcontent_chromaticity').value == 0 || document.getElementById('h_addcontent_chromaticity').value == '')
                {
                    $.post("libs/modules/calculation/order.ajax.php",
                        {exec: 'updateAddPaperChroma', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id},
                        function(data) {
                            // Work on returned data

                            document.getElementById('additional_paperchroma').innerHTML = data;
                        });
                }
            }


            // Falls Seitenzahl haendisch eingetragen wird
            function focusAddContentPages()
            {
                id = 0;
<? if ($order->getProduct()->getHasEnvelope()) { ?>
                    if(document.getElementById('h_envelope_paper').value == 0 || document.getElementById('h_envelope_paper').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateEnvPaper', product: <?= $order->getProduct()->getId() ?>, id: id}, 
                        function(data) {
                            // Work on returned data

                            document.getElementById('envelope_paperprops').innerHTML = data;
                        });
                    } 
<? } else if ($order->getProduct()->hasMachineOfType(Machine::TYPE_FOLDER)) { ?>
                    $.post("libs/modules/calculation/order.ajax.php", {exec: 'updateFoldtypes', pages: document.getElementsByName('h_content_pages')[0].value, id: id}, function(data) {
                        // Work on returned data 
                        document.getElementById('order_folding').innerHTML = data;
                        //		calcOpenFormat();
                    });
<? } else { ?>
                    document.getElementById('tr_order_amount').style.display = '';
                    document.getElementById('tr_order_sorts').style.display = '';
<? } ?>
            }

            function setAddContentPages(val)
            {
                document.getElementById('h_addcontent_pages').value = val;
            }
</script>
<? require_once 'order.js.add.php';?>
<script language="javascript">            
<? // ------------------------------ JavaScript fuer den Umschlag ------------------------------------------------------------- ?>
            
            function clickEnvelopePaper(id)
            {
                $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                id=id.substr(3);
                document.getElementById('h_envelope_paper').value = id;
                oldval = document.getElementById('h_envelope_paper_weight').value;

                if (id == 0)
                {
                    document.getElementById('tr_envelope_weight').style.display = 'none';
                    document.getElementById('tr_envelope_chromaticity').style.display = 'none';
                    document.getElementById('tr_envelope_pages').style.display = 'none';
                    document.getElementById('tr_order_amount').style.display = '';
                    document.getElementById('tr_order_sorts').style.display = '';
                } else
                {
                    document.getElementById('tr_envelope_weight').style.display = '';
                    document.getElementById('tr_envelope_chromaticity').style.display = '';
                    document.getElementById('tr_envelope_pages').style.display = '';
                    //neu eingef�gt//
                    $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateEnvPaperWeight', product: <?= $order->getProduct()->getId() ?>, id: id, oldval: oldval}, 
                        function(data) {
                            // Work on returned data
                        
                            document.getElementById('envelope_paperweight').innerHTML = data;
                        });
                     //Ende neu eingef�gt//
                    
                }
            }
            
                    function clickEnvelopeWeight(id)
                    {
                        $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                        id=id.substr(3);
                        document.getElementById('h_envelope_paper_weight').value = id;

                        if(document.getElementById('h_envelope_pages').value == 0 || document.getElementById('h_envelope_pages').value == '')
                        {

                            $.post("libs/modules/calculation/order.ajax.php",
                                {exec: 'updateEnvPaperPages', orderId: <?= $order->getId() ?>, id: id},
                                function(data) {
                                    // Work on returned data

                                    document.getElementById('envelope_paperpages').innerHTML = data;
                                });
                        }
                    }
            
            
            
                    function clickEnvelopeChromaticity(id)
                    {
                        $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                        id=id.substr(3);
                
                        document.getElementById('h_envelope_chromaticity').value = id;


                        // Offenes Format Umschlag anzeigen
                        document.getElementById('tr_envelope_format').style.display = '';

                        /* Offenes Format Umschlag anpassen */
                        var prod_width = parseInt(document.getElementById('order_product_width').value);
                        var prod_height = parseInt(document.getElementById('order_product_height').value);
                        id = document.getElementById('h_envelope_pages').value;

                        // Get chromaticity factor
                        var chroma = document.getElementById('h_envelope_chromaticity').value;
                        $.post("libs/modules/calculation/order.ajax.php", {exec: "getReversePrinting", chromaId: chroma}, function(data) {
    						// Work on returned data
    						if(data == 0)
    							chromaFactor = 2;
    						else
    							chromaFactor = 1;
    							
    	                    if(id == 2)
    	                    {
    	                        document.getElementById('order_envelope_width_open').value = prod_width * 1 * chromaFactor;
    	                        document.getElementById('order_envelope_height_open').value = prod_height;
    	                    } else if(id == 4)
    	                    {
    	                        document.getElementById('order_envelope_width_open').value = prod_width * 2 * chromaFactor;
    	                        document.getElementById('order_envelope_height_open').value = prod_height;
    	                    } else if (id == 6)
    	                    {
    	                        document.getElementById('order_envelope_width_open').value = prod_width * 3 * chromaFactor;
    	                        document.getElementById('order_envelope_height_open').value = prod_height;
    	                    } else if (id == 8)
    	                    {
    	                        document.getElementById('order_envelope_width_open').value = prod_width * 4 * chromaFactor;
    	                        document.getElementById('order_envelope_height_open').value = prod_height;
    	                    } else if (id == 10)
    	                    {
    	                        document.getElementById('order_envelope_width_open').value = prod_width * 5 * chromaFactor;
    	                        document.getElementById('order_envelope_height_open').value = prod_height;
    	                    }
    							
    					});

                        <? if ($order->getProduct()->hasMachineOfType(Machine::TYPE_FOLDER)) { ?>
                            $.post("libs/modules/calculation/order.ajax.php", {exec: 'updateFoldtypes', pages: document.getElementsByName('h_content_pages')[0].value, id: id}, function(data) {
                                // Work on returned data
                                document.getElementById('order_folding').innerHTML = data;
                                //		calcOpenFormat();
                            });
                        <? } else { ?>
                            document.getElementById('tr_order_amount').style.display = '';
                            document.getElementById('tr_order_sorts').style.display = '';
                        <? } ?>
                            
                    }
            
            
                    function calcOpenFormat()
                    {
                        var foldid = parseInt(document.getElementById('h_folding').value);
                        var closedWidth = parseInt(document.getElementById('order_product_width').value);
                        var closedHeight = parseInt(document.getElementById('order_product_height').value);
						var fHeight = <?= $order->getProduct()->getFactorHeight() ?>;
                        var fWidth = <?= $order->getProduct()->getFactorWidth() ?>;

                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'calcOpenFormat', foldid: foldid, height: closedHeight, width: closedWidth, fWidth: fWidth, fHeight: fHeight}, 
                        function(data) {
                            var sizes = data.split('_');
                            document.getElementById('order_product_width_open').value = sizes[0];
                            document.getElementById('order_product_height_open').value = sizes[1];
                        });
        
                }
    
                function clickEnvelopePages(id)
                {
                    $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                    id=parseInt(id.substr(3));
                    document.getElementById('h_envelope_pages').value = id;

                    /* Offenes Format Umschlag anpassen */
                    var prod_width = parseInt(document.getElementById('order_product_width').value);
                    var prod_height = parseInt(document.getElementById('order_product_height').value);


                    if(document.getElementById('h_envelope_chromaticity').value == 0 || document.getElementById('h_envelope_chromaticity').value == '')
                    {

                        $.post("libs/modules/calculation/order.ajax.php",
                            {exec: 'updateEnvPaperChroma', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id},
                            function(data) {
                                // Work on returned data

                                document.getElementById('envelope_paperchroma').innerHTML = data;
                            });
                    }

                    // Get chromaticity factor
//                    var chroma = document.getElementById('h_envelope_chromaticity').value;
//                    $.post("libs/modules/calculation/order.ajax.php", {exec: "getReversePrinting", chromaId: chroma}, function(data) {
//						// Work on returned data
//						if(data == 0)
//							chromaFactor = 2;
//						else
//							chromaFactor = 1;
//
//	                    if(id == 2)
//	                    {
//	                        document.getElementById('order_envelope_width_open').value = prod_width * 1 * chromaFactor;
//	                        document.getElementById('order_envelope_height_open').value = prod_height;
//	                    } else if(id == 4)
//	                    {
//	                        document.getElementById('order_envelope_width_open').value = prod_width * 2 * chromaFactor;
//	                        document.getElementById('order_envelope_height_open').value = prod_height;
//	                    } else if (id == 6)
//	                    {
//	                        document.getElementById('order_envelope_width_open').value = prod_width * 3 * chromaFactor;
//	                        document.getElementById('order_envelope_height_open').value = prod_height;
//	                    } else if (id == 8)
//	                    {
//	                        document.getElementById('order_envelope_width_open').value = prod_width * 4 * chromaFactor;
//	                        document.getElementById('order_envelope_height_open').value = prod_height;
//	                    } else if (id == 10)
//	                    {
//	                        document.getElementById('order_envelope_width_open').value = prod_width * 5 * chromaFactor;
//	                        document.getElementById('order_envelope_height_open').value = prod_height;
//	                    }
//
//					});
                    

	
                    }
            
            function clickFolding(id)
            {
                $("#"+id).toggleClass( "btn-info").addClass("btn-success");
                id=id.substr(3);
                
                document.getElementById('h_folding').value = id;
                updateFormat(document.getElementById('h_product_format').value);
                
                document.getElementById('tr_order_amount').style.display = '';
                document.getElementById('tr_order_sorts').style.display = '';
            }
            
            function addAmount() 
            {
                var text = '<input name="addorder_amount[]"  class="form-control" value="" > ';
                document.getElementById('div_order_amount').insertAdjacentHTML('beforeEnd', text);
                var text = '<input name="addorder_sorts[]"  class="form-control" value="1" > ';
                document.getElementById('div_order_sorts').insertAdjacentHTML('beforeEnd', text);
            } 


            function clickAmount()
            {
            	 document.getElementById('tr_buttons').style.display = '';
            }

            function showAddPaperFormat() {
				document.getElementById('paperformat_custom_container').style.display = 'block';
            }

            function navigateToPaperModule() {

            	// ds / 08.05.2014
            	// Store current URI with module ID to get redirected to this page later.
                var returnTo = encodeURIComponent("<?= $_SERVER['REQUEST_URI'] . '&pid=' . $_CONFIG->orderPid ?>&cloneProduct=1"),
                	productId = <?= $order->getProduct()->getId() ?>; // This product will be cloned later.
                

				if(window.confirm('Sie werden nun zur Papierverwaltung weitergeleitet. Ihre bisherigen Eingaben werden nicht gespeichert. Möchten Sie fortfahren?')) {
					window.location.href = 'index.php?page=libs/modules/paper/paper.php&exec=edit&returnTo=' + returnTo + '&cloneProductId=' + productId;
				} else {
					return false; // User clicked "Cancel" and does not wish to get redirected to paper management page.
				}               
				
            }   

</script>


<?php // Qickmove generation
$quickmove = new QuickMove();
if($_USER->hasRightsByGroup(Group::RIGHT_MACHINE_SELECTION)) {
    $quickmove->addItem('Weiter','#',"document.getElementsByName('nextstep')[0].value='3';document.product_config_form.submit();",'glyphicon-chevron-right');
} else {
    $quickmove->addItem('Weiter','#',"document.getElementsByName('nextstep')[0].value='4';document.product_config_form.submit();",'glyphicon-chevron-right');
}
echo $quickmove->generate();
// end of Quickmove generation ?>



<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="product_config_form">
    <input name="exec" value="edit" type="hidden"> 
    <input name="subexec" value="save" type="hidden"> 
    <input name="step" value="2" type="hidden">
    <input name="id" value="<?= $order->getId() ?>" type="hidden"> 
    <input name="calc_id" value="<?= $calc->getId() ?>" type="hidden">
    <input name="nextstep" value="" type="hidden">

    <!-- Hidden Fields for Configuration -->
    <input type="hidden" name="h_product_format" id="h_product_format" value="<?= $calc->getProductFormat()->getId() ?>">
    <input type="hidden" name="h_content_paper" id="h_content_paper" value="<?= $calc->getPaperContent()->getId() ?>">
    <input type="hidden" name="h_content_paper_weight" id="h_content_paper_weight" value="<?= $calc->getPaperContentWeight() ?>">
    <input type="hidden" name="h_content_pages" id="h_content_pages" value="<?= $calc->getPagesContent() ?>">
    <input type="hidden" name="h_content_chromaticity" id="h_content_chromaticity" value="<?= $calc->getChromaticitiesContent()->getId() ?>">

    <input type="hidden" name="h_addcontent_paper" id="h_addcontent_paper" value="<?= $calc->getPaperAddContent()->getId() ?>">
    <input type="hidden" name="h_addcontent_paper_weight" id="h_addcontent_paper_weight" value="<?= $calc->getPaperAddContentWeight() ?>">
    <input type="hidden" name="h_addcontent_pages" id="h_addcontent_pages" value="<?= $calc->getPagesAddContent() ?>">
    <input type="hidden" name="h_addcontent_chromaticity" id="h_addcontent_chromaticity" value="<?= $calc->getChromaticitiesAddContent()->getId() ?>">

    <input type="hidden" name="h_envelope_paper" id="h_envelope_paper" value="<?= $calc->getPaperEnvelope()->getId() ?>">
    <input type="hidden" name="h_envelope_paper_weight" id="h_envelope_paper_weight" value="<?= $calc->getPaperEnvelopeWeight() ?>">
    <input type="hidden" name="h_envelope_pages" id="h_envelope_pages" value="<?= $calc->getPagesEnvelope() ?>">
    <input type="hidden" name="h_envelope_chromaticity" id="h_envelope_chromaticity" value="<?= $calc->getChromaticitiesEnvelope()->getId() ?>">
    
    <input type="hidden" name="h_addcontent2_paper" id="h_addcontent2_paper" value="<?= $calc->getPaperAddContent2()->getId() ?>">
    <input type="hidden" name="h_addcontent2_paper_weight" id="h_addcontent2_paper_weight" value="<?= $calc->getPaperAddContent2Weight() ?>">
    <input type="hidden" name="h_addcontent2_pages" id="h_addcontent2_pages" value="<?= $calc->getPagesAddContent2() ?>">
    <input type="hidden" name="h_addcontent2_chromaticity" id="h_addcontent2_chromaticity" value="<?= $calc->getChromaticitiesAddContent2()->getId() ?>">
    
    <input type="hidden" name="h_addcontent3_paper" id="h_addcontent3_paper" value="<?= $calc->getPaperAddContent3()->getId() ?>">
    <input type="hidden" name="h_addcontent3_paper_weight" id="h_addcontent3_paper_weight" value="<?= $calc->getPaperAddContent3Weight() ?>">
    <input type="hidden" name="h_addcontent3_pages" id="h_addcontent3_pages" value="<?= $calc->getPagesAddContent3() ?>">
    <input type="hidden" name="h_addcontent3_chromaticity" id="h_addcontent3_chromaticity" value="<?= $calc->getChromaticitiesAddContent3()->getId() ?>">

    <input type="hidden" name="h_folding" id="h_folding" value="<?= $calc->getFolding()->getId() ?>">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Inhalt 1
            </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $_LANG->get('Endformat') ?></td>
                        <td style=" margin-top: 30px; margin-right: 30px;  margin-bottom: 50px; margin-left: 80px;" valign="top">
                            <?
                            foreach ($order->getProduct()->getAvailablePaperFormats() as $pf) {
                                echo '  <input type="button" class="btn btn-default btn-info" id="01_' . $pf->getId() . '" name="format" value="';
                                echo $pf->getName() . "\n" . '(' . $pf->getWidth() . ' x ' . $pf->getHeight() . ' '.$_LANG->get('mm').')"';
                                echo 'onclick="clickProductFormat(\'content\',this.id)"';
                                if ($calc->getProductFormat()->getId() == $pf->getId())
                                    echo ' ';
                                echo '>' . "\n";
                            }
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td id="paper_free" <? //if($calc->getProductFormat() != 0) echo 'style="display:none"' ?>>
                            <div class="col-md-6">
                                 <div class="row">
                                     <div class="form-group">
                                         <label for="" class="col-sm-2 control-label">Breite</label>
                                         <div class="col-sm-4">
                                             <div class="input-group">
                                                 <input name="order_product_width" class="form-control" id="order_product_width" value="<?= $calc->getProductFormatWidth() ?>">
                                                 <span class="input-group-addon">mm</span>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <label for="" class="col-sm-2 control-label">Höhe</label>
                                         <div class="col-sm-4">
                                             <div class="input-group">
                                                 <input name="order_product_height" class="form-control" id="order_product_height" value="<?= $calc->getProductFormatHeight() ?>">
                                                 <span class="input-group-addon">mm</span>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 control-label">Breite (offenes Format)</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input name="order_product_width_open" class="form-control" id="order_product_width_open" value="<?= $calc->getProductFormatWidthOpen() ?>">
                                                <span class="input-group-addon">mm</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 control-label">Höhe (offenes Format)</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input name="order_product_height_open" class="form-control" id="order_product_height_open" value="<?= $calc->getProductFormatHeightOpen() ?>">
                                                <span class="input-group-addon">mm</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>Bitte beachten Sie bei einer Kalkulation von Rollenprodukten folgendes:<br/>
                            Höhe und Breite des Produktes sind inkl. Anschnitt und Rapport anzugeben.</td>
                    </tr>

                    <?
                    // Matrial Inhalt
                    if ($order->getProduct()->getHasContent()) {
                        ?>
                        <tr>
                            <td><?= $_LANG->get('Inhalt 1') ?></td>
                            <td>
                                <div  style=" padding: 30px;" id="paper">
                                    <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                        foreach ($order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paper) {
                                            $paper = new Paper($paper["id"]);
                                            echo '<input type="button"';
                                            if ($calc->getPaperContent()->getId() == $paper->getId())
                                                echo ' ';

                                            echo ' class="btn btn-default btn-info" id="02_' . $paper->getId() . '" name="paperprops" value="' . $paper->getName() . '"
											onclick="clickPaperContent(this.id)">' . "\n";
                                        }

                                        echo '<input type="button" class="btn btn-default btn-info" name="custompaperprop" value="[+] Neues Papier..." onclick="navigateToPaperModule()" />';
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <?
                    }



                    // Matrial Inhalt Gewicht
                    if ($order->getProduct()->getHasContent()) {
                        ?>
                        <tr>
                            <td><?= $_LANG->get('Inhalt 1 Gewicht') ?>
                            </td>
                            <td>

                                <div id="paper_weight"><?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                        $_REQUEST["product"] = (int) $_REQUEST["product"];


                                        $papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT);

                                        foreach (($papers[$calc->getPaperContent()->getId()]) as $weight => $val) {
                                            if ($weight != "id") {

                                                echo '<input type="button" ';
                                                if ($calc->getPaperContentWeight() == $weight)
                                                    echo ' ';

                                                echo ' class="btn btn-default btn-info" id="03_' . $weight . '" name="paperweight" value="' . $weight . ' '.$_LANG->get('g').'"
				onclick="clickContentWeight(this.id)">' . "\n";
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                            </td>
                        </tr>
                    <? } ?>


                    <tr>


                        <td><?= $_LANG->get('Inhalt 1 bedr. Seiten') ?></td>
                        <td>
                            <div id="number_pages">
                                <?
                                if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                    if($order->getProduct()->getType() == Product::TYPE_NORMAL)
                                        foreach ($order->getProduct()->getAvailablePageCounts() as $pc) {
                                            echo '<input type="button" ';
                                            if ($calc->getPagesContent() == $pc)
                                                echo ' ';
                                            echo ' class="btn btn-default btn-info" id="04_' . $pc . '" name="numberpages_content" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
    				onclick="clickContentPages(this.id, false)">' . "\n";
                                        }
                                    else
                                        echo '<input name="numberpages_content" value="'.$calc->getPagesContent().'" style="width:60px" class="text"
                                        onfocus="focusContentPages()" onblur="setContentPages(this.value)">';
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>


                        <td><?= $_LANG->get('Inhalt 1 Farbigkeit') ?></td>
                        <td>
                            <div id="print_chrom"> <?
                                if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                    foreach (Chromaticity::getAllChromaticities() as $pc) {
                                        echo '<input type="button" ';
                                        if ($calc->getChromaticitiesContent()->getId() == $pc->getId())
                                            echo ' ';
                                        echo '  class="btn btn-default btn-info" id="05_' . $pc->getId() . '" name="chroma" value="' . $pc->getName() . '"
				onclick="clickContentChromaticity(this.id)">' . "\n";
                                    }
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <? // Zusaetzlicher Inhalt
        if ($order->getProduct()->getHasAddContent()) {?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Inhalt 2
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $_LANG->get('Inhalt 2') ?>
                            </td>
                            <td>

                                <div id="additional_paper">
                                    <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                        echo '<input type="button" ';

                                        if ($calc->getPaperAddContent()->getId() == 0) {
                                            echo ' ';
                                        }

                                        echo ' class="btn btn-default btn-info" id="06_0" name="addpaper" value="' . $_LANG->get('nicht vorhanden') . '"
				onclick="clickAddPaper(this.id)">' . "\n";
                                        $addSelected = false;
                                        foreach ($order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paper) {
                                            $paper = new Paper($paper["id"]);
                                            echo '<input type="button"';
                                            if ($calc->getPaperAddContent()->getId() == $paper->getId()) {
                                                $addSelected = true;
                                                echo ' ';
                                            }

                                            echo '  class="btn btn-default btn-info" id="06_' . $paper->getId() . '" name="addpaper" value="' . $paper->getName() . '"
				onclick="clickAddPaper(this.id)">' . "\n";
                                        }
                                        if ($addSelected === false)
                                            echo '<script language="javascript">document.getElementById(\'06_0\').style.backgroundColor = \'#e3e3e3\'</script>';
                                    }
                                    ?>
                                </div>

                            </td>
                        </tr>
                        <tr id="tr_addcontent_weight"<? if ($calc->getPaperAddContent()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                            <td><?= $_LANG->get('Inhalt 2 Gewicht') ?>
                            </td>
                            <td>
                                <div id="additional_paperweight">
                                    <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                        $_REQUEST["product"] = (int) $_REQUEST["product"];


                                        $papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT);

                                        foreach (($papers[$calc->getPaperContent()->getId()]) as $weight => $val) {
                                            if ($weight != "id") {

                                                echo '<input type="button" ';
                                                if ($calc->getpaperAddContentWeight() == $weight)
                                                    echo ' ';

                                                echo 'class="btn btn-default btn-info" id="07_' . $weight . '" name="addpaperweight" value="' . $weight . ' '.$_LANG->get('g').'"
				onclick="clickAddPaperWeight(this.id)">' . "\n";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>


                        <tr id="tr_addcontent_pages"<? if ($calc->getPaperAddContent()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                            <td><?= $_LANG->get('Inhalt 2 bedr. Seiten') ?>
                            </td>
                            <td class="content_row_clear">
                                <div id="additional_paperpages">

                                    <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                        if($order->getProduct()->getType() == Product::TYPE_NORMAL)
                                            foreach ($order->getProduct()->getAvailablePageCounts() as $pc) {
                                                echo '<input type="button" ';
                                                if ($calc->getPagesAddContent() == $pc)
                                                    echo ' ';
                                                echo ' class="btn btn-default btn-info" id="08_' . $pc . '" name="addpaperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
    				onclick="clickAddPaperPages(this.id)">' . "\n";
                                            }
                                        else
                                            echo '<input name="numberpages_addcontent" value="'.$calc->getPagesAddContent().'" style="width:60px" class="text"
                                    onfocus="focusAddContentPages()" onblur="setAddContentPages(this.value)">';
                                    }
                                    ?>
                                </div>
                            </td>

                        </tr>

                        <tr id="tr_addcontent_chromaticity"<? if ($calc->getPaperAddContent()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                            <td><?= $_LANG->get('Inhalt 2 Farbigkeit') ?>
                            </td>
                            <td>
                                <div id="additional_paperchroma"> <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                        $prod = new Product($_REQUEST["product"]);
                                        foreach (Chromaticity::getAllChromaticities() as $pc) {
                                            echo '<input type="button"';
                                            if ($calc->getChromaticitiesAddContent()->getId() == $pc->getId())
                                                echo '';
                                            echo ' class="btn btn-default btn-info" id="14_' . $pc->getId() . '" name="addpaperchroma" value="' . $pc->getName() . '"
				onclick="clickAddPaperChromaticity(this.id)">' . "\n";
                                        }
                                    }
                                    ?>
                                </div>

                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?
        }

        require_once 'order.php.add.php';

        // Material Umschlag
        if ($order->getProduct()->getHasEnvelope()) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Umschlag
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $_LANG->get('Material Umschlag') ?>
                            </td>
                            <td>
                                <div id="envelope_paperprops">

                                    <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                        echo '<input type="button" ';

                                        if ($calc->getPaperEnvelope()->getId() == 0) {
                                            echo ' ';
                                        }

                                        echo ' class="btn btn-default btn-info" id="09_0"  name="envpaper" value="' . $_LANG->get('nicht vorhanden') . '"
				onclick="clickEnvelopePaper(this.id)">' . "\n";
                                        $envSelected = false;
                                        foreach ($order->getProduct()->getSelectedPapersIds(Calculation::PAPER_ENVELOPE) as $paper) {
                                            $paper = new Paper($paper["id"]);
                                            echo '<input type="button"';
                                            if ($calc->getPaperEnvelope()->getId() == $paper->getId()) {
                                                echo ' ';
                                                $envSelected = true;
                                            }

                                            echo '  class="btn btn-default btn-info" id="09_' . $paper->getId() . '" name="envpaper" value="' . $paper->getName() . '"
				onclick="clickEnvelopePaper(this.id)">' . "\n";
                                        }
                                        if ($envSelected === false)
                                            echo '<script language="javascript">document.getElementById(\'09_0\').style.backgroundColor = \'#e3e3e3\'</script>';
                                    }
                                    ?>

                                </div>
                            </td>
                        </tr>
                        <tr id="tr_envelope_weight" <? if ($calc->getPaperEnvelope()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                            <td><?= $_LANG->get('Mat. Umschlag Gewicht') ?>
                            </td>
                            <td>
                                <div id="envelope_paperweight">
                                    <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                        $_REQUEST["product"] = (int) $_REQUEST["product"];


                                        $papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_ENVELOPE);

                                        foreach (($papers[$calc->getPaperEnvelope()->getId()]) as $weight => $val) {
                                            if ($weight != "id") {
                                                echo '<input type="button" ';
                                                if ($calc->getPaperEnvelopeWeight() == $weight)
                                                    echo ' ';

                                                echo 'class="btn btn-default btn-info" id="10_' . $weight . '" name="envpaperweight" value="' . $weight . ' '.$_LANG->get('g').'"
				onclick="clickEnvelopeWeight(this.id)">' . "\n";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>

                        <tr id="tr_envelope_pages" <? if ($calc->getPaperEnvelope()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                            <td><?= $_LANG->get('Mat. Umschl. bedr. Seiten') ?>
                            </td>
                            <td>
                                <div id="envelope_paperpages"> <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                        $pages = array("2", "4", "6", "8");

                                        foreach ($pages as $pc) {
                                            echo '<input type="button"  class="btn btn-default btn-info"';

                                            if ($calc->getPagesEnvelope() == $pc)
                                                echo ' ';
                                            echo ' id="11_' . $pc . '" name="envpaperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
				onclick="clickEnvelopePages(this.id)">' . "\n";
                                        }
                                    }
                                    ?>

                                </div>
                            </td>

                        </tr>


                        <tr id="tr_envelope_chromaticity" <? if ($calc->getPaperEnvelope()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                            <td><?= $_LANG->get('Mat. Umschl. Farbigkeit') ?>
                            </td>
                            <td>
                                <div id="envelope_paperchroma"> <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                        $prod = new Product($_REQUEST["product"]);
                                        foreach (Chromaticity::getAllChromaticities() as $pc) {
                                            echo '<input type="button"';
                                            if ($calc->getChromaticitiesEnvelope()->getId() == $pc->getId())
                                                echo ' ';
                                            echo '  class="btn btn-default btn-info" id="15_' . $pc->getId() . '" name="envpaperchroma" value="' . $pc->getName() . '"
				onclick="clickEnvelopeChromaticity(this.id)">' . "\n";
                                        }
                                    }
                                    ?>

                                </div>

                            </td>

                        </tr>

                        <tr id="tr_envelope_format" <? if ($calc->getPaperEnvelope()->getId() == 0)
                            echo ' style="display:none"'; ?> >
                            <td><?= $_LANG->get('Umschlag offenes Format') ?></td>
                            <td>
                                <div id="envelope_format">
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 control-label">Breite</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input name="order_envelope_width_open" class="form-control" id="order_envelope_width_open" value="<?= $calc->getEnvelopeWidthOpen() ?>">
                                                <span class="input-group-addon">mm</span>
                                            </div>
                                        </div>
                                        <label for="" class="col-sm-2 control-label">Höhe</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input name="order_envelope_height_open" class="form-control" id="order_envelope_height_open" value="<?= $calc->getEnvelopeHeightOpen() ?>">
                                                <span class="input-group-addon">mm</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        <? } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Optionen Weiterverarbeitung
                </h3>
            </div>
            <? if ($order->getProduct()->hasMachineOfType(Machine::TYPE_FOLDER)) { ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $_LANG->get('Falzung') ?></td>
                            <td>
                                <div id="order_folding"> <?
                                    if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                        $_REQUEST["pages"] = (int) $_REQUEST["pages"];
                                        $firstId = true;
                                        foreach (Foldtype::getFoldTypesForPages($calc->getPagesContent()) as $ft) {
                                            echo '<input type="button"';
                                            if ($calc->getFolding()->getId() == $ft->getId())
                                                echo ' ';
                                            echo '  class="btn btn-default btn-info" id="12_' . $ft->getId() . '" name="foldtype" value="' . $ft->getName() . '"
				onclick="clickFolding(this.id)">' . "\n";
                                        }
                                    }
                                    ?>


                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            <? } ?>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Auflage</th>
                        <th>Sorten</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="tr_order_amount" <? if ($calc->getId() == 0 && $_REQUEST["subexec"] != "copy")
                        echo ' style="display:none"'; ?>>
                        <td>
                            <div class="col-md-6">
                                <div id="div_order_amount">
                                    <input name="order_amount" id="13_0" class="form-control" value="<?= $calc->getAmount() ?>" onclick="clickAmount()" onfocus="clickAmount()">
                                    <?
                                    if ($_REQUEST["addorder_amount"])
                                    {	//gln
                                        foreach($_REQUEST["addorder_amount"] as $addamount)
                                        {
                                            if((int)$addamount > 0)
                                                echo '<input name="addorder_amount[]"  class="form-control" value="'.$addamount.'" onclick="clickAmount()" onfocus="clickAmount()"> ';
                                        }
                                    }
                                    else
                                    {
                                        //gln
                                        $calculations = Calculation::getCalculationsForUpdate($order, $calc, Calculation::ORDER_AMOUNT);
                                        //$count_calcs = count($calculations);
//echo $count_calcs;
                                        foreach($calculations as $calcul)
                                        {
                                            ?>
                                            <input name="addorder_amount[]" class="form-control" value="<?= $calcul->getAmount() ?>" onclick="clickAmount()" onfocus="clickAmount()">
                                            <?
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <span class="glyphicons glyphicons-plus" onclick="addAmount()"></span>
                        </td>
                        <td id="tr_order_sorts" <? if ($calc->getId() == 0 && $_REQUEST["subexec"] != "copy")
                            echo ' style="display:none"'; ?>>
                            <div class="col-md-6">
                                <div id="div_order_sorts">
                                    <input name="order_sorts" id="23_0" class="form-control" value="<?= $calc->getSorts() ?>">
                                    <?
                                    if ($_REQUEST["addorder_sorts"])
                                    {	//gln
                                        foreach($_REQUEST["addorder_sorts"] as $addsorts)
                                        {
                                            if((int)$addsorts > 0)
                                                echo '<input name="addorder_sorts[]" class="form-control" value="'.$addsorts.'"> ';
                                        }
                                    }
                                    else
                                    {
                                        //gln
                                        $calculations = Calculation::getCalculationsForUpdate($order, $calc, Calculation::ORDER_AMOUNT);
                                        //$count_calcs = count($calculations);
//echo $count_calcs;
                                        foreach($calculations as $calcul)
                                        {
                                            ?>
                                            <input name="addorder_sorts[]" class="form-control" value="<?= $calcul->getSorts() ?>">
                                            <?
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
</form>
