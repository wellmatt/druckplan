<?php
/**
 * Created by PhpStorm.
 * User: ascherer
 * Date: 03.03.2016
 * Time: 07:53
 */

require_once 'marketing.class.php';

if ($_REQUEST["exec"] == "save")
{
//    prettyPrint($_REQUEST);
    if ($_REQUEST["column"])
        foreach ($_REQUEST["column"] as $id => $item) {
            $column = new MarketingColumn((int)$id);
            $column->setTitle($item['title']);
            $column->setSort((int)$item['sort']);
            $column->setList(new MarketingList($_REQUEST["id"]));
            $column->save();
        }
    if ($_REQUEST["removed"])
        foreach ($_REQUEST["removed"] as $item) {
            $delcolumn = new MarketingColumn((int)$item);
            $delcolumn->delete();
        }
    if ($_REQUEST["listname"]){
        $tmp_list = new MarketingList($_REQUEST["id"]);
        $tmp_list->setTitle($_REQUEST["listname"]);
        $tmp_list->save();
    }
}

$columns = MarketingColumn::getAllColumnsForList($_REQUEST["id"]);
$list = new MarketingList($_REQUEST["id"]);
?>
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 250px; }
    #sortable li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; height: 1.5em; }
    html>body #sortable li { height: 1.5em; line-height: 1.2em; }
    .ui-state-highlight { height: 1.5em; line-height: 1.2em; }
</style>

<div id="fl_menu">
    <div class="label">Quick Move</div>
    <div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="#" class="menu_item" onclick="$('#marketing_column_form').submit();">Speichern</a>
    </div>
</div>

<div class="box1">
    <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="marketing_column_form" id="marketing_column_form">
        <input type="hidden" name="exec" value="save"/>
        <input type="hidden" name="id" value="<?=$_REQUEST["id"]?>"/>
        <div id="removed"></div>
        <h3>Marketing Spalten</h3></br>

        Listen Name: <input name="listname" id="listname" type="text" value="<?php echo $list->getTitle();?>"><br>
        <ul id="sortable">
            <?php foreach ($columns as $column) {?>
                <li class="ui-state-default">
                    <input type="hidden" class="marketing_id" value="<?php echo $column->getId()?>">
                    <input type="hidden" class="marketing_sort" name="column[<?php echo $column->getId()?>][sort]" value="<?php echo $column->getSort()?>">
                    <input type="hidden" class="marketing_title" name="column[<?php echo $column->getId()?>][title]" value="<?php echo $column->getTitle()?>">
                    <span><?php echo $column->getTitle()?></span>
                    <img src="images/icons/cross.png" class="pull-right pointer" onclick="removeColumn(this);"/>
                    <img src="images/icons/pencil.png" class="pull-right pointer" onclick="newLabel(this);"/>
                </li>
            <?php } ?>
        </ul>
        </br>
        Neue Spalte: <input id="newcolumn" type="text"> <button type="button" onclick="addColumn();" class="btn btn-sm">Hinzufügen</button>
    </form>
</div>

<script>
    function newLabel(el)
    {
        var oldlabel = $(el).parent().children('span').text();
        var newlabel = window.prompt("Neuer Titel der Spalte",oldlabel);
        $(el).parent().children('input.marketing_title').val(newlabel);
        $(el).parent().children('span').text(newlabel);
    }
    function removeColumn(el)
    {
        var r = confirm("Sicher?");
        if (r == true) {
            var id = $(el).parent().children('#sortable > li > input.marketing_id').val();
            if (id)
                $('#removed').append('<input type="hidden" name="removed[]" value="'+id+'"/>');
            $(el).parent().remove();
            $('#sortable > li > input.marketing_sort').each(function (index) {
                $(this).val(index);
            });
        }
    }
    function addColumn()
    {
        var label = $('#newcolumn').val();
        var count = $('#sortable > li > input.marketing_sort').length+1;
        var insert = '<li class="ui-state-default">';
        insert += '<input type="hidden" class="marketing_sort" name="column[0][sort]" value="'+count+'">';
        insert += '<input type="hidden" class="marketing_title" name="column[0][title]" value="'+label+'">';
        insert += '<span>'+label+'</span>';
        insert += '<img src="images/icons/cross.png" class="pull-right pointer" onclick="removeColumn(this);"/>';
        insert += '<img src="images/icons/pencil.png" class="pull-right pointer" onclick="newLabel(this);"/></li>';
        $('#sortable').append(insert);
        $('#newcolumn').val('');
        $('#marketing_column_form').submit();
    }
    $(function() {
        $( "#sortable" ).sortable({
            placeholder: "ui-state-highlight",
            stop: function( event, ui ) {
                $('#sortable > li > input.marketing_sort').each(function (index) {
                    $(this).val(index);
                });
            }
        });
        $( "#sortable" ).disableSelection();
    });
</script>