<?php
    require_once '../../../config.php';

    $aColumns = array( 'null', 'id', 'number', 'title', 'customer', 'deliverydate', 'comment', 'type' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "orders";
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "type" )
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
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' && $aColumns[$i] != "status" )
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
    $sQuery = "SELECT * FROM ( SELECT DISTINCT
               orders.id,orders.number,orders.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) AS customer,
               orders.delivery_date as deliverydate,orders.notes as `comment`,'K' as `type`
               FROM orders INNER JOIN businesscontact ON orders.businesscontact_id = businesscontact.id
               INNER JOIN orders_calculations ON orders_calculations.order_id = orders.id
               WHERE orders_calculations.state = 1
               UNION
               SELECT collectiveinvoice.id,collectiveinvoice.number,collectiveinvoice.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) as customer,
               collectiveinvoice.deliverydate,collectiveinvoice.`comment`,'V' as `type`
               FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
               WHERE `status` >= 3 AND collectiveinvoice.needs_planning = 1) t1  
               $sWhere
               $sOrder
               $sLimit";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
//     $sQuery = "
//         SELECT FOUND_ROWS()
//     ";
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.") 
        FROM ( SELECT DISTINCT
               orders.id,orders.number,orders.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) AS customer,
               orders.delivery_date as deliverydate,orders.notes as `comment`,'K' as `type`
               FROM orders INNER JOIN businesscontact ON orders.businesscontact_id = businesscontact.id
               INNER JOIN orders_calculations ON orders_calculations.order_id = orders.id
               WHERE orders_calculations.state = 1
               UNION
               SELECT collectiveinvoice.id,collectiveinvoice.number,collectiveinvoice.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) as customer,
               collectiveinvoice.deliverydate,collectiveinvoice.`comment`,'V' as `type`
               FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
               WHERE `status` >= 3 AND collectiveinvoice.needs_planning = 1) t1  
        $sWhere
    ";
//     var_dump($sQuery);

    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM ( SELECT DISTINCT
        orders.id,orders.number,orders.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) AS customer,
        orders.delivery_date as deliverydate,orders.notes as `comment`,'K' as `type`
        FROM orders INNER JOIN businesscontact ON orders.businesscontact_id = businesscontact.id
        INNER JOIN orders_calculations ON orders_calculations.order_id = orders.id
        WHERE orders_calculations.state = 1
        UNION
        SELECT collectiveinvoice.id,collectiveinvoice.number,collectiveinvoice.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) as customer,
        collectiveinvoice.deliverydate,collectiveinvoice.`comment`,'V' as `type`
        FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
        WHERE `status` >= 3 AND collectiveinvoice.needs_planning = 1) t1  
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
//      $aColumns = array( 'id', 'number', 'title', 'customer', 'deliverydate', 'comment', 'type' );
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'customer' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'null' )
            {
                $row[] = null;
            }
            else if ( $aColumns[$i] == 'number' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'title' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'comment' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'deliverydate' )
            {
                $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'type' )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
        }
        $row[] = null;
// 		var_dump($row); echo "</br>";
        $output['aaData'][] = $row;
    }
    
//     var_dump($output); echo "</br>";
     
    echo json_encode( $output );
    
//     echo  json_last_error();
?>