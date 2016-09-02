<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       01.06.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/basic/license/license.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;
$_LICENSE = new License();
if (!$_LICENSE->isValid())
    die("No valid licensefile, please contact iPactor GmbH for further assistance");

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));

if($_REQUEST["exec"] == "checkMachineType")
{
    $mach = new Machine((int)$_REQUEST["machId"]);
    
    if($_LICENSE->isAllowed($mach))
        echo "1";
    else
        echo "0";
}

if($_REQUEST["exec"] == "updateDifficultyFields")
{
    $mach = new Machine((int)$_REQUEST["machId"]);
    $val = (int)$_REQUEST["val"];
    $unit = (int)$_REQUEST["unit"];
    $id = (int)$_REQUEST["id"];
    if($val != $unit)
    {
        // Andere Einheit ausgew�hlt
?>

        <div id="tr_difficulty_fields_<?php echo $id ;?>" class="form-group">
            <label for="" class="col-sm-3 control-label">&nbsp;</label>
            <div class="col-sm-3">
                <input class=form-control name="machine_difficulty[<?php echo $id; ?>][values][]">
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input class="form-control" name="machine_difficulty[<?php echo $id; ?>][percents][]">
                 <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">&nbsp;</div>
        </div>

<?php
    } else
    {
        // Alte Einheit ausgew�hlt
        if(count($mach->getDifficulties()) > 0)
        {
?>
                <?php
                $x = 0;
                foreach($mach->getDifficulties() as $diff)
                {
                    if ($diff["id"] == $id){
                        foreach($diff["values"] as $diff_values){
                ?>
                            <div id="tr_difficulty_fields_<?php echo $id; ?>" class="form-group">
                                <label for="" class="col-sm-3 control-label">&nbsp;</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="machine_difficulty[<?php echo $id; ?>][values][]"
                                           value="<?php echo $diff["values"][$x]; ?>">
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <input class="form-control"
                                               name="machine_difficulty[<?php echo $id; ?>][percents][]"
                                               value="<?php echo $diff["percents"][$x]; ?>">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">&nbsp;</div>
                            </div>


                            <?php
                            $x++;
                        }
                    }
                }
                ?>


<?php
        } else
        {
            ?>
            <div id="tr_difficulty_fields_<?php echo $id ;?>" class="form-group">
                <label for="" class="col-sm-3 control-label">&nbsp;</label>
                <div class="col-sm-3">
                    <input class="form-control" name="machine_difficulty[<?php echo $id ;?>][values][]">
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input class="form-control" name="machine_difficulty[<?php echo $id ;?>][percents][]">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">&nbsp;</div>
            </div>

<?php
        }
    }
}
?>