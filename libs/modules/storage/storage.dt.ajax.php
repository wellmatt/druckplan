<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once '../../../config.php';

$aColumns = array( 'id', 'name', 'alloc', 'intext' );

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
    $sWhere = "HAVING (";
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
            $sWhere = "HAVING ";
        }
        else
        {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
    }
}

if ($_GET['ajax_nr'] != ""){
    if ($sWhere == ""){
        $sWhere .= " WHERE number LIKE '%{$_GET['ajax_nr']}%' ";
    } else {
        $sWhere .= " AND number LIKE '%{$_GET['ajax_nr']}%' ";
    }
}

if ($_GET['ajax_location'] != ""){
    if ($sWhere == ""){
        $sWhere .= " WHERE location LIKE '%{$_GET['ajax_location']}%' ";
    } else {
        $sWhere .= " AND location LIKE '%{$_GET['ajax_location']}%' ";
    }
}

if ($_GET['ajax_type'] != ""){
    if ($sWhere == ""){
        $sWhere .= " WHERE `type` LIKE '%{$_GET['ajax_type']}%' ";
    } else {
        $sWhere .= " AND `type` LIKE '%{$_GET['ajax_type']}%' ";
    }
}

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "SELECT
            storage_areas.id,
            storage_areas.`name`,
            COALESCE(SUM(storage_positions.allocation),0) as alloc,
            storage_areas.intext
            FROM
            storage_areas
            LEFT JOIN storage_positions ON storage_areas.id = storage_positions.area
           $sWhere
            GROUP BY storage_areas.id
           $sOrder
           $sLimit
           ";

//     var_dump($sQuery);

$rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );

/* Data set length after filtering */
$sQuery = "SELECT
            count(id)
            FROM
            (
            SELECT
            storage_areas.id,
            storage_areas.`name`,
            COALESCE(SUM(storage_positions.allocation),0) as alloc
            FROM
            storage_areas
            LEFT JOIN storage_positions ON storage_areas.id = storage_positions.area
            $sWhere
            GROUP BY storage_areas.id
            ) t1
            ";
//     var_dump($sQuery);
$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];


/* Total data set length */
$sQuery = "SELECT COUNT(storage_areas.id)
           FROM storage_areas";
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
        } elseif ( $aColumns[$i] == 'alloc')
        {
            $row[] = $aRow[ $aColumns[$i] ].'%';
        } elseif ( $aColumns[$i] == 'intext')
        {
            if ($aRow[ $aColumns[$i] ] == 2){
                $row[] = "Ja";
            } else {
                $row[] = "Nein";
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