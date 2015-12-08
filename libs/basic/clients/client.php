<?

$_REQUEST["id"] = 1;
require_once('client.add.php');

/*
if ($_REQUEST["exec"] == "edit")
{
   require_once('client.add.php');
} else
{

if ($_REQUEST["exec"] == "delete")
{
   $client = new Client($_REQUEST["id"]);
   $client->delete();
}
$clients = Client::getAllClients(Client::ORDER_NAME);
?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Mandanten')?></td>
      <td></td>
      <td width="200" class="content_header" align="right"><a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/building--plus.png"> <?=$_LANG->get('Mandanten hinzuf&uuml;gen')?></a></td>
   </tr>
</table>
<div class="box1">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
   <colgroup>
      <col width="20">
      <col width="200">
      <col>
      <col width="40">
      <col width="80">
   </colgroup>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('ID')?></td>
      <td class="content_row_header"><?=$_LANG->get('Mandant')?></td>
      <td class="content_row_header"><?=$_LANG->get('Adresse')?></td>
      <td class="content_row_header"><?=$_LANG->get('Aktiv')?></td>
      <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
   </tr>
   <? $x=0; foreach($clients as $c)
   { $streets = $c->getStreets()?>
      <tr class="<?=getRowColor($x)?>">
         <td class="content_row"><?=$c->getId()?></td>
         <td class="content_row"><?=$c->getName()?></td>
         <td class="content_row"><?=$streets[0]?>, <?=$c->getPostcode()?> <?=$c->getCity()?></td>
         <td class="content_row"><? if($c->isActive()) echo '<img src="images/status/green_small.gif">'; else echo '<img src="images/status/red_small.gif">';?></td>
         <td class="content_row">
            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$c->getId()?>"><img src="images/icons/pencil.png"></a>
            <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$c->getId()?>')"><img src="images/icons/cross-script.png"></a>
         </td>
      </tr>
   <? $x++; } ?>
</table>
</div>
<?} ?>
*/