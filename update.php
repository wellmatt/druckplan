<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once("config.php");
require_once("libs/basic/mysql.php");
require_once 'libs/modules/backup/backup.class.php';

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$break = '
';
$sql = '';
$uptodate = false;
$updatedata = json_decode(file_get_contents("http://ccp.mein-druckplan.de/public/api/updates"));


if ($updatedata && $updatedata->success == 1 && isset($updatedata->data)){
    if (is_array($updatedata->data) && count($updatedata->data) > 0){

        $update = $updatedata->data[0];
        $updatedate = strtotime($update->created_at);
        if ($updatedate < $installationdate){
            $uptodate = true;
        } else {
            if ($update->version != $_CONFIG->version) {
                foreach (array_reverse($updatedata->data) as $item) {
                    $tmp_updatedate = strtotime($item->created_at);
                    if ($tmp_updatedate > $installationdate)
                        $sql .= ' '.$item->sql;
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

if ($uptodate === false){

    $backup = new Backup();
    $backup->saveFiles();
    $backup->saveDB();

    if (file_exists("update.rar"))
        unlink("update.rar");

    file_put_contents("update.rar", fopen("http://ccp.mein-druckplan.de/public/uploads/".$update->file, 'r'));

    $output = null;
    $status = null;
    exec('unrar x -o+ update.rar',$output,$status);
    unlink("update.rar");
    if (is_array($output))
        $output = implode($break,$output);

    $res = $DB->no_result_multi($sql);
    $error = $DB->getLastError();

    $print =    '<div class="alert alert-success">'.
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.
                '<strong>Info!</strong><pre>'.$output.$break.$break.$error.'</pre>'.
                '</div>';

    echo $print;
}