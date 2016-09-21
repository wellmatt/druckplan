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
$updatedata = json_decode(file_get_contents("http://ccp.mein-druckplan.de/public/api/updates"));
if ($updatedata && $updatedata->success == 1 && isset($updatedata->data)){
    if (is_array($updatedata->data)){
        foreach (array_reverse($updatedata->data) as $item) {
            $changelog .= $break.$break.$item->created_at.$break.$item->changelog;
            $sql .= $break.$item->sql;
        }
        $update = $updatedata->data[0];
    } else {
        $update = $updatedata->data;
        $changelog = $update->changelog;
        $sql = $update->sql;
    }
}

?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Update Info</h3>
	  </div>
	  <div class="panel-body">
          <div class="form-group">
              <label for="" class="col-sm-2 control-label">Version</label>
              <div class="col-sm-10 form-text">
                  <?php echo $update->version;?>
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
</div>
