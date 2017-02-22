<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'upvote.class.php';


if ($_REQUEST["exec"] == "save"){
    $array = [
        'title' => $_REQUEST["title"],
        'description' => $DB->escape(trim($_REQUEST["description"])),
        'customer' => (int)($_REQUEST["customer"]),
//        'upvotes' => 0,
//        'downvotes' => 0
    ];
    if ($_REQUEST["addvote"] == 'upvote'){
        $array['upvotes'] = (int)$_REQUEST["upvotes"]+1;
    } else if ($_REQUEST["addvote"] == 'downvote'){
        $array['downvotes'] = (int)$_REQUEST["downvotes"]+1;
    }
    if ($_REQUEST['id']==0) {
        $array['crtuser'] = $_USER->getId();
        $array['crtdate'] = time();
        $array['upvotes'] = 0;
        $array['downvotes'] = 0;
    }
    $upvote = new UpVote((int)$_REQUEST["id"], $array);
    $upvote->save();
    $_REQUEST["id"] = $upvote->getId();
}

$upvote = new UpVote((int)$_REQUEST["id"]);

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/upvotes/upvote.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#upvote_form').submit();",'glyphicon-floppy-disk');
if ($upvote->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/upvotes/upvote.overview.php&exec=delete&delid=".$upvote->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">UpVote - <?php echo $upvote->getTitle();?></h3>
	  </div>
	  <div class="panel-body">
          <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="upvote_form" id="upvote_form" method="post" class="form-horizontal" role="form">
              <input type="hidden" name="id" value="<?php echo $_REQUEST["id"];?>">
              <input type="hidden" name="exec" value="save">
              <input type="hidden" name="upvotes" value="<?php echo $upvote->getUpvotes();?>">
              <input type="hidden" name="downvotes" value="<?php echo $upvote->getDownvotes();?>">
              <input type="hidden" name="addvote" id="addvote" value="">
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Titel</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="title" id="title" value="<?php echo $upvote->getTitle();?>">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Beschreibung</label>
                  <div class="col-sm-10">
                      <textarea class="form-control" name="description" id="description"><?php echo $upvote->getDescription();?></textarea>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Kunde</label>
                  <div class="col-sm-10">
                      <select name="customer" id="customer" class="form-control">
                          <?php if ($upvote->getId() > 0){?>
                              <option value="<?php echo $upvote->getCustomer()->getId();?>"><?php echo $upvote->getCustomer()->getNameAsLine();?></option>
                          <?php } ?>
                      </select>
                  </div>
              </div>
              <?php if ($upvote->getId() > 0){?>
                  <div class="form-group">
                      <label for="" class="col-sm-2 control-label">Votes</label>
                      <div class="col-md-1 form-text"><?php echo $upvote->getUpvotes();?> <span id="upvote" class="glyphicons glyphicons-thumbs-up pointer" onclick="Vote(1);"></span></div>
                      <div class="col-md-1 form-text"><?php echo $upvote->getDownvotes();?> <span id="downvote" class="glyphicons glyphicons-thumbs-down pointer" onclick="Vote(0);"></span></div>
                  </div>
              <?php } ?>
          </form>
	  </div>
</div>

<script language="JavaScript">
    function Vote(type){
        if (type == 1){
            $('#upvote').css('background', 'lightgreen');
            $('#downvote').css('background', 'inherit');
            $('#addvote').val('upvote');
        } else {
            $('#upvote').css('background', 'inherit');
            $('#downvote').css('background', 'lightgreen');
            $('#addvote').val('downvote');
        }
    }
    $(function () {
        var editor = CKEDITOR.replace( 'description', {
            // Define the toolbar groups as it is a more accessible solution.
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'tools' },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                { name: 'styles' },
                { name: 'colors' }
            ]
            // Remove the redundant buttons from toolbar groups defined above.
            //removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
        } );

        $("#customer").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_businesscontact",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            language: "de",
            multiple: false,
            allowClear: false,
            tags: false
        }).val(<?php echo $upvote->getCustomer()->getId();?>).trigger('change');

    });
</script>
