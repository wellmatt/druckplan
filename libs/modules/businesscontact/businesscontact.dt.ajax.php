<?php

    require_once '../../../config.php';

    $aColumns = array( 'id', 'cust_number', 'matchcode', 'name1', 'city', 'customer', 'supplier', 'attribute' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "businesscontact";
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "attribute" )
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
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' && $aColumns[$i] != "attribute" )
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
        $sWhere = " WHERE active = 1 ";
    }
    else
    {
        $sWhere .= " AND active = 1 ";
    }
    
    if ( isset($_GET['filter_attrib']) && $_GET['filter_attrib'] != "0" ){
        $tmp_attrib_filter = explode("|",$_GET['filter_attrib']);
        $sWhere .= " AND businesscontact_attributes.attribute_id = ".$tmp_attrib_filter[0]." AND businesscontact_attributes.item_id = ".$tmp_attrib_filter[1]." ";
    }
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS DISTINCT id, matchcode, cust_number, name1, city, customer, supplier
        FROM   $sTable 
        LEFT JOIN businesscontact_attributes ON businesscontact.id = businesscontact_attributes.businesscontact_id
        $sWhere
        $sOrder
        $sLimit
    ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT FOUND_ROWS()
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   $sTable WHERE active = 1
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
//         echo "Durchlauf f�r :" . $aRow[ $aColumns[0] ] . "</br> </br>";
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == "version" )
            {
                /* Special output formatting for 'version' column */
                $row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'customer' )
            {
                switch ($aRow[ $aColumns[$i] ]) {
                    case 0:
                        $row[] = 'kein';
                        break;
                    case 1:
                        $row[] = 'Kunde';
                        break;
                    case 2:
                        $row[] = 'Interessent';
                        break;
                    case 3:
                        $row[] = 'Spezial';
                        break;
                }
            }
            else if ( $aColumns[$i] == 'supplier' )
            {
                if ($aRow[$aColumns[$i]] == 1){
                    $row[] = 'ja';
                } else {
                    $row[] = 'nein';
                }
            }
            else if ( $aColumns[$i] == 'name1' )
            {
//                 $row[] = utf8_encode(nl2br(htmlentities($aRow[ $aColumns[$i] ])));
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'city' )
            {
                $row[] = nl2br(htmlentities($aRow[ $aColumns[$i] ]));
            }
            else if ( $aColumns[$i] == 'matchcode' )
            {
                if ($aRow[ $aColumns[$i] ] != NULL && $aRow[ $aColumns[$i] ] != "")
                    $row[] = nl2br(htmlentities($aRow[ $aColumns[$i] ]));
                else 
                    $row[] = nl2br(htmlentities(" "));
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'attribute' )
            {
                $tmp_row = '';
                $sQueryAttributes = 'SELECT businesscontact_attributes.attribute_id, businesscontact_attributes.item_id, attributes.title, attributes_items.title
                                     FROM businesscontact_attributes INNER JOIN attributes ON businesscontact_attributes.attribute_id = attributes.id
                                     INNER JOIN attributes_items ON businesscontact_attributes.item_id = attributes_items.id AND businesscontact_attributes.attribute_id = attributes_items.attribute_id
                                     WHERE businesscontact_attributes.businesscontact_id = '.$aRow[ $aColumns[0] ];
//                 var_dump($sQueryAttributes); echo "</br>";
                $rAttributes = mysql_query( $sQueryAttributes, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
                while ($data = mysql_fetch_row($rAttributes)){
                    $tmp_row .= $data[2] . " - " . $data[3] . "\n";
                }
                if ($tmp_row == ''){
                    $tmp_row = 'keine';
                }
                $row[] = nl2br(htmlentities($tmp_row));
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities($aRow[ $aColumns[$i] ]));
            }
        }
		$row[] = '<a class="icon-link" href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id='.$aRow[ $aColumns[0] ].'"><img src="../images/icons/pencil.png"></a>
		          <a class="icon-link" href="#" onclick="askDel(\'index.php?page=libs/modules/businesscontact/businesscontact.php&exec=delete&id='.$aRow[ $aColumns[0] ].'\')"><img	src="images/icons/cross-script.png"> </a>';
// 		var_dump($row); echo "</br>";
        $output['aaData'][] = $row;
    }
    
//     var_dump($output); echo "</br>";
     
    echo json_encode( $output );
    
//     echo  json_last_error();
?>