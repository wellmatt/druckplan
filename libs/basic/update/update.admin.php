<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
$break = '
';
$changelog = '';
$sql = '';
$uptodate = false;
$installationdate = strtotime($_CONFIG->version_date);
$updatedata = json_decode(file_get_contents("http://ccp.mein-druckplan.de/public/api/updates"));

if ($updatedata && $updatedata->success == 1 && isset($updatedata->data)){
    if (is_array($updatedata->data) && count($updatedata->data) > 0){

        $update = $updatedata->data[0];
        $updatedate = strtotime($update->created_at);
        if ($updatedate < $installationdate){
            $uptodate = true;
        } else {
            if ($update->version != $_CONFIG->version) {
                foreach ($updatedata->data as $item) {
                    $tmp_updatedate = strtotime($item->created_at);
                    if ($tmp_updatedate > $installationdate)
                        $changelog .= $break . $break . $item->created_at . ': ' . $item->version . $break . $item->changelog;
                }
                foreach (array_reverse($updatedata->data) as $item) {
                    $tmp_updatedate = strtotime($item->created_at);
                    if ($tmp_updatedate > $installationdate)
                        $sql .= $break . $item->sql;
                }
            } else {
                $uptodate = true;
            }
        }

    } else {

        if (!$updatedata->data === []){

            if ($updatedate < $installationdate){
                $uptodate = true;
            } else {
                $update = $updatedata->data;
                if ($update->version != $_CONFIG->version) {
                    $changelog = $update->changelog;
                    $sql = $update->sql;
                } else {
                    $uptodate = true;
                }
            }

        } else {
            $uptodate = true;
        }

    }
} else {
    $uptodate = true;
}

?>

<script>
    function tryUpdate(){
        $.ajax({
            type: "GET",
            url: "update.php",
            success: function(data)
            {
                $( '#updatestatus' ).append( data );
            }
        });
    }
</script>

<div class="row">
    <div class="col-md-12" id="updatestatus"></div>
</div>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Update Info
                <span class="pull-right">Installiert: <?php echo $_CONFIG->version;?></span>
            </h3>
	  </div>
	  <div class="panel-body">
          <?php if ($uptodate == false){ ?>
              <div id="updatedata">
                  <div class="form-group">
                      <label for="" class="col-sm-2 control-label">Version</label>
                      <div class="col-sm-9 form-text">
                          <?php echo $update->version;?>
                      </div>
                      <div class="col-sm-1">
                          <button type="button" onclick="tryUpdate(); $(this).prop('disabled', true);" class="btn btn-default">Update</button>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="" class="col-sm-2 control-label">Datum</label>
                      <div class="col-sm-10 form-text">
                          <?php echo $update->created_at;?>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="" class="col-sm-2 control-label">Changelog</label>
                      <div class="col-sm-10 form-text">
                          <pre><?php echo $changelog;?></pre>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="" class="col-sm-2 control-label">SQL</label>
                      <div class="col-sm-10 form-text">
                          <pre><?php echo $sql;?></pre>
                      </div>
                  </div>
              </div>
          <?php } else { ?>
              <h3>Already Up to Date!</h3>
          <?php } ?>
	  </div>
</div>
