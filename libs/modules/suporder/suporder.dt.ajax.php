<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once '../../../config.php';

$aColumns = array( 'id', 'number', 'title', 'suppliername', 'status', 'crtdate' );

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "storage_areas";

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
if ( ! $gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
{
    fatal_error( 'Could not open connection to server' );
}

if ( ! mysql_select_db( $gaSql['db'], $gaSql['link'] ) )
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
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
        {
            $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
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
        $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
    }
}


/*
 * SQL queries
 * Get data to display
 */
$sQuery = "SELECT
            suporders.id,
            suporders.number,
            suporders.title,
            suporders.`status`,
            suporders.crtdate,
            businesscontact.name1 as suppliername
            FROM
            suporders
            INNER JOIN businesscontact ON suporders.supplier = businesscontact.id
           $sWhere
           $sOrder
           $sLimit
           ";

//     var_dump($sQuery);

$rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );

/* Data set length after filtering */
$sQuery = "SELECT COUNT(suporders.id)
           FROM suporders
           $sWhere";
//     var_dump($sQuery);
$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];


/* Total data set length */
$sQuery = "SELECT COUNT(suporders.id)
           FROM suporders";
//     var_dump($sQuery);
$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
$aResultTotal = mysql_fetch_array($rResultTotal);
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

while ( $aRow = mysql_fetch_array( $rResult ) )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( $aColumns[$i] == 'id' )
        {
            /* do not print the id */
            $row[] = $aRow[ $aColumns[$i] ];
        } elseif ( $aColumns[$i] == 'crtdate')
        {
            $row[] = date('d.m.Y H:i',$aRow[ $aColumns[$i] ]);
        } elseif ( $aColumns[$i] == 'status')
        {
            switch ($aRow[$aColumns[$i]]){
                case 1:
                    $status = 'Offen';
                    break;
                case 2:
                    $status = 'Bestellt';
                    break;
                case 3:
                    $status = 'Ware Eingegangen';
                    break;
                case 4:
                    $status = 'Bezahlt';
                    break;
                case 5:
                    $status = 'Erledigt';
                    break;
                default:
                    $status = 'Unbekannt';
                    break;
            }
            $row[] = $status;
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