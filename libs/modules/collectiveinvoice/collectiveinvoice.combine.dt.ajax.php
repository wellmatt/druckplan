<?php
    require_once '../../../config.php';

    $aColumns = array( 'id', 'number', 'cust_name', 'title', 'crtdat', 'status' );
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "crtdat" && $aColumns[$i] != "status" && $aColumns[$i] != "type" )
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
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' && $aColumns[$i] != "crtdat" && $aColumns[$i] != "status" )
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
        $sWhere = " WHERE status > 0 ";
    }
    else
    {
        $sWhere .= " AND status > 0 ";
    }
    
    if ($_GET['start'] != ""){
        $sWhere .= " AND crtdat >= {$_GET['start']} ";
    }
    if ($_GET['end'] != ""){
        $sWhere .= " AND crtdat <= {$_GET['end']} ";
    }

    if ($_GET['cust_id'] != ""){
        $sWhere .= " AND bcid = {$_GET['cust_id']} ";
    }
    
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM
               (SELECT collectiveinvoice.id,collectiveinvoice.number,CONCAT(businesscontact.name1,' ',businesscontact.name2) as cust_name,collectiveinvoice.title,
               collectiveinvoice.crtdate as crtdat,collectiveinvoice.`status`, businesscontact.id as bcid 
               FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id WHERE collectiveinvoice.`status` > 0) a   
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
        FROM   
        (SELECT collectiveinvoice.id,collectiveinvoice.number,CONCAT(businesscontact.name1,' ',businesscontact.name2) as cust_name,
        collectiveinvoice.title,collectiveinvoice.crtdate,collectiveinvoice.`status`, businesscontact.id as bcid 
        FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id WHERE collectiveinvoice.`status` > 0) a
        $sWhere
    ";
//     var_dump($sQuery);

    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   
        (SELECT collectiveinvoice.id,collectiveinvoice.number,CONCAT(businesscontact.name1,' ',businesscontact.name2) as cust_name,
        collectiveinvoice.title,collectiveinvoice.crtdate,collectiveinvoice.`status`
        FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id WHERE collectiveinvoice.`status` > 0) a
        WHERE status > 0
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
            if ( $aColumns[$i] == 'cust_name' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'number' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'title' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'crtdat' )
            {
                $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'status' )
            {
                $tmp_row = '';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 1){
                    $tmp_row .= 'red.gif';
                } else {
                    $tmp_row .= 'black.gif';
                }
                $tmp_row .= '" title="Vorgang angelegt">';

                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 2){
                    $tmp_row .= 'orange.gif';
                } else {
                    $tmp_row .= 'black.gif';
                }
                $tmp_row .= '" title="Vorgang gesendet">';

                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 3){
                    $tmp_row .= 'yellow.gif';
                } else {
                    $tmp_row .= 'black.gif';
                }
                $tmp_row .= '" title="Vorgang angenommen">';

                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 4){
                    $tmp_row .= 'lila.gif';
                } else {
                    $tmp_row .= 'black.gif';
                }
                $tmp_row .= '" title="In Produktion">';

                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 5){
                    $tmp_row .= 'green.gif';
                } else {
                    $tmp_row .= 'black.gif';
                }
                $tmp_row .= '" title="Erledigt">';
                
                
                $row[] = nl2br($tmp_row);
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