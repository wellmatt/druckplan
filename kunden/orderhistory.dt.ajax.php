<?php
/**
 * Created by PhpStorm.
 * User: ascherer
 * Date: 11.02.2016
 * Time: 12:09
 */
error_reporting(-1);
ini_set('display_errors', 1);

chdir('../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/attachment/attachment.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
global $_USER;
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
//$_LANG = $_USER->getLang();

if (!$_REQUEST["customerid"])
{
    $output = Array();
    echo json_encode( $output );
    die();
} else {
    $customerid = (int)$_REQUEST["customerid"];
}

$aColumns = array( 'number', 'crtdate', 'positions', 'deliv_id', 'inv_id', 'status', 'id' );

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "collectiveinvoice.id";

/* DB table to use */
$sTable = "collectiveinvoice";

/* Database connection information */
$gaSql['user']       = $_CONFIG->db->user;
$gaSql['password']   = $_CONFIG->db->pass;
$gaSql['db']         = $_CONFIG->db->name;
$gaSql['server']     = $_CONFIG->db->host;


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */

/*
 * Local functions
 */
function fatal_error ( $sErrorMessage = '' )
{
    header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
    die( $sErrorMessage );
}


/*
 * MySQL connection
 */
if ( ! $gaSql['link'] = mysqli_connect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
{
    fatal_error( 'Could not open connection to server' );
}

if ( ! mysqli_select_db( $gaSql['link'], $gaSql['db']) )
{
    fatal_error( 'Could not select database ' );
}


/*
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
    $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
        intval( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = "";
if ( isset( $_GET['iSortCol_0'] ) )
{
    $sOrder = "ORDER BY  ";
    for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
    {
        if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
        {
            $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
        }
    }

    $sOrder = substr_replace( $sOrder, "", -2 );
    if ( $sOrder == "ORDER BY" )
    {
        $sOrder = "";
    }
}


/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
{
    $_GET['sSearch'] = utf8_decode($_GET['sSearch']);
    $sWhere = "WHERE (";
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" )
        {
            $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch'] )."%' OR ";
        }
    }
    $sWhere = substr_replace( $sWhere, "", -3 );
    $sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($aColumns) ; $i++ )
{
    if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
    {
        if ( $sWhere == "" )
        {
            $sWhere = "WHERE ";
        }
        else
        {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch_'.$i])."%' ";
    }
}

if ( $sWhere == "" )
{
    $sWhere = " WHERE status > 0 ";
}
else
{
    $sWhere .= " AND status > 0 ";
}

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "SELECT * FROM (
                SELECT
                collectiveinvoice.id,
                collectiveinvoice.`status`,
                collectiveinvoice.number,
                collectiveinvoice.deliveryaddress as deliv_id,
                collectiveinvoice.invoiceaddress as inv_id,
                collectiveinvoice.crtdate
                FROM
                collectiveinvoice
                WHERE collectiveinvoice.businesscontact = {$customerid}
               ) t1
               $sWhere
               $sOrder
               $sLimit
               ";

//     var_dump($sQuery);

$rResult = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno($gaSql['link']) );

/* Data set length after filtering */
$sQuery = "
        SELECT count(id) FROM (
                SELECT
                collectiveinvoice.id,
                collectiveinvoice.`status`,
                collectiveinvoice.number,
                collectiveinvoice.deliveryaddress as deliv_id,
                collectiveinvoice.invoiceaddress as inv_id,
                collectiveinvoice.crtdate
                FROM
                collectiveinvoice
                WHERE collectiveinvoice.businesscontact = {$customerid}
               ) t1
        $sWhere
    ";
//     var_dump($sQuery);
$rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno($gaSql['link']) );
$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];


/* Total data set length */
$sQuery = "
        SELECT count(id) FROM (
                SELECT
                collectiveinvoice.id,
                collectiveinvoice.`status`,
                collectiveinvoice.number,
                collectiveinvoice.deliveryaddress as deliv_id,
                collectiveinvoice.invoiceaddress as inv_id,
                collectiveinvoice.crtdate
                FROM
                collectiveinvoice
                WHERE collectiveinvoice.businesscontact = {$customerid}
               ) t1
        WHERE status >= 1
    ";
//     var_dump($sQuery);
$rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno($gaSql['link']) );
$aResultTotal = mysqli_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


/*
 * Output
 */
$output = array(
    "sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

//$aColumns = array( 'id', 'number', 'positions', 'deliv_id', 'inv_id', 'crtdate', 'status' );
while ( $aRow = mysqli_fetch_array( $rResult ) )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( $aColumns[$i] == 'id' )
        {
//            $row[] = $aRow[ $aColumns[$i] ];
        }
        else if ( $aColumns[$i] == 'deliv_id' )
        {
            $deliv_adr = new Address($aRow[$aColumns[$i]]);
            $row[] = $deliv_adr->getAddressAsLine();
        }
        else if ( $aColumns[$i] == 'inv_id' )
        {
            $inv_adr = new Address($aRow[$aColumns[$i]]);
            $row[] = $inv_adr->getAddressAsLine();
        }
        else if ( $aColumns[$i] == 'crtdate' )
        {
            $row[] = date("d.m.Y",$aRow['crtdate']);
        }
        else if ( $aColumns[$i] == 'positions' )
        {
            $order = new CollectiveInvoice($aRow['id']);
            $allpos = $order->getPositions();
            $posrow = '';
            foreach ($allpos AS $pos){
                $posrow .= $pos->getCommentForShop() . ' ';
                if ($pos->getFile_attach()>0){
                    $tmp_attach = new Attachment($pos->getFile_attach());
                    $posrow .= '<a href="../'.Attachment::FILE_DESTINATION.$tmp_attach->getFilename().'" download="'.$tmp_attach->getOrig_filename().'">
                                              <img src="../images/icons/disk--arrow.png" title="Angehängte Datei herunterladen"> '.$tmp_attach->getOrig_filename().'</a>';
                }
            }
            $row[] = $posrow;
        }
        else if ( $aColumns[$i] == 'status' )
        {
            switch ($aRow['status']) {
                case 1: $row[]  = "Angelegt";break;
                case 2: $row[]  = "Gesendet u. Bestellt";break;
                case 3: $row[]  = "angenommen";break;
                case 4: $row[]  = "In Produktion";break;
                case 5: $row[]  = "Erledigt";break;
                default: $row[] = "...";
            }
        }
        else
        {
            /* General output */
            $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode( $output );