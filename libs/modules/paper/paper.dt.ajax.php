<?php

    require_once '../../../config.php';

    $aColumns = array( 'id', 'name', 'sizes', 'weight' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "papers";
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" && $aColumns[$i] != "sizes" && $aColumns[$i] != "weight" )
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
        $sWhere = " WHERE status = 1 ";
    }
    else
    {
        $sWhere .= " AND status = 1 ";
    }
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * 
               FROM papers 
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
               SELECT COUNT(id)
               FROM papers 
               $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
                SELECT COUNT(id) 
                FROM papers 
                WHERE status = 1
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
     
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'sizes' )
            {
                $tmp_row = '';
                $sQuerySizes = 'SELECT * FROM papers_sizes 
                                WHERE paper_id = '.$aRow[ $aColumns[0] ].'
                                ORDER BY width';
                $rAttributes = mysql_query( $sQuerySizes, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
                while ($data = mysql_fetch_row($rAttributes)){
                    $tmp_row .= $data[1] . "x" . $data[2] . "\n";
                }
                $row[] = nl2br(htmlentities(utf8_encode($tmp_row)));
            }
            else if ( $aColumns[$i] == 'weight' )
            {
                $tmp_row = '';
                $sQuerySizes = 'SELECT * FROM papers_weights 
                                WHERE paper_id = '.$aRow[ $aColumns[0] ].' 
                                ORDER BY weight';
                $rAttributes = mysql_query( $sQuerySizes, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
                while ($data = mysql_fetch_row($rAttributes)){
                    $tmp_row .= $data[1] . "g \n";
                }
                $row[] = nl2br(htmlentities(utf8_encode($tmp_row)));
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        $row[] = '<a class="icon-link" href="index.php?page=libs/modules/paper/paper.php&exec=edit&id='.$aRow[ $aColumns[0] ].'"><img src="images/icons/pencil.png"></a>
                  <a class="icon-link" href="index.php?page=libs/modules/paper/paper.php&exec=copy&id='.$aRow[ $aColumns[0] ].'"><img src="images/icons/scripts.png"></a>
                  <a class="icon-link" href="#"	onclick="askDel(\'index.php?page=libs/modules/paper/paper.php&exec=delete&id='.$aRow[ $aColumns[0] ].'\')"><img src="images/icons/cross-script.png"></a>';
        $output['aaData'][] = $row;
    }
    
//     var_dump($output); echo "</br>";
     
    echo json_encode( $output );
    
//     echo  json_last_error();
?>