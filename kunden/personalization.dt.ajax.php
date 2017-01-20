<?php

    require_once '../config.php';
    
    if (!$_REQUEST["customerid"])
    {
        $output = Array();
        echo json_encode( $output );
        die();
    } else {
        $customerid = (int)$_REQUEST["customerid"];
    }

    $aColumns = array( 'id', 'descr', 'title', 'crtdate', 'orderdate', 'amount' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "article";
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" && $aColumns[$i] != "art_picture" )
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
    
    if ( $sWhere == "" )
    {
        $sWhere = " WHERE status >= 1 ";
    }
    else
    {
        $sWhere .= " AND status >= 1 ";
    }
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM (
                SELECT
                personalization_orders.id,
                personalization_orders.title AS descr,
                personalization.title,
                personalization_orders.crtdate,
                personalization_orders.orderdate,
                personalization_orders.amount,
                personalization_orders.status
                FROM
                personalization_orders
                INNER JOIN personalization ON personalization.id = personalization_orders.persoid 
                WHERE personalization_orders.customerid = {$customerid} 
               ) t1 
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT count(id) FROM (
                SELECT
                personalization_orders.id,
                personalization_orders.title AS descr,
                personalization.title,
                personalization_orders.crtdate,
                personalization_orders.orderdate,
                personalization_orders.amount,
                personalization_orders.status
                FROM
                personalization_orders
                INNER JOIN personalization ON personalization.id = personalization_orders.persoid
                WHERE personalization_orders.customerid = {$customerid} 
               ) t1 
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT count(id) FROM (
                SELECT
                personalization_orders.id,
                personalization_orders.title AS descr,
                personalization.title,
                personalization_orders.crtdate,
                personalization_orders.orderdate,
                personalization_orders.amount,
                personalization_orders.status
                FROM
                personalization_orders
                INNER JOIN personalization ON personalization.id = personalization_orders.persoid
                WHERE personalization_orders.customerid = {$customerid} 
               ) t1 
        WHERE status >= 1
    ";
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

//     $aColumns = array( 'id', 'descr', 'title', 'crtdate', 'orderdate', 'amount' );
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'id' )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'descr' )
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow['descr'])));
            }
            else if ( $aColumns[$i] == 'title' )
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow['title'])));
            }
            else if ( $aColumns[$i] == 'crtdate' )
            {
                $row[] = date("d.m.Y",$aRow['crtdate']);
            }
            else if ( $aColumns[$i] == 'orderdate' )
            {
                if ($aRow['orderdate']>0)
                    $row[] = date("d.m.Y",$aRow['orderdate']);
                else 
                    $row[] = "";
            }
            else if ( $aColumns[$i] == 'amount' )
            {
                $row[] = $aRow['amount'] . " Stk.";
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        $row[] = '<a href="index.php?pid=40&persoorderid='.$aRow[ $aColumns[0] ].'&exec=edit" class="button">Ansehen</a>
			      &ensp;<a href="index.php?pid=40&deleteid='.$aRow[ $aColumns[0] ].'&exec=delete" class="button" 
                  onclick="return confirm(\'Personalisierung wirklich l&ouml;schen?\')"><span style="color: red;" class="glyphicons glyphicons-remove"></span></a>';
        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );
?>