<?
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once('libs/modules/calculation/order.class.php');



$_REQUEST["selyear"] = (int)$_REQUEST["selyear"];
if ($_REQUEST["selyear"])
{
/*****************************************************************
 * Jahr wurde ausgewählt
 */

$customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,BusinessContact::FILTER_CUST_IST);	

$counts = Order::getCountOrdersPerCustMonth($_REQUEST["selyear"]);

$values = Array();
foreach ($counts as $count)
{
   $values[$count['month']][$count['cust']] = $count['count'];
}
?>

<div class="box1">
<table class="standard">
   <colgroup>
      <col>
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
      <col width="60">
   </colgroup>
   <tr>
      <td class="content_row_header">Name<div style="float:right"><?=$_REQUEST["selyear"]?></div></td>
      <? 
      $months = array("Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
   for ($m = 0; $m < 12; $m++) {
   	?>
      <td class="content_row_header" style="text-align:center"><?=$_LANG->get($months[$m])?></td>
      <?
   }
   ?>
   </tr>
   <?
   
   $x = 0;	
foreach ($customers as $customer)
{
?>
   <tr class="<?=getRowColor($x)?>">
   <td class="content_row">
   <?
   if ($customer->getName1() != "")
      echo $customer->getName1();
   else
   echo $customer->getName2();
   ?>
   </td>
   <?
      for ($m = 1; $m < 13; $m++) {
      	?>
        <td class="content_row" style="text-align:center">&nbsp;<?=$values[$m][$customer->getId()] ?></td>
        <? 
      $sum[$m] += $values[$m][$customer->getId()];
      }
   ?>
      </tr>
   <?
   $x++;
}

?>
<tr>
   <td class="content_row_header"><?=$_LANG->get('Summe')?>:</td>
<?
for ($m = 1; $m < 13; $m++) {
   echo '<td class="content_row_header" style="text-align:center">'.$sum[$m].'&nbsp;</td>';
}
?>
</tr>
</table>
</div>
<? } else { 
//-----------------------------------------------------------------------
// Kein Jahr ausgewÃ¤hlt
//

$customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,BusinessContact::FILTER_CUST_IST);

$counts = Order::getCountOrdersPerCust();

$values = Array();
foreach ($counts as $count)
{
   $values[$count['year']][$count['cust']] = $count['count'];
}
?>


<div class="box1">
<table class="standard">
   <colgroup>
      <col>
      <col width="90">
      <col width="90">
      <col width="90">
      <col width="90">
      <col width="90">
   </colgroup>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('Name')?></td>
   <?
   for ($y = date('Y') - 4; $y <= date('Y'); $y++) {
      echo '<td class="content_row_header" style="text-align:center"><a href="index.php?page='.$_REQUEST['page'].'&exec=projectsPerCustomer&selyear='.$y.'">'.$y.'</a></td>';
   }
   ?>
   </tr>
<?
$x = 0;
foreach ($customers as $customer)
{
?>
   <tr class="<?=getRowColor($x)?>">
   <td class="content_row">
   <?
   if ($customer->getName1() != "")
      echo $customer->getName1();
   else
   echo $customer->getName2();
   ?>
   </td>
   <? 
    for ($y = date('Y') - 4; $y <= date('Y'); $y++) {
    ?>
   <td class="content_row" style="text-align:center">&nbsp;<?=$values[$y][$customer->getId()] ?></td>
   <?
     $sum[$y] += $values[$y][$customer->getId()];}
   ?>
      </tr>
   <?
   $x++;
}
?>
<tr>
   <td class="content_row_header"><?=$_LANG->get('Summe')?>:</td>
<?
for ($y = date('Y') - 4; $y <= date('Y'); $y++) {
   echo '<td class="content_row_header" style="text-align:center">'.$sum[$y].'&nbsp;</td>';
}
?>
</tr>
</table>
</div>
<? } ?>
<br /><br />
<div>
 <ul class="postnav_save">
                <a href="libs/modules/statistics/pdf.projects_per_cust.php?selyear=<?=$_REQUEST["selyear"]?>" target="_blank"><?=$_LANG->get('PDF-Anzeige')?></a>
            </ul>
</div>