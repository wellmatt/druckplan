<?php

    require_once '../../../config.php';

    $aColumns = array( 'id', 'preview', 'title', 'bname', 'atitle', 'hidden' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "personalization";
     
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

    if ($_GET['customer'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE bid = {$_GET['customer']} ";
        } else {
            $sWhere .= " AND bid = {$_GET['customer']} ";
        }
    }
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM
               ( SELECT 
               personalization.id,
               personalization.preview,
               personalization.title,
               CONCAT(businesscontact.name1, ' ',businesscontact.name2) as bname,
               businesscontact.id as bid,
               article.title as atitle,
               personalization.hidden
               FROM
               personalization
               LEFT JOIN article ON personalization.article = article.id
               LEFT JOIN businesscontact ON personalization.customer = businesscontact.id  WHERE personalization.`status` > 0 ) t1 
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT COUNT(id) FROM
               ( SELECT 
               personalization.id,
               personalization.preview,
               personalization.title,
               CONCAT(businesscontact.name1, ' ',businesscontact.name2) as bname,
               businesscontact.id as bid,
               article.title as atitle,
               personalization.hidden
               FROM
               personalization
               LEFT JOIN article ON personalization.article = article.id
               LEFT JOIN businesscontact ON personalization.customer = businesscontact.id  WHERE personalization.`status` > 0 ) t1 
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(personalization.id) 
        FROM 
        personalization 
        LEFT JOIN article ON personalization.article = article.id 
        LEFT JOIN businesscontact ON personalization.customer = businesscontact.id 
        WHERE personalization.`status` > 0
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
            if ( $aColumns[$i] == 'preview' )
            {
                if ($aRow[$aColumns[$i]] != NULL && $aRow[$aColumns[$i]] !=""){
					$row[] = '<img src="docs/personalization/'.$aRow[$aColumns[$i]].'" width="100px">&nbsp;';
        		} else {
        			$row[] = '<img src="images/icons/image.png" title="Kein Bild hinterlegt" alt="Bild">';
        		}
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'bid' )
            {
                /* do not print */
            }
            else if ( $aColumns[$i] == 'hidden' )
            {
                if ($aRow[$aColumns[$i]] == 1){
                    $row[] = '<img src="images/status/red_small.gif">';
                } else {
                    $row[] = '<img src="images/status/green_small.gif">';
                }
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
//         $row[] = '<a class="icon-link" href="index.php?page=libs/modules/personalization/personalization.php&exec=edit&id='.$aRow[ $aColumns[0] ].'"><img src="images/icons/pencil.png" title="Bearbeiten"></a>
//                   <a class="icon-link" href="#"	onclick="askDel(\'index.php?page=libs/modules/personalization/personalization.php&exec=delete&id='.$aRow[ $aColumns[0] ].'\')"><img src="images/icons/cross-script.png" title="L&ouml;schen"></a>';
// 		var_dump($row); echo "</br>";
        $output['aaData'][] = $row;
    }
    
//     var_dump($output); echo "</br>";
     
    echo json_encode( $output );
    
//     echo  json_last_error();
?>