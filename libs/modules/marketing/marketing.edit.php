<?php
/**
 * Created by PhpStorm.
 * User: ascherer
 * Date: 03.03.2016
 * Time: 09:44
 */
require_once 'marketing.class.php';
$columns = MarketingColumn::getAllColumnsForList($_REQUEST["list"]);

if ($_REQUEST["exec"] == "delete"){
    $del_job = new Marketing($_REQUEST["id"]);
    $del_job->delete();
    header('Location: index.php?page=libs/modules/marketing/marketing.overview.php');
}

if ($_REQUEST["exec"] == "save"){
//    prettyPrint($_REQUEST);
    $marketingjob = new Marketing($_REQUEST['id']);
    $marketingjob->setList(new MarketingList($_REQUEST["list"]));
    $marketingjob->setTitle($_REQUEST["title"]);
    $marketingjob->setBusinesscontact(new BusinessContact((int)$_REQUEST["businesscontact"]));
    if ($_REQUEST['column'])
        $marketingjob->setData($_REQUEST['column']);
    $marketingjob->save();
    $_REQUEST['id'] = $marketingjob->getId();
}

$marketingjob = new Marketing($_REQUEST['id']);
$lists = MarketingList::getAllLists();
?>

<div id="fl_menu">
    <div class="label">Quick Move</div>
    <div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=libs/modules/marketing/marketing.overview.php" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#marketing_job_form').submit();">Speichern</a>
        <?php if ($marketingjob->getId()>0){ ?>
        <a href="#" class="menu_item_delete" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$marketingjob->getId()?>');">Löschen</a>
        <?php } ?>
    </div>
</div>

<div class="box1">
    <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="marketing_job_form" id="marketing_job_form">
        <input type="hidden" name="exec" value="save"/>
        <input type="hidden" name="list" value="<?php echo $_REQUEST["list"];?>"/>
        <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>"/>
        <h3>Marketing Job</h3></br>

        <table>
            <tr>
                <td>Titel</td>
                <td><input type="text" name="title" value="<?php echo $marketingjob->getTitle();?>" required style="width: 100%"></td>
            </tr>
            <tr>
                <td>Kunde</td>
                <td>
                    <input type="text" name="search" id="search" value="<?php echo $marketingjob->getBusinesscontact()->getNameAsLine();?>" style="width:280px" required>
                    <input type="hidden" name="businesscontact" id="businesscontact" value="<?php echo $marketingjob->getBusinesscontact()->getId();?>" required>
                </td>
            </tr>
            <?php foreach ($columns as $column) {?>
                <tr>
                    <td><?php echo $column->getTitle();?></td>
                    <td><input type="text" name="column[<?php echo $column->getId();?>]" value="<?php echo $marketingjob->getColumnValue($column->getId());?>" required style="width: 100%"></td>
                </tr>
            <?php } ?>
        </table>
    </form>
</div>


<script>
    $(function() {
        $( "#search" ).autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer',
            minLength: 2,
            dataType: "json",
            select: function(event, ui) {
                $('#search').val(ui.item.label);
                $('#businesscontact').val(ui.item.value);
                return false;
            }
        });
    });
</script>