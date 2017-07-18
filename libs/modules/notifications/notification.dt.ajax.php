<?php

    require_once '../../../config.php';

    $aColumns = array( 'id', 'title', 'crtdate', 'path', 'state' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "notifications";
     
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
 
    if ( ! mysqli_select_db( $gaSql['link'], $gaSql['db'] ) )
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" && $aColumns[$i] != "path" )
            {
                $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string( $gaSql['link'], $_GET['sSearch'] )."%' OR ";
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
            $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string( $gaSql['link'], $_GET['sSearch_'.$i])."%' ";
        }
    }
    
    if ($sWhere == ""){
        $sWhere .= " WHERE user = ".$_REQUEST["userid"]." ";
    } else {
        $sWhere .= " AND user = ".$_REQUEST["userid"]." ";
    }
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM notifications 
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT COUNT(id) FROM notifications 
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(id) FROM notifications WHERE user = ".$_REQUEST["userid"]." 
    ";
//     var_dump($sQuery);
    $rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
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
     
    while ( $aRow = mysqli_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'id' )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'crtdate' )
            {
                $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'path' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'title' )
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
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
?>