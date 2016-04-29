<div class="panel-body">
    <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="tradegroup_edit" id="tradegroup_edit"
          class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.tradegroup_title))">
        <input type="hidden" name="exec" value="edit">
        <input type="hidden" name="subexec" value="save">
        <input type="hidden" name="id" value="<?=$tradegroup->getId()?>">

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Titel</label>
            <div class="col-sm-10">
                <input id="tradegroup_title" name="tradegroup_title" type="text" class="form-control" value="<?=$tradegroup->getTitle()?>">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Beschreibung</label>
            <div class="col-sm-10">
                <textarea id="tradegroup_desc" name="tradegroup_desc" type="text" class="form-control"><?=$tradegroup->getDesc()?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Shop-Freigabe</label>
            <div class="col-sm-10">
                <input 	id="tradegroup_shoprel" name="tradegroup_shoprel" class="text" type="checkbox" value="1" <?if ($tradegroup->getShoprel() == 1) echo "checked"; ?>>
            </div>
        </div>


        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Übergeordnete Gruppe</label>
            <div class="col-sm-10">
                <select id="tradegroup_parentid" name="tradegroup_parentid" type="text" class="form-control">
                    <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
                    <?	foreach ($all_tradegroups as $tg){
                        if ($tg->getId() != $tradegroup->getId()){ ?>
                            <option value="<?=$tg->getId()?>"
                                <?if ($tradegroup->getParentID() == $tg->getId()) echo "selected" ;?> ><?= $tg->getTitle()?></option>
                        <?		}
                        printSubTradegroupsForSelect($tg->getID(), 0);
                    } ?>
                </select>
            </div>
        </div>
    </form>
</div>
</div>