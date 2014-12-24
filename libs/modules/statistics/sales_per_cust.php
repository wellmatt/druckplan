<?
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once('libs/modules/documents/document.class.php');


$_REQUEST["selyear"] = (int)$_REQUEST["selyear"];
if ($_REQUEST["selyear"])
{
/*****************************************************************
 * Jahr wurde ausgew�hlt
 */

?>

<div class="box1" style="width:1300px">
<table width="1300px">
   <colgroup>
      <col width="500">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
      <col width="150">
   </colgroup>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('Name')?> <div style="float:right"><?=$_REQUEST["selyear"]?></div></td>
   <?
   $months = array("Januar","Februar","M�rz","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
   for ($m = 0; $m < 12; $m++) {
   	?>
      <td class="content_row_header" style="text-align:right"><?=$_LANG->get($months[$m])?></td>
      <?
   }
   ?>
   </tr>
<?
$customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,BusinessContact::FILTER_CUST_IST);

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
   
      $sales1 = Document::salesPerCustMonthMod1($customer->getId(), $_REQUEST["selyear"], $m);
      $sales2 = Document::salesPerCustMonthMod2($customer->getId(), $_REQUEST["selyear"], $m);
      
       $total_month1[$m] += number_format($sales1[0][0]['total'], 2, '.', '');
       $total_month2[$m] += number_format($sales2[0][0]['total'], 2, '.', '');
       echo '<td class="content_row" style="text-align:right">'.printPrice($sales1[0][0]['total'] + $sales2[0][0]['total']) .' EUR</td>';
     
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
	
   echo '<td class="content_row_header" style="text-align:right">'.printPrice($total_month1[$m] + $total_month2[$m]).' EUR</td>';
}
?>
</tr>
</table>
</div>
<?
} else 
{
//-----------------------------------------------------------------------
// Kein Jahr ausgewählt
//
?>

<div class="box1">
<table class="standard">
   <colgroup>
      <col>
      <col width="100">
      <col width="100">
      <col width="100">
      <col width="100">
   </colgroup>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('Name')?></td>
   <?
   for ($y = date('Y') - 3; $y <= date('Y'); $y++) {
   ?>
      <td class="content_row_header" style="text-align:center">
         <a href="index.php?page=<?=$_REQUEST['page']?>&exec=salesPerCustomer&selyear=<?=$y?>"><?=$y?></a>
      </td>
      <? 
   }
   ?>
   </tr>
<?
$customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,BusinessContact::FILTER_CUST_IST);


$x = 0;
foreach ($customers as $i => $customer)
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
   for ($y = date('Y') - 3; $y <= date('Y'); $y++) {
   
  $sales1 = Document::salesPerCustMod1($customer->getId(), $y);
  $sales2 = Document::salesPerCustMod2($customer->getId(), $y);
  $total_year1[$y] += number_format($sales1[0][0]['total'], 2, '.', '');
  $total_year2[$y] += number_format($sales2[0][0]['total'], 2, '.', '');
  echo '<td class="content_row" style="text-align:right">'.printPrice($sales1[0][0]['total']+$sales2[0][0]['total']) .' EUR</td>';
    
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
for ($y = date('Y') - 3; $y <= date('Y'); $y++) {
   echo '<td class="content_row_header" style="text-align:right">'.printPrice($total_year1[$y] + $total_year2[$y]).' EUR</td>';
}
?>
</tr>
</table>
</div>
<? } // if selyear?>

<br /><br />
<div>
 <ul class="postnav_save">
                <a href="libs/modules/statistics/pdf.sales_per_cust.php?selyear=<?=$_REQUEST["selyear"]?>" target="_blank"><?=$_LANG->get('PDF-Anzeige')?></a>
            </ul>
</div>