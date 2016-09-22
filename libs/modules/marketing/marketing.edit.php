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
    $marketingjob->setCrtdate(strtotime($_REQUEST["date"]));
    if ($_REQUEST['column'])
        $marketingjob->setData($_REQUEST['column']);
    $marketingjob->save();
    $_REQUEST['id'] = $marketingjob->getId();
}

$marketingjob = new Marketing($_REQUEST['id']);
$lists = MarketingList::getAllLists();
?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/marketing/marketing.overview.php&list='.$_REQUEST['list'].'',null,'glyphicon-step-backward');
$quickmove->addItem('Als neuen Eintrag speichern','#',"$('#entry_id').val(0); $('#marketing_job_form').submit();",'glyphicon-floppy-disk');
$quickmove->addItem('Speichern','#',"$('#marketing_job_form').submit();",'glyphicon-floppy-disk');
if ($marketingjob->getId()>0){
    $quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$marketingjob->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="marketing_job_form" id="marketing_job_form" class="form-horizontal">
    <input type="hidden" name="exec" value="save"/>
    <input type="hidden" name="list" value="<?php echo $_REQUEST["list"];?>"/>
    <input type="hidden" id="entry_id" name="id" value="<?php echo $_REQUEST['id'];?>"/>

    <div class="panel panel-default">
          <div class="panel-heading">
                <h3 class="panel-title">
                    Marketing Job
                </h3>
          </div>
          <div class="panel-body">

              <div class="form-group">
                  <label for="" class="col-sm-1 control-label">Titel</label>
                  <div class="col-sm-3">
                      <input type="text" name="title" class="form-control" value="<?php echo $marketingjob->getTitle();?>" >
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-1 control-label">Kunde</label>
                  <div class="col-sm-3">
                      <input class="form-control" type="text" name="search" id="search" value="<?php echo $marketingjob->getBusinesscontact()->getNameAsLine();?>" >
                      <input class="form-control" type="hidden" name="businesscontact" id="businesscontact" value="<?php echo $marketingjob->getBusinesscontact()->getId();?>" >
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-1 control-label">Datum</label>
                  <div class="col-sm-3">
                      <input class=form-control type="text" name="date" id="date" value="<?if($marketingjob->getCrtdate() != 0){ echo date('d.m.Y', $marketingjob->getCrtdate());} elseif ($marketingjob->getId()==0) { echo date('d.m.Y'); }?>">
                  </div>
              </div>

              <?php foreach ($columns as $column) {?>
              <div class="form-group">
                  <label for="" class="col-sm-1 control-label"><?php echo $column->getTitle();?></label>
                  <div class="col-sm-3">
                      <input class="form-control" type="text" name="column[<?php echo $column->getId();?>]" value="<?php echo $marketingjob->getColumnValue($column->getId());?>">
                  </div>
              </div>
              <?php } ?>
          </div>
    </div>
</form>



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
        $('#date').datetimepicker({
            lang:'de',
            i18n:{
                de:{
                    months:[
                        'Januar','Februar','März','April',
                        'Mai','Juni','Juli','August',
                        'September','Oktober','November','Dezember',
                    ],
                    dayOfWeek:[
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            timepicker:false,
            format:'d.m.Y'
        });
    });
</script>