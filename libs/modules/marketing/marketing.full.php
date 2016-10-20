<?php
chdir("../../../");
require_once 'config.php';
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'thirdparty/phpfastcache/phpfastcache.php';
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/chat/chat.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/abonnements/abonnement.class.php';
require_once 'libs/modules/marketing/marketing.class.php';

//     error_reporting(-1);
//     ini_set('display_errors', 1);

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
if ($_REQUEST["userid"]){
    $_USER = new User((int)$_REQUEST["userid"]);
} else {
    $_USER = new User();
    $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
}
$_LANG = $_USER->getLang();

if ($_USER == false){
    error_log("Login failed (basic-importer.php)");
    die("Login failed");
}
$columns = MarketingColumn::getAllColumns();
$marketjobs = Marketing::getAll();

?>
<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery.blockUI.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../../jscripts/moment/moment-with-locales.min.js"></script>
<!-- /jQuery -->

<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>

<!-- MegaNavbar -->
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- /MegaNavbar -->

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/date-uk.js"></script>


<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                Marketingplan
            </h3>
	  </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table id="marketing_table" class="table table-hover">
                <thead>
                <tr>
                    <th><?= $_LANG->get('ID') ?></th>
                    <th><?= $_LANG->get('Titel') ?></th>
                    <th><?= $_LANG->get('Kunde') ?></th>
                    <th><?= $_LANG->get('Datum') ?></th>
                    <?php foreach ($columns as $column) { ?>
                        <th><?php echo $column->getTitle() ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <?php foreach ($marketjobs as $marketjob) { ?>
                    <tr>
                        <td><?php echo $marketjob->getId(); ?></td>
                        <td><?php echo $marketjob->getTitle(); ?></td>
                        <td><?php echo $marketjob->getBusinesscontact()->getNameAsLine(); ?></td>
                        <td><?php echo date('d.m.y H:i',$marketjob->getCrtdate()); ?></td>
                        <?php foreach ($columns as $column) { ?>
                            <td><?php echo $marketjob->getColumnValue($column->getId());?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tfoot>
                <tr>
                    <th><?= $_LANG->get('ID') ?></th>
                    <th><?= $_LANG->get('Titel') ?></th>
                    <th><?= $_LANG->get('Kunde') ?></th>
                    <th><?= $_LANG->get('Datum') ?></th>
                    <?php foreach ($columns as $column) { ?>
                        <th><?php echo $column->getTitle() ?></th>
                    <?php } ?>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>



<script language="JavaScript">
    $(document).ready(function () {
        var marketingtable = $('#marketing_table').DataTable({
            
            "aaSorting": [[3, "desc"]],
            "dom": 'T<"clear">flrtip',
            "tableTools": {
                "sSwfPath": "../../../jscripts/datatable/copy_csv_xls_pdf.swf",
                "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sPdfMessage": "Contilas - Articles"
                    },
                    "print"
                ]
            },
            "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"]],
            "language": {
                "emptyTable": "Keine Daten vorhanden",
                "info": "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": "Keine Seiten vorhanden",
                "infoFiltered": "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix": "",
                "thousands": ".",
                "lengthMenu": "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing": "Verarbeite...",
                "search": "Suche:",
                "zeroRecords": "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first": "Erste",
                    "last": "Letzte",
                    "next": "N&auml;chste",
                    "previous": "Vorherige"
                },
                "aria": {
                    "sortAscending": ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        });
    });
</script>