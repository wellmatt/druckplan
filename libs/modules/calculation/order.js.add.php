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
                for(var i=0;i<document.getElementsByName('add2paper').length;i++){
                    document.getElementsByName('add2paper')[i].style.backgroundImage="url(images/page/organizer.png)";
                    document.getElementsByName('add2paper')[i].style.color="#000";
                }
                document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                document.getElementById(id).style.color="#fff";
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
                    for(var i=0;i<document.getElementsByName('add2paperweight').length;i++){
                        document.getElementsByName('add2paperweight')[i].style.backgroundImage="url(images/page/organizer.png)";
                        document.getElementsByName('add2paperweight')[i].style.color="#000";
                    }
                    document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                    document.getElementById(id).style.color="#fff";
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
                    for(var i=0;i<document.getElementsByName('add2paperchroma').length;i++){
                        document.getElementsByName('add2paperchroma')[i].style.backgroundImage="url(images/page/organizer.png)";
                        document.getElementsByName('add2paperchroma')[i].style.color="#000";
                    }
                    document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                    document.getElementById(id).style.color="#fff";
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
                for(var i=0;i<document.getElementsByName('add2paperpages').length;i++){
                    document.getElementsByName('add2paperpages')[i].style.backgroundImage="url(images/page/organizer.png)";
                    document.getElementsByName('add2paperpages')[i].style.color="#000";
                }
                document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                document.getElementById(id).style.color="#fff";
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
                for(var i=0;i<document.getElementsByName('add3paper').length;i++){
                    document.getElementsByName('add3paper')[i].style.backgroundImage="url(images/page/organizer.png)";
                    document.getElementsByName('add3paper')[i].style.color="#000";
                }
                document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                document.getElementById(id).style.color="#fff";
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
                    for(var i=0;i<document.getElementsByName('add3paperweight').length;i++){
                        document.getElementsByName('add3paperweight')[i].style.backgroundImage="url(images/page/organizer.png)";
                        document.getElementsByName('add3paperweight')[i].style.color="#000";
                    }
                    document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                    document.getElementById(id).style.color="#fff";
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
                    for(var i=0;i<document.getElementsByName('add3paperchroma').length;i++){
                        document.getElementsByName('add3paperchroma')[i].style.backgroundImage="url(images/page/organizer.png)";
                        document.getElementsByName('add3paperchroma')[i].style.color="#000";
                    }
                    document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                    document.getElementById(id).style.color="#fff";
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
                for(var i=0;i<document.getElementsByName('add3paperpages').length;i++){
                    document.getElementsByName('add3paperpages')[i].style.backgroundImage="url(images/page/organizer.png)";
                    document.getElementsByName('add3paperpages')[i].style.color="#000";
                }
                document.getElementById(id).style.backgroundImage="url(images/page/organizer-selected.png)";
                document.getElementById(id).style.color="#fff";
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

