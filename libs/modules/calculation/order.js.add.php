<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			11.12.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------


?>
<script language="javascript">
<? // ------------------------------ JavaScript fuer den zus. Inhalt 2 --------------------------------------------------------- ?>
            
            function clickAdd2Paper(id)
            {
                var buttons = $('[name=add2paper]');
                for (let x=0;x<buttons.length;x++){
                    $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                }
                $("#"+id).removeClass("btn-info").addClass("btn-success");

                id=id.substr(3);
                
                document.getElementById('h_addcontent2_paper').value = id;
                oldval = document.getElementById('h_addcontent2_paper_weight').value;
                
                if (id == 0)
                {
                    document.getElementById('tr_addcontent2_weight').style.display = 'none';
                    document.getElementById('tr_addcontent2_chromaticity').style.display = 'none';
                    document.getElementById('tr_addcontent2_pages').style.display = 'none';
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
                        document.getElementById('tr_addcontent2_weight').style.display = '';
                        document.getElementById('tr_addcontent2_chromaticity').style.display = '';
                        document.getElementById('tr_addcontent2_pages').style.display = '';
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateAdd2PaperWeight', product: <?= $order->getProduct()->getId() ?>, id: id, oldval: oldval}, 
                        function(data) {
                            // Work on returned data
                        
                            document.getElementById('additional2_paperweight').innerHTML = data;
                        });
                    }
                }
            
                function clickAdd2PaperWeight(id)
                {
                    var buttons = $('[name=add2paperweight]');
                    for (let x=0;x<buttons.length;x++){
                        $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                    }
                    $("#"+id).removeClass("btn-info").addClass("btn-success");

                    id=id.substr(3);
                
                    document.getElementById('h_addcontent2_paper_weight').value = id;

                    if(document.getElementById('h_addcontent2_pages').value == 0 || document.getElementById('h_addcontent2_pages').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php",
                            {exec: 'updateAdd2PaperPages', product: <?= $order->getProduct()->getId() ?>, id: id},
                            function(data) {
                                // Work on returned data
                                document.getElementById('additional2_paperpages').innerHTML = data;
                            });
                    }
                }
            
            
                function clickAdd2PaperChromaticity(id)
                {
                    var buttons = $('[name=add2paperchroma]');
                    for (let x=0;x<buttons.length;x++){
                        $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                    }
                    $("#"+id).removeClass("btn-info").addClass("btn-success");

                    id=id.substr(3);
                    document.getElementById('h_addcontent2_chromaticity').value = id;


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
                    <? } ?>
                }



            function clickAdd2PaperPages(id)
            {
                var buttons = $('[name=add2paperpages]');
                for (let x=0;x<buttons.length;x++){
                    $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                }
                $("#"+id).removeClass("btn-info").addClass("btn-success");

                id=id.substr(3);
                document.getElementById('h_addcontent2_pages').value = id;


                if(document.getElementById('h_addcontent2_chromaticity').value == 0 || document.getElementById('h_addcontent2_chromaticity').value == '')
                {
                    $.post("libs/modules/calculation/order.ajax.php",
                        {exec: 'updateAdd2PaperChroma', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id},
                        function(data) {
                            // Work on returned data

                            document.getElementById('additional2_paperchroma').innerHTML = data;
                        });
                }

            }


            // Falls Seitenzahl haendisch eingetragen wird
            function focusAddContent2Pages()
            {
                id = 0;
			<? if ($order->getProduct()->getHasEnvelope()) { ?>
                    if(document.getElementById('h_envelope_paper').value == 0 || document.getElementById('h_envelope_paper').value == ''){
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateEnvPaper', product: <?= $order->getProduct()->getId() ?>, id: id}, 
                        function(data) {
                            // Work on returned data

                            document.getElementById('envelope_paperprops').innerHTML = data;
                        });
                    } 
			<? } else if ($order->getProduct()->hasMachineOfType(Machine::TYPE_FOLDER)) { ?>
                    $.post("libs/modules/calculation/order.ajax.php",
                            {exec: 'updateFoldtypes', pages: document.getElementsByName('h_content_pages')[0].value, id: id}, 
                            function(data) {
		                        // Work on returned data 
		                        document.getElementById('order_folding').innerHTML = data;
		                        //		calcOpenFormat();
		                    });
			<? } else { ?>
                    document.getElementById('tr_order_amount').style.display = '';
			<? } ?>
            }

            function setAddContent2Pages(val)
            {
                document.getElementById('h_addcontent2_pages').value = val;
            }

</script>


<script language="javascript">
<? // ------------------------------ JavaScript fuer den zus. Inhalt 3 --------------------------------------------------------- ?>
            
            function clickAdd3Paper(id)
            {
                var buttons = $('[name=add3paper]');
                for (let x=0;x<buttons.length;x++){
                    $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                }
                $("#"+id).removeClass("btn-info").addClass("btn-success");

                id=id.substr(3);
                
                document.getElementById('h_addcontent3_paper').value = id;
                oldval = document.getElementById('h_addcontent3_paper_weight').value;
                
                if (id == 0)
                {
                    document.getElementById('tr_addcontent3_weight').style.display = 'none';
                    document.getElementById('tr_addcontent3_chromaticity').style.display = 'none';
                    document.getElementById('tr_addcontent3_pages').style.display = 'none';
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
                        document.getElementById('tr_addcontent3_weight').style.display = '';
                        document.getElementById('tr_addcontent3_chromaticity').style.display = '';
                        document.getElementById('tr_addcontent3_pages').style.display = '';
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateAdd3PaperWeight', product: <?= $order->getProduct()->getId() ?>, id: id, oldval: oldval}, 
                        function(data) {
                            // Work on returned data
                        
                            document.getElementById('additional3_paperweight').innerHTML = data;
                        });
                    }
                }
            
                function clickAdd3PaperWeight(id)
                {
                    var buttons = $('[name=add3paperweight]');
                    for (let x=0;x<buttons.length;x++){
                        $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                    }
                    $("#"+id).removeClass("btn-info").addClass("btn-success");

                    id=id.substr(3);
                
                    document.getElementById('h_addcontent3_paper_weight').value = id;

                    if(document.getElementById('h_addcontent3_pages').value == 0 || document.getElementById('h_addcontent3_pages').value == '')
                    {
                        $.post("libs/modules/calculation/order.ajax.php",
                            {exec: 'updateAdd3PaperPages', product: <?= $order->getProduct()->getId() ?>, id: id},
                            function(data) {
                                // Work on returned data
                                document.getElementById('additional3_paperpages').innerHTML = data;
                            });
                    }
                }
            
            
                function clickAdd3PaperChromaticity(id)
                {

                    var buttons = $('[name=add3paperchroma]');
                    for (let x=0;x<buttons.length;x++){
                        $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                    }
                    $("#"+id).removeClass("btn-info").addClass("btn-success");
                    id=id.substr(3);
                    document.getElementById('h_addcontent3_chromaticity').value = id;

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
                    $.post("libs/modules/calculation/order.ajax.php",
                        {exec: 'updateFoldtypes', pages: document.getElementsByName('h_content_pages')[0].value, id: id},
                        function(data) {
                            // Work on returned data
                            document.getElementById('order_folding').innerHTML = data;
                            //		calcOpenFormat();
                        });
                    <? } else { ?>
                    document.getElementById('tr_order_amount').style.display = '';
                    <? } ?>
                }



            function clickAdd3PaperPages(id)
            {
                var buttons = $('[name=add3paperpages]');
                for (let x=0;x<buttons.length;x++){
                    $(buttons[x]).removeClass("btn-success").addClass("btn-info");
                }
                $("#"+id).removeClass("btn-info").addClass("btn-success");

                id=id.substr(3);
                document.getElementById('h_addcontent3_pages').value = id;

                if(document.getElementById('h_addcontent3_chromaticity').value == 0 || document.getElementById('h_addcontent3_chromaticity').value == '')
                {
                    $.post("libs/modules/calculation/order.ajax.php",
                        {exec: 'updateAdd3PaperChroma', product: <?= $order->getProduct()->getId() ?>, orderId: <?= $order->getId() ?>, id: id},
                        function(data) {
                            // Work on returned data

                            document.getElementById('additional3_paperchroma').innerHTML = data;
                        });
                }
            }


            // Falls Seitenzahl haendisch eingetragen wird
            function focusAddContent3Pages()
            {
                id = 0;
			<? if ($order->getProduct()->getHasEnvelope()) { ?>
                    if(document.getElementById('h_envelope_paper').value == 0 || document.getElementById('h_envelope_paper').value == ''){
                        $.post("libs/modules/calculation/order.ajax.php", 
                        {exec: 'updateEnvPaper', product: <?= $order->getProduct()->getId() ?>, id: id}, 
                        function(data) {
                            // Work on returned data

                            document.getElementById('envelope_paperprops').innerHTML = data;
                        });
                    } 
			<? } else if ($order->getProduct()->hasMachineOfType(Machine::TYPE_FOLDER)) { ?>
                    $.post("libs/modules/calculation/order.ajax.php",
                            {exec: 'updateFoldtypes', pages: document.getElementsByName('h_content_pages')[0].value, id: id}, 
                            function(data) {
		                        // Work on returned data 
		                        document.getElementById('order_folding').innerHTML = data;
		                        //		calcOpenFormat();
		                    });
			<? } else { ?>
                    document.getElementById('tr_order_amount').style.display = '';
			<? } ?>
            }

            function setAddContent3Pages(val)
            {
                document.getElementById('h_addcontent3_pages').value = val;
            }

</script>

