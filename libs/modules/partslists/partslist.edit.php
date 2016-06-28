<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once 'libs/modules/partslists/partslist.class.php';

if ($_REQUEST['exec'] == 'save'){
    if ((int)$_REQUEST["id"] > 0){
        $array = [
            'title' => $_REQUEST["title"],
            'price' => tofloat($_REQUEST["price"]),
        ];
    } else {
        $array = [
            'title' => $_REQUEST["title"],
            'price' => tofloat($_REQUEST["price"]),
            'crtdate' => time(),
            'crtuser' => $_USER->getId(),
        ];
    }
    $partslist = new Partslist((int)$_REQUEST["id"], $array);
    $savemsg = getSaveMessage($partslist->save()).$DB->getLastError();
    $_REQUEST["id"] = $partslist->getId();
}

$partslist = new Partslist((int)$_REQUEST['id']);

if ($partslist->getId()>0){
    $artlist = $partslist->getMyArticles();
} else {
    $artlist = null;
}

?>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/partslists/partslist.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#partslistform').submit();",'glyphicon-floppy-disk');
if ($_USER->isAdmin() && $partslist->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/partslists/partslist.overview.php&exec=delete&did=".$partslist->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<?php if (isset($savemsg)) { ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Hinweis!</strong> <?= $savemsg ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Stückliste<?php if ($partslist->getId()>0) echo ' - '.$partslist->getTitle(); else echo ' - Neu';?></h3>
            </div>
            <div class="panel-body">
                <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="partslistform" id="partslistform" method="post" class="form-horizontal" role="form">
                    <input type="hidden" name="exec" value="save">
                    <input type="hidden" name="id" id="id" value="<?php echo (int)$_REQUEST['id'];?>">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Titel</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" id="title" value="<?php echo $partslist->getTitle();?>" placeholder="Titel der Stückliste">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Preis</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" name="price" id="price" value="<?php echo printPrice($partslist->getPrice(),2);?>" placeholder="Preis der Stückliste">
                                <div class="input-group-addon"><span>€</span></div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($partslist->getId()>0){
                        ?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Erstellt von</label>
                            <div class="col-sm-10 form-text">
                                <?php echo $partslist->getCrtuser()->getNameAsLine();?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Erstellt am</label>
                            <div class="col-sm-10 form-text">
                                <?php echo date('d.m.y',$partslist->getCrtdate());?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php if ($partslist->getId()>0){?>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            Artikel
                                            <small>
                                                <span class="glyphicons glyphicons-plus pointer" title="neuer Artikel" onclick="callBoxFancyArtFrame('libs/modules/partslists/partslist.article.frame.php?list=<?php echo $partslist->getId();?>');"></span>
                                            </small>
                                        </h3>
                                    </div>
                                    <div class="panel-body" id="partslistarticles"></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        load_content();
    });
    function unblock(){
        $('#partslistarticles').unblock();
    }
    function load_content(){
        $('#partslistarticles').block({ message: '<h3><img src="images/page/busy.gif"/> einen Augenblick...</h3>' });
        $('#partslistarticles').load( "libs/modules/partslists/partslist.edit.positions.php?list="+$('#id').val(), null, unblock );
    }
</script>

<script>
    $(function() {
        $("a#hiddenclicker_artframe").fancybox({
            'type'    : 'iframe',
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'padding'		:	25,
            'margin'        :   25,
            'scrolling'     :   'no',
            'width'		    :	1000,
            'height'        :   800,
            'onComplete'    :   function() {
                    $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                    $('#fancybox-wrap').css('top','25px');
                });
            },
            'overlayShow'	:	true,
            'helpers'		:   { overlay:null, closeClick:true }
        });
    });
    function callBoxFancyArtFrame(my_href) {
        var j1 = document.getElementById("hiddenclicker_artframe");
        j1.href = my_href;
        $('#hiddenclicker_artframe').trigger('click');
    }
</script>
<div id="hidden_clicker95" style="display:none"><a id="hiddenclicker_artframe" href="http://www.google.com" >Hidden Clicker</a></div>