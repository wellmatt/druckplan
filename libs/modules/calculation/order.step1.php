<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       27.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//---------------------------------------------------------------------------------- 
?>
<script type="text/javascript">
message = '<?=$_LANG->get('Mit Wechsel des Produkts werden alle Kalkulationen/Teilauftr%E4ge gel%F6scht. Weiter?')?>';

function checkCalc(product)
{
    <?
	$calculations = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
	if(count($calculations)==0){ 
        ?>
             location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&id=<?=$_REQUEST["id"]?>&selProduct='+product+'&step=2';
        <?
    } else {
    
        ?>
        if(product != <?=$order->getProduct()->getId()?>)
        {
            Check = confirm(unescape(message));
            if (Check == false)
                return false;
            else
            	location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&id=<?=$_REQUEST["id"]?>&selProduct='+product+'&clearorder=1&step=2';
        } else
        {
            var subexec = "<?=$_REQUEST["subexec"]?>";
            if (subexec == "copy"){
            	location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&subexec=copy&calc_id=<?=$calculations[0]->getId()?>&id=<?=$_REQUEST["id"]?>&step=2';
            } else {
            	location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&id=<?=$_REQUEST["id"]?>&step=4';
            }
        }
        <?
    }
?>
	
}
</script>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Produktauswahl</h3>
	  </div>
	  <div class="panel-body">
          <div class="row">
              <?
              foreach($products_nonindi as $p)
              {
                  ?>
                  <div class="col-md-2">
                      <div class="panel panel-default" style="height: 150px;">
                          <div class="panel-heading">
                              <h3 class="panel-title"><?=$p->getName()?></h3>
                          </div>
                          <div class="panel-body">
                              <img src="images/products/<?=$p->getPicture()?>" class="pointer" onclick="checkCalc(<?=$p->getId()?>)">
                          </div>
                      </div>
                  </div>
                  <?
              }
              ?>
          </div>
	  </div>
</div>
<?php if(!empty($individualProducts)){ ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Individuelle Produkte</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?
            foreach($individualProducts as $p)
            {
                ?>
                <div class="col-md-2">
                    <div class="panel panel-default" style="height: 150px;">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?=$p->getName()?></h3>
                        </div>
                        <div class="panel-body">
                            <img src="images/products/<?=$p->getPicture()?>" class="pointer" onclick="checkCalc(<?=$p->getId()?>)">
                        </div>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
    </div>
</div>
<?php } ?>